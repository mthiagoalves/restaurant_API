<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\v1\OrderRepository;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderRepository::getAllOrders();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataOrder = $request->all();

        return OrderRepository::storeOrder($dataOrder);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $orderId)
    {
        return OrderRepository::getOneOrder($orderId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $orderId)
    {
        $dataOrder = $request->all();

        return OrderRepository::updateOrder($dataOrder, $orderId);
    }

    /**
     * Delete the specified resource in DB.
     */
    public function sendToTrash(string $orderId)
    {
        return OrderRepository::sendToTrash($orderId);
    }

    /**
     * Delete the specified resource in DB.
     */
    public function destroy(string $orderId)
    {
        return OrderRepository::destroyOrder($orderId);
    }

    public function getOrderCreatedOnSeason()
    {
        return OrderRepository::getOrderCreatedOnSeason();
    }

    public function addMoreProductToOrder(Request $request)
    {
        $dataOrderProduct = $request->all();

        return OrderRepository::addMoreProductToOrder($dataOrderProduct);
    }

    public function updateOrderProducts(Request $request, $orderProductId)
    {
        $dataOrderProduct = $request->all();
        
        return OrderRepository::updateOrderProducts($dataOrderProduct, $orderProductId);
    }
}
