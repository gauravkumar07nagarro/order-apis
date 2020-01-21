<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;


/**
 *  Order API Integration Test
 *  class OrderApiTest
 *
 *  @author Gaurav Kumar <gaurav.kumar07@nagarro.com>
 */

class OrderApiTest extends TestCase
{

    use RefreshDatabase;

    protected $apiRoute;

    protected static $orderId;

    public function setUp():void
    {
        parent::setup();
        $this->apiRoute = app('Dingo\Api\Routing\UrlGenerator')->version('v1');
    }


    /**
     * Test if order is created successfully
     *
     * return if Google Api is invalid
     */
    public function testSuccessfulOrderCreation()
    {

        $jsonRequestBody = [
            'origin'        => ["28.459497", "77.026634"],
            'destination'   => ["26.912434", "75.787270"]
        ];

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('POST', $this->apiRoute->route('orders.store'), $jsonRequestBody);

        $jsonResponse = json_decode($response->getContent(), TRUE);

        if ( $response->getStatusCode() == Response::HTTP_UNPROCESSABLE_ENTITY ) {

            echo $jsonResponse['error'] . PHP_EOL;
            exit(0);
        }

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'distance',
            'status',
            'id'
        ]);
    }


    /**
     * Test failure with correct geolocations but distance cannot be calculated
     */
    public function testFailureForInvalidDistanceWhileOrderCreation()
    {

        $jsonRequestBody = [
            'origin'        => [ "28.459497", "77.026634"],
            'destination'   => ["35.689487", "139.691711"]
        ];

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('POST', $this->apiRoute->route('orders.store'), $jsonRequestBody);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Test if Order Listed Successfully
     *
     * @return void
     */
    public function testSuccessfulOrderList()
    {
        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('GET', $this->apiRoute->route('orders.index'));

        $response->assertStatus(Response::HTTP_OK);
    }


    /**
     * Test failures of orders listing
     * @dataProvider listOrdersFailureDataProvider
     * @return void
     */
    public function testFailuresOfOrderList($input, $errorResponse)
    {
        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('GET', $this->apiRoute->route('orders.index'), $input);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
     * Test update order status with valid order id and valid status value which is TAKEN
     */
    public function testUpdateOrderStatusSuccess()
    {

        // Create a new order first

        $jsonRequestBody = [
            'origin'        => ["28.459497", "77.026634"],
            'destination'   => ["26.912434", "75.787270"]
        ];

        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('POST', $this->apiRoute->route('orders.store'), $jsonRequestBody);

        $jsonResponse = json_decode($response->getContent(), TRUE);

        $orderId = $jsonResponse['id'];

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'distance',
            'status',
            'id'
        ]);


        // Update the order now

        $jsonRequestBody = [
            'status' => Order::TAKEN
        ];

        $url = $this->apiRoute->route('orders.patch', ['id' => $orderId]);

        $response = $this->withHeaders(['Content-type' => 'application/json'])->json('PATCH', $url, $jsonRequestBody);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['status']);
    }

    /**
     * Function to check the failure results while updating order status
     *
     * @dataProvider updateStatusFailureDataProvider
     */

    public function testUpdateStatusFailures($input, $errorResponse)
    {
        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => 1]), $input);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Test Update status failure with zero id
     */
    public function testUpdateStatusFailureWithZeroId()
    {
        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => 0]), ['status' => 'TAKEN' ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Test Update status failure with blank id
     */
    public function testUpdateStatusFailureWithBlankId()
    {
        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => " "]), ['status' => 'TAKEN' ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['error']);
    }

    /**
     * Test Update status failure with non integer id
     */
    public function testUpdateStatusFailureWithNonIntegerId()
    {
        $response = $this->withHeaders([
            'Content-type' => 'application/json',
        ])->json('PATCH', $this->apiRoute->route('orders.patch', ['id' => "test"]), ['status' => 'TAKEN' ]);

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

}
