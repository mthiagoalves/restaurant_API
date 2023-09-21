<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\UserResource;
use App\Models\User;

class UserRepository
{
    public static function getAllUsers()
    {
        return UserResource::collection(User::all());
    }

    public static function getOneUsers($id)
    {
        return new UserResource(User::where('id', $id)->first());
    }
}
