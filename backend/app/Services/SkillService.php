<?php

namespace App\Services;

use App\Models\Skill;

class SkillService
{
    public function getAllSkills()
    {
        return Skill::all();
    }

    public function createSkill(array $data)
    {
        return Skill::create($data);
    }

    public function updateSkill(Skill $skill, array $data)
    {
        $skill->update($data);
        return $skill;
    }

    public function deleteSkill(Skill $skill)
    {
        $skill->delete();
    }
}