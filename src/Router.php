<?php

namespace App;

class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function put($path, $handler) {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete($path, $handler) {
        $this->routes['DELETE'][$path] = $handler;
    }

    private function getRequestBody() {
        // For testing environment
        if (isset($GLOBALS['__test_input'])) {
            return $GLOBALS['__test_input'];
        }
        
        // For normal environment
        return file_get_contents('php://input');
    }

    private function matchRoute($method, $uri): ?array {
        if (!isset($this->routes[$method])) {
            return null;
        }

        if (isset($this->routes[$method][$uri])) {
            return [
                'handler' => $this->routes[$method][$uri],
                'params' => []
            ];
        }

        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = '@^' . $pattern . '$@';

            if (preg_match($pattern, $uri, $matches)) {
                // Extract parameter names from route
                preg_match_all('/\{([^}]+)\}/', $route, $paramNames);
                array_shift($matches);

                $params = array_combine($paramNames[1], $matches);
                
                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }

        return null;
    }

    public function handle($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        
        $match = $this->matchRoute($method, $uri);
        
        if (!$match) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Route not found']);
            return;
        }

        $requestBody = [];
        if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
            $input = $this->getRequestBody();
            if (!empty($input)) {
                $requestBody = json_decode($input, true) ?? [];
            }
        }

        [$controller, $method] = explode('@', $match['handler']);
        $controllerClass = "App\\Controllers\\{$controller}";
        $controllerInstance = new $controllerClass();

        if (in_array($method, ['POST', 'PUT']) && !empty($requestBody)) {
            $match['params'] = array_merge($match['params'], ['data' => $requestBody]);
        }
        
        $response = call_user_func_array(
            [$controllerInstance, $method],
            $match['params']
        );
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
