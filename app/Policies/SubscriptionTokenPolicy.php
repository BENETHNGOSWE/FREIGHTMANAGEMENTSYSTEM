<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubscriptionToken;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionTokenPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the subscriptionToken can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list subscriptiontokens');
    }

    /**
     * Determine whether the subscriptionToken can view the model.
     */
    public function view(User $user, SubscriptionToken $model): bool
    {
        return $user->hasPermissionTo('view subscriptiontokens');
    }

    /**
     * Determine whether the subscriptionToken can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create subscriptiontokens');
    }

    /**
     * Determine whether the subscriptionToken can update the model.
     */
    public function update(User $user, SubscriptionToken $model): bool
    {
        return $user->hasPermissionTo('update subscriptiontokens');
    }

    /**
     * Determine whether the subscriptionToken can delete the model.
     */
    public function delete(User $user, SubscriptionToken $model): bool
    {
        return $user->hasPermissionTo('delete subscriptiontokens');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete subscriptiontokens');
    }

    /**
     * Determine whether the subscriptionToken can restore the model.
     */
    public function restore(User $user, SubscriptionToken $model): bool
    {
        return false;
    }

    /**
     * Determine whether the subscriptionToken can permanently delete the model.
     */
    public function forceDelete(User $user, SubscriptionToken $model): bool
    {
        return false;
    }
}
