<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'content' => [
                'en' => $this->title['en'],
                'es' => $this->title['es'],
            ],
            'image_url' => $this->image_url,
            'published_at' => $this->published_at,
        ];
    }
}
