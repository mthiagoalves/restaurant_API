<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Traits\HttpResponses;
use Carbon\Carbon;
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
            'table_id' => 'string|required',
            'products' => 'array|required',
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $user = UserRepository::getUserAuthenticated();

        $orderValidated = $validator->validated();

        $orderValidated['user_id'] = $user->id;
        $orderValidated['status'] = 'OP';

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

    public static function updateOrder($dataOrder, $orderId)
    {
        $validator = Validator::make($dataOrder, [
            'status' => 'string|nullable',
            'products' => 'array|nullable'
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $orderValidated = $validator->validated();

        $orderAtUpdated = Order::where('id', $orderId)->with('order_product')->first();

        if (isset($orderValidated['status'])) {
            $orderAtUpdated->update(['status' => $orderValidated['status']]);
        }
    }

    public static function sendToTrash($orderId)
    {
        $orderAtDeleted = Order::find($orderId);

        if (!$orderAtDeleted) {
            $orderTrashed = Order::onlyTrashed()->find($orderId);

            if (!$orderTrashed) {
                return HttpResponses::error('Order not found', 404);
            }

            return HttpResponses::success('Order was deleted', 200, new OrderResource($orderTrashed));
        }

        $orderAtDeleted->delete();

        if ($orderAtDeleted) {
            return HttpResponses::success('Order has been deleted', 200, new OrderResource($orderAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete order', 422);
    }

    public static function destroyOrder($orderId)
    {
        $orderAtDeleted = Order::find($orderId);

        if (!$orderAtDeleted) {
            $orderTrashed = Order::onlyTrashed()->find($orderId);

            if (!$orderTrashed) {
                return HttpResponses::error('Order not found', 404);
            }

            return HttpResponses::success('Order was deleted', 200, new OrderResource($orderTrashed));
        }

        $orderAtDeleted->forceDelete();

        if ($orderAtDeleted) {
            return HttpResponses::success('Order has been deleted', 200, new OrderResource($orderAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete order', 422);
    }

    public static function getOrderCreatedOnSeason()
    {
        $user = UserRepository::getUserAuthenticated();

        $order = Order::where('user_id', $user->id)->whereDate('created_at', '<=', Carbon::today())->orderBy('id', 'ASC')->first();

        if (!$order) {
            $orderTrashed = Order::onlyTrashed()->find($order->id);

            if (!$orderTrashed) {
                return HttpResponses::error('Order not found', 404);
            }

            return HttpResponses::success('Order was deleted', 200, new OrderResource($orderTrashed));
        }

        return new OrderResource($order);
    }
}
