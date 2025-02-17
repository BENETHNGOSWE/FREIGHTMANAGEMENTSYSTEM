<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

            <li>
                <a href="{{ route('home') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-users"></i>
                    <span class="nav-text">Staffs</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.create.staff') }}">
                            <i class="fa fa-user"></i>
                            <span>+Add new</span>
                        </a></li>

                        <li><a href="{{ route('admin.list.staff') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>List Staffs</span>
                        </a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-box"></i>
                    <span class="nav-text">Containers</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.create.container') }}">
                            <i class="fa fa-box"></i>
                            <span>+Add new</span>
                        </a></li>

                        <li><a href="{{ route('admin.list.container') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>List Containers</span>
                        </a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-user"></i>
                    <span class="nav-text">Customers</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.create.customer') }}">
                            <i class="fa fa-user"></i>
                            <span>+Add new</span>
                        </a></li>

                        <li><a href="{{ route('admin.list.customer') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>List Customers</span>
                        </a></li>
                        

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-truck"></i>
                    <span class="nav-text">Loads</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.loadpackage') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>List Loads</span>
                        </a></li>

                </ul>

                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.loadtrackingpage.loadpackage') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Track Your Load</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-file-invoice"></i>
                    <span class="nav-text">Invoices</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.invoice') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>Load Invoices</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-lightbulb"></i>
                    <span class="nav-text">Leads</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.customercategory') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>Categories List</span>
                        </a></li>

                    <li><a href="{{ route('admin.list.customerlead') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>Leads List</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-comments"></i>
                    <span class="nav-text">Message Multipe</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.message.multiple') }}">
                            <i class="fas fa-comments"></i>
                            <span>Chat With Customer</span>
                        </a></li>
                    <li><a href="{{ route('admin.message.index') }}">
                            <i class="fas fa-list"></i>
                            <span>Message List</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="#" aria-expanded="false">
                    <i class="fas fa-user"></i>
                    <span class="nav-text">Manage Profile</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.show.profile', $clientadmin) }}">
                            <i class="fas fa-user"></i>
                            <span>Your Profile</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="#" aria-expanded="false">
                    <i class="fa fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.payment') }}">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment Infomation</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.branch') }}">
                            <i class="fas fa-building"></i>
                            <span>Branches</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.unittype') }}">
                            <i class="fa-solid fa-puzzle-piece"></i>
                            <span>Units</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.loadpackagetpye') }}">
                            <i class="fa-solid fa-truck"></i>
                            <span>Loads Types</span>
                        </a></li>

                </ul>
                {{-- <ul aria-expanded="false">
                    <li><a href="{{ route('owner.list.assignname') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Sender Name</span>
                        </a></li>
                </ul> --}}
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.activename') }}">
                            <i class="fa-solid fa-circle"></i>
                            <span>Active Name</span>
                        </a></li>
                </ul>
            </li>

        </ul>

    </div>
</div>