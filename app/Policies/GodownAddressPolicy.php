<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GodownAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class GodownAddressPolicy
{
     use HandlesAuthorization;

    /**
     * Determine whether the branch can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list godown_addresses');
    }

    /**
     * Determine whether the branch can view the model.
     */
    public function view(User $user, GodownAddress $model): bool
    {
        return $user->hasPermissionTo('view godown_addresses');
    }

    /**
     * Determine whether the branch can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create godown_addresses');
    }

    /**
     * Determine whether the branch can update the model.
     */
    public function update(User $user, GodownAddress $model): bool
    {
        return $user->hasPermissionTo('update godown_addresses');
    }

    /**
     * Determine whether the branch can delete the model.
     */
    public function delete(User $user, GodownAddress $model): bool
    {
        return $user->hasPermissionTo('delete godown_addresses');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete godown_addresses');
    }

    /**
     * Determine whether the branch can restore the model.
     */
    public function restore(User $user, GodownAddress $model): bool
    {
        return false;
    }

    /**
     * Determine whether the branch can permanently delete the model.
     */
    public function forceDelete(User $user, GodownAddress $model): bool
    {
        return false;
    }
}
