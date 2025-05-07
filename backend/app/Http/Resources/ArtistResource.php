<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->getFullNameAttribute(),
                'email' => $this->user->email
            ],
            'name' => $this->user->getFullNameAttribute(),
            'profile_image' => $this->profile_image,
            'minibio' => translate($this->minibio),
            'bio' => translate($this->bio),
            'skills' => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($skill) => translate($skill->name))
            ),
            'social_links' => [
                'website' => $this->social_links['website'] ?? null,
                'instagram' => $this->social_links['instagram'] ?? null,
                'twitter' => $this->social_links['twitter'] ?? null,
                'flickr' => $this->social_links['flickr'] ?? null,
            ],
            'profile_image_url' => $this->profile_image_url,
            'artworks' => $this->whenLoaded('artworks', function () {
                return $this->artworks
                    ? $this->artworks->map(fn ($artwork) => [
                    'id' => $artwork->id,
                    'title' => $artwork->title,
                    'description' => translate($artwork->description),
                    'creation_date' => $artwork->creation_date,
                ]) : [];
            }),
        ];
    }
}
