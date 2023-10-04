<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\v1\UserRepository;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserRepository::getAllUsers();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataUser = $request->all();

        return UserRepository::storeUser($dataUser);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $userId)
    {
        return UserRepository::getOneUser($userId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $userId)
    {
        $dataUser = $request->all();

        return UserRepository::updateUser($dataUser, $userId);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function sendToTrash(string $userId)
    {
        return UserRepository::sendToTrash($userId);
    }

    /**
     * Remove the specified resource to DB.
     */
    public function destroy(string $userId)
    {
        return UserRepository::destroyUser($userId);
    }

    /**
     * Display the specified authenticated resource.
     */
    public function getUserAuthenticated()
    {
        return UserRepository::getUserAuthenticated();
    }
    /**
     * Update the specified authenticated user.
     */
    public function updateUserAuthenticated(Request $request)
    {
        $dataUser = $request->all();

        return UserRepository::updateUserAuthenticated($dataUser);
    }
}
