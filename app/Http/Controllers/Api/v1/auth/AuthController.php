<?php

namespace App\Http\Controllers\Api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\v1\AuthRepository;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request)
    {
        return AuthRepository::login($request);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return HttpResponses::success('Token Revoked', 200);
    }
}
