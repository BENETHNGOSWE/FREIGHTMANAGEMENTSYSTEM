<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the clientPayment can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list clientpayments');
    }

    /**
     * Determine whether the clientPayment can view the model.
     */
    public function view(User $user, ClientPayment $model): bool
    {
        return $user->hasPermissionTo('view clientpayments');
    }

    /**
     * Determine whether the clientPayment can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create clientpayments');
    }

    /**
     * Determine whether the clientPayment can update the model.
     */
    public function update(User $user, ClientPayment $model): bool
    {
        return $user->hasPermissionTo('update clientpayments');
    }

    /**
     * Determine whether the clientPayment can delete the model.
     */
    public function delete(User $user, ClientPayment $model): bool
    {
        return $user->hasPermissionTo('delete clientpayments');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete clientpayments');
    }

    /**
     * Determine whether the clientPayment can restore the model.
     */
    public function restore(User $user, ClientPayment $model): bool
    {
        return false;
    }

    /**
     * Determine whether the clientPayment can permanently delete the model.
     */
    public function forceDelete(User $user, ClientPayment $model): bool
    {
        return false;
    }
}
