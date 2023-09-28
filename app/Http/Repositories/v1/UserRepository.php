<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    use HttpResponses;

    public static function getAllUsers()
    {
        return UserResource::collection(User::all());
    }

    public static function getOneUser($userId)
    {
        self::verifyUserExistent($userId);

        $user = User::where('id', $userId)->first();

        if ($user == null) {
            $userTrashed = User::onlyTrashed()->find($userId);
            return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
        }

        return new UserResource($user);
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

        $userValidated = $validator->validated();

        self::validateUniqueFields($userValidated["username"], $userValidated["email"]);

        $userValidated['password'] = self::passwordToHash($userValidated['password']);

        $created = User::create($userValidated);

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

        self::verifyUserExistent($userId);

        self::validateUniqueFields($validator->validated()["username"], $validator->validated()["email"]);

        $userAtUpdated = User::findOrFail($userId);

        $userAtUpdated->update([
            "name" => $validator->validated()["name"],
            "username" => $validator->validated()["username"],
            "email" => $validator->validated()["email"],
            "password" => self::passwordToHash($validator->validated()['password'])
        ]);

        if ($userAtUpdated) {
            return HttpResponses::success('User has been updated', 200, new UserResource($userAtUpdated));
        }

        return HttpResponses::error('Something wrong to update user', 422);
    }

    public static function sendToTrash($userId)
    {

        self::verifyUserExistent($userId);

        $userAtDeleted = User::find($userId);

        $userAtDeleted->delete();

        if ($userAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new UserResource($userAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete user', 422);
    }

    public static function destroyUser($userId)
    {
        self::verifyUserExistent($userId);

        $userAtDeleted = User::find($userId);

        $userAtDeleted->forceDelete();

        if ($userAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new UserResource($userAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete user', 422);
    }

    private static function verifyUserExistent($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return HttpResponses::error('User not found', 404);
        }
    }

    private static function validateUniqueFields($username, $email)
    {
        if (User::where('username', $username)->exists()) {
            return HttpResponses::error('Username already exist, please insert another.', 422);
        } else if (User::where('email', $email)->exists()) {
            return HttpResponses::error('Email already registred, please insert another.', 422);
        }
    }

    private static function passwordToHash($userPassword)
    {
        return Hash::make($userPassword);
    }
}
