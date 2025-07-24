<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
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
            'name' => [
                'en' => $this->name['en'] ?? '',
                'es' => $this->name['es'] ?? '',
            ],
            'profile_image' => $this->profile_image,
            'published' => $this->published,
        ];
    }
}
