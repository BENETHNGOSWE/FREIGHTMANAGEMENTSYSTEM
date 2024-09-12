<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create default permissions
        Permission::create(['name' => 'list banks']);
        Permission::create(['name' => 'view banks']);
        Permission::create(['name' => 'create banks']);
        Permission::create(['name' => 'update banks']);
        Permission::create(['name' => 'delete banks']);

        Permission::create(['name' => 'list branches']);
        Permission::create(['name' => 'view branches']);
        Permission::create(['name' => 'create branches']);
        Permission::create(['name' => 'update branches']);
        Permission::create(['name' => 'delete branches']);

        Permission::create(['name' => 'list clients']);
        Permission::create(['name' => 'view clients']);
        Permission::create(['name' => 'create clients']);
        Permission::create(['name' => 'update clients']);
        Permission::create(['name' => 'delete clients']);

        Permission::create(['name' => 'list clientadmins']);
        Permission::create(['name' => 'view clientadmins']);
        Permission::create(['name' => 'create clientadmins']);
        Permission::create(['name' => 'update clientadmins']);
        Permission::create(['name' => 'delete clientadmins']);

        Permission::create(['name' => 'list clientpayments']);
        Permission::create(['name' => 'view clientpayments']);
        Permission::create(['name' => 'create clientpayments']);
        Permission::create(['name' => 'update clientpayments']);
        Permission::create(['name' => 'delete clientpayments']);

        Permission::create(['name' => 'list clienttypes']);
        Permission::create(['name' => 'view clienttypes']);
        Permission::create(['name' => 'create clienttypes']);
        Permission::create(['name' => 'update clienttypes']);
        Permission::create(['name' => 'delete clienttypes']);

        Permission::create(['name' => 'list countries']);
        Permission::create(['name' => 'view countries']);
        Permission::create(['name' => 'create countries']);
        Permission::create(['name' => 'update countries']);
        Permission::create(['name' => 'delete countries']);

        Permission::create(['name' => 'list messages']);
        Permission::create(['name' => 'view messages']);
        Permission::create(['name' => 'create messages']);
        Permission::create(['name' => 'update messages']);
        Permission::create(['name' => 'delete messages']);

        Permission::create(['name' => 'list modules']);
        Permission::create(['name' => 'view modules']);
        Permission::create(['name' => 'create modules']);
        Permission::create(['name' => 'update modules']);
        Permission::create(['name' => 'delete modules']);

        Permission::create(['name' => 'list packages']);
        Permission::create(['name' => 'view packages']);
        Permission::create(['name' => 'create packages']);
        Permission::create(['name' => 'update packages']);
        Permission::create(['name' => 'delete packages']);

        Permission::create(['name' => 'list patients']);
        Permission::create(['name' => 'view patients']);
        Permission::create(['name' => 'create patients']);
        Permission::create(['name' => 'update patients']);
        Permission::create(['name' => 'delete patients']);

        Permission::create(['name' => 'list staffs']);
        Permission::create(['name' => 'view staffs']);
        Permission::create(['name' => 'create staffs']);
        Permission::create(['name' => 'update staffs']);
        Permission::create(['name' => 'delete staffs']);

        Permission::create(['name' => 'list paymenttypes']);
        Permission::create(['name' => 'view paymenttypes']);
        Permission::create(['name' => 'create paymenttypes']);
        Permission::create(['name' => 'update paymenttypes']);
        Permission::create(['name' => 'delete paymenttypes']);

        Permission::create(['name' => 'list role']);
        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'list subscriptions']);
        Permission::create(['name' => 'view subscriptions']);
        Permission::create(['name' => 'create subscriptions']);
        Permission::create(['name' => 'update subscriptions']);
        Permission::create(['name' => 'delete subscriptions']);

        Permission::create(['name' => 'list subscriptioninvoices']);
        Permission::create(['name' => 'view subscriptioninvoices']);
        Permission::create(['name' => 'create subscriptioninvoices']);
        Permission::create(['name' => 'update subscriptioninvoices']);
        Permission::create(['name' => 'delete subscriptioninvoices']);

        Permission::create(['name' => 'list subscriptiontokens']);
        Permission::create(['name' => 'view subscriptiontokens']);
        Permission::create(['name' => 'create subscriptiontokens']);
        Permission::create(['name' => 'update subscriptiontokens']);
        Permission::create(['name' => 'delete subscriptiontokens']);

        Permission::create(['name' => 'list userroles']);
        Permission::create(['name' => 'view userroles']);
        Permission::create(['name' => 'create userroles']);
        Permission::create(['name' => 'update userroles']);
        Permission::create(['name' => 'delete userroles']);

        // Create admin exclusive permissions
        Permission::create(['name' => 'list roles']);
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'update roles']);
        Permission::create(['name' => 'delete roles']);

        Permission::create(['name' => 'list permissions']);
        Permission::create(['name' => 'view permissions']);
        Permission::create(['name' => 'create permissions']);
        Permission::create(['name' => 'update permissions']);
        Permission::create(['name' => 'delete permissions']);

        Permission::create(['name' => 'list users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        // Create user role and assign existing permissions
        //  $currentPermissions = Permission::all();
        $userRole = Role::create(['name' => 'user']);
        $superAdminRole = Role::create(['name' => 'owner']);
        $adminRole = Role::create(['name' => 'admin']);
        $staffRole = Role::create(['name' => 'staff']);
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdminRole->givePermissionTo($allPermissionNames);

        // Create admin role and assign all permissions
        $allPermissions = Permission::all();
        // $adminRole = Role::create(['name' => 'owner']);
        $superAdminRole->givePermissionTo($allPermissions);

        $superAdminUser = User::firstOrCreate(
            [
                'email' => 'superadmin@gmail.com',
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('12345678'),
            ],
        );

        $superAdminUser->assignRole($superAdminRole);

        $adminUser = User::firstOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
            ],
        );

        $adminUser->assignRole($adminRole);

        $staffUser = User::firstOrCreate(
            [
                'email' => 'staff@gmail.com',
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@gmail.com',
                'password' => Hash::make('12345678'),
            ],
        );

        $staffUser->assignRole($staffRole);

        // $user = \App\Models\User::whereEmail('superadmin@admin.com')->first();

        // if ($user) {
        //     $user->assignRole($adminRole);
        // }
    }
}
