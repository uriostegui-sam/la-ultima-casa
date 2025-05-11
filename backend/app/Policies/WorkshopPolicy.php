<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Auth\Access\Response;

class WorkshopPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workshop $workshop): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workshop $workshop): bool
    {
        return $user->isAdmin();
    }
}
