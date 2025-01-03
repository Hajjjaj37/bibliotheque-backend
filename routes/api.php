<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\CategoryController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('registre', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Product routes (public)
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('products/search', [ProductController::class, 'search']);
Route::get('product/{category}', [ProductController::class, 'filterByCategory']);


Route::resource('category', "App\Http\Controllers\CategoryController");


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    // Cart routes
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/items', [CartController::class, 'addItem']);
    Route::put('cart/items/{id}', [CartController::class, 'updateItem']);
    Route::delete('cart/items/{id}', [CartController::class, 'removeItem']);
    Route::delete('cart', [CartController::class, 'clear']);

    // Order routes
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}', [OrderController::class, 'update']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);

    // Payment routes
    Route::post('payment', [PaymentController::class, 'process']);
    Route::post('payments/capture', [PaymentController::class, 'capturePayment']);
    Route::post('payment/process', [PaymentController::class, 'process']);
    Route::get('payment/success', [PaymentController::class, 'success']);

    // Delivery routes
    Route::get('deliveries', [DeliveryController::class, 'index']);
    Route::post('deliveries', [DeliveryController::class, 'store']);
    Route::get('deliveries/{id}', [DeliveryController::class, 'show']);
    Route::put('deliveries/{id}', [DeliveryController::class, 'update']);
    Route::get('deliveries/search', [DeliveryController::class, 'search']);

    // Review routes
    Route::get('products/{productId}/reviews', [ReviewController::class, 'index']);
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

    // Admin routes
           // Product management
           Route::post('products', [ProductController::class, 'store']);
           Route::put('products/{products}', [ProductController::class, 'update']);
           Route::delete('products/{id}', [ProductController::class, 'destroy']);

           // User management
           Route::get('users', [UserController::class, 'index']);
           Route::post('users', [UserController::class, 'store']);
           Route::get('users/{id}', [UserController::class, 'show']);
           Route::put('users/{user}', [UserController::class, 'update']);
           Route::delete('users/{id}', [UserController::class, 'destroy']);
           Route::get('users/search', [UserController::class, 'search']);

           // Statistics
           Route::get('stats', [StatsController::class, 'index']);

    // Reclamation routes
    Route::apiResource('reclamations', ReclamationController::class);
});


