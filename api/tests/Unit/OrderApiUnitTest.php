<?php

namespace Tests\Unit;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ListOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\PatchOrderRequest;
use Illuminate\Http\Response;
use Tests\TestCase;
use Mockery;

/**
 * Order APIs Unit Test Cases
 *
 * Class OrderApiUnitTest
 *
 * @author Gaurav Kumar <gaurav.kumar07@nagarro.com>
 *
 */

class OrderApiUnitTest extends TestCase
{

    protected $apiRoute;

    private $orderServiceMock;

    private $apiController;

    public function setUp():void
    {
        parent::setup();
        $this->apiRoute = app('Dingo\Api\Routing\UrlGenerator')->version('v1');
        $this->orderServiceMock = Mockery::mock('App\Services\OrderService');
        $this->apiController = $this->app->instance(ApiController::class, new ApiController($this->orderServiceMock) );
    }

    /**
     * Function to test if order is created successfully
    */

    public function testSuccessfullOrderCreation()
    {
        $input = [
            "origin" => ["28.459497", "77.026634", "77.026634"],
            "destination" => ["26.912434", "77.026634"]
        ];

        $output = [
            "total_distance" => 226694,
            "status" => "UNASSIGNED",
            "id" => 17
        ];

        $request = new StoreOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('createNewOrder')->andReturn($output);
        $response =  $this->apiController->store($request);
        $result = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * Failure while creating order with correct locations but distance cannot be calculated
     */

    public function testFailureForInvalidDistanceWhileOrderCreation()
    {

        $input = [
            'origin'        => [ "28.459497", "77.026634"],
            'destination'   => ["35.689487", "139.691711"]
        ];

        $request = new StoreOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('createNewOrder')->andReturn(['error' => trans('origin.google_api_distance_exception')]);
        $response =  $this->apiController->store($request);
        $result = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $result);
    }

    /**
     * Exception while creating a new order.
     *
     */
    public function testExceptionWhileCreatingOrder()
    {

        $input = [
            "origin" => ["28.459497", "77.026634", "77.026634"],
            "destination" => ["26.912434", "77.026634"]
        ];

        $request = new StoreOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('createOrder')->andThrow(new \Exception());
        $response =  $this->apiController->store($request);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $jsonResponse);
    }

     /**
     * Test if there are errors while creating an order
     *
     * @dataProvider newOrderFailureDataProvider
     *
     */

    public function testFailureCasesWhileCreatingOrder($input, $errorResponse)
    {

        $request = new StoreOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('createNewOrder')->andReturn([]);

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('POST', $this->apiRoute->route('orders.store'), $input);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Data Provider For Generating The Failure Cases
     *
     */
    public function newOrderFailureDataProvider()
    {
        return [
            [
                [
                    'origin'=>[]

                ],
                'The origin field is required.'
            ],
            [
                [
                    'origin'=>''
                ],
                'The origin must be an array. | The origin must contain 2 items. | Invalid Origin Lat & Long values, please enter valid Lat & Long as a string'
            ],
            [
                [
                    'origin'        =>  [222.9099],
                    'destination'   =>  [1212]
                ],
                'The origin must contain 2 items. | Invalid Origin Lat & Long values, please enter valid Lat & Long as a string | The destination must contain 2 items. | Invalid Destination Lat & Long values, please enter valid Lat & Long as a string'
            ],
            [
                [
                    'origin'    =>  ["28.459497", "77.026634", "77.026634"],
                    "destination"=> ["26.912434", "77.026634"]
                ],
                'The origin must contain 2 items. | Invalid Origin Lat & Long values, please enter valid Lat & Long as a string'
            ],

            [
                [
                    'origin'=>["28.459497", "77.0266341111111"],
                    'destination'=>["26.912434", "77.0211111111116634"]
                ],
                'Invalid Origin Lat & Long values, please enter valid Lat & Long as a string | Invalid Destination Lat & Long values, please enter valid Lat & Long as a string'
            ],
        ];
    }


    /**
     * Function to test the success results while updating status
     */
    public function testUpdateStatusSuccess()
    {

        $input = [
            'status' => 'TAKEN'
        ];

        $request = new PatchOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('updateStatus')->andReturn(['status' => 'Success']);

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => 1]), $input);

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['status']);
    }

    /**
     * Test Update status request with invalid id
     */
    public function testUpdateStatusFailureWithInvalidId()
    {

        $errorResponse = [
            "error" => trans('order.orders_not_found')
        ];
        $request = new PatchOrderRequest();
        $this->orderServiceMock->shouldReceive('updateStatus')->andReturn($errorResponse);

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => 909090909]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Function to check the failure results while updating order status
     *
     * @dataProvider updateStatusFailureDataProvider
     */

    public function testUpdateStatusFailures($input, $errorResponse)
    {
        $request = new PatchOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('updateStatus')->andReturn(['error' => $errorResponse]);

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => 1]), $input);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Data Provider For Generating The Failure Cases While Updating Status
     *
     */
    public function updateStatusFailureDataProvider()
    {
        return [
            [
                [
                    'status'=> ""

                ],
                'The status field is required.'
            ],
            [
                [
                    'status'=>'dummy-input'
                ],
                'satus value is invalid'
            ],
            [
                [
                    'status'=> 1
                ],
                'satus value is invalid'
            ],
            [
                [
                    'status'=> -1
                ],
                'satus value is invalid'
            ]
        ];
    }

    /**
     * Function to check the failure results while updating order status
     *
     * @dataProvider listOrdersFailureDataProvider
     */

    public function testListOrdersFailures($input, $errorResponse)
    {
        $request = new ListOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('listOrders')->andReturn(['error' => $errorResponse]);

        $response = $this->apiController->index($request);
        $responseBody = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $responseBody);
    }


    /**
     * Data Provider For Generating The Failure Cases While Listing Orders
     *
     */
    public function listOrdersFailureDataProvider()
    {
        return [
            [
                [
                    'page'=> ""

                ],
                'The page field is required.'
            ],
            [
                [
                    'page'=>'dummy-input'
                ],
                'page cannot be string'
            ],
            [
                [
                    'page'=> -1
                ],
                'page must be greater than equal to 0'
            ],
            [
                [
                    'page'=> '@!@!dsad0909'
                ],
                'page must be integer and should be greater than equal to 0'
            ],
            [
                [
                    'limit'=> ""

                ],
                'The limit field is required.'
            ],
            [
                [
                    'limit'=>'dummy-input'
                ],
                'limit cannot be string'
            ],
            [
                [
                    'limit'=> -1
                ],
                'limit must be greater than equal to 0'
            ],
            [
                [
                    'limit'=> '@!@!dsad0909'
                ],
                'limit must be integer and should be greater than equal to 0'
            ]
        ];
    }

    /**
     * Function to check the success results while listing orders
     *
     */

    public function testListOrdersSuccess()
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

        $request = new ListOrderRequest();
        $request = $request->replace($input);

        $this->orderServiceMock->shouldReceive('listOrders')->andReturn($output);
        $response = $this->apiController->index($request);
        $responseBody = json_decode($response->getContent(), true);

        foreach ($responseBody as $key => $responseData) {
            $this->assertArrayHasKey('id', $responseData);
        }
    }

    /**
     * Function to test the exception while listing orders
     */
    public function testListOrderException()
    {
        $input = [
            "limit" => 1,
            "page"  => 1
        ];

        $request = new ListOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('listOrders')->andThrow(new \Exception);

        $response = $this->apiController->index($request);
        $responseBody = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $responseBody);
    }

    /**
     * Function to test the exception while updating order
     */
    public function testUpdateOrderException()
    {
        $input = [
            'status' => 'TAKEN'
        ];
        $request = new PatchOrderRequest();
        $request = $request->replace($input);
        $this->orderServiceMock->shouldReceive('updateStatus')->andThrow(new \Exception);

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => 1]), $input);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Tear Down close mock service
     */

    public function tearDown():void
    {
        parent::tearDown();

        Mockery::close();
    }

}
