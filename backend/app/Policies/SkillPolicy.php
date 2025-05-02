<?php

namespace App\Policies;

use App\Models\Skill;
use App\Models\User;

class SkillPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a model.
     */
    public function view(User $user, Skill $skill): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update a model.
     */
    public function update(User $user, Skill $skill): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a model.
     */
    public function delete(User $user, Skill $skill): bool
    {
        return $user->isAdmin();
    }
}
