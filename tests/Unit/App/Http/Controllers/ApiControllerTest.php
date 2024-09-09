<?php

namespace Tests\Unit\App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Faker\Factory;
use Illuminate\Http\Request as LaravelRequest;
use ReflectionClass;
use Tests\TestCase;

class ApiControllerTest extends TestCase
{
    /**
     * Used for creating fake data
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * Test that the Class is constructed with required request
     */
    public function testConstructor(): void
    {

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Check that the basic are right
        self::assertInstanceOf(ApiController::class, $apiController);
        self::assertTrue(property_exists($apiController, 'request'));

        // Make the ApiController class request property accessible
        $reflection = new ReflectionClass(ApiController::class);
        $property = $reflection->getProperty('request');
        $property->setAccessible(true);

        // Test the the request property is indeed a request object
        self::assertInstanceOf(LaravelRequest::class, $property->getValue($apiController));
    }

    /**
     * Test Pagination offset output
     */
    public function testConstructOffset(): void
    {

        // Dummy Offset
        $offset = 123;

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject('GET', ['offset' => $offset]);

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Make the ApiController class method  accessible
        $reflection = new ReflectionClass(ApiController::class);

        // Set property as accessible
        $property = $reflection->getProperty('offset');
        $property->setAccessible(true);

        // Test the the request property is indeed a request object
        self::assertIsInt($property->getValue($apiController));

        // Assert that we get the correct output
        self::assertEquals($property->getValue($apiController), $offset);
    }

    /**
     * Test Pagination offset output with no input
     */
    public function testConstructOffsetDefault(): void
    {

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Make the ApiController class method  accessible
        $reflection = new ReflectionClass(ApiController::class);

        // Set property as accessible
        $property = $reflection->getProperty('offset');
        $property->setAccessible(true);

        // Test the the request property is indeed a request object
        self::assertIsInt($property->getValue($apiController));

        // Assert that we get the correct output
        self::assertEquals(0, $property->getValue($apiController));
    }

    /**
     * Test Pagination Limit output
     */
    public function testConstructLimit(): void
    {

        // Dummy Offset
        $limit = 123;

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject('GET', ['limit' => $limit]);

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Make the ApiController class method  accessible
        $reflection = new ReflectionClass(ApiController::class);

        // Set property as accessible
        $property = $reflection->getProperty('limit');
        $property->setAccessible(true);

        // Test the request property is indeed a request object
        self::assertIsInt($property->getValue($apiController));

        // Assert that we get the correct output
        self::assertEquals($property->getValue($apiController), $limit);
    }

    /**
     * Test Pagination limit output with no input
     */
    public function testConstructLimitDefault(): void
    {

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Make the ApiController class method  accessible
        $reflection = new ReflectionClass(ApiController::class);

        // Set property as accessible
        $property = $reflection->getProperty('limit');
        $property->setAccessible(true);

        // Test the request property is indeed a request object
        self::assertIsInt($property->getValue($apiController));

        // Assert that we get the correct output
        self::assertEquals($property->getValue($apiController), $apiController::PAGINATION_LIMIT);
    }

    /**
     * Test getSuccessFulResponse method
     */
    public function testGetSuccessfulResponse(): void
    {

        // Dummy Data
        $dummyData = [
            'item1' => 'value', 'item2' => 'mushrooms', 'item3' => 123,
        ];

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Set the response data
        $apiController->setResponseData($dummyData);

        // Run the method
        $result = $apiController->getSuccessfulResponse(200);

        // Assert that we get the correct output
        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals(['data' => $dummyData], $result->getData(true));
    }

    /**
     * Test getSuccessFulResponse method with Pagination
     */
    public function testGetSuccessfulResponseWithPagination(): void
    {

        // Dummy Data
        $dummyDataPage1 = [
            'item1' => 'value', 'item2' => 'mushrooms',
        ];

        $dummyMeta = [
            'count' => count($dummyDataPage1), 'total' => 1234,
        ];

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject('GET', ['limit' => 2]);

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Set teh response data
        $apiController->addResponseMeta($dummyMeta);

        // Set the response data
        $apiController->setResponseData($dummyDataPage1);

        // Run the method
        $result = $apiController->getSuccessfulResponse(200);

        // Assert that we get the correct output
        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals([
            'meta' => $dummyMeta, 'data' => $dummyDataPage1,
        ], $result->getData(true));
    }

    /**
     * Test getSuccessfulResponse method with no output
     */
    public function testGetSuccessfulResponseEmptyResponse(): void
    {
        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);

        // Run the method
        $result = $apiController->getSuccessfulResponse(204);

        // Assert that we get the correct output
        self::assertEquals(204, $result->getStatusCode());
        self::assertEmpty($result->getData(true));
    }

    /**
     * Test setResponseData method
     */
    public function testSetResponseData(): void
    {

        // Dummy Data
        $dummyDataPage = [
            'item1' => 'value', 'item2' => 'mushrooms',
        ];

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);
        $apiController->setResponseData($dummyDataPage);

        // Make the ApiController class method  accessible
        $reflection = new ReflectionClass(ApiController::class);

        // Set property as accessible
        $property = $reflection->getProperty('responseData');
        $property->setAccessible(true);

        // Test the result is an array
        self::assertIsArray($property->getValue($apiController));

        // Assert that we get the correct output
        self::assertEquals($property->getValue($apiController), $dummyDataPage);
    }

    /**
     * Test getErrorResponse method
     */
    public function testGetErrorResponse(): void
    {

        // Dummy Data
        $dummyErrors = [
            [
                'code' => 'TEST_MY_ERROR', 'message' => 'Something is being tested',
            ],
        ];

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);
        $result = $apiController::getErrorResponse(400, $dummyErrors);

        // Assert that we get the correct output
        self::assertEquals(400, $result->getStatusCode());
        self::assertEquals(['errors' => $dummyErrors], $result->getData(true));
    }

    /**
     * Test addResponseMeta method
     */
    public function testAddResponseMeta(): void
    {

        // Dummy
        $dummyMeta = [
            'count' => 1, 'total' => 1234,
        ];

        // Create a faked get request
        $fakeGetRequest = $this->createFakeRequestObject();

        // Spin up the ApiController with a faked request
        $apiController = new ApiController($fakeGetRequest);
        $apiController->addResponseMeta($dummyMeta);

        // Make the ApiController class method  accessible
        $reflection = new ReflectionClass(ApiController::class);

        // Set property as accessible
        $property = $reflection->getProperty('responseMeta');
        $property->setAccessible(true);

        // Test the result is an array
        self::assertIsArray($property->getValue($apiController));

        // Assert that we get the correct output
        self::assertEquals($property->getValue($apiController), $dummyMeta);
    }
}
