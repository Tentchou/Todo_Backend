<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Routes d'authentification
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset'); // Nommez la route pour la fonction de réinitialisation

// Routes protégées par l'authentification Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return new \App\Http\Resources\UserResource($request->user());
    });

    // Routes pour les Todos (CRUD avec recherche et filtrage dans index)
    Route::apiResource('todos', TodoController::class);

    // Routes pour les Catégories
    Route::apiResource('categories', CategoryController::class);

    // Routes pour les Tags
    Route::apiResource('tags', TagController::class);
});

Route::get('/debug-env', function () {
    return [
        'APP_ENV' => env('APP_ENV'),
        'SESSION_SAMESITE' => env('SESSION_SAMESITE'),
        'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE'),
        'SANCTUM_STATEFUL_DOMAINS' => env('SANCTUM_STATEFUL_DOMAINS'),
    ];
});
