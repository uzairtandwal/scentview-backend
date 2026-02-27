<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController; // ✅ OrderController import kiya

/*
|--------------------------------------------------------------------------
| Public Routes (Sab ke liye khuli hain - Guest Mode)
|--------------------------------------------------------------------------
*/use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

Route::get('/test-email', function () {
    try {
        Mail::raw('ScentView Email Test', function ($message) {
            $message->to('aapki-email@gmail.com') // Yahan apni email likhein
                    ->subject('SMTP Test Work');
        });
        return "✅ Email sent! Check your inbox.";
    } catch (\Exception $e) {
        Log::error("❌ SMTP Error: " . $e->getMessage());
        return "❌ Error: " . $e->getMessage();
    }
});
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/slider-products', [ProductController::class, 'slider']);
Route::get('/products/{product}', [ProductController::class, 'show']);
// Is route ke zariye Flutter data bhejay gi
// ✅ Function ka naam updateFcmToken hona chahiye jo controller mein hai
Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
// Auth Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sirf Logged-in Users/Admins ke liye)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile & Logout
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ✅ Orders Logic: Ye sirf logged-in user hi kar sakta hai
    Route::post('/orders', [OrderController::class, 'store']); // Order mangwane ke liye
    Route::get('/orders', [OrderController::class, 'index']);  // Purane orders dekhne ke liye

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Sirf Admin ke liye - Dashboard Control)
    |--------------------------------------------------------------------------
    | Note: Filhal 'admin' middleware commented hai taake aap testing kar sakein,
    | lekin 'auth:sanctum' ke andar hona zaroori hai.
    */
    // Route::middleware('admin')->group(function () {
        
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Product Management
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Banner Management
        Route::post('/banners', [BannerController::class, 'store']);
        Route::put('/banners/{banner}', [BannerController::class, 'update']);
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);

        // File/Image Upload
        Route::post('/upload', [UploadController::class, 'store']);
        
    // });
});