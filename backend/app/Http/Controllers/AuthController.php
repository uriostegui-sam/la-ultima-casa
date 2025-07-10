<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

        $artist = $user->artist()->first();

        return response()->json([
            'user' => [
                ...$user->toArray(),
                'artist_id' => $artist?->id,
            ],
            'token' => $token->plainTextToken
        ], 201);
    }


    public function login(LoginRequest $request)
    {
        if (!$request->authenticate()) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        $token = $this->authService->createAuthToken($user);

        $artist = $user->artist()->first();

        return response()->json([
            'user' => [
                ...$user->toArray(),
                'artist_id' => $artist?->id,
            ],
            'token' => $token->plainTextToken
        ]);
    }

    //update password
    public function updatePassword(UpdatePasswordRequest $request){
        $user = $request->user();

        if(!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['incorrectPassword'],
            ]);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json(['message' => 'successPassword']);
    }

    //reset password
    public function resetPassword(Request $request) {
        $user = User::findOrFail($request->id);
        
        $temporaryPassword = explode("@",$user->email)[0] . date("Y");
        $user->password = Hash::make($temporaryPassword);
        // $user->must_change_password = true;
        $user->save();

        return response()->json([
            'message' => 'resetPassword',
            'temporary_password' => $temporaryPassword
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

            $artist = $user->artist()->first();

            return response()->json([
                'user' => [
                    ...$user->toArray(),
                    'artist_id' => $artist?->id,
                ],
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

        return response()->json(['message' => 'Logged out successfully']);
    }
}
