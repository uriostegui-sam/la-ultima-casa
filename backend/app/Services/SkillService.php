<?php

namespace App\Services;

use App\Models\Skill;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SkillService
{
    public function getAllSkills()
    {
        return Skill::all();
    }

    public function createSkill(array $data)
    {
        if (array_key_exists('profile_image', $data) && $data['profile_image'] instanceof UploadedFile) {
            $data['profile_image'] = $this->storeProfileImage($data['profile_image'], $data);
        }

        $skill = Skill::create([
            'profile_image' => $data['profile_image'] ?? null,
            'name' => $data['name'],
            'published' => $data['published'] ?? false,
        ]);

        return $skill;
    }

    public function updateSkill(Skill $skill, array $data)
    {
        if (isset($data['profile_image'])) {
            $this->deleteOldImage($skill->profile_image);
            $data['profile_image'] = $this->storeProfileImage($data['profile_image'], $skill);
        }

        $skill->update($data);
        return $skill;
    }

    public function deleteSkill(Skill $skill)
    {
        $skill->delete();
    }

    protected function deleteOldImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function storeProfileImage(UploadedFile $image, Skill|array $skill): string
    {
        $name = is_array($skill) ? $skill['name']['es'] : $skill->name['es'];
        $slug = Str::slug($name);
        $extension = $image->getClientOriginalExtension();
        $filename = "{$slug}.{$extension}";

        return $image->storeAs('skills/profile-images', $filename, 'public');
    }
}
