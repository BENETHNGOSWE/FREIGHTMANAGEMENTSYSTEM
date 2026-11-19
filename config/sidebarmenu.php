<?php

return [

    [
        'title' => 'Dashboard',
        'icon' => 'flaticon-381-networking',
        'route' => 'home',
        'permission' => null,
        'module' => 'dashboard',
    ],

    // Client Profile
    [
        'title' => 'Clients',
        'icon'  => 'fa fa-circle-h',
        'permission' => 'create clients',
        'module' => 'customers',
        'owner_only' => true,
        'children' => [
            [
                'title' => 'Clients List',
                'icon'  => 'fa fa-house-medical',
                'route' => 'list.clients',
                'permission' => 'create clients',
            ],
            [
                'title' => 'Onboard Details',
                'icon'  => 'fa fa-file',
                'route' => 'list.clients', 
                'permission' => 'create clients',
            ],
        ],
    ],

    // Package Profile
    [
        'title' => 'Packages',
        'icon'  => 'fa fa-circle-h',
        'permission' => 'view kodapackages',
        'module' => 'loads',
        'owner_only' => true,
        'children' => [
            [
                'title' => 'Create',
                'icon'  => 'fa fa-house-medical',
                'route' => 'owner.create.package',
                'permission' => 'create kodapackages',
            ],
            [
                'title' => 'List',
                'icon'  => 'fa fa-file',
                'route' => 'owner.list.package', 
                'permission' => 'view kodapackages',
            ],
        ],
    ],

    // Sender Name Profile
    [
        'title' => 'Sender Name',
        'icon'  => 'fa fa-circle-h',
        'permission' => 'view sendernames',
        'module' => 'sms_sender',
        'owner_only' => true,
        'children' => [
            [
                'title' => 'Assign Name',
                'icon'  => 'fa fa-house-medical',
                'route' => 'owner.list.assignname',
                'permission' => 'view sendernames',
            ]
        ],
    ],

    // MessageCount Profile
    [
        'title' => 'Message Count',
        'icon'  => 'fa fa-circle-h',
        'permission' => 'view customermessages',
        'module' => 'sms_reports',
        'owner_only' => true,
        'children' => [
            [
                'title' => 'Assign Name',
                'icon'  => 'fa fa-house-medical',
                'route' => 'owner.list.customermessage',
                'permission' => 'view customermessages',
            ]
        ],
    ],

    // Demo Profile
    [
        'title' => 'Demo request',
        'icon'  => 'fa fa-circle-h',
        'permission' => 'view demorequests',
        'module' => 'dashboard',
        'owner_only' => true,
        'children' => [
            [
                'title' => 'Demo request',
                'icon'  => 'fa fa-house-medical',
                'route' => 'owner.list.demorequest',
                'permission' => 'view demorequests',
            ]
        ],
    ],

    // Staffs
    [
        'title' => 'Staffs',
        'icon' => 'fa fa-users',
        'permission' => 'list staffs',
        'module' => 'staff',
        'children' => [
            [
                'title' => '+Add new',
                'route' => 'admin.create.staff',
                'permission' => 'create staffs',
            ],
            [
                'title' => 'List Staffs',
                'route' => 'admin.list.staff',
                'permission' => 'list staffs',
            ],
        ],
    ],

     // Containers
    [
        'title' => 'Containers',
        'icon' => 'fa fa-box',
        'permission' => 'list containers',
        'module' => 'containers',
        'children' => [
            [
                'title' => '+Add new',
                'route' => 'admin.create.container',
                'permission' => 'create containers',
            ],
            [
                'title' => 'List Containers',
                'route' => 'admin.list.container',
                'permission' => 'list containers',
            ],
        ],
    ],

    // Customers
    [
        'title' => 'Customers',
        'icon' => 'fa fa-user',
        'permission' => 'list customers',
        'module' => 'customers',
        'children' => [
            [
                'title' => '+Add new',
                'route' => 'admin.create.customer',
                'permission' => 'create customers',
            ],
            [
                'title' => 'List Customers',
                'route' => 'admin.list.customer',
                'permission' => 'view customers',
            ],
        ],
    ],

   
    // Loads
    [
        'title' => 'Loads',
        'icon' => 'fas fa-truck',
        'permission' => 'list loadpackages',
        'module' => 'loads',
        'children' => [
            [
                'title' => 'List Loads',
                'route' => 'admin.list.loadpackage',
                'permission' => 'view loadpackages',
            ],
            [
                'title' => 'Track Your Load',
                'route' => 'admin.loadtrackingpage.loadpackage',
                'permission' => 'view loadpackages',
            ],
        ],
    ],

    // Invoices
    [
        'title' => 'Invoices',
        'icon' => 'fas fa-file-invoice',
        'permission' => 'list invoices',
        'module' => 'invoices',
        'children' => [
            [
                'title' => 'Load Invoices',
                'route' => 'admin.list.invoice',
                'permission' => 'view invoices',
            ],
            [
                'title' => 'Invoice Details',
                'route' => 'admin.list.companyinvoicedetail',
                'permission' => 'view companyinvoicedetails',
            ],
        ],
    ],

    // Expenses
    [
        'title' => 'Expenses',
        'icon' => 'fas fa-file-invoice',
        'permission' => 'list office_expenses',
        'module' => 'expenses',
        'children' => [
            [
                'title' => 'Expenses Category',
                'route' => 'admin.list.categoryexpense',
                'permission' => 'view office_expense_categories',
            ],
            [
                'title' => 'Expenses List',
                'route' => 'admin.list.officeexpense',
                'permission' => 'view office_expenses',
            ],
        ],
    ],

    // Leads
    [
        'title' => 'Leads',
        'icon' => 'fas fa-lightbulb',
        'permission' => 'view customercategories',
        'module' => 'customers',
        'children' => [
            [
                'title' => 'Categories List',
                'route' => 'admin.list.customercategory',
                'permission' => 'view customercategories',
            ],
            [
                'title' => 'Leads List',
                'route' => 'admin.list.customerlead',
                'permission' => 'view customerleads',
            ],
        ],
    ],

    // Messages
    [
        'title' => 'Message',
        'icon' => 'fa fa-comments',
        'permission' => 'view messages',
        'module' => 'sms_bulk',
        'children' => [
            [
                'title' => 'Chat With Customer',
                'route' => 'admin.message.lead',
                'permission' => 'create messages',
            ],
            [
                'title' => 'Chat With Customer / Multiple',
                'route' => 'admin.message.multiple',
                'permission' => 'create messages',
            ],
            [
                'title' => 'Message List',
                'route' => 'admin.message.index',
                'permission' => 'view messages',
            ],
            [
                'title' => 'SMS Balance',
                'route' => 'admin.sms.balance',
                'permission' => 'view messages',
            ],
        ],
    ],

    // Cargo operations: the physical journey of every package
    [
        'title' => 'Cargo Operations',
        'icon' => 'fas fa-boxes',
        'permission' => null,
        'module' => 'cargo',
        'children' => [
            ['title' => 'Cargo List', 'route' => 'cargo.index', 'permission' => null],
            ['title' => 'Warehouse Verification', 'route' => 'cargo.verification', 'permission' => null],
            ['title' => 'Release Control', 'route' => 'cargo.release.index', 'permission' => null],
            ['title' => 'Merge Payment', 'route' => 'invoices.merge.create', 'permission' => null],
        ],
    ],

    // Storage - its own section: it has its own settings, money and reports
    [
        'title' => 'Storage',
        'icon' => 'fas fa-warehouse',
        'permission' => null,
        'module' => 'storage',
        'children' => [
            ['title' => 'Cargo in Storage', 'route' => 'storage.index', 'permission' => null],
            ['title' => 'Storage Payments', 'route' => 'storage.payments', 'permission' => null],
            ['title' => 'Storage Settings', 'route' => 'storage.settings', 'permission' => null],
        ],
    ],

    // Accounts - the full accounting module (chart of accounts, journals, ledgers, reports)
    [
        'title' => 'Accounts',
        'icon' => 'fas fa-calculator',
        'permission' => null,
        'module' => 'accounts',
        'children' => [
            ['title' => 'Accounts Dashboard', 'route' => 'accounts.dashboard', 'permission' => null],
            ['title' => 'Chart of Accounts', 'route' => 'accounts.chart.index', 'permission' => null],
            ['title' => 'Journal Entries', 'route' => 'accounts.journal.index', 'permission' => null],
            ['title' => 'General Ledger', 'route' => 'accounts.ledger.index', 'permission' => null],
            ['title' => 'Trial Balance', 'route' => 'accounts.reports.trial_balance', 'permission' => null],
            ['title' => 'Profit & Loss', 'route' => 'accounts.reports.profit_loss', 'permission' => null],
            ['title' => 'Balance Sheet', 'route' => 'accounts.reports.balance_sheet', 'permission' => null],
            ['title' => 'Receivables & Ageing', 'route' => 'accounts.reports.receivables', 'permission' => null],
            ['title' => 'Cash & Bank Book', 'route' => 'accounts.reports.cash_bank', 'permission' => null],
            ['title' => 'Accounts Settings', 'route' => 'accounts.settings', 'permission' => null],
        ],
    ],

    // Financial Reports
    [
        'title' => 'Financial Reports',
        'icon' => 'fas fa-file-invoice-dollar',
        'permission' => 'view financial reports',
        'module' => 'reports',
        'children' => [
            [
                'title' => 'Invoice Report',
                'route' => 'chiefoperation.list.financialreport',
                'permission' => 'view financial reports',
            ],
            [
                'title' => 'Expenses Report',
                'route' => 'admin.expense.report',
                'permission' => 'view financial reports',
            ],
            [
                'title' => 'CBM Record',
                'route' => 'sales.list.salesreport',
                'permission' => 'view financial reports',
            ],
        ],
    ],

    // Sales Reports
    [
        'title' => 'Sales Reports',
        'icon' => 'fas fa-file-invoice-dollar',
        'permission' => 'view sales_reports',
        'module' => 'reports',
        'children' => [
            [
                'title' => 'CBM Record',
                'route' => 'sales.list.salesreport',
                'permission' => 'view sales_reports',
            ],
            [
                'title' => 'Create',
                'route' => 'sales.create.salesreport',
                'permission' => 'create sales_reports',
            ],
        ],
    ],

    // Delivery 
    [
        'title' => 'Derivery Order',
        'icon' => 'fas fa-file-invoice-dollar',
        'permission' => 'view derivery_orders',
        'module' => 'reports',
        'children' => [
           [
                'title' => '+Add new',
                'route' => 'godownmanager.create.deriveryorder',
                'permission' => 'create derivery_orders',
            ],
            [
                'title' => 'List Deriveries',
                'route' => 'godownmanager.list.deriveryorder',
                'permission' => 'view derivery_orders',
            ],
        ],
    ],

    // Manage Profile
    [
        'title' => 'Manage Profile',
        'icon' => 'fas fa-user',
        'permission' => 'view staffs',
        'module' => 'staff',
        'children' => [
            [
                'title' => 'Your Profile',
                'route' => 'admin.viewprofile.staff',
                'permission' => 'view staffs',
            ],
        ],
    ],

    // // Settings
    [
        'title' => 'Settings',
        'icon' => 'fa fa-cog',
        'permission' => 'view settings',
        'module' => 'staff',
        'children' => [
            [
                'title' => 'Roles',
                'route' => 'roles.index',
                'permission' => 'view roles',
            ],
            [
                'title' => 'Payment Information',
                'route' => 'admin.list.payment',
                'permission' => 'view payments',
            ],
            [
                'title' => 'Godown Details',
                'route' => 'admin.list.godownaddress',
                'permission' => 'view godown_addresses',
            ],
            [
                'title' => 'Branches',
                'route' => 'admin.list.branch',
                'permission' => 'view branches',
            ],
            [
                'title' => 'Units',
                'route' => 'admin.list.unittype',
                'permission' => 'view unit_types',
            ],
            [
                'title' => 'Sender Name',
                'route' => 'owner.list.sendername',
                'permission' => 'view sendernames',
            ],
            [
                'title' => 'Loads Types',
                'route' => 'admin.list.loadpackagetpye',
                'permission' => 'view loadpackagetypes',
            ],
            [
                'title' => 'Invoice Details',
                'route' => 'admin.list.companyinvoicedetail',
                'permission' => 'view companyinvoicedetails',
            ],
            [
                'title' => 'Active Name',
                'route' => 'admin.list.activename',
                'permission' => 'view clientactivenames',
            ],
        ],
    ],

];
