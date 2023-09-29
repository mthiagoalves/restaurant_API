<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\v1\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CategoryRepository::getAllCategories();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataCategory = $request->all();

        return CategoryRepository::storeCategory($dataCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $categoryId)
    {
        return CategoryRepository::getOneCategory($categoryId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $categoryId)
    {
        $dataCategory = $request->all();

        return CategoryRepository::updateCategory($dataCategory, $categoryId);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function sendToTrash(string $categoryId)
    {
        return CategoryRepository::sendToTrash($categoryId);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $categoryId)
    {
        return CategoryRepository::destoyCategory($categoryId);
    }
}
