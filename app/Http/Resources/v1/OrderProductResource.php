<?php

namespace App\Http\Resources\v1;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    private array $orderStatus = ['OP' => 'Opening', 'RC' => 'Received', 'IP' => 'In Preparation', 'ID' => 'To Delivered', 'CP' => 'Complete'];

    public function toArray(Request $request): array
    {
        return [
            'name' => $this->product->name,
            'slug' => $this->product->slug,
            'quantity' => $this->quantity,
            'note' => $this->note,
            'price' => $this->product->price,
            'status' => $this->orderStatus[$this->status]
        ];
    }
}
