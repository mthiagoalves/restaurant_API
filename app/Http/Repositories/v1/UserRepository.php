<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;


class UserRepository
{
    use HttpResponses;

    public static function getAllUsers()
    {
        return UserResource::collection(User::all());
    }

    public static function getOneUsers($id)
    {
        return new UserResource(User::where('id', $id)->first());
    }

    public static function storeUser($dataUser)
    {
        $validator = Validator::make($dataUser, [
            "name" => "string|max:150|required",
            "username" => "string|max:150|required",
            "email" => "email|required",
            "email_verified_at" => "nullable",
            "password" => ["string", "required", "regex:/((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/"],
            "remember_token" => "string|nullable"
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data Invalid', 422, $validator->errors());
        }

        if (User::where('username', $validator->validated()["username"])->exists()) {
            return HttpResponses::error('Username already exist, please insert another.', 422);
        } else if (User::where('email', $validator->validated()["email"])->exists()) {
            return HttpResponses::error('Email already registred, please insert another.', 422);
        }

        $created = User::create($validator->validated());

        if ($created) {
            return HttpResponses::success('User created successfully', 200, new UserResource($created));
        }

        return HttpResponses::error('Something wrong to create user', 400);
    }

    public static function updateUser($dataUser, $userId)
    {

        $validator = Validator::make($dataUser, [
            "name" => "string|max:150|required",
            "username" => "string|max:150|required",
            "email" => "email|required",
            "email_verified_at" => "nullable",
            "password" => ["string", "required", "regex:/((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/"],
            "remember_token" => "string|nullable"
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data Invalid', 422, $validator->errors());
        }

        if (User::where('id', '!=', $userId)->get() && User::where('username', $validator->validated()["username"])->exists()) {
            return HttpResponses::error('Username already exist, please insert another.', 500);
        } else if (User::where('email', $validator->validated()["email"])->exists() && User::where('id', '!=', $userId)->exists()) {
            return HttpResponses::error('Email already registred, please insert another.', 500);
        }

        $updated = User::where('id', $userId)->update([
            "name" => $validator->validated()["name"],
            "username" => $validator->validated()["username"],
            "email" => $validator->validated()["email"],
            "password" => $validator->validated()["password"]
        ]);

        if ($updated) {
            return HttpResponses::success('User has been updated', 200, new UserResource($updated));
        }

        return HttpResponses::error('Something wrong to update user', 422);
    }

    private static function validatedNewEmail()
    {
    }
}
