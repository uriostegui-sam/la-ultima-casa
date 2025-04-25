<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SkillController;
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

// Public routes (no auth needed)
Route::middleware('api')->group(function () {
    Route::apiResource('artists', ArtistController::class)->only(['index', 'show']);
    Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
    Route::apiResource('artworks', ArtworkController::class)->only(['index', 'show']);
});

// Authentication routes
Route::prefix('auth')->middleware('throttle:api')->group(function () {
    // Email/Password
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Google OAuth
    Route::get('/google', [AuthController::class, 'googleAuth'])->middleware('throttle:20,1');
    Route::get('/google/callback', [AuthController::class, 'googleCallback']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', fn () => response()->json(auth()->user()));
    });
});

// Authenticated user routes (both artists and admins)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::put('/artists/{artist}', [ArtistController::class, 'update'])
        ->middleware('can:update,artist');
    Route::apiResource('skills', SkillController::class)->except(['index', 'show']);
    Route::delete('/artworks/{artwork}/images/{image}', [ArtworkController::class, 'destroyImage']);
    Route::apiResource('artworks', ArtworkController::class)->except(['index', 'show']);;
});

// Admin-only routes
Route::middleware(['auth:sanctum', 'admin', 'throttle:api'])->group(function () {
    Route::apiResource('artists', ArtistController::class)->except(['index', 'show']);
    Route::delete('/artworks/{artwork}', [ArtworkController::class, 'destroy']);
});