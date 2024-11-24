<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientAdmin;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientAdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the clientAdmin can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list client_admins');
    }

    /**
     * Determine whether the clientAdmin can view the model.
     */
    public function view(User $user, ClientAdmin $model): bool
    {
        return $user->hasPermissionTo('view client_admins');
    }

    /**
     * Determine whether the clientAdmin can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create client_admins');
    }

    /**
     * Determine whether the clientAdmin can update the model.
     */
    public function update(User $user, ClientAdmin $model): bool
    {
        return $user->hasPermissionTo('update client_admins');
    }

    /**
     * Determine whether the clientAdmin can delete the model.
     */
    public function delete(User $user, ClientAdmin $model): bool
    {
        return $user->hasPermissionTo('delete client_admins');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete client_admins');
    }

    /**
     * Determine whether the clientAdmin can restore the model.
     */
    public function restore(User $user, ClientAdmin $model): bool
    {
        return false;
    }

    /**
     * Determine whether the clientAdmin can permanently delete the model.
     */
    public function forceDelete(User $user, ClientAdmin $model): bool
    {
        return false;
    }
}
