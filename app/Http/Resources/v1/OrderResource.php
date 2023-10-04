<?php

namespace App\Http\Resources\v1;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    private array $orderStatus = ['OP' => 'Opening', 'IC' => 'In consumption', 'AB' => 'Asked for the bill', 'P' => 'Paid'];

    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'name' => $this->user->name,
                'username' => $this->user->username,
                'email' => $this->user->email,
            ],
            'table' => [
                'tableNumble' => $this->table->number
            ],
            'products' => OrderProductResource::collection($this->order_product),
            'status' => $this->orderStatus[$this->status],
        ];
    }
}
