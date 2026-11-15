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
        $models = ['banks', 'branches', 'containers', 'clients', 'client_admins', 'clientpayments', 'client_types', 'companyinvoicedetails','godown_addresses',
                    'countries', 'customers', 'customercategories', 'customerleads', 'client_sendername', 'derivery_orders', 'messages', 'modules', 'packages', 
                    'loadpackagetypes', 'loadpackages', 'office_expense_categories', 'office_expenses', 'staffs', 'staff_types', 'payments', 'role', 'sendernames', 'subscriptions', 'subscription_invoices',
                    'subscription_tokens', 'sales_reports',  'userroles', 'roles', 'permissions', 'users', 'unit_types', 'invoices', 'invoice_loads', 'kodapackages', 'customermessages','demorequests', 'settings','clientactivenames',
                    // Accounting module
                    'accounts', 'journal_entries', 'accounting_reports'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissionName = $action . ' ' . $model;
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                }
            }
        }

        foreach (['view financial reports', 'print release order', 'print final invoice',
                 'view accounting reports', 'manage accounting settings', 'post journal entries'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view client_sendername',
            'view banks', 'create banks', 'update banks', 'delete banks',
            'view branches', 'create branches', 'update branches', 'delete branches',
            'view containers', 'create containers', 'update containers', 'delete containers',
            'view customers', 'create customers', 'update customers', 'delete customers',
            'view staffs', 'update staffs',
            'view loadpackagetypes', 'create loadpackagetypes', 'update loadpackagetypes', 'delete loadpackagetypes',
            'view loadpackages', 'create loadpackages', 'update loadpackages', 'delete loadpackages',
            'view unit_types', 'create unit_types', 'update unit_types', 'delete unit_types',
            'view invoices', 'create invoices', 'update invoices', 'delete invoices',
            'view invoice_loads', 'create invoice_loads', 'update invoice_loads', 'delete invoice_loads',
            'view companyinvoicedetails', 'create companyinvoicedetails', 'update companyinvoicedetails', 'delete companyinvoicedetails',
            'view office_expense_categories', 'create office_expense_categories', 'update office_expense_categories', 'delete office_expense_categories',
            'view office_expenses', 'create office_expenses', 'update office_expenses', 'delete office_expenses',
            'view sales_reports', 'create sales_reports', 'update sales_reports', 'delete sales_reports',
            // Accounts
            'list accounts', 'view accounts', 'create accounts', 'update accounts', 'delete accounts',
            'list journal_entries', 'view journal_entries', 'create journal_entries', 'update journal_entries', 'delete journal_entries',
            'list accounting_reports', 'view accounting_reports',
            'view accounting reports', 'manage accounting settings', 'post journal entries',
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
            'view loadpackagetypes', 'create loadpackagetypes', 'update loadpackagetypes', 'delete loadpackagetypes',
            'view loadpackages', 'create loadpackages', 'update loadpackages', 'delete loadpackages',
            'view unit_types', 'create unit_types', 'update unit_types', 'delete unit_types',
            'view invoices', 'create invoices', 'update invoices', 'delete invoices',
            'view invoice_loads', 'create invoice_loads', 'update invoice_loads', 'delete invoice_loads',
            'view companyinvoicedetails', 'create companyinvoicedetails', 'update companyinvoicedetails', 'delete companyinvoicedetails',
            'view office_expense_categories', 'create office_expense_categories', 'update office_expense_categories', 'delete office_expense_categories',
            'view office_expenses', 'create office_expenses', 'update office_expenses', 'delete office_expenses',
            'view financial reports', 'print release order', 'print final invoice',
            // Accounts
            'list accounts', 'view accounts', 'create accounts', 'update accounts',
            'list journal_entries', 'view journal_entries', 'create journal_entries', 'update journal_entries',
            'list accounting_reports', 'view accounting_reports',
            'view accounting reports', 'post journal entries',
        ]);


        $godownmanager = Role::firstOrCreate(['name' => 'godownmanager', 'guard_name' => 'web']);
        $godownmanager->syncPermissions([
            'view derivery_orders', 'create derivery_orders', 'update derivery_orders', 'delete derivery_orders',
        ]);

        $hr = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $hr->syncPermissions([
            'view derivery_orders', 'create derivery_orders', 'update derivery_orders', 'delete derivery_orders',
            'view branches', 'create branches', 'update branches', 'delete branches',
            'view containers', 'create containers', 'update containers', 'delete containers',
            'view customers', 'create customers', 'update customers', 'delete customers',
            'view staffs', 'create staffs', 'update staffs', 'delete staffs',
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
            'print final invoice',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
            'view office_expense_categories', 'create office_expense_categories', 'update office_expense_categories',
        ]);

        $Cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $Cashier->syncPermissions([
            'view client_sendername',
            'view invoices','create invoices','update invoices',
            'view invoice_loads','create invoice_loads','update invoice_loads',
            'view loadpackagetypes', 'create loadpackagetypes', 'update loadpackagetypes', 'delete loadpackagetypes',
            'view loadpackages', 'create loadpackages', 'update loadpackages', 'delete loadpackages',
            'view containers', 'create containers', 'update containers', 'delete containers',
            'view customers', 'create customers', 'update customers', 'delete customers',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
            'view office_expense_categories', 'create office_expense_categories', 'update office_expense_categories',
            'view office_expenses', 'create office_expenses', 'update office_expenses',
            'print release order', 'print final invoice',
            // Accounts: the cashier records payments, so they must see the books
            'list accounts', 'view accounts',
            'list journal_entries', 'view journal_entries', 'create journal_entries',
            'list accounting_reports', 'view accounting_reports', 'view accounting reports',
        ]);

        $kodasmspackage = Role::firstOrCreate(['name' => 'kodasmspackage', 'guard_name' => 'web']);
        $kodasmspackage->syncPermissions([
            'view client_sendername',
            'view customercategories', 'create customercategories', 'update customercategories', 'delete customercategories',
            'view customerleads', 'create customerleads', 'update customerleads', 'delete customerleads',
            'view messages', 'create messages', 'update messages', 'delete messages',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(
            Permission::all()->reject(function ($permission) {
                return str_contains($permission->name, 'clients') 
                    || str_contains($permission->name, 'client_admins')
                    || str_contains($permission->name, 'sendernames')
                    || str_contains($permission->name, 'kodapackages')
                    || str_contains($permission->name, 'customermessages')
                    || str_contains($permission->name, 'demorequests');
            })
        );

        $super_admin = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web'], ['is_super_admin' => true]);
        $super_admin->syncPermissions(Permission::all());

        $user = \App\Models\User::whereEmail('owner@fms.com')->first();

        if ($user) {
            $user->assignRole($super_admin);
        }
    }
}


