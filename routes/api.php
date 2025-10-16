<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\QuestionController;

Route::get('/user-types', [UserController::class, 'getUserTypes']);
Route::get('/document-types', [UserController::class, 'getDocumentTypes']);
Route::get('/countries', [LocationController::class, 'getCountries']);
Route::get('/states', [LocationController::class, 'getStates']);
Route::get('/localities', [LocationController::class, 'getLocalities']);
Route::get('/zip-codes', [LocationController::class, 'getZipCodes']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // ABM usuarios y perfiles (protegido)
    Route::resource('activities', ActivityController::class);
    Route::resource('users', UserController::class);

    Route::prefix('questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::get('/{id}', [QuestionController::class, 'show']);
        Route::put('/{id}', [QuestionController::class, 'update']);
        Route::delete('/{id}', [QuestionController::class, 'destroy']);
    });

});