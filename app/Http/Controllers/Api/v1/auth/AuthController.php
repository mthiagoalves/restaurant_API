<?php

namespace App\Http\Controllers\Api\v1\auth;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {

            $user = $request->user();

            $abilities = ($user->user_level === 'admin') ? ['admin'] : ['user'];

            return HttpResponses::success('Authorized', 200, [
                'token' => $request->user()->createToken('anytime', $abilities)->plainTextToken
            ]);
        }

        return HttpResponses::error('Not authorized', 403);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return HttpResponses::success('Token Revoked', 200);
    }
}
