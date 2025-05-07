<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\Skill;
use App\Models\Artist;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WorkshopService
{
    public function createWorkshop(array $data, UploadedFile $image): Workshop
    {
        $data['cover_image_path'] = $this->storeImage($image);

        $workshop = Workshop::create($data);

        if (isset($data['skills'])) {
            $workshop->skills()->sync($data['skills']);
        }

        return $workshop;
    }

    public function updateWorkshop(Workshop $workshop, array $data, ?UploadedFile $image = null): Workshop
    {
        if ($image) {
            $this->deleteImage($workshop->cover_image_path);
            $data['cover_image_path'] = $this->storeImage($image);
        }

        $workshop->update($data);

        if (isset($data['skills'])) {
            $workshop->skills()->sync($data['skills']);
        }

        return $workshop;
    }

    public function deleteWorkshop(Workshop $workshop): void
    {
        $this->deleteImage($workshop->cover_image_path);
        $workshop->delete();
    }

    public function storeImage(UploadedFile $image): string
    {
        return $image->store('workshops/images', 'public');
    }

    public function deleteImage(string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function getPaginatedWorkshops($perPage = 10)
    {
        return Workshop::with('artist', 'skills')->paginate($perPage);
    }
    
}