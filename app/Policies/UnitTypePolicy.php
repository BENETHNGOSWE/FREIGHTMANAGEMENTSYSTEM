<?php

namespace App\Policies;

use App\Models\UnitType;
use App\Models\User;

class UnitTypePolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list unit_types');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UnitType $model): bool
    {
        return $user->hasPermissionTo('view unit_types');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create unit_types');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UnitType $model): bool
    {
        return $user->hasPermissionTo('update unit_types');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UnitType $model): bool
    {
        return $user->hasPermissionTo('delete unit_types');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UnitType $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UnitType $model): bool
    {
        return false;
    }
}
