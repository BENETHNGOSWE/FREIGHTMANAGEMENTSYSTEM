<?php

namespace App\Policies;

use App\Models\User;
use App\Models\officeExpenseCategory;

class OfficeExpenseCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list office_expense_categories');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, officeExpenseCategory $model): bool
    {
        return $user->hasPermissionTo('view office_expense_categories');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create office_expense_categories');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, officeExpenseCategory $model): bool
    {
        return $user->hasPermissionTo('update office_expense_categories');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, officeExpenseCategory $model): bool
    {
        return $user->hasPermissionTo('delete office_expense_categories');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, officeExpenseCategory $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, officeExpenseCategory $model): bool
    {
        return false;
    }
}