@auth
@if(Auth::user()->getRoleNames()[0] == 'super-admin')
    @include('dashboards.owner.sidebar')
@endif
@if(Auth::user()->getRoleNames()[0] == 'admin')
@include('dashboards.admin.sidebar')
@endif
@if(Auth::user()->getRoleNames()[0] =='owner')
@include('dashboards.owner.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'manager')
@include('dashboards.manager.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'kodasmspackage')
@include('dashboards.kodasms.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'chiefoperation')
@include('dashboards.chiefoperation.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'godownmanager')
@include('dashboards.godownmanager.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'salesperson')
@include('dashboards.sales.sidebar')
@endif


@if(Auth::user()->getRoleNames()[0] == 'SalesManager')
@include('dashboards.sales.sidebar')
@endif


@if (Auth::user()->getRoleNames()[0] == 'receptionist')
@include('dashboards.reception.sidebar')
@endif

@if (Auth::user()->getRoleNames()[0] == 'Cashier')
@include('dashboards.cashier.sidebar')
@endif

@endauth
