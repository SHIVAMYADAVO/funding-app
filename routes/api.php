<?php
use App\Http\Controllers\api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//authentication
Route::post('login', [ApiController::class, 'loginUser']);
Route::post('register', [ApiController::class, 'registerUser']);
Route::get('profile', [ApiController::class, 'profile']);
Route::get('logout', [ApiController::class, 'logout']);
Route::post('forgot-password', [ApiController::class, 'forgotpasswordsendOtp']);
Route::post('updatePassword/{otp}', [ApiController::class, 'updatePassword']);
Route::get('banner-get', [ApiController::class, 'bannerget']);
Route::get('category-get', [ApiController::class, 'categoryget']);
Route::get('product-get', [ApiController::class, 'productget']);
Route::post('update-Profile', [ApiController::class, 'updateProfile']);
Route::post('/favorite/toggle', [ApiController::class, 'toggleFavorite']);
Route::get('/favorite/list', [ApiController::class, 'getFavorites']);
//
