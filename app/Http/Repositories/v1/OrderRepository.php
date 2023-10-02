<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\OrderResource;
use App\Models\Order;
use App\Traits\HttpResponses;

class OrderRepository
{
    use HttpResponses;

    public static function getAllOrders()
    {
        return OrderResource::collection(Order::all());
    }
}
