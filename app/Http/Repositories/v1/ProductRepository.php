<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\ProductResource;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Validator;

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

        if ($product == null) {
            $productTrashed = Product::onlyTrashed()->find($productId);
            return HttpResponses::success('Product was deleted', 200, new ProductResource($productTrashed));
        }

        return new ProductResource($product);
    }

    public static function storeProduct($dataProduct)
    {
        $validator = Validator::make($dataProduct, [
            'name' => 'string|max:150|required',
            'slug' => 'string|max:150|required',
            'category_id' => 'integer|required',
            'description' => 'string|nullable',
            'price' => 'decimal:2,3|required',
            'order' => 'integer|nullable'
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $productValidated = $validator->validated();

        self::verifyUniqueFields($productValidated['slug']);

        $productCreated = Product::create($productValidated);

        if ($productCreated) {
            return HttpResponses::success('Product created successfully', 200, new ProductResource($productCreated));
        }

        return HttpResponses::error('Something wrong to create product', 400);
    }

    public static function verifyUniqueFields($productSlug)
    {
        if (Product::where('slug', $productSlug)->exists()) {
            return HttpResponses::error('Slug already exist, please insert another', 422);
        }
    }

    private static function verifyProductExistent($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return HttpResponses::error('Product not found', 404);
        }
    }
}
