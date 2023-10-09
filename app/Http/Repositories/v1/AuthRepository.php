<?php

namespace App\Http\Repositories\v1;

use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{
    use HttpResponses;

    public static function login($request)
    {
        if (Auth::attempt($request->only('email', 'password')) || Auth::attempt($request->only('username', 'password'))) {

            $userAuthenticated = $request->user();

            $abilities = ($userAuthenticated->user_level === 'admin') ? ['admin'] : ['user'];

            return HttpResponses::success('Authorized', 200, [
                'token' => $request->user()->createToken('anytime', $abilities)->plainTextToken
            ]);
        }

        return HttpResponses::error('Not authorized', 403);
    }
}
