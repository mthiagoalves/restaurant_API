<?php

use App\Http\Controllers\Api\v1\auth\AuthController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\EmailController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\TableController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    //Auth
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    //register new user
    Route::post('/users/store', [UserController::class, 'store'])->name('storeUser');

    Route::middleware(['auth:sanctum', 'ability:user,admin'])->group(function () {
        Route::middleware('verified')->group(function () {
            // Users Authenticated
            Route::get('/in/{username}', [UserController::class, 'getUserAuthenticated'])->name('getUserAuthenticated');
            Route::put('/in/{username}', [UserController::class, 'updateUserAuthenticated'])->name('updateUserAuthenticated');

            //Products
            Route::get('/products', [ProductController::class, 'index'])->name('getAllProducts');
            Route::get('/products/{id}', [ProductController::class, 'show'])->name('getOneProduct');

            // Orders
            Route::post('/orders/store', [OrderController::class, 'store'])->name('storeOrder');
            Route::get('/orders-active', [OrderController::class, 'getOrderCreatedOnSeason'])->name('getOrderWithUserAuthenticated');
            Route::post('/orders-add-products', [OrderController::class, 'addMoreProductToOrder'])->name('addMoreProductToOrder');
            Route::patch('/orders-update-products/{id}', [OrderController::class, 'updateOrderProducts'])->name('updateOrderProducts');
            Route::delete('/remove-product-from-the-order/{id}', [OrderController::class, 'removeProductFromTheOrder'])->name('removeProductFromTheOrder');
            Route::delete('/send-order-to-trash', [OrderController::class, 'sendToTrash'])->name('sendOrderToTrash');
        });

        // Email verify
        Route::get('/email/verify', [EmailController::class, 'showVerificationNotice'])->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', [EmailController::class, 'verify'])->name('verification.verify');

        Route::post('/email/verification-notification', [EmailController::class, 'sendVerificationNotification'])->name('verification.send');

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::middleware('auth:sanctum', 'ability:admin', 'verified')->group(function () {

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('getAllUsers');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('getOneUser');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('updateUser');
        Route::delete('/users/{id}',  [UserController::class, 'sendToTrash'])->name('sendUserToTrash');
        Route::delete('/users-destroy/{id}',  [UserController::class, 'destroy'])->name('destroyUser');

        // Tables
        Route::get('/tables', [TableController::class, 'index'])->name('getAllTables');
        Route::get('/tables/{id}', [TableController::class, 'show'])->name('getOneTable');
        Route::post('/tables/store', [TableController::class, 'store'])->name('storeTable');
        Route::put('/tables/{id}', [TableController::class, 'update'])->name('updateTable');
        Route::delete('/tables/{id}', [TableController::class, 'sendToTrash'])->name('sendTableToTrash');
        Route::delete('/tables-destroy/{id}', [TableController::class, 'destroy'])->name('destroyTable');

        // Products
        Route::post('/products/store', [ProductController::class, 'store'])->name('storeProduct');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('updateProduct');
        Route::delete('/products/{id}', [ProductController::class, 'sendToTrash'])->name('sendProductToTrash');
        Route::delete('/products-destroy/{id}', [ProductController::class, 'destroy'])->name('destroyProduct');

        //Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('getAllCategories');
        Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('getOneCategory');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('storeCategory');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('updateCategory');
        Route::delete('/categories/{id}', [CategoryController::class, 'sendToTrash'])->name('sendCategoryToTrash');
        Route::delete('/categories-destroy/{id}', [CategoryController::class, 'destroy'])->name('destroyCategory');

        //Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('getAllOrders');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('getOneOrders');
        Route::patch('/orders/{id}', [OrderController::class, 'update'])->name('updateOrder');
        Route::delete('/orders-destroy/{id}', [OrderController::class, 'destroy'])->name('destroyOrder');
    });
});
