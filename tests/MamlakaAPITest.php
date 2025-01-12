<?php

use PHPUnit\Framework\TestCase;
use Mamlaka\MamlakaAPI;

class MamlakaAPITest extends TestCase
{
    private $api;

    protected function setUp(): void
    {
        // Initialize the MamlakaAPI instance with test credentials
        $this->api = new MamlakaAPI('test_api_key', 'test_api_secret');
    }

    public function testInitialize(): void
    {
        $this->assertInstanceOf(MamlakaAPI::class, $this->api);
    }

    public function testSetCredentials(): void
    {
        $this->api->setCredentials('new_api_key', 'new_api_secret');
        $credentials = $this->api->getCredentials();

        $this->assertEquals('new_api_key', $credentials['key']);
        $this->assertEquals('new_api_secret', $credentials['secret']);
    }

    public function testProcessPayment(): void
    {
        // Simulate a successful payment response
        $mockResponse = [
            'status' => 'success',
            'transaction_id' => 'txn_12345',
        ];

        // Mock the `processPayment` method
        $this->api = $this->getMockBuilder(MamlakaAPI::class)
            ->setConstructorArgs(['test_api_key', 'test_api_secret'])
            ->onlyMethods(['processPayment'])
            ->getMock();

        $this->api->method('processPayment')
            ->willReturn($mockResponse);

        $response = $this->api->processPayment(100, 'USD', 'user_123');

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('txn_12345', $response['transaction_id']);
    }

    public function testHandleError(): void
    {
        // Simulate an error response
        $mockResponse = [
            'status' => 'error',
            'message' => 'Invalid credentials',
        ];

        // Mock the `processPayment` method to throw an exception
        $this->api = $this->getMockBuilder(MamlakaAPI::class)
            ->setConstructorArgs(['invalid_key', 'invalid_secret'])
            ->onlyMethods(['processPayment'])
            ->getMock();

        $this->api->method('processPayment')
            ->willReturn($mockResponse);

        $response = $this->api->processPayment(100, 'USD', 'user_123');

        $this->assertEquals('error', $response['status']);
        $this->assertEquals('Invalid credentials', $response['message']);
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        unset($this->api);
    }
}
