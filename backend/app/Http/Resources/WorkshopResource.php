<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => [
                'en' => $this->title['en'] ?? '',
                'es' => $this->title['es'] ?? '',
            ],
            'description' => [
                'en' => $this->description['en'] ?? '',
                'es' => $this->description['es'] ?? '',
            ],
            'type' => $this->type ?? '',
            'start_date' => $this->start_date ?? '',
            'end_date' => $this->end_date ?? '',
            'price' => $this->price ?? 0,
            'max_students' => $this->max_students ?? 0,
            'cover_image_path' => $this->cover_image_path ?? '',
            'featured_position' => $this->featured_position ?? false,
            'skills' => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($skill) => [
                    'id' => $skill->id ?? '',
                    'en' => $skill->name['en'] ?? '',
                    'es' => $skill->name['es'] ?? '',
                ])
            ),
            'artist_id' => $this->artist_id ?? null,
            'artist' => $this->whenLoaded('artist', function () {
                return new ArtistResource($this->artist);
            }),
        ];
    }
}
