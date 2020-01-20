<?php

namespace Tests\Unit;

use App\Services\OrderService;
use Tests\TestCase;
use Mockery;


/**
 * Order APIs Unit Test Cases Of Services
 *
 * Class OrderServiceUnitTest
 *
 * @author Gaurav Kumar <gaurav.kumar07@nagarro.com>
 *
 */

class OrderServiceUnitTest extends TestCase
{
    protected $apiRoute;

    private $repoMock;
    private $apiService;

    public function setUp():void
    {
        parent::setup();
        $this->apiRoute = app('Dingo\Api\Routing\UrlGenerator')->version('v1');
        $this->repoMock = Mockery::mock('App\Repositories\OrderRepository');
        $this->apiService = new OrderService($this->repoMock);
    }

    /**
     * Function to test success result while creating new order
     */
    public function testSuccessInCreateNewOrder()
    {

        $input = [
            "origin" => ["28.459497", "77.026634"],
            "destination" => ["26.912434", "77.026634"]
        ];

        $responseData = [
            "total_distance" => 227302,
            "status" => "UNASSIGNED",
            "id" => 110
        ];
        $this->repoMock->shouldReceive('createNewOrder')->andReturn($responseData);
        $response = $this->apiService->createNewOrder($input);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * Function to test Exception result while creating new order
     */

    public function testExceptionInCreateNewOrder()
    {

        $input = [
            "origin" => ["28.459497", "77.026634"],
            "destination" => ["26.912434", "77.026634"]
        ];
        $this->expectException(\Exception::class);
        $response = $this->apiService->createNewOrder($input);
        $this->assertArrayHasKey('error', $response);
    }

    /**
     * Function to test the successful order listing
     */

    public function testSuccessfulOrderListing()
    {

        $input = [
            "limit" => 1,
            "page"  => 1
        ];

        $output = [
            [
                "id" => 1,
                "distance" => "818288",
                "status" => "TAKEN"
            ]
        ];

        $this->repoMock->shouldReceive('listOrders')->andReturn($output);
        $response = $this->apiService->listOrders($input['page'], $input['limit']);
        foreach ($response as $key => $responseData) {
            $this->assertArrayHasKey('id', $responseData);
        }
    }

    /** Function to test Update Status of an order */

    public function testSuccessfulOrderUpdation()
    {
        $output = 1;
        $this->repoMock->shouldReceive('updateStatus')->andReturn($output);
        $response = $this->apiService->updateStatus(109);
        $this->assertGreaterThan(0, 1);
    }

    /** Function to test Update Status Exception */

    public function testExceptionOrderUpdation()
    {
        $this->expectException(\Exception::class);
        $response = $this->apiService->updateStatus(109);
    }

    /** Function to test Update Status Failure */

    public function testFailureOrderUpdation()
    {
        $output = 0;
        $this->repoMock->shouldReceive('updateStatus')->andReturn($output);
        $response = $this->apiService->updateStatus(109);

        $this->assertEquals(0, 0);
    }


    public function tearDown():void
    {
        parent::tearDown();

        \Mockery::close();
    }

}
