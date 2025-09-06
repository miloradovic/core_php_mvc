<?php

namespace App;

use App\Router;
use App\Routes\Api;

class AppRouter {
    private static $router;

    public static function init() {
        self::$router = new Router();
        self::registerRoutes();
        return self::$router;
    }

    private static function registerRoutes() {
        Api::register(self::$router);
    }

    public static function run() {
        self::$router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }

    public static function get($path, $handler) {
        return self::$router->get($path, $handler);
    }

    public static function post($path, $handler) {
        return self::$router->post($path, $handler);
    }

    public static function put($path, $handler) {
        return self::$router->put($path, $handler);
    }

    public static function delete($path, $handler) {
        return self::$router->delete($path, $handler);
    }
}
