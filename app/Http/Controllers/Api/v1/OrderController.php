<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\OrderResource;
use App\Models\Order;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'table_id' => 'required',
            'payment_type' => 'required|string|max:2',
            'paid' => 'required|boolean',
            'payment_date' => 'nullable',
            'value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = Order::create($validator->validated());

        if ($created) {
            return $this->success('Order created', 200, new OrderResource($created));
        }
        return $this->error('Something wrong to craeted order', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'table_id' => 'required',
            'payment_type' => 'required|string|max:2',
            'paid' => 'required|boolean',
            'payment_date' => 'nullable',
            'value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $updated = $order->update([
            'user_id' => $validated['user_id'],
            'table_id' => $validated['table_id'],
            'payment_type' => $validated['payment_type'],
            'paid' => $validated['paid'],
            'payment_date' => $validated['paid'] ? $validated['payment_date'] : null
        ]);

        if ($updated) {
            return $this->success('Orders Updated', 200, new OrderResource($updated));
        }

        return $this->error('Orders not updated, something wrong', 400);
    }

    /**
     * Delete the specified resource in storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
