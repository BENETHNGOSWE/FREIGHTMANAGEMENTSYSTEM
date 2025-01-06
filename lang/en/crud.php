<?php

return [
    'common' => [
        'actions' => 'Actions',
        'create' => 'Create',
        'edit' => 'Edit',
        'update' => 'Update',
        'new' => 'New',
        'cancel' => 'Cancel',
        'attach' => 'Attach',
        'detach' => 'Detach',
        'save' => 'Save',
        'delete' => 'Delete',
        'delete_selected' => 'Delete selected',
        'search' => 'Search...',
        'back' => 'Back to Index',
        'are_you_sure' => 'Are you sure?',
        'no_items_found' => 'No items found',
        'created' => 'Successfully created',
        'saved' => 'Saved successfully',
        'removed' => 'Successfully removed',
    ],

    'banks' => [
        'name' => 'Banks',
        'index_title' => 'Banks List',
        'new_title' => 'New Bank',
        'create_title' => 'Create Bank',
        'edit_title' => 'Edit Bank',
        'show_title' => 'Show Bank',
        'inputs' => [
            'name' => 'Name',
            'status' => 'Status',
        ],
    ],

    'branches' => [
        'name' => 'Branches',
        'index_title' => 'Branches List',
        'new_title' => 'New Branch',
        'create_title' => 'Create Branch',
        'edit_title' => 'Edit Branch',
        'show_title' => 'Show Branch',
        'inputs' => [
            'branch_code' => 'Branch Code',
            'branch_name' => 'Branch Name',
        ],
    ],

    'clients' => [
        'name' => 'Clients',
        'index_title' => 'Clients List',
        'new_title' => 'New Client',
        'create_title' => 'Create Client',
        'edit_title' => 'Edit Client',
        'show_title' => 'Show Client',
        'inputs' => [
            'client_name' => 'Client Name',
            'client_logo' => 'Client Logo',
            'client_location' => 'Client Location',
            'client_address_one' => 'Client Address One',
            'client_address_two' => 'Client Address Two',
            'client_tinnumber' => 'Client TIN Number',
            'client_phonenumber' => 'Client Phone Number',
            'client_email' => 'Client Email',
            'client_city' => 'Client City',
            'client_country' => 'Client Country',
            'client_zipcode' => 'Client Zip Code',
            'client_fax' => 'Client Fax',
            'client_website_link' => 'Client Website Link',
            'client_type_id' => 'Client Type',
        ],
    ],

    'client_admins' => [
        'name' => 'Client Admins',
        'index_title' => 'ClientAdmins List',
        'new_title' => 'New Client admin',
        'create_title' => 'Create ClientAdmin',
        'edit_title' => 'Edit ClientAdmin',
        'show_title' => 'Show ClientAdmin',
        'inputs' => [
            'user_id' => 'User',
            'client_id' => 'Client',
        ],
    ],

    'client_payments' => [
        'name' => 'Client Payments',
        'index_title' => 'ClientPayments List',
        'new_title' => 'New Client payment',
        'create_title' => 'Create ClientPayment',
        'edit_title' => 'Edit ClientPayment',
        'show_title' => 'Show ClientPayment',
        'inputs' => [
            'amount' => 'Amount',
            'date' => 'Date',
            'payment_type_id' => 'Payment Type',
        ],
    ],

    'client_types' => [
        'name' => 'Client Types',
        'index_title' => 'ClientTypes List',
        'new_title' => 'New Client type',
        'create_title' => 'Create ClientType',
        'edit_title' => 'Edit ClientType',
        'show_title' => 'Show ClientType',
        'inputs' => [
            'name' => 'Name',
        ],
    ],


    'containers' => [
        'name' => 'Containers',
        'index_title' => 'Containers List',
        'new_title' => 'New Container',
        'create_title' => 'Create Container',
        'edit_title' => 'Edit Container',
        'show_title' => 'Show Container',
        'inputs' => [
            'container_number' => 'Container Number',
            'container_size' => 'Container Size',
            'container_volume' => 'Container Volume',
            'container_status' => 'Container Status',
        ],
    ],


    'customers' => [
        'name' => 'Customers',
        'index_title' => 'Customers List',
        'new_title' => 'New Customer',
        'create_title' => 'Create Customer',
        'edit_title' => 'Edit Customer',
        'show_title' => 'Show Customer',
        'inputs' => [
            'customer_no' => 'Customer No',
            'customer_firstname' => 'First Name',
            'customer_middlename' => 'Middle Name',
            'customer_lastname' => 'Last Name',
            'customer_address' => 'Address',
            'customer_photo' => 'Photo',
            'customer_city' => 'City',
            'customer_country' => 'Country',
            'customer_email' => 'Email',
            'phone' => 'Phone',
            'customer_document' => 'Document',
            'customer_bio' => 'Bio',
            'customer_gender' => 'Gender',
        ],
    ],
    
    'customercategories' => [
        'name' => 'Category',
        'index_title' => 'Category List',
        'create_title' => 'Create Category',
        'edit_title' => 'Edit Category',
        'show_title' => 'Show Category',
        'inputs' => [
            'categoryname' => 'Category Name',
            'categorydescription' => 'Category Description',
        ],
    ],

    'customerleads' => [
        'name' => 'Customer Leads',
        'index_title' => 'Customer Leads List',
        'new_title' => 'New Customer Leads',
        'create_title' => 'Create Customer Leads',
        'edit_title' => 'Edit Customer Leads',
        'show_title' => 'Show Customer Leads',
        'inputs' => [
            'customer_no' => 'Customer No',
            'customer_firstname' => 'First Name',
            'customer_middlename' => 'Middle Name',
            'customer_lastname' => 'Last Name',
            'customer_address' => 'Address',
            'customer_photo' => 'Photo',
            'customer_city' => 'City',
            'customer_country' => 'Country',
            'customer_email' => 'Email',
            'phone' => 'Phone',
            'customer_document' => 'Document',
            'customer_bio' => 'Bio',
            'customer_gender' => 'Gender',
        ],
    ],

    'loadpackagetypes' => [
        'name' => 'Load Types',
        'index_title' => 'Load Types List',
        'create_title' => 'Create Load Type',
        'edit_title' => 'Edit Load Types',
        'show_title' => 'Show Load Types',
        'inputs' => [
            'load_type_name' => 'Load Type Name',
            'load_type_description' => 'Load Type Description',
        ],
    ],

    'loadpackages' => [
        'name' => 'Loadpackages',
        'index_title' => 'Load List',
        'create_title' => 'Create Load',
        'edit_title' => 'Edit Load',
        'show_title' => 'Show Load',
        'inputs' => [
            'load_no' => 'Product Number',
            'load_name' => 'Product Name',
            'load_description' => 'Product  Description',
            'load_trucking_number' => 'Product Trucking No',
            'load_price' => 'Product Price',
            'load_status' => 'Shipment Status',
            'load_transport_date' => 'ETD',
            'load_delivery_date' => 'Delivery Date',
            'load_type_id' => 'Load Type',
        ],
    ],

    'invoices' => [
        'name' => 'invoices',
        'index_title' => 'invoices List',
        'new_title' => 'New Invoive',
        'create_title' => 'Create Invoive',
        'edit_title' => 'Edit Invoive',
        'show_title' => 'Show Invoive',
        'inputs' => [
            'invoice_no' => 'Invoive No',
            'prefix' => 'Prefix',
            'total_amount' => 'Total Amount',
            'total_balance' => 'Balance Amount',
            'status' => 'Invoice Status',
        ],
    ],

    'messages' => [
        'name' => 'Messages',
        'index_title' => 'Messages List',
        'new_title' => 'New Message',
        'create_title' => 'Create Message',
        'edit_title' => 'Edit Message',
        'show_title' => 'Show Message',
        'inputs' => [
            'sender_id' => 'Sender Id',
            'receiver_id' => 'Receiver Id',
            'body' => 'Body',
            'status' => 'Status',
            'opened' => 'Opened',
            'client_id' => 'Client',
        ],
    ],

    'modules' => [
        'name' => 'Modules',
        'index_title' => 'Modules List',
        'new_title' => 'New Module',
        'create_title' => 'Create Module',
        'edit_title' => 'Edit Module',
        'show_title' => 'Show Module',
        'inputs' => [
            'name' => 'Name',
            'features' => 'Features',
            'client_id' => 'Client',
        ],
    ],

    'packages' => [
        'name' => 'Packages',
        'index_title' => 'Packages List',
        'new_title' => 'New Package',
        'create_title' => 'Create Package',
        'edit_title' => 'Edit Package',
        'show_title' => 'Show Package',
        'inputs' => [
            'name' => 'Name',
            'monthly_fee' => 'Monthly Fee',
            'yearly_fee' => 'Yearly Fee',
            'features' => 'Features',
            'client_id' => 'Client',
        ],
    ],

    'paymentInformations' => [
        'name' => 'Payment Info',
        'index_title' => 'Payment Info List',
        'new_title' => 'New Payment Info',
        'create_title' => 'Create Payment Info',
        'edit_title' => 'Edit Payment Info',
        'show_title' => 'Show Payment Info',
        'inputs' => [
            'Bank_name' => 'Bank Name',
            'account_name' => 'Account Name',
            'account_number' => 'Account Number',
        ],
    ],

    'patients' => [
        'name' => 'Patients',
        'index_title' => 'Patients List',
        'new_title' => 'New Patient',
        'create_title' => 'Create Patient',
        'edit_title' => 'Edit Patient',
        'show_title' => 'Show Patient',
        'inputs' => [
            'user_id' => 'User',
            'patientID' => 'Patient Id',
            'has_insurance' => 'Has Insurance',
            'city' => 'City',
        ],
    ],

    'payment_types' => [
        'name' => 'Payment Types',
        'index_title' => 'PaymentTypes List',
        'new_title' => 'New Payment type',
        'create_title' => 'Create PaymentType',
        'edit_title' => 'Edit PaymentType',
        'show_title' => 'Show PaymentType',
        'inputs' => [
            'name' => 'Name',
        ],
    ],

    'roles' => [
        'name' => 'Roles',
        'index_title' => 'Roles List',
        'new_title' => 'New Role',
        'create_title' => 'Create Role',
        'edit_title' => 'Edit Role',
        'show_title' => 'Show Role',
        'inputs' => [
            'name' => 'Name',
        ],
    ],

    'sendernames' => [
        'name' => 'Sender Name',
        'index_title' => 'Sender Name List',
        'new_title' => 'New Sender Name',
        'create_title' => 'Create Sender Name',
        'edit_title' => 'Edit Sender Name',
        'show_title' => 'Show Sender Name',
        'inputs' => [
            'name' => 'Sender Name',
        ],
    ],

    'subscriptions' => [
        'name' => 'Subscriptions',
        'index_title' => 'Subscriptions List',
        'new_title' => 'New Subscription',
        'create_title' => 'Create Subscription',
        'edit_title' => 'Edit Subscription',
        'show_title' => 'Show Subscription',
        'inputs' => [
            'client_id' => 'Client',
            'start_date' => 'Start Date',
            'expire_date' => 'Expire Date',
            'package_id' => 'Package',
            'status' => 'Status',
        ],
    ],

    'subscription_invoices' => [
        'name' => 'Subscription Invoices',
        'index_title' => 'SubscriptionInvoices List',
        'new_title' => 'New Subscription invoice',
        'create_title' => 'Create SubscriptionInvoice',
        'edit_title' => 'Edit SubscriptionInvoice',
        'show_title' => 'Show SubscriptionInvoice',
        'inputs' => [
            'amount' => 'Amount',
            'balance' => 'Balance',
            'bill_date' => 'Bill Date',
            'due_date' => 'Due Date',
            'status' => 'Status',
            'subscription_id' => 'Subscription',
            'client_id' => 'Client',
        ],
    ],

    'subscription_tokens' => [
        'name' => 'Subscription Tokens',
        'index_title' => 'SubscriptionTokens List',
        'new_title' => 'New Subscription token',
        'create_title' => 'Create SubscriptionToken',
        'edit_title' => 'Edit SubscriptionToken',
        'show_title' => 'Show SubscriptionToken',
        'inputs' => [
            'subscription_id' => 'Subscription',
            'subscription_invoice_id' => 'Subscription Invoice',
            'token_' => 'Token',
            'expire_date' => 'Expire Date',
            'status' => 'Status',
            'client_id' => 'Client',
        ],
    ],

    'unittypes' => [
        'name' => 'Unit Types',
        'index_title' => 'Unit Types List',
        'new_title' => 'New Unit Type',
        'create_title' => 'Create Unit Type',
        'edit_title' => 'Edit Unit Type',
        'show_title' => 'Show Unit Type',
        'inputs' => [
            'unittype_name' => 'Unit Type Name',
            'unitytype_initial' => 'Unit Initial',
        ],
    ],

    'users' => [
        'name' => 'Users',
        'index_title' => 'Users List',
        'new_title' => 'New User',
        'create_title' => 'Create User',
        'edit_title' => 'Edit User',
        'show_title' => 'Show User',
        'inputs' => [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'phone' => 'Phone',
            'address' => 'Address',
            'registration_date' => 'Registration Date',
            'photo' => 'Photo',
            'gender' => 'Gender',
            'dob' => 'Dob',
            'status' => 'Status',
            'country_id' => 'Country',
            'role_id' => 'Role',
            'client_id' => 'Client',
        ],
    ],

    'user_roles' => [
        'name' => 'User Roles',
        'index_title' => 'UserRoles List',
        'new_title' => 'New User role',
        'create_title' => 'Create UserRole',
        'edit_title' => 'Edit UserRole',
        'show_title' => 'Show UserRole',
        'inputs' => [
            'user_id' => 'User',
            'role_id' => 'Role',
        ],
    ],

    'roles' => [
        'name' => 'Roles',
        'index_title' => 'Roles List',
        'create_title' => 'Create Role',
        'edit_title' => 'Edit Role',
        'show_title' => 'Show Role',
        'inputs' => [
            'name' => 'Name',
        ],
    ],

    'permissions' => [
        'name' => 'Permissions',
        'index_title' => 'Permissions List',
        'create_title' => 'Create Permission',
        'edit_title' => 'Edit Permission',
        'show_title' => 'Show Permission',
        'inputs' => [
            'name' => 'Name',
        ],
    ],
];
