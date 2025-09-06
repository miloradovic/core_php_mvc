<?php

namespace App\Controllers;

class Controller {
    protected function success($data, $statusCode = 200) {
        http_response_code($statusCode);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    protected function error($message, $statusCode = 400) {
        http_response_code($statusCode);
        return [
            'success' => false,
            'error' => $message
        ];
    }

    protected function getRequestBody() {
        // Check if we're in a test environment
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            return json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
        }
        return json_decode(file_get_contents('php://input'), true);
    }
}
