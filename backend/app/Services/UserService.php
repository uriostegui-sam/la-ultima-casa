<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class UserService
{
    public function findOrCreateFromGoogle(SocialiteUser $googleUser): User
    {
        return User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            $this->extractGoogleUserData($googleUser)
        );
    }

    protected function extractGoogleUserData(SocialiteUser $googleUser): array
    {
        $nameParts = explode(' ', $googleUser->name, 2);

        return [
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'google_id' => $googleUser->getId(),
            'role' => 'artist',
            'email_verified_at' => now(),
        ];
    }
}