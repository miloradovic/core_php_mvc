<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use App\Models\StorageManager;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearStorage();
    }

    protected function clearStorage(): void
    {
        StorageManager::getInstance()->clear();
    }

    protected function createRequest(string $method, string $uri, array $data = []): array
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        if (!empty($data)) {
            $jsonData = json_encode($data);
            $GLOBALS['__test_input'] = $jsonData;
            // Create a memory stream to simulate php://input
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $jsonData);
            rewind($stream);
            // Override the default input stream
            $GLOBALS['HTTP_RAW_POST_DATA'] = $jsonData;
        }

        // Capture output
        ob_start();
        require __DIR__ . '/../index.php';
        $output = ob_get_clean();

        if (!$output) {
            return ['error' => 'No response from server'];
        }

        $response = json_decode($output, true);
        return $response ?: ['error' => $output];
    }

    protected function get(string $uri): array
    {
        return $this->createRequest('GET', $uri);
    }

    protected function post(string $uri, array $data = []): array
    {
        return $this->createRequest('POST', $uri, $data);
    }

    protected function delete(string $uri): array
    {
        return $this->createRequest('DELETE', $uri);
    }
}
