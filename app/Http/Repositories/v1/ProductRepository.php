<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\ProductResource;
use App\Models\Product;

class ProductRepository
{
    public static function getAllProducts()
    {
        return ProductResource::collection(Product::all());
    }

    public static function getOneProduct($productId)
    {
    }
}
