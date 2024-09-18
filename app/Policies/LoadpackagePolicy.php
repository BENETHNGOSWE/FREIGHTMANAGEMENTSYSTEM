<?php

namespace App\Policies;

use App\Models\Loadpackage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoadpackagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list loadpackages');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loadpackage $loadpackage): bool
    {
        return $user->hasPermissionTo('view loadpackages');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create loadpackages');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Loadpackage $loadpackage): bool
    {
        return $user->hasPermissionTo('update loadpackages');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Loadpackage $loadpackage): bool
    {
        return $user->hasPermissionTo('delete loadpackages');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Loadpackage $loadpackage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Loadpackage $loadpackage): bool
    {
        return false;
    }
}
