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
                    <span class="nav-text">Derivery Order</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('godownmanager.create.deriveryorder') }}">+Add new</a></li>
                    <li><a href="{{ route('godownmanager.list.deriveryorder') }}">List Deriveries </a></li>
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
               
            </li>

        </ul>

    </div>
</div>
