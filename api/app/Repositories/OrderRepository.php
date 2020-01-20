<?php
namespace App\Repositories;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\Helper;

class OrderRepository
{
    use Helper;
    /**
     * Function to fetch order details.
     *
     */
    public function listOrders($request)
    {
        $limit = $request->has('limit') ? $request->limit : config('api.per_page');
        $page = $request->has('page') ? $request->page : 1;
        return Order::limit($limit)->offset(($page - 1) * $limit)->get();
    }

    /**
     * Function to create a new Order
    */

    public function createNewOrder(array $orderData)
    {
        try {
            $totalDistance = $this->calculateDistanceBetweenOriginAndDestination( $orderData['origin_lat'].','. $orderData['origin_long'],
            $orderData['dest_lat']. ','. $orderData['dest_long'] );

            $orderData['total_distance'] = $totalDistance;
            $orderData['status'] = Order::UNASSIGNED;

            return Order::create($orderData);
        } catch (\Throwable $th) {
            throw $th;
        }

    }

    /**
     * Function to update the status of the order
     */
    public function updateStatus(int $id)
    {
        DB::beginTransaction();
        $affectedRows = 0;
        try {
            $result = Order::where(['id' => $id, 'status' => Order::UNASSIGNED])->lockForUpdate()->first();
            if ($result) {
                $affectedRows = Order::where([
                    'id' => $id
                ])->update([
                    'status' => Order::TAKEN
                ]);
                DB::commit();
            } else {
                DB::rollback();
                throw new \Exception(trans('order.orders_not_found'));
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update Status Failure' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }

        return $affectedRows;
    }
}

