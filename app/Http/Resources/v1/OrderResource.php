<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    private array $orderStatus = ['OP' => 'Opening', 'RC' => 'Received', 'IP' => 'In Preparation', 'ID' => 'To Delivered', 'CP' => 'Complete'];

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
            'status' => $this->orderStatus[$this->status],
        ];
    }
}
