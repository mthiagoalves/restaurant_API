<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\CategoryResource;
use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Validator;

class CategoryRepository
{
    use HttpResponses;

    public static function getAllCategories()
    {
        return CategoryResource::collection(Category::all());
    }

    public static function getOneCategory($categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            $categoryTrashed = Category::onlyTrashed()->find($categoryId);

            if (!$categoryTrashed) {
                return HttpResponses::error('Category not found', 404);
            }

            return HttpResponses::success('Category was deleted', 200, new CategoryResource($categoryTrashed));
        }

        return new CategoryResource($category);
    }

    public static function storeCategory($dataCategory)
    {
        $validator = Validator::make($dataCategory, [
            'name' => 'string|max:150|required',
            'slug' => 'string|max:150|required',
            'order' => 'integer|nullable'
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $categoryValidated = $validator->validated();

        if (Category::where('slug', $categoryValidated['slug'])->exists()) {
            return HttpResponses::error('Slug already exist, please insert another', 422);
        }

        $categoryCreated = Category::create($categoryValidated);

        if ($categoryCreated) {
            return HttpResponses::success('Category created successfully', 200, new CategoryResource($categoryCreated));
        }

        return HttpResponses::error('Something wrong to create category', 400);
    }

    public static function updateCategory($dataCategory, $categoryId)
    {
        $validator = Validator::make($dataCategory, [
            'name' => 'string|max:150|required',
            'slug' => 'string|max:150|required',
            'order' => 'integer|nullable'
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $categoryAtUpdated = Category::find($categoryId);

        if (!$categoryAtUpdated) {
            $categoryTrashed = Category::onlyTrashed()->find($categoryId);

            if (!$categoryTrashed) {
                return HttpResponses::error('Category not found', 404);
            }

            return HttpResponses::success('Category was deleted', 200, new CategoryResource($categoryTrashed));
        }

        $categoryValidated = $validator->validated();

        if (Category::where('slug', $categoryValidated['slug'])->exists()) {
            return HttpResponses::error('Slug already exist, please insert another', 422);
        }

        $categoryAtUpdated->update([
            "name" => $categoryValidated['name'],
            "slug" => $categoryValidated['slug'],
            "order" => $categoryValidated['order']
        ]);

        if ($categoryAtUpdated) {
            return HttpResponses::success('Category has been updated', 200, new CategoryResource($categoryAtUpdated));
        }

        return HttpResponses::error('Something wrong to update category', 422);
    }

    public static function sendToTrash($categoryId)
    {
        $categoryAtDeleted = Category::find($categoryId);

        if (!$categoryAtDeleted) {
            $categoryTrashed = Category::onlyTrashed()->find($categoryId);

            if (!$categoryTrashed) {
                return HttpResponses::error('Category not found', 404);
            }

            return HttpResponses::success('Category was deleted', 200, new CategoryResource($categoryTrashed));
        }

        $categoryAtDeleted->delete();

        if ($categoryAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new CategoryResource($categoryAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete category', 422);
    }

    public static function destoyCategory($categoryId)
    {
        $categoryAtDestoyed = Category::find($categoryId);

        if (!$categoryAtDestoyed) {
            $categoryTrashed = Category::onlyTrashed()->find($categoryId);

            if (!$categoryTrashed) {
                return HttpResponses::error('Category not found', 404);
            }

            return HttpResponses::success('Category was deleted', 200, new CategoryResource($categoryTrashed));
        }

        $categoryAtDestoyed->forceDelete();

        if ($categoryAtDestoyed) {
            return HttpResponses::success('User has been deleted', 200, new CategoryResource($categoryAtDestoyed));
        }
        return HttpResponses::error('Something wrong to delete category', 422);
    }
}
