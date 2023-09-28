<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\ProductResource;
use App\Models\Product;
use App\Traits\HttpResponses;


class ProductRepository
{
    public static function getAllProducts()
    {
        return ProductResource::collection(Product::all());
    }

    public static function getOneProduct($productId)
    {
        self::verifyProductExistent($productId);

        $product = Product::where('id', $productId)->first();

        if($product == null) {
            $productTrashed = Product::onlyTrashed()->find($productId);
            return HttpResponses::success('Product was deleted', 200, new ProductResource($productTrashed));
        }

        return new ProductResource($product);
    }

    private static function verifyProductExistent($productId) {
        $product = Product::find($productId);

        if(!$product){
            return HttpResponses::error('Product not found', 404);
        }
    }
}
