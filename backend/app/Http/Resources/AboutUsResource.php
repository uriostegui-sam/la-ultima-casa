<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'number' => $this->number ?? null,
            'cover_image' => $this->cover_image ?? null,
            'mail' => $this->mail ?? null,
            'address' => [
                'text' => $this->address['text'] ?? '',
                'map' => $this->address['map'] ?? '',
            ],
            'description' => [
                'es' => $this->description['es'] ?? '',
                'en' => $this->description['en'] ?? '',
            ],
            'logo_header' => $this->logo_header ?? null,
            'logo_footer' => $this->logo_footer ?? null,
        ];
    }
}
