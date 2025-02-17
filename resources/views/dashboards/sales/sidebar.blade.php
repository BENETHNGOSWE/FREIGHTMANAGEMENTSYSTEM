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
                    <i class="fa fa-flask"></i>
                    <span class="nav-text">Message</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.message.lead') }}">
                            <i class="fas fa-microscope"></i>
                            <span>Chat With Customer</span>
                        </a></li>
                    <li><a href="{{ route('admin.message.index') }}">
                            <i class="fas fa-mercury"></i>
                            <span>Message List</span>
                        </a></li>

                </ul>
            </li>

            @if(auth()->user()->hasRole('SalesManager'))
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-flask"></i>
                    <span class="nav-text">Sales Report</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('sales.list.salesreport') }}">
                            <i class="fas fa-microscope"></i>
                            <span>CBM Record</span>
                        </a></li>
                    <li><a href="{{ route('sales.create.salesreport') }}">
                            <i class="fas fa-mercury"></i>
                            <span>Create</span>
                        </a></li>
                </ul>
            </li>
        @endif

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
                    <li><a href="{{ route('admin.viewprofile.staff') }}">
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
                    <li><a href="{{ route('owner.list.assignname') }}">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>Sender Name</span>
                        </a></li>
                </ul>
                
            </li>
        </ul>

    </div>
</div>
