<?php

namespace App\Policies;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArtworkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Artwork $artwork): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isArtist();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Artwork $artwork): bool
    {
        return $user->id === $artwork->artist->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Artwork $artwork): bool
    {
        return $user->id === $artwork->artist->user_id || $user->isAdmin();
    }
}
