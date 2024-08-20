
<div class="deznav">
    <div class="deznav-scroll">
      <ul class="metismenu" id="menu">
       
        <li>
          <a href="{{route('dashboard.index')}}" class="ai-icon" aria-expanded="false">
            <i class="fa fa-gauge"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li>
          <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
            <i class="fa fa-circle-h"></i>
            <span class="nav-text">Hospitals</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="{{ route('list.hospitals') }}"><i class="fa fa-house-medical" style="font-size: 16px;"></i> Hospital list</a></li>
            <li><a href="{{ route('list.onboardhospitals')}}"><i class="fa fa-file" style="font-size: 16px;"></i> Onboard Details</a></li>
          </ul>
        </li>
        <li>
          <a class="ai-icon" href="{{route('list.subscriptions')}}" aria-expanded="false">
            <i class="fa fa-filter-circle-dollar"></i>
            <span class="nav-text">Subscription</span>
          </a>
        </li>
        <li>
          <a class="ai-icon" href="{{route('owner.list.invoices')}}" aria-expanded="false">
            {{-- <i class="fa fa-filter-circle-dollar"></i> --}}
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span class="nav-text">Invoices</span>
          </a>
        </li>
        <li>
          <a class="ai-icon" href="{{route('owner.list.payments')}}" aria-expanded="false">
            {{-- <i class="fa fa-filter-circle-dollar"></i> --}}
            <i class="fa-solid fa-money-check-dollar"></i>
            <span class="nav-text">Payments</span>
          </a>
        </li>
        <li>
          <a class="ai-icon" href="{{route('list.modules')}}" aria-expanded="false">
            <i class="fa fa-file-lines"></i>
            <span class="nav-text">Modules</span>
          </a>
        </li>
        <li>
          <a class="ai-icon" href="{{route('list.packages')}}" aria-expanded="false">
            <i class="fa fa-box-open"></i>
            <span class="nav-text">Packages</span>
          </a>
        </li>
       
        <li>
          <a class="has-arrow ai-icon" href="{{route('owner.system-settings')}}" aria-expanded="false">
            <i class="fa fa-cog"></i>
            <span class="nav-text">Settings</span>
          </a>
        </li>

        {{-- <li>
          <a class="has-arrow ai-icon" href="{{route('users.ndex')}}" aria-expanded="false">
            <i class="fa fa-cog"></i>
            <span class="nav-text">Users</span>
          </a>
        </li> --}}
       
      </ul>

    </div>
  </div>