<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['incorrectPassword'],
            ]);
        }

        $user->password = Hash::make($request->newPassword);
        $user->must_change_password = false;
        $user->save();

        return response()->json(['message' => 'successPassword']);
    }

    public function generateResetToken(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        // Confirm user is admin
        $user = User::findOrFail($request->id_admin);
        if (!$user->isAdmin()) {
            return response()->json(['message' => $user], 403);
        }
        
        $token = Str::random(40);
        DB::table('password_resets')->updateOrInsert(
            ['user_id' => $request->id],
            [
                'token' => $token,
                'expires_at' => Carbon::now()->addDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'resetTokenCreated',
            'token' => $token,
        ]);
    }

    //reset password
    public function resetPasswordWithToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$record || Carbon::parse($record->expires_at)->isPast()) {
            return response()->json(['message' => 'invalidToken'], 422);
        }

        $user = User::findOrFail($record->user_id);
        $user->password = Hash::make($request->new_password);
        $user->must_change_password = false;
        $user->save();

        DB::table('password_resets')->where('token', $request->token)->delete();

        return response()->json(['message' => 'resetPasswordSuccess']);
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
