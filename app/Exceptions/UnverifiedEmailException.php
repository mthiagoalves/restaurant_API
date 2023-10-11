<?php

namespace App\Exceptions;


use Exception;

class UnverifiedEmailException extends Exception
{
    public function render()
    {
        return response()->json([
            'error' => 'Your email address is not verified. Please verify your email to access this resource.',
        ], 403);
    }
}
