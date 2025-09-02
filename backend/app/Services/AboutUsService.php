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

        if (array_key_exists('logo_header', $data) && $data['logo_header'] instanceof UploadedFile) {
            $data['logo_header'] = $this->storeCoverImage($data['logo_header'], $data);
        }

        if (array_key_exists('logo_footer', $data) && $data['logo_footer'] instanceof UploadedFile) {
            $data['logo_footer'] = $this->storeCoverImage($data['logo_footer'], $data);
        }

        $aboutUs = AboutUs::create([
            'cover_image' => $data['cover_image'] ?? null,
            'logo_header' => $data['logo_header'] ?? null,
            'logo_footer' => $data['logo_footer'] ?? null,
            'address' => $data['address'],
            'number' => $data['number'],
            'mail' => $data['mail'],
            'description' => $data['description']
        ]);

        return $aboutUs;
    }

    public function updateAboutUs(AboutUs $aboutUs, array $data)
    {
        if (isset($data['cover_image'])) {
            $this->deleteOldImage($aboutUs->cover_image);
            $data['cover_image'] = $this->storeCoverImage($data['cover_image'], $aboutUs);
        }

        if (isset($data['logo_header'])) {
            $this->deleteOldImage($aboutUs->logo_header);
            $data['logo_header'] = $this->storeLogoHeader($data['logo_header'], $aboutUs);
        }

        if (isset($data['logo_footer'])) {
            $this->deleteOldImage($aboutUs->logo_footer);
            $data['logo_footer'] = $this->storeLogoFooter($data['logo_footer'], $aboutUs);
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

    protected function storeLogoHeader(UploadedFile $image): string
    {
        $name = 'logo_header';
        $extension = $image->getClientOriginalExtension();
        $filename = "{$name}.{$extension}";

        return $image->storeAs('aboutUs/logo', $filename, 'public');
    }

    protected function storeLogoFooter(UploadedFile $image): string
    {
        $name = 'logo_footer';
        $extension = $image->getClientOriginalExtension();
        $filename = "{$name}.{$extension}";

        return $image->storeAs('aboutUs/logo', $filename, 'public');
    }
}
