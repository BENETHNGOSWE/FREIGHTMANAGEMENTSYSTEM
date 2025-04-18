<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $actions = ['list', 'view', 'create', 'update', 'delete'];
        $models = ['banks', 'branches', 'containers', 'clients', 'client_admins', 'clientpayments', 'client_types', 'companyinvoicedetails',
                    'countries', 'customers', 'customercategories', 'customerleads', 'client_sendername', 'derivery_orders', 'messages', 'modules', 'packages', 
                    'loadpackagetypes', 'loadpackages', 'staffs', 'staff_types', 'payments', 'role', 'sendernames', 'subscriptions', 'subscription_invoices',
                    'subscription_tokens', 'sales_reports',  'userroles', 'roles', 'permissions', 'users', 'unit_types', 'invoices', 'invoice_loads'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissionName = $action . ' ' . $model;
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                }
            }
        }

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view client_sendername',
            'view banks', 'create banks', 'update banks', 'delete banks',
            'view branches', 'create branches', 'update branches', 'delete branches',
            'view containers', 'create containers', 'update containers', 'delete containers',
            'view customers', 'create customers', 'update customers', 'delete customers',
            'view staffs', 'update staffs',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
            'view loadpackagetypes', 'create loadpackagetypes', 'update loadpackagetypes', 'delete loadpackagetypes',
            'view loadpackages', 'create loadpackages', 'update loadpackages', 'delete loadpackages',
            'view unit_types', 'create unit_types', 'update unit_types', 'delete unit_types',
            'view invoices', 'create invoices', 'update invoices', 'delete invoices',
            'view invoice_loads', 'create invoice_loads', 'update invoice_loads', 'delete invoice_loads',
            'view companyinvoicedetails', 'create companyinvoicedetails', 'update companyinvoicedetails', 'delete companyinvoicedetails',
        ]);

        $chiefoperation = Role::firstOrCreate(['name' => 'chiefoperation', 'guard_name' => 'web']);
        $chiefoperation->syncPermissions([
            'view client_sendername',
            'view banks', 'create banks', 'update banks', 'delete banks',
            'view staffs', 'update staffs',
            'view branches', 'create branches', 'update branches', 'delete branches',
            'view containers', 'create containers', 'update containers', 'delete containers',
            'view customers', 'create customers', 'update customers', 'delete customers',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
            'view loadpackagetypes', 'create loadpackagetypes', 'update loadpackagetypes', 'delete loadpackagetypes',
            'view loadpackages', 'create loadpackages', 'update loadpackages', 'delete loadpackages',
            'view unit_types', 'create unit_types', 'update unit_types', 'delete unit_types',
            'view invoices', 'create invoices', 'update invoices', 'delete invoices',
            'view invoice_loads', 'create invoice_loads', 'update invoice_loads', 'delete invoice_loads',
            'view companyinvoicedetails', 'create companyinvoicedetails', 'update companyinvoicedetails', 'delete companyinvoicedetails',
        ]);


        $godownmanager = Role::firstOrCreate(['name' => 'godownmanager', 'guard_name' => 'web']);
        $godownmanager->syncPermissions([
           
            'view customers',
            'view customercategories',
            'view loadpackagetypes',
            'view loadpackages',
            'view invoices', 
            'view invoice_loads',
            'view derivery_orders', 'create derivery_orders', 'update derivery_orders', 'delete derivery_orders',
        ]);

        $salesperson = Role::firstOrCreate(['name' => 'salesperson', 'guard_name' => 'web']);
        $salesperson->syncPermissions([
           
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
        ]);


        $SalesManager = Role::firstOrCreate(['name' => 'SalesManager', 'guard_name' => 'web']);
        $SalesManager->syncPermissions([
           
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view sales_reports', 'create sales_reports', 'update sales_reports', 'delete sales_reports',
            'view messages', 'create messages', 'update messages', 'delete messages',
            'view staffs',
        ]);
        
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->syncPermissions([
            'view client_sendername',
            'view invoices',
            'view invoice_loads',
            'view loadpackagetypes',
            'view loadpackages',
            'view customers',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
        ]);

        $kodasmspackage = Role::firstOrCreate(['name' => 'kodasmspackage', 'guard_name' => 'web']);
        $kodasmspackage->syncPermissions([
            'view client_sendername',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all()->except(['create clients', 'update clients', 'delete clients', 'create clientadmins', 'update clientadmins', 'delete clientadmins']));

        $super_admin = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web'], ['is_super_admin' => true]);
        $super_admin->syncPermissions(Permission::all());

        $user = \App\Models\User::whereEmail('owner@fms.com')->first();

        if ($user) {
            $user->assignRole($super_admin);
        }
    }
}


