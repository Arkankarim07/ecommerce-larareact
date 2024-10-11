<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\RegistrationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute untuk apiResource
Route::group(['prefix' => 'v1'], function () {

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('products', [ProductController::class, 'index']);
        Route::post('products/create', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products', [ProductController::class, 'destroy']);
        Route::post('logout', function (Request $request) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Logout successful',
            ], 200);
        });;
    });

    Route::get('products/{product}', [ProductController::class, 'show']);

    // Rute untuk bulk delete
    Route::delete('products/bulk-delete', [ProductController::class, 'bulkDestroy']);

    // Rute untuk apiResource Brand

    Route::get('brands', [BrandController::class, 'index']);
    Route::get('brands/{brand}', [BrandController::class, 'show']);
    Route::post('brands', [BrandController::class, 'store']);
    Route::delete('brands/{brand}', [BrandController::class, 'destroy']);

    // untuk yang ada gambar di form-data tulis di key _method value put agar bisa jalan 
    Route::put('brands/{brand}', [BrandController::class, 'update']);

    Route::post('users', [RegistrationController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});
