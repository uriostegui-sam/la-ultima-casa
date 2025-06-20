<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\Skill;
use App\Models\Artist;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkshopService
{
    public function createWorkshop(array $data, UploadedFile $image): Workshop
    {
        $position = isset($data['featured_position'])
            ? (int) $data['featured_position']
            : null;

        if ($position !== null && Workshop::where('featured_position', $position)->exists()) {
            throw ValidationException::withMessages([
                'featured_position' => 'spotAlreadyTaken',
            ]);
        }

        $data['cover_image_path'] = $this->storeImage($image, $data);

        $workshop = Workshop::create([
            'artist_id' => $data['artist_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'cover_image_path' => $data['cover_image_path'],
            'start_date' => $data['start_date'] ?? now(),
            'end_date' => $data['end_date'] ?? now()->addDays(7),
            'type' => $data['type'] ?? 'temporary',
            'price' => $data['price'] ?? 0.0,
            'max_students' => $data['max_students'] ?? 0,
            'featured_position' => $position,
        ]);

        if (!empty($data['skills'])) {
            $workshop->skills()->sync($data['skills']);
        }

        return $workshop->load('skills');
    }

    public function updateWorkshop(Workshop $workshop, array $data, ?UploadedFile $image = null): Workshop
    {
        if (array_key_exists('featured_position', $data)) {
            $position = $data['featured_position'] !== null
                ? (int) $data['featured_position']
                : null;

            if ($position !== null &&
                Workshop::where('featured_position', $position)
                        ->where('id', '!=', $workshop->id)
                        ->exists()) {

                throw ValidationException::withMessages([
                    'featured_position' => 'spotAlreadyTaken',
                ]);
            }

            $data['featured_position'] = $position;
        }

        if ($image) {
            $this->deleteImage($workshop->cover_image_path);
            $data['cover_image_path'] = $this->storeImage($image, $workshop);
        }

        $workshop->update($data);

        if (array_key_exists('skills', $data)) {
            $workshop->skills()->sync($data['skills'] ?? []);
        }

        return $workshop->fresh()->load('skills');
    }

    public function deleteWorkshop(Workshop $workshop): void
    {
        $this->deleteImage($workshop->cover_image_path);
        $workshop->delete();
    }

    public function storeImage(UploadedFile $image, Workshop|array $workshop): string
    {
        $name = is_array($workshop) ? $workshop['title']['es'] : $workshop->title['es'];
        $slug = Str::slug($name);
        $extension = $image->getClientOriginalExtension();
        $filename = "{$slug}.{$extension}";

        return $image->storeAs('workshops/images', $filename, 'public');
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
