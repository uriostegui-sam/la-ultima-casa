<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\ArtworkImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $path = $this->storeImage($image, $artwork, $index + 1);
            
            ArtworkImage::create([
                'artwork_id' => $artwork->id,
                'path' => $path,
                'is_primary' => $index === 0,
                'order' => $index
            ]);
        }
    }

    public function storeImage(UploadedFile $image, Artwork $artwork, int $index): string
    {
        $slug = Str::slug($artwork->title);
        $extension = $image->getClientOriginalExtension();
        $filename = "{$slug}-{$index}.{$extension}";
        
        return $image->storeAs(
            'artworks/images', 
            $filename, 
            'public'
        );
    }

    public function deleteImage(ArtworkImage $image): void
    {
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();
    }

    public function getPaginatedArtworks()
    {
        $query = Artwork::with(['artist.user', 'images']);

        if (request()->has('artist_id')) {
            $query->where('artist_id', request('artist_id'));
        }

        if (request()->has('year')) {
            $query->whereYear('creation_date', request('year'));
        }

        return $query->latest()->paginate(10); 
    }
}