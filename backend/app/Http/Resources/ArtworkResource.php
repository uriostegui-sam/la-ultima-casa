<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtworkResource extends JsonResource
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
            'title' => $this->title,
            'description' => [
                'en' => $this->description['en'] ?? '',
                'es' => $this->description['es'] ?? '',
            ],
            'dimensions' => [
                'width' => $this->dimensions['width'] ?? '',
                'height' => $this->dimensions['height'] ?? '',
                'depth' => $this->dimensions['depth'] ?? '',
            ],
            'creation_date' => $this->creation_date,
            'order' => $this->order,
            'artist_id' => $this->artist_id,
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            'images' => ArtworkImageResource::collection($this->images),
        ];
    }
}
