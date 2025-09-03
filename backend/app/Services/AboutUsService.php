<?php

namespace App\Services;

use App\Models\AboutUs;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutUsService
{    
    private $logoFields = ['logo_header', 'logo_footer', 'logo_hero', 'logo_favicon'];

    public function getAllAboutUs()
    {
        return AboutUs::all();
    }

    public function createAboutUs(array $data)
    {
        if (array_key_exists('cover_image', $data) && $data['cover_image'] instanceof UploadedFile) {
            $data['cover_image'] = $this->storeCoverImage($data['cover_image'], $data);
        }

        foreach ($this->logoFields as $logo) {
            if (array_key_exists($logo, $data) && $data[$logo] instanceof UploadedFile) {
                $data[$logo] = $this->storeLogo($data[$logo], $logo);
            }
        }

        $aboutUs = AboutUs::create([
            'cover_image' => $data['cover_image'] ?? null,
            'logo_header' => $data['logo_header'] ?? null,
            'logo_footer' => $data['logo_footer'] ?? null,
            'logo_hero' => $data['logo_hero'] ?? null,
            'logo_favicon' => $data['logo_favicon'] ?? null,
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

        foreach ($this->logoFields as $logo) {
            if (isset($data[$logo])) {
                $this->deleteOldImage($aboutUs->{$logo});
                $data[$logo] = $this->storeLogo($data[$logo], $logo);
            }
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

    protected function storeLogo(UploadedFile $image, string $typeOfLogo): string
    {
        $name = $typeOfLogo;
        $extension = $image->getClientOriginalExtension();
        $filename = "{$name}.{$extension}";

        return $image->storeAs('aboutUs/logo', $filename, 'public');
    }
}
