<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customerlead;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerleadPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
   
    /**
     * Determine whether the Customer can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list customerleads');
    }

    /**
     * Determine whether the Customer can view the model.
     */
    public function view(User $user, Customerlead $model): bool
    {
        return $user->hasPermissionTo('view customerleads');
    }

    /**
     * Determine whether the Customer can create models.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create customerleads');
    }

    /**
     * Determine whether the Customer can update the model.
     */
    public function update(User $user, Customerlead $model): bool
    {
        return $user->hasPermissionTo('update customerleads');
    }

    /**
     * Determine whether the Customer can delete the model.
     */
    public function delete(User $user, Customerlead $model): bool
    {
        return $user->hasPermissionTo('delete customerleads');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete customerleads');
    }

    /**
     * Determine whether the Customer can restore the model.
     */
    public function restore(User $user, Customerlead $model): bool
    {
        return false;
    }

    /**
     * Determine whether the Customer can permanently delete the model.
     */
    public function forceDelete(User $user, Customerlead $model): bool
    {
        return false;
    }
}
