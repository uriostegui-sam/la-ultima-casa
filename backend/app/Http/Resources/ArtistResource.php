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
            'minibio' => [
                'en' => $this->minibio['en'],
                'es' => $this->minibio['es'],
            ],
            'bio' => [
                'en' => $this->bio['en'],
                'es' => $this->bio['es'],
            ],
            'skills' => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($skill) => [
                    'en' => $skill->name['en'],
                    'es' => $skill->name['es'],
                ])
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
                    'description' => [
                        'en' => $artwork->description['en'],
                        'es' => $artwork->description['es'],
                    ],
                    'creation_date' => $artwork->creation_date,
                    'images' => $artwork->images->map(fn($img) => [
                        'id' => $img->id,
                        'path' => $img->path,
                        'is_primary' => $img->is_primary,
                        'order' => $img->order,
                        'url' => $img->url,
                    ]),
                ]) : [];
            }),
        ];
    }
}
