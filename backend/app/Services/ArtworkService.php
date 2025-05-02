<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\ArtworkImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    public function storeImages(Artwork $artwork, array $images): void
    {
        foreach ($images as $index => $image) {
            $path = $this->storeImage($image);
            
            ArtworkImage::create([
                'artwork_id' => $artwork->id,
                'path' => $path,
                'is_primary' => $index === 0, // First image is primary
                'order' => $index
            ]);
        }
    }

    protected function storeImage(UploadedFile $image): string
    {
        return $image->store('artworks/images', 'public');
    }

    public function deleteImage(ArtworkImage $image): void
    {
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();
    }
}