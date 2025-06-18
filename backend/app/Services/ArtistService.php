<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtistService
{
    public function createArtist(array $data): Artist
    {
        if (empty($data['user_id']) && !empty($data['user'])) {
            $user = User::create([
                'first_name' => $data['user']['name'],
                'last_name' => $data['user']['lastname'],
                'email' => $data['user']['email'],
                'password' => bcrypt('laultimacasa'), // or set a default/password reset flow
                'role' => 'artist',
            ]);
            $data['user_id'] = $user->id;
        }
        
        if (array_key_exists('profile_image', $data) && $data['profile_image'] instanceof UploadedFile) {
            $data['profile_image'] = $this->storeProfileImage($data['profile_image'], $data);
        }

        $artist = Artist::create([
            'user_id' => $data['user_id'],
            'profile_image' => $data['profile_image_path'] ?? null,
            'minibio' => $data['minibio'],
            'bio' => $data['bio'],
            'social_links' => $data['social_links'] ?? []
        ]);

        if (!empty($data['skills'])) {
            $artist->skills()->sync($data['skills']);
        }

        return $artist->load('skills', 'user');
    }

    public function updateArtist(Artist $artist, array $data): Artist
    {
        if (isset($data['profile_image'])) {
            $this->deleteOldImage($artist->profile_image);
            $data['profile_image'] = $this->storeProfileImage($data['profile_image'], $artist);
        }
        if (isset($data['user'])) {
            $artist->user->update([
                'first_name' => $data['user']['name'],
                'last_name' => $data['user']['lastname'],
                'email' => $data['user']['email'] ?? $artist->user->email,
            ]);
        }

        $artist->update($data);

        if (array_key_exists('skills', $data)) {
            $artist->skills()->sync($data['skills'] ?? []);
        }

        return $artist->fresh()->load('skills', 'user');
    }

    protected function deleteOldImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function storeProfileImage(UploadedFile $image, Artist|array $artist): string
    {
        $name = is_array($artist) ? $artist['user']['name'] . ' ' . $artist['user']['lastname'] : $artist->user->first_name . ' ' . $artist->user->last_name;
        $slug = Str::slug($name);
        $extension = $image->getClientOriginalExtension();
        $filename = "{$slug}.{$extension}";

        return $image->storeAs('artists/profile-images', $filename, 'public');
    }
}