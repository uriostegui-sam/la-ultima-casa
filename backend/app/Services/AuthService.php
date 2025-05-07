<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    public function registerUser(array $data): User
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
    }

    public function createAuthToken(User $user): NewAccessToken
    {
        return $user->createToken('api-token');
    }

    public function logoutUser(User $user): void
    {
        $user->tokens()->delete();
    }
}