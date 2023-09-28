<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\CategoryResource;
use App\Models\Category;
use App\Traits\HttpResponses;

class CategoryRepository
{
    use HttpResponses;

    public static function getAllCategories()
    {
        return CategoryResource::collection(Category::all());
    }

    
}
