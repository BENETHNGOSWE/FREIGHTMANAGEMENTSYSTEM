<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="https://vemto.app/favicon.png" alt="Vemto Logo" class="brand-image bg-white img-circle">
        <span class="brand-text font-weight-light">hms_database [Imported] [Imported] [Imported]</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu">

                @auth
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="nav-icon icon ion-md-pulse"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon icon ion-md-apps"></i>
                        <p>
                            Apps
                            <i class="nav-icon right icon ion-md-arrow-round-back"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                            @can('view-any', App\Models\Bank::class)
                            <li class="nav-item">
                                <a href="{{ route('banks.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Banks</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Branch::class)
                            <li class="nav-item">
                                <a href="{{ route('branches.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Branches</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Client::class)
                            <li class="nav-item">
                                <a href="{{ route('clients.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Clients</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\ClientAdmin::class)
                            <li class="nav-item">
                                <a href="{{ route('client-admins.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Client Admins</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\ClientPayment::class)
                            <li class="nav-item">
                                <a href="{{ route('client-payments.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Client Payments</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\ClientType::class)
                            <li class="nav-item">
                                <a href="{{ route('client-types.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Client Types</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Country::class)
                            <li class="nav-item">
                                <a href="{{ route('countries.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Countries</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Message::class)
                            <li class="nav-item">
                                <a href="{{ route('messages.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Messages</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Module::class)
                            <li class="nav-item">
                                <a href="{{ route('modules.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Modules</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Package::class)
                            <li class="nav-item">
                                <a href="{{ route('packages.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Packages</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Patient::class)
                            <li class="nav-item">
                                <a href="{{ route('patients.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Patients</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\PaymentType::class)
                            <li class="nav-item">
                                <a href="{{ route('payment-types.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Payment Types</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Role::class)
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\Subscription::class)
                            <li class="nav-item">
                                <a href="{{ route('subscriptions.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Subscriptions</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\SubscriptionInvoice::class)
                            <li class="nav-item">
                                <a href="{{ route('subscription-invoices.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Subscription Invoices</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\SubscriptionToken::class)
                            <li class="nav-item">
                                <a href="{{ route('subscription-tokens.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Subscription Tokens</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\User::class)
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                            @endcan
                            @can('view-any', App\Models\UserRole::class)
                            <li class="nav-item">
                                <a href="{{ route('user-roles.index') }}" class="nav-link">
                                    <i class="nav-icon icon ion-md-radio-button-off"></i>
                                    <p>User Roles</p>
                                </a>
                            </li>
                            @endcan
                    </ul>
                </li>

                @if (Auth::user()->can('view-any', Spatie\Permission\Models\Role::class) || 
                    Auth::user()->can('view-any', Spatie\Permission\Models\Permission::class))
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon icon ion-md-key"></i>
                        <p>
                            Access Management
                            <i class="nav-icon right icon ion-md-arrow-round-back"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('view-any', Spatie\Permission\Models\Role::class)
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link">
                                <i class="nav-icon icon ion-md-radio-button-off"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        @endcan

                        @can('view-any', Spatie\Permission\Models\Permission::class)
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}" class="nav-link">
                                <i class="nav-icon icon ion-md-radio-button-off"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endauth

                <li class="nav-item">
                    <a href="https://adminlte.io/docs/3.1//index.html" target="_blank" class="nav-link">
                        <i class="nav-icon icon ion-md-help-circle-outline"></i>
                        <p>Docs</p>
                    </a>
                </li>

                @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon icon ion-md-exit"></i>
                        <p>{{ __('Logout') }}</p>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
                @endauth
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>