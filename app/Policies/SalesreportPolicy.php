<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalesReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesreportPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
   
    /**
     * Determine whether the SalesReport can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list sales_reports');
    }

    /**
     * Determine whether the SalesReport can view the model.
     */
    public function view(User $user, SalesReport $model): bool
    {
        return $user->hasPermissionTo('view sales_reports');
    }

    /**
     * Determine whether the SalesReport can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create sales_reports');
    }

    /**
     * Determine whether the SalesReport can update the model.
     */
    public function update(User $user, SalesReport $model): bool
    {
        return $user->hasPermissionTo('update sales_reports');
    }

    /**
     * Determine whether the SalesReport can delete the model.
     */
    public function delete(User $user, SalesReport $model): bool
    {
        return $user->hasPermissionTo('delete sales_reports');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete sales_reports');
    }

    /**
     * Determine whether the SalesReport can restore the model.
     */
    public function restore(User $user, SalesReport $model): bool
    {
        return false;
    }

    /**
     * Determine whether the SalesReport can permanently delete the model.
     */
    public function forceDelete(User $user, SalesReport $model): bool
    {
        return false;
    }
}
