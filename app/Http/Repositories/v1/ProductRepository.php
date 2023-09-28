<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\ProductResource;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Validator;

class ProductRepository
{
    use HttpResponses;

    public static function getAllProducts()
    {
        return ProductResource::collection(Product::all());
    }

    public static function getOneProduct($productId)
    {
        $product = Product::where('id', $productId)->first();

        if (!$product) {
            $productTrashed = Product::onlyTrashed()->find($productId);

            if (!$productTrashed) {
                return HttpResponses::error('Product not found', 404);
            }

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

        if (Product::where('slug', $productValidated['slug'])->exists()) {
            return HttpResponses::error('Slug already exist, please insert another', 422);
        }

        $productCreated = Product::create($productValidated);

        if ($productCreated) {
            return HttpResponses::success('Product created successfully', 200, new ProductResource($productCreated));
        }

        return HttpResponses::error('Something wrong to create product', 400);
    }

    public static function updateProduct($dataProduct, $productId)
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

        if (!$productValidated) {
            $productTrashed = Product::onlyTrashed()->find($productId);

            if (!$productTrashed) {
                return HttpResponses::error('Product not found', 404);
            }

            return HttpResponses::success('Product was deleted', 200, new ProductResource($productTrashed));
        }

        if (Product::where('slug', $productValidated['slug'])->exists()) {
            return HttpResponses::error('Slug already exist, please insert another', 422);
        }

        $productAtUpdated = Product::findOrFail($productId);

        $productAtUpdated->update([
            "name" => $productValidated['name'],
            "slug" => $productValidated['slug'],
            "categoy_id" => $productValidated['category_id'],
            "description" => $productValidated['description'],
            "price" => $productValidated['price'],
            "order" => $productValidated['order']
        ]);

        if ($productAtUpdated) {
            return HttpResponses::success('Product has been updated', 200, new ProductResource($productAtUpdated));
        }

        return HttpResponses::error('Something wrong to update product', 422);
    }

    public static function sendToTrash($productId)
    {
        $productAtDeleted = Product::find($productId);

        if (!$productAtDeleted) {
            $productTrashed = Product::onlyTrashed()->find($productId);

            if (!$productTrashed) {
                return HttpResponses::error('Product not found', 404);
            }

            return HttpResponses::success('Product was deleted', 200, new ProductResource($productTrashed));
        }

        $productAtDeleted->delete();

        if ($productAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new ProductResource($productAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete product', 422);
    }

    public static function destoyProduct($productId)
    {
        $productAtDestoyed = Product::find($productId);

        if (!$productAtDestoyed) {
            $productTrashed = Product::onlyTrashed()->find($productId);

            if (!$productTrashed) {
                return HttpResponses::error('Product not found', 404);
            }

            return HttpResponses::success('Product was deleted', 200, new ProductResource($productTrashed));
        }

        $productAtDestoyed->forceDelete();

        if ($productAtDestoyed) {
            return HttpResponses::success('User has been deleted', 200, new ProductResource($productAtDestoyed));
        }
        return HttpResponses::error('Something wrong to delete product', 422);
    }
}
