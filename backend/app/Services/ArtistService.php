<?php

namespace App\Services;

use App\Models\Artist;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArtistService
{
    public function createArtist(array $data): Artist
    {
        if (array_key_exists('profile_image', $data) && $data['profile_image'] instanceof UploadedFile) {
            $data['profile_image'] = $this->storeProfileImage($data['profile_image']);
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

        return $artist->load('skills');
    }

    public function updateArtist(Artist $artist, array $data): Artist
    {
        if (isset($data['profile_image'])) {
            $this->deleteOldImage($artist->profile_image);
            $data['profile_image'] = $data['profile_image']->store(
                'artists/profile-images',
                'public'
            );
        }

        $artist->update($data);

        if (array_key_exists('skills', $data)) {
            $artist->skills()->sync($data['skills'] ?? []);
        }

        return $artist->fresh()->load('skills');
    }

    protected function deleteOldImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function storeProfileImage(UploadedFile $image): string
    {
        return $image->store('artists/profile-images', 'public');
    }

    public function getDetailedArtist(Artist $artist): array
    {
        $artist->load([
            'user',
            'skills',
            'artworks.images' => function($query) {
                $query->orderBy('order');
            }
        ]);

        return [
            'id' => $artist->id,
            'user' => [
                        'id' => $artist->user->id,
                        'name' => $artist->user->getFullNameAttribute(),
                        'email' => $artist->user->email
                    ],
            'bio' => translate($artist->bio),
            'profile_image_url' => $artist->profile_image_url,
            'skills' => $artist->skills->map(fn ($skill) => translate($skill->name)),
            'artworks' => $artist->artworks ? $artist->artworks->take(5)->map(function ($artwork) {
                return [
                    'id' => $artwork->id,
                    'title' => $artwork->title,
                    'description' => translate($artwork->description),
                    'creation_date' => $artwork->creation_date,
                    'images' => $artwork->images->map(fn ($img) => [
                        'path' => $img->path,
                        'is_primary' => $img->is_primary,
                        'order' => $img->order
                    ])
                ];
            }) : []
        ];
    }
}