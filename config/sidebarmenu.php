<?php

return [

    [
        'title' => 'Dashboard',
        'icon' => 'flaticon-381-networking',
        'route' => 'home',
        'permission' => null, 
    ],

    // Client Profile
    [
        'title' => 'Clients',
        'icon'  => 'fa fa-circle-h',
        'permission' => 'create clients',
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
        ],
    ],

    // Financial Reports
    [
        'title' => 'Financial Reports',
        'icon' => 'fas fa-file-invoice-dollar',
        'permission' => 'view companyinvoicedetails',
        'children' => [
            [
                'title' => 'Invoice Report',
                'route' => 'chiefoperation.list.financialreport',
                'permission' => 'view companyinvoicedetails',
            ],
            [
                'title' => 'Expenses Report',
                'route' => 'admin.expense.report',
                'permission' => 'view companyinvoicedetails',
            ],
            [
                'title' => 'CBM Record',
                'route' => 'sales.list.salesreport',
                'permission' => 'view companyinvoicedetails',
            ],
        ],
    ],

    // Sales Reports
    [
        'title' => 'Sales Reports',
        'icon' => 'fas fa-file-invoice-dollar',
        'permission' => 'view sales_reports',
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
                'route' => 'owner.list.assignname',
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
