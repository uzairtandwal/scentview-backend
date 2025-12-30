<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// ✅ Ye Imports bohot zaroori hain, warna error ayega
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return "Cache Cleared";
});

// Public Routes (Baghair Login ke data milega)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

// ✅ Create Routes (Ab ye Public hain - 401 Error Khatam)
Route::post('/categories', [CategoryController::class, 'store']);
Route::post('/products', [ProductController::class, 'store']);

// Agar image upload ka route bhi hai to wo bhi yahan add karein
// Route::post('/upload', [ImageController::class, 'upload']);

// User Route (Login user check karne ke liye)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/banners', [AdminController::class, 'banners'])->name('admin.banners');
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
});