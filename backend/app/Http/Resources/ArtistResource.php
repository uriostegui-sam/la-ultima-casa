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
                'name' => $this->user->first_name,
                'lastname' => $this->user->last_name,
                'email' => $this->user->email
            ],
            'name' => $this->user->getFullNameAttribute(),
            'profile_image' => $this->profile_image,
            'minibio' => [
                'en' => $this->minibio['en'] ?? '',
                'es' => $this->minibio['es'] ?? '',
            ],
            'bio' => [
                'en' => $this->bio['en'] ?? '',
                'es' => $this->bio['es']?? '',
            ],
            'skills' => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($skill) => [
                    'id' => $skill->id,
                    'en' => $skill->name['en'] ?? '',
                    'es' => $skill->name['es'] ?? '',
                ])
            ),
            'social_links' => [
                'website' => $this->social_links['website'] ?? null,
                'instagram' => $this->social_links['instagram'] ?? null,
                'twitter' => $this->social_links['twitter'] ?? null,
                'flickr' => $this->social_links['flickr'] ?? null,
            ],
            'profile_image_url' => $this->profile_image_url,
            'artworks' => ArtworkResource::collection($this->whenLoaded('artworks')),
        ];
    }
}
