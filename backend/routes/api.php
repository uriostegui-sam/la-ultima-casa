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
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1'); // 5 req/min
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // 5 req/min

    // Google OAuth
    Route::get('/google', [AuthController::class, 'googleAuth'])->middleware('throttle:20,1');
    Route::get('/google/callback', [AuthController::class, 'googleCallback']);

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function () {
            return response()->json(auth()->user());
        });
    });
});