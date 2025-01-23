<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DeriveryOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeriveryorderPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
   
    /**
     * Determine whether the DeriveryOrder can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list derivery_orders');
    }

    /**
     * Determine whether the DeriveryOrder can view the model.
     */
    public function view(User $user, DeriveryOrder $model): bool
    {
        return $user->hasPermissionTo('view derivery_orders');
    }

    /**
     * Determine whether the DeriveryOrder can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create derivery_orders');
    }

    /**
     * Determine whether the DeriveryOrder can update the model.
     */
    public function update(User $user, DeriveryOrder $model): bool
    {
        return $user->hasPermissionTo('update derivery_orders');
    }

    /**
     * Determine whether the DeriveryOrder can delete the model.
     */
    public function delete(User $user, DeriveryOrder $model): bool
    {
        return $user->hasPermissionTo('delete derivery_orders');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete derivery_orders');
    }

    /**
     * Determine whether the DeriveryOrder can restore the model.
     */
    public function restore(User $user, DeriveryOrder $model): bool
    {
        return false;
    }

    /**
     * Determine whether the DeriveryOrder can permanently delete the model.
     */
    public function forceDelete(User $user, DeriveryOrder $model): bool
    {
        return false;
    }
}