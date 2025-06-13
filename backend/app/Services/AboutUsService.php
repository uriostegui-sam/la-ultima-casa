<?php

namespace App\Services;

use App\Models\AboutUs;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutUsService
{
    public function getAllAboutUs()
    {
        return AboutUs::all();
    }

    public function createAboutUs(array $data)
    {
        if (array_key_exists('cover_image', $data) && $data['cover_image'] instanceof UploadedFile) {
            $data['cover_image'] = $this->storeCoverImage($data['cover_image'], $data);
        }

        $aboutUs = AboutUs::create([
            'cover_image' => $data['cover_image'] ?? null,
            'address' => $data['address'],
            'number' => $data['number'],
            'mail' => $data['mail'],
        ]);

        return $aboutUs;
    }

    public function updateAboutUs(AboutUs $aboutUs, array $data)
    {
        if (isset($data['cover_image'])) {
            $this->deleteOldImage($aboutUs->cover_image);
            $data['cover_image'] = $this->storeCoverImage($data['cover_image'], $aboutUs);
        }

        $aboutUs->update($data);
        return $aboutUs;
    }

    public function deleteAboutUs(AboutUs $aboutUs)
    {
        $aboutUs->delete();
    }

    protected function deleteOldImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function storeCoverImage(UploadedFile $image): string
    {
        $name = 'cover_image';
        $slug = Str::slug($name);
        $extension = $image->getClientOriginalExtension();
        $filename = "{$slug}.{$extension}";

        return $image->storeAs('aboutUs/cover-image', $filename, 'public');
    }
}
