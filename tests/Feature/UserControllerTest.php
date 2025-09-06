<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    private array $validUserData = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'dateOfBirth' => '1990-01-01'
    ];

    protected function getJsonResponseData($response): array
    {
        if (is_string($response)) {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            return ['error' => $response];
        }
        return $response;
    }

    public function test_can_get_empty_users_list(): void
    {
        $response = $this->getJsonResponseData($this->get('/users'));

        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);
        $this->assertEmpty($response['data']);
    }

    public function test_can_create_user(): void
    {
        $response = $this->getJsonResponseData($this->post('/users', $this->validUserData));

        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($this->validUserData['email'], $response['data']['email']);
    }

    public function test_cannot_create_user_under_18(): void
    {
        $underage = array_merge($this->validUserData, [
            'dateOfBirth' => date('Y-m-d', strtotime('-17 years'))
        ]);

        $response = $this->getJsonResponseData($this->post('/users', $underage));

        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('must be at least', $response['error']['dateOfBirth'][0]);
    }

    public function test_cannot_create_user_with_duplicate_email(): void
    {
        // Create first user
        $this->getJsonResponseData($this->post('/users', $this->validUserData));

        // Try to create second user with same email
        $response = $this->getJsonResponseData($this->post('/users', $this->validUserData));

        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('has already been taken', $response['error']['email'][0]);
    }

    public function test_can_get_single_user(): void
    {
        // Create a user first
        $createResponse = $this->getJsonResponseData($this->post('/users', $this->validUserData));
        $userId = $createResponse['data']['id'];

        // Get the user
        $response = $this->getJsonResponseData($this->get("/users/{$userId}"));

        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($this->validUserData['email'], $response['data']['email']);
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        $response = $this->getJsonResponseData($this->get('/users/999'));

        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('not found', $response['error']);
    }

    public function test_can_delete_user(): void
    {
        // Create a user first
        $createResponse = $this->getJsonResponseData($this->post('/users', $this->validUserData));
        $userId = $createResponse['data']['id'];

        // Delete the user
        $response = $this->getJsonResponseData($this->delete("/users/{$userId}"));

        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('message', $response['data']);

        // Verify user is gone
        $getResponse = $this->getJsonResponseData($this->get("/users/{$userId}"));
        $this->assertFalse($getResponse['success']);
        $this->assertArrayHasKey('error', $getResponse);
        $this->assertStringContainsString('not found', $getResponse['error']);
    }
}
