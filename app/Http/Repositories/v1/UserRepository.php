<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
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
        $user = User::where('id', $userId)->first();

        if (!$user) {
            $userTrashed = User::onlyTrashed()->find($userId);

            if ($userTrashed) {
                return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
            }

            return HttpResponses::error('User not found', 404);
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

        if (User::where('username', $userValidated['username'])->exists()) {
            return HttpResponses::error('Username already exist, please insert another.', 422);
        } else if (User::where('email', $userValidated['email'])->exists()) {
            return HttpResponses::error('Email already registred, please insert another.', 422);
        }

        $userValidated['password'] = self::passwordToHash($userValidated['password']);

        $userValidated['user_level'] = 'user';

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

        if (!User::find($userId)) {
            $userTrashed = User::onlyTrashed()->find($userId);

            if ($userTrashed) {
                return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
            }

            return HttpResponses::error('User not found', 404);
        }

        if (User::where('username', $validator->validated()["username"])->exists()) {
            return HttpResponses::error('Username already exist, please insert another.', 422);
        } else if (User::where('email', $validator->validated()["email"])->exists()) {
            return HttpResponses::error('Email already registred, please insert another.', 422);
        }

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

        if (!User::find($userId)) {
            $userTrashed = User::onlyTrashed()->find($userId);

            if ($userTrashed) {
                return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
            }

            return HttpResponses::error('User not found', 404);
        }

        $userAtDeleted = User::find($userId);

        $userAtDeleted->delete();

        if ($userAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new UserResource($userAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete user', 422);
    }

    public static function destroyUser($userId)
    {
        if (!User::find($userId)) {
            $userTrashed = User::onlyTrashed()->find($userId);

            if ($userTrashed) {
                return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
            }

            return HttpResponses::error('User not found', 404);
        }

        $userAtDeleted = User::find($userId);

        $userAtDeleted->forceDelete();

        if ($userAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new UserResource($userAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete user', 422);
    }

    private static function passwordToHash($userPassword)
    {
        return Hash::make($userPassword);
    }

    // Authenticated functions

    public static function getUserAuthenticated()
    {
        $userAuthenticated = Auth::user();

        $user = User::where('id', $userAuthenticated->id)->first();

        if (!$user) {
            $userTrashed = User::onlyTrashed()->find($user->id);

            if ($userTrashed) {
                return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
            }

            return HttpResponses::error('User not found', 404);
        }

        return new UserResource($user);
    }

    public static function updateUserAuthenticated($dataUser)
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

        $userAuthenticated = Auth::user();

        $user = User::where('id', $userAuthenticated->id)->first();

        if (!$user) {
            $userTrashed = User::onlyTrashed()->find($user->id);

            if ($userTrashed) {
                return HttpResponses::success('User was deleted', 200, new UserResource($userTrashed));
            }

            return HttpResponses::error('User not found', 404);
        }

        if (User::where('username', $validator->validated()["username"])->exists()) {
            return HttpResponses::error('Username already exist, please insert another.', 422);
        } else if (User::where('email', $validator->validated()["email"])->exists()) {
            return HttpResponses::error('Email already registred, please insert another.', 422);
        }

        $user->update([
            "name" => $validator->validated()["name"],
            "username" => $validator->validated()["username"],
            "email" => $validator->validated()["email"],
            "password" => self::passwordToHash($validator->validated()['password'])
        ]);

        if ($user) {
            return HttpResponses::success('User has been updated', 200, new UserResource($user));
        }

        return HttpResponses::error('Something wrong to update user', 422);
    }
}
