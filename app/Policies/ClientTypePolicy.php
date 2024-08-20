<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the clientType can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list clienttypes');
    }

    /**
     * Determine whether the clientType can view the model.
     */
    public function view(User $user, ClientType $model): bool
    {
        return $user->hasPermissionTo('view clienttypes');
    }

    /**
     * Determine whether the clientType can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create clienttypes');
    }

    /**
     * Determine whether the clientType can update the model.
     */
    public function update(User $user, ClientType $model): bool
    {
        return $user->hasPermissionTo('update clienttypes');
    }

    /**
     * Determine whether the clientType can delete the model.
     */
    public function delete(User $user, ClientType $model): bool
    {
        return $user->hasPermissionTo('delete clienttypes');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete clienttypes');
    }

    /**
     * Determine whether the clientType can restore the model.
     */
    public function restore(User $user, ClientType $model): bool
    {
        return false;
    }

    /**
     * Determine whether the clientType can permanently delete the model.
     */
    public function forceDelete(User $user, ClientType $model): bool
    {
        return false;
    }
}
