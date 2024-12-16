<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sendername;
use Illuminate\Auth\Access\HandlesAuthorization;

class SendernamePolicy
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
        return $user->hasPermissionTo('list sendernames');
    }

    /**
     * Determine whether the Customer can view the model.
     */
    public function view(User $user, Sendername $model): bool
    {
        return $user->hasPermissionTo('view sendernames');
    }

    /**
     * Determine whether the Customer can create models.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create sendernames');
    }

    /**
     * Determine whether the Customer can update the model.
     */
    public function update(User $user, Sendername $model): bool
    {
        return $user->hasPermissionTo('update sendernames');
    }

    /**
     * Determine whether the Customer can delete the model.
     */
    public function delete(User $user, Sendername $model): bool
    {
        return $user->hasPermissionTo('delete sendernames');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete sendernames');
    }

    /**
     * Determine whether the Customer can restore the model.
     */
    public function restore(User $user, Sendername $model): bool
    {
        return false;
    }

    /**
     * Determine whether the Customer can permanently delete the model.
     */
    public function forceDelete(User $user, Sendername $model): bool
    {
        return false;
    }
}
