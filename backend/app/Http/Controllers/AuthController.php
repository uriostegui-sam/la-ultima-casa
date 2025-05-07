<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->registerUser($request->validated());
        $token = $this->authService->createAuthToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (!$request->authenticate()) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $this->authService->createAuthToken($request->user());

        return response()->json([
            'user' => $request->user(),
            'token' => $token->plainTextToken
        ]);
    }

    // Google Auth
    public function googleAuth()
    {
        // Generate a stateless URL for frontend redirect
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl()
        ]);
    }

    // Google Callback
    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
    
            $user = $this->userService->findOrCreateFromGoogle($googleUser);
            $token = $this->authService->createAuthToken($user);

            return response()->json([
                'user' => $user,
                'token' => $token->plainTextToken
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Google authentication failed',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 401);
        }
    }

    // Logout
    public function logout()
    {
        $this->authService->logoutUser(auth()->user());

        return response()->json(['message' => 'Not authenticated'], 401);
    }
}
