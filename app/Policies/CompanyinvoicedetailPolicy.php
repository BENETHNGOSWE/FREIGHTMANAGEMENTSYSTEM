<?php

namespace App\Policies;

use App\Models\Companyinvoicedetail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyinvoicedetailPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list companyinvoicedetails');
    }

    /**
     * Determine whether the Companyinvoicedetail can view the model.
     */
    public function view(User $user, Companyinvoicedetail $model): bool
    {
        return $user->hasPermissionTo('view companyinvoicedetails');
    }

    /**
     * Determine whether the Companyinvoicedetail can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create companyinvoicedetails');
    }

    /**
     * Determine whether the Companyinvoicedetail can update the model.
     */
    public function update(User $user, Companyinvoicedetail $model): bool
    {
        return $user->hasPermissionTo('update companyinvoicedetails');
    }

    /**
     * Determine whether the Companyinvoicedetail can delete the model.
     */
    public function delete(User $user, Companyinvoicedetail $model): bool
    {
        return $user->hasPermissionTo('delete companyinvoicedetails');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete companyinvoicedetails');
    }

    /**
     * Determine whether the Companyinvoicedetail can restore the model.
     */
    public function restore(User $user, Companyinvoicedetail $model): bool
    {
        return false;
    }

    /**
     * Determine whether the Companyinvoicedetail can permanently delete the model.
     */
    public function forceDelete(User $user, Companyinvoicedetail $model): bool
    {
        return false;
    }
}
