<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReviewController;

Route::group(['prefix' => 'common'], function () {
    Route::get('/user-types', [UserController::class, 'getUserTypes']);
    Route::get('/document-types', [UserController::class, 'getDocumentTypes']);
    Route::get('/countries', [LocationController::class, 'getCountries']);
    Route::get('/states', [LocationController::class, 'getStates']);
    Route::get('/localities', [LocationController::class, 'getLocalities']);
    Route::get('/zip-codes', [LocationController::class, 'getZipCodes']);
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'admin_login'])->name('login');

Route::middleware('auth:api', 'role:Administrador, Profesional')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // ABM usuarios y perfiles (protegido)
    Route::resource('activities', ActivityController::class);
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/profile_picture', [UserController::class, 'uploadProfilePicture']);
    Route::post('/users/{id}/change-status', [UserController::class, 'setActiveStatus']);

    Route::prefix('questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::get('/{id}', [QuestionController::class, 'show']);
        Route::put('/{id}', [QuestionController::class, 'update']);
        Route::delete('/{id}', [QuestionController::class, 'destroy']);
    });

    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::get('/{id}', [ReviewController::class, 'show']);
        Route::put('/{id}', [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });
});

// Recuperar contraseña (envío de correo y cambio directo)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::group(['prefix' => 'professionals'], function () {
    Route::post('/login', [AuthController::class, 'professional_login'])->name('professional_login');
    Route::get('/', [UserController::class, 'professionals']);
    Route::get('/{professional}', [UserController::class, 'show_professionals']);
    Route::get('/{professional}/get/reviews', [UserController::class, 'get_reviews_professional']);
    Route::get('/{professional}/get/questions', [UserController::class, 'get_questions_professional']);

    Route::middleware('auth:api', 'role:Profesional')->group(function () {
        Route::get('/get/reviews', [UserController::class, 'get_reviews_professional']);
        Route::get('/get/questions', [UserController::class, 'get_questions_professional']);
        Route::get('/get/detail', [UserController::class, 'get_professional_detail']);
        Route::put('/respond/review/{review_id}', [UserController::class, 'professionals_respond_review']);
        Route::put('/respond/question/{question_id}', [UserController::class, 'professionals_respond_question']);
        Route::put('/update_profile', [UserController::class, 'professional_update_profile']);
        Route::post('/update_profile_picture', [UserController::class, 'uploadProfilePictureProfessional']);
    });
});