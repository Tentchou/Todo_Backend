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

use Illuminate\Foundation\Auth\EmailVerificationRequest;

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


// Route pour envoyer le lien de vérification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['status' => 'verification-link-sent']);
})->middleware(['auth:sanctum'])->name('verification.send');

// Route pour vérifier l'email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Route pour vérifier si email est vérifié
Route::get('/email/verify', function (Request $request) {
    return $request->user()->hasVerifiedEmail()
        ? response()->json(['verified' => true])
        : response()->json(['verified' => false]);
})->middleware(['auth:sanctum'])->name('verification.notice');

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
