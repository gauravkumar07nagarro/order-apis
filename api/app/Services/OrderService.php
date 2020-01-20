<?php
namespace App\Services;
use App\Repositories\OrderRepository;
class OrderService
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Function to update the status of the order
     */
    public function updateStatus(int $id)
    {
        return $this->orderRepository->updateStatus($id);
    }

    /**
     * Function to create a new Order
    */

    public function createNewOrder(array $orderData)
    {
        return $this->orderRepository->createNewOrder($orderData);
    }

    /**
     * List Orders
     */
    public function listOrders($request)
    {
        return $this->orderRepository->listOrders($request);
    }
}
