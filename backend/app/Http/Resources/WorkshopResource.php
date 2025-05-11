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
                'en' => $this->title['en'],
                'es' => $this->title['es'],
            ],
            'description' => [
                'en' => $this->description['en'],
                'es' => $this->description['es'],
            ],
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'price' => $this->price,
            'max_students' => $this->max_students,
            'cover_image_url' => $this->cover_image_url,
            'skills' => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($skill) => translate($skill->name))
            ),
            'artist_id' => $this->artist_id,
            'artist' => $this->whenLoaded('artist', function () {
                return new ArtistResource($this->artist);
            }),
        ];
    }
}
