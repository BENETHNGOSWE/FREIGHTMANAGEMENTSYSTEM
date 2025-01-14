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
                    <li><a href="{{ route('admin.loadtrackingpage.loadpackage') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Track Your Load</span>
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
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-flask"></i>
                    <span class="nav-text">Message</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.message') }}">
                            <i class="fas fa-microscope"></i>
                            <span>Chat With Customer</span>
                        </a></li>
                    <li><a href="{{ route('admin.message.index') }}">
                            <i class="fas fa-mercury"></i>
                            <span>Message List</span>
                        </a></li>

                </ul>
            </li>

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-pills"></i>
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
                    <i class="fas fa-building"></i>
                    <span class="nav-text">Manage Profile</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.show.staff', $staff) }}">
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
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.payment') }}">
                            <i class="fas fa-building"></i>
                            <span>Payment Infomation</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.unittype') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Units</span>
                        </a></li>

                </ul>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.loadpackagetpye') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Loads Types</span>
                        </a></li>

                </ul>
            </li>



        </ul>

    </div>
</div>
