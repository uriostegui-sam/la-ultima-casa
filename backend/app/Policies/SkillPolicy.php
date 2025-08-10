<?php

namespace App\Policies;

use App\Models\User;

class SkillPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
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
    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a model.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
