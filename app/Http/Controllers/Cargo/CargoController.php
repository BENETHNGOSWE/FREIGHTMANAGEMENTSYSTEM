<?php

namespace App\Http\Controllers\Cargo;

use App\Http\Controllers\Controller;
use App\Support\Accounting\ClientContext;

abstract class CargoController extends Controller
{
    protected function clientId(): int
    {
        return ClientContext::id();
    }

    /** Warehouse / finance roles keep access even before the permission seeder is re-run. */
    protected function allow(string $permission = ''): void
    {
        $user = auth()->user();
        abort_unless($user, 401);

        if ($user->hasAnyRole(['owner', 'admin', 'chiefoperation', 'manager', 'Cashier', 'godownmanager'])) {
            return;
        }

        abort_unless(
            $permission === '' || $user->can($permission) || $user->can('view loadpackages'),
            403,
            'You do not have access to this page.'
        );
    }
}
