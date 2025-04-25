<?php

namespace App\Services;

use App\Models\Artwork;
use Illuminate\Http\UploadedFile;

class ArtworkService
{
    public function createArtwork(array $data, UploadedFile $image): Artwork
    {
        return Artwork::create([
            'artist_id' => $data['artist_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'image_path' => $this->storeImage($image),
            'dimensions' => $data['dimensions'] ?? null,
            'creation_date' => $data['creation_date'] ?? now()
        ]);
    }

    protected function storeImage(UploadedFile $image): string
    {
        return $image->store('artworks', 'public');
    }
}