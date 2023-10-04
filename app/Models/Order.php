<?php

namespace App\Models;

use App\Http\Resources\v1\OrderResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'table_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function order_product()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function filter(Request $request)
    {
        $queryFilter = (new OrderFilter)->filter($request);

        if (empty($queryFilter)) {
            return OrderResource::collection(Order::with('user')->get());
        }

        $data = Order::with('user');

        if (!empty($queryFilter['whereIn'])) {
            var_dump($queryFilter['whereIn']);
            // foreach ($queryFilter['whereIn'] as $value) {
            //   $data->whereIn($value[0], $value[1]);
            // }
        }

        // $resource = $data->where($queryFilter['where'])->get();

        // return InvoiceResource::collection($resource);
    }
}
