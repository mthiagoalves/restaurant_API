<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Validator;

class OrderRepository
{
    use HttpResponses;

    public static function getAllOrders()
    {
        return OrderResource::collection(Order::all());
    }

    public static function getOneOrder($orderId)
    {

        $order = Order::find($orderId);

        if (!$order) {
            $orderTrashed = Order::onlyTrashed()->find($orderId);

            if (!$orderTrashed) {
                return HttpResponses::error('Order not found', 404);
            }

            return HttpResponses::success('Order was deleted', 200, new OrderResource($orderTrashed));
        }

        return new OrderResource($order);
    }

    public static function storeOrder($dataOrder)
    {

        $validator = Validator::make($dataOrder, [
            'user_id' => 'string|required',
            'table_id' => 'string|required',
            'status' => 'string|nullable',
            'products' => 'array|required'
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $orderValidated = $validator->validated();

        $orderCreated = Order::create($orderValidated);

        if ($orderCreated) {
            foreach ($orderValidated['products'] as $productData) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $orderCreated->id;
                $orderProduct->product_id = $productData['product_id'];
                $orderProduct->quantity = $productData['quantity'];
                $orderProduct->note = $productData['note'];
                $orderProduct->status = 'OP';

                $orderProduct->save();
            }
            return HttpResponses::success('Order created', 201);
        }

        return HttpResponses::error('Something wrong to create order', 400);
    }
}
