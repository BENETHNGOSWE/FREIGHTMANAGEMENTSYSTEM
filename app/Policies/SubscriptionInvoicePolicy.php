<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubscriptionInvoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionInvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the subscriptionInvoice can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list subscriptioninvoices');
    }

    /**
     * Determine whether the subscriptionInvoice can view the model.
     */
    public function view(User $user, SubscriptionInvoice $model): bool
    {
        return $user->hasPermissionTo('view subscriptioninvoices');
    }

    /**
     * Determine whether the subscriptionInvoice can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create subscriptioninvoices');
    }

    /**
     * Determine whether the subscriptionInvoice can update the model.
     */
    public function update(User $user, SubscriptionInvoice $model): bool
    {
        return $user->hasPermissionTo('update subscriptioninvoices');
    }

    /**
     * Determine whether the subscriptionInvoice can delete the model.
     */
    public function delete(User $user, SubscriptionInvoice $model): bool
    {
        return $user->hasPermissionTo('delete subscriptioninvoices');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete subscriptioninvoices');
    }

    /**
     * Determine whether the subscriptionInvoice can restore the model.
     */
    public function restore(User $user, SubscriptionInvoice $model): bool
    {
        return false;
    }

    /**
     * Determine whether the subscriptionInvoice can permanently delete the model.
     */
    public function forceDelete(User $user, SubscriptionInvoice $model): bool
    {
        return false;
    }
}
