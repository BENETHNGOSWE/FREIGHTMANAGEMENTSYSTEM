<?php

namespace App\Policies;

use App\Models\Loadpackagetype;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoadpackagetypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list loadpackagetypes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loadpackagetype $model): bool
    {
        return $user->hasPermissionTo('view loadpackagetypes');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create loadpackagetypes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Loadpackagetype $model): bool
    {
        return $user->hasPermissionTo('update loadpackagetypes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Loadpackagetype $model): bool
    {
        return $user->hasPermissionTo('delete loadpackagetypes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Loadpackagetype $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Loadpackagetype $model): bool
    {
        return false;
    }
}