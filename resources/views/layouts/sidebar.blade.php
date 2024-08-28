@auth
@if(Auth::user()->getRoleNames()[0] == 'super-admin')
    @include('dashboards.owner.sidebar')
@endif
@if(Auth::user()->getRoleNames()[0] == 'doctor')
@include('dashboards.doctor.sidebar')
@endif
@if(Auth::user()->getRoleNames()[0] =='owner')
@include('dashboards.owner.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'patient')
@include('dashboards.patient.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'receptionist')
@include('dashboards.receptionist.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'pharmacist')
@include('dashboards.pharmacist.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'laboratorist')
@include('dashboards.laboratorist.sidebar')
@endif

@if(Auth::user()->getRoleNames()[0] == 'accountant')
@include('dashboards.accountant.sidebar')
@endif


@if(Auth::user()->getRoleNames()[0] == 'hr')
@include('dashboards.hr.sidebar')
@endif

@endauth
