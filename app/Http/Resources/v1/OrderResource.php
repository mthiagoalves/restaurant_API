<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    private array $types = ['CD' => 'Cartão Débito', 'CC' => 'Cartão Crédito', 'B' => 'Boleto', 'P' => 'Pix'];

    public function toArray(Request $request): array
    {
        $paid = $this->paid;
        return [
            'user' => [
                'name' => $this->user->name,
                'username' => $this->user->username,
                'email' => $this->user->email,
            ],
            'table' => [
                'tableNumble' => $this->table->number
            ],
            'payment_type' => $this->types[$this->payment_type],
            'value' => 'R$ ' . number_format($this->value, 2, ',', '.'),
            'paid' => $paid ? 'Pago' : 'Não Pago',
            'paymentDate' => $paid ? Carbon::parse($this->payment_date)->format('d/m/Y H:i:s') : Null,
            'paymentSince' => $paid ? Carbon::parse($this->payment_date)->diffForHumans() : Null
        ];
    }
}
