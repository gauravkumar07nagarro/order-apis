<?php
/**
 * Class is used to perform API operations on Orders table.
 *
 * @author Gaurav Kumar <gaurav.kumar07@nagarro.com>
 */

namespace App\Http\Controllers;

use App\Http\Requests\ListOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\PatchOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Response;
use App\Traits\Helper;
use Dingo\Api\Routing\Helpers as ApiHelper;

/**
 * @SWG\Swagger(
 *   basePath="/",
 *   @SWG\Info(
 *     title="Order APIs",
 *     version="1.0.0"
 *   )
 * )
 */

class ApiController extends Controller
{

    use ApiHelper, Helper;

    protected $orderService;


    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @SWG\Get(
     *      path="/orders",
     *      tags={"Orders"},
     *      operationId="ApiV1GetOrders",
     *      summary="Get Orders",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          type="integer"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          required=false,
     *          type="integer"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="[{'id': 10,'distance': 170201,'status': 'UNASSIGNED'},{'id': 11,'distance': 170211,'status':'UNASSIGNED'}]"
     *      ),
     * ),
     */

    public function index(ListOrderRequest $request) : object {
        try {
            $orders =  $this->orderService->listOrders($request);

            if (!empty($orders) ) {
                return $this->apiSuccessResponse($orders);
            } else {
                return $this->apiSuccessResponse([]);
            }
        } catch (\Throwable $th) {
            return $this->apiExceptionResponse($th);
        }
    }

    /**
     *
     * @SWG\Post(
     *      path="/orders",
     *      tags={"Orders"},
     *      operationId="ApiV1saveOrder",
     *      summary="Create a new Order",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="origin", type="array",  @SWG\Items(type="string"), example={"28.053049", "76.107712"}),
     *              @SWG\Property(property="destination", type="array",  @SWG\Items(type="string"), example={"26.912434", "75.787270"})
     *          ),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="{'distance': 170201,'status': 'UNASSIGNED','id': 8}"
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="{'error': 'Invalid location, please enter valid location or check google api'}"
     *      ),
     * )
     */

    public function store(StoreOrderRequest $request) : object {


        $originLat  = $request->origin[0];
        $originLong = $request->origin[1];

        $destLat  = $request->destination[0];
        $destLong = $request->destination[1];

        try {
            $orderData = [
                'origin_lat'        => $originLat,
                'origin_long'       => $originLong,
                'dest_lat'          => $destLat,
                'dest_long'         => $destLong
            ];
            $order =  $this->orderService->createNewOrder($orderData);

            if ( !empty($order) ) {
                return $this->apiSuccessResponse($order, Response::HTTP_OK );
            }
        } catch (\Throwable $th) {
            return $this->apiExceptionResponse($th);
        }
    }

    /**
     *
     * @SWG\Patch(
     *      path="/orders/{id}",
     *      tags={"Orders"},
     *      operationId="ApiV1pdateUOrderStatus",
     *      summary="Update order status",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(property="status", type="string",example="TAKEN")
     *          ),
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          type="integer"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="{'status': 'SUCCESS'}"
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="{'error': 'Invalid location, please enter valid location or check google api'}"
     *      ),
     * )
     * Function to Update Order Status by id
     *
     * @author gaurav.kumar07@nagarro.com
     *
     * @param Illuminate\Http\Request   $request
     * @param $id
     * @return object
     */

    public function update(PatchOrderRequest $request, int $id) : object {

        try {
            $order = $this->orderService->updateStatus($id);
            if ($order) {
                return $this->apiSuccessResponse(['status' => trans('order.update_success')], Response::HTTP_OK );
            } else {
                return $this->apiErrorResponse(trans('order.update_failure'), Response::HTTP_NOT_FOUND );
            }
        } catch (\Throwable $th) {
            return $this->apiExceptionResponse($th);
        }

    }

}
