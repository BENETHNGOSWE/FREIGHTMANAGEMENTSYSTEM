<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customercategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomercategoryPolicy
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
        return $user->hasPermissionTo('list customercategories');
    }

    /**
     * Determine whether the Customer can view the model.
     */
    public function view(User $user, Customercategory $model): bool
    {
        return $user->hasPermissionTo('view customercategories');
    }

    /**
     * Determine whether the Customer can create models.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create customercategories');
    }

    /**
     * Determine whether the Customer can update the model.
     */
    public function update(User $user, Customercategory $model): bool
    {
        return $user->hasPermissionTo('update customercategories');
    }

    /**
     * Determine whether the Customer can delete the model.
     */
    public function delete(User $user, Customercategory $model): bool
    {
        return $user->hasPermissionTo('delete customercategories');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete customercategories');
    }

    /**
     * Determine whether the Customer can restore the model.
     */
    public function restore(User $user, Customercategory $model): bool
    {
        return false;
    }

    /**
     * Determine whether the Customer can permanently delete the model.
     */
    public function forceDelete(User $user, Customercategory $model): bool
    {
        return false;
    }
}

