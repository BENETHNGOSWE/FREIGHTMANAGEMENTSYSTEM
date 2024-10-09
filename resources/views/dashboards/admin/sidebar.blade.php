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
                    <li><a href="{{ route('admin.create.staff') }}">+Add new</a></li>
                    <li><a href="{{ route('admin.list.staff') }}">List Staffs </a></li>
                    <li><a href="#">Staff Types</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-building"></i>
                    <span class="nav-text">Customers</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.create.customer') }}">+Add new</a></li>
                    <li><a href="{{ route('admin.list.customer') }}">List Customers </a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-bed"></i>
                    <span class="nav-text">Loads</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.loadpackage') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>List Loads</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.loadpackagetpye') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Loads Types</span>
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
                    <i class="fa fa-flask"></i>
                    <span class="nav-text">Message</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">
                            <i class="fas fa-microscope"></i>
                            <span>Chat With Customer</span>
                        </a></li>
                    <li><a href="#">
                            <i class="fas fa-mercury"></i>
                            <span>Diagnosis</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-pills"></i>
                    <span class="nav-text">Invoices</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.invoice') }}">
                            <i class="fa-solid fa-list-ol"></i>
                            <span>Load Invoices</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="#">
                            <i class="fa fa-plus"></i>
                            <span>Add new</span>
                        </a></li>

                </ul>
            </li>

            {{-- <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa-solid fa-database"></i>
                    <span class="nav-text">Assets</span>
                </a>

                <ul aria-expanded="false">

                    <li>

                        <a href="#">
                            <i class="fa-solid fa-list-ol"></i>

                            ListAssets</a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-suitcase"></i>
                            Categories</a>
                    </li>
                    <li><a href="#">
                            <i class="fas fa-tag"></i>
                            Assets Types</a>
                    </li>
                    <li><a href="#">
                            <i class="fas fa-clipboard"></i>
                            Assignments</a></li>

                    <li><a href="#">
                            <i class="fas fa-clipboard"></i>
                            Maintance</a></li>

                    <li><a href="">
                            <i class="fas fa-clipboard"></i>
                            Maintanance Record</a></li>
                </ul>








            </li> --}}

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-building"></i>
                    <span class="nav-text">Manage Profile</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.show.profile', $clientadmin) }}">
                            <i class="fas fa-building"></i>
                            <span>Your Profile</span>
                        </a></li>

                </ul>
            </li>
            <li>
                <a class="has-arrow ai-icon" href="#" aria-expanded="false">
                    <i class="fa fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>

        </ul>

    </div>
</div>
