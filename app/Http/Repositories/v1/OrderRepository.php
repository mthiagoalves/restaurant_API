<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\OrderProductResource;
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

        $order = Order::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::today())->orderBy('id', 'ASC')->first();

        if (!$order) {
            return HttpResponses::error('No order created', 404);
        }

        return new OrderResource($order);
    }

    public static function addMoreProductToOrder($dataOrderProduct)
    {
        $validator = Validator::make($dataOrderProduct, [
            'products' => 'array|required',
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $user = UserRepository::getUserAuthenticated();

        $order = Order::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::today())->orderBy('id', 'ASC')->first();

        if ($order) {
            foreach ($validator->validated()['products'] as $productData) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $productData['product_id'];
                $orderProduct->quantity = $productData['quantity'];
                $orderProduct->note = $productData['note'];
                $orderProduct->status = 'OP';

                $orderProduct->save();
            }
            return HttpResponses::success('Order created', 201);
        }
    }

    public static function updateOrderProducts($dataOrderProduct, $orderProductId)
    {
        $validator = Validator::make($dataOrderProduct, [
            'quantity' => 'integer|required',
            'note' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $user = UserRepository::getUserAuthenticated();

        $order = Order::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::today())->orderBy('id', 'ASC')->first();

        $orderProduct = OrderProduct::where('id', $orderProductId)->where('order_id', $order->id)->where('status', 'RC')->first();

        if (!$orderProduct) {
            return HttpResponses::error('Product is not added in your order', 404);
        }

        $orderProduct->update([
            'quantity' => $validator->validated()['quantity'],
            'note' => $validator->validated()['note'],
            'status' => 'RC'
        ]);

        if ($orderProduct) {
            return HttpResponses::success('Order updated', 200, new OrderResource(Order::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::today())->orderBy('id', 'ASC')->first()));
        }

        return HttpResponses::error('Something wrong to updated order', 422);
    }

    public static function removeProductFromTheOrder($orderProductId)
    {
        $user = UserRepository::getUserAuthenticated();

        $order = Order::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::today())->orderBy('id', 'ASC')->first();

        $orderProduct = OrderProduct::where('id', $orderProductId)->where('order_id', $order->id)->where('status', 'RC')->first();

        if (!$orderProduct) {
            return HttpResponses::error('Product is not added in your order or is it already being produced', 404);
        }

        $orderProduct->delete();

        if ($orderProduct) {
            return HttpResponses::success('Product removed from order', 200, new OrderResource($order));
        }
        return HttpResponses::error('Something wrong to delete order', 422);
    }

    public static function sendToTrash()
    {
        $user = UserRepository::getUserAuthenticated();

        $orderAtDeleted = Order::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::today())->orderBy('id', 'ASC')->first();

        if (!$orderAtDeleted) {
            return HttpResponses::error('Order not found', 404);
        } elseif ($orderAtDeleted->status !== 'OP') {
            return HttpResponses::error("Your orders already opening, you can't delete it", 402);
        }

        $orderAtDeleted->delete();

        if ($orderAtDeleted) {
            return HttpResponses::success('Order has been deleted', 200, new OrderResource($orderAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete order', 422);
    }
}
