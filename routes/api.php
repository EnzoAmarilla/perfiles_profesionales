<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\ProfileUserController;

Route::get('/activities', [ActivityController::class, 'index']);
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/localities', [LocalityController::class, 'index']);
Route::get('/provinces/{province}/localities', [LocalityController::class, 'byProvince']);
// Route::resource('profiles', ProfileUserController::class);
// Route::resource('users', UserController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // ABM usuarios y perfiles (protegido)
    Route::resource('users', UserController::class);
    Route::resource('profiles', ProfileUserController::class);
});