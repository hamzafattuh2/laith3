<?php
use App\Http\Controllers\TouristController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TourGuideController;
use App\Http\Controllers\TwoFactorController;

use App\Http\Middleware\TwoFactor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



//test1
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([
    // auth::class,
// verified::class,
TwoFactor::class
])->middleware('auth:sanctum')->name('dashboard');//add two factor

Route::resource('verify', TwoFactorController::class);

//end test1

Route::get('/user/home',[UserController::class,'get1'])->middleware('auth:sanctum');

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::get('login1',[UserController::class,'login1']);

Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');

Route::post('registerTourGuide',[TourGuideController::class,'registerTourGuide']);//true
Route::post('loginTourGuide',[TourGuideController::class,'loginTourGuide']);//true
Route::post('logoutTourGuide',[TourGuideController::class,'logoutTourGuide'])->middleware('auth:sanctum');//true

Route::post('registerTourist',[TouristController::class,'registerTourist']);
Route::post('loginTourist',[TouristController::class,'loginTourist']);//true
