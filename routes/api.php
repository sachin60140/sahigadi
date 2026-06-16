<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;

// Public Auth Routes
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:auth');
Route::post('/auth/dealer-login', [AuthController::class, 'dealerLogin'])->middleware('throttle:auth');

// Public Data Routes
Route::get('/public/home', [PublicController::class, 'home']);
Route::get('/public/cars', [PublicController::class, 'cars']);
Route::get('/public/cars/{slug}', [PublicController::class, 'carDetail']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    
    // Dashboards
    Route::get('/dashboard/customer', [\App\Http\Controllers\Api\DashboardApiController::class, 'customerDashboard']);
    Route::get('/dashboard/dealer', [\App\Http\Controllers\Api\DashboardApiController::class, 'dealerDashboard']);

    // Actions
    Route::post('/actions/enquiry', [\App\Http\Controllers\Api\ActionController::class, 'createEnquiry']);
    Route::post('/actions/customer-listing', [\App\Http\Controllers\Api\ActionController::class, 'createCustomerListing']);
});
