<?php

use App\Http\Controllers\AuthController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



// test route for language translation
Route::get('/test-language', function () {
    return response()->json([
        'message' => __('messages.welcome'),
        'title' => translate([
            'en' => 'Hello',
            'es' => 'Hola'
        ]),
        'current_locale' => app()->getLocale()
    ]);
});

// Authentication routes 
Route::prefix('auth')->group(function () {
    // Email/Password
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth
    Route::get('/google', [AuthController::class, 'googleAuth']);
    Route::get('/google/callback', [AuthController::class, 'googleCallback']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('/user', function () {
        return response()->json(auth()->user());
    })->middleware('auth:sanctum');
});
