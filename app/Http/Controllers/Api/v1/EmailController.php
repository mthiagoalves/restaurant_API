<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    use HttpResponses;

    public function showVerificationNotice()
    {
        return $this->error('Please verify your email.', 401);
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success('Email already verified', 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->success('Email verified successfully', 200);
    }

    public function sendVerificationNotification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->success('Verification link sent.', 200);
    }
}
