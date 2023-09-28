<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\v1\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return ProductRepository::getAllProducts();
    }

    public function show($productId)
    {
        return ProductRepository::getOneProduct($productId);
    }

    public function store(Request $request){
        $dataProduct = $request->all();

        return ProductRepository::storeProduct($dataProduct);
    }

    public function update(Request $request) {

        $dataProduct = $request->all();

        return ProductRepository::updateProduct($dataProduct);
    }
}
