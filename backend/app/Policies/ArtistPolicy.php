<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArtistPolicy
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
        // Only admins can create artist profiles manually
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Artist $artist): bool
    {
        // Admins can edit any profile
        // Artists can only edit their own profile
        return $user->isAdmin() || $user->id === $artist->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Artist $artist): bool
    {
        // Only admins can delete profiles
        return $user->isAdmin();
    }
}
