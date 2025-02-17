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
                    <i class="fa fa-truck"></i>
                    <span class="nav-text">Derivery Order</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('godownmanager.create.deriveryorder') }}">+Add new</a></li>
                    <li><a href="{{ route('godownmanager.list.deriveryorder') }}">List Deriveries </a></li>
                </ul>
            </li>

          

            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="nav-text">Invoices</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.list.invoice') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Load Invoices</span>
                        </a></li>

                </ul>
            </li>


            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user"></i>
                    <span class="nav-text">Manage Profile</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.viewprofile.staff') }}">
                            <i class="fas fa-user"></i>
                            <span>Your Profile</span>
                        </a></li>

                </ul>
            </li>

        </ul>

    </div>
</div>
