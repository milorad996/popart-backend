<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarModelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->middleware('guest');
    Route::post('register', 'register')->middleware('guest');
});
Route::middleware('auth:api')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('customer-profile', 'getMyProfile');
    });
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('/customers', [UserController::class, 'getCustomers']);
});



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/listings', [ListingController::class, 'listingsByCategory']);
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/filter', [ListingController::class, 'filter']);
Route::get('/listings/{id}', [ListingController::class, 'show']);


Route::middleware('auth:api')->group(function () {
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);

    Route::delete('/listings/{listingId}/images/{imageId}', [ListingController::class, 'deleteImage']);

    Route::get('/my-listings', function () {
        return \App\Models\Listing::with(['category', 'images'])
            ->where('user_id', auth()->id())
            ->paginate(10);
    });
});
