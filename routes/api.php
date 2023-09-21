<?php

use App\Http\Controllers\api\v1\OrderController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/store', [UserController::class, 'store']);

    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::put('/order/{id}', [OrderController::class, 'update']);
});
