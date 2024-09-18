@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Wards</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Ward
                        Show</a></span></p>
        </div>

    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('list.wards') }}" class="mr-4"
                        ><i class="icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.wards.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.wards.inputs.ward_no')</h5>
                        <span>{{ $ward->ward_no ?? '-' }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <h5>@lang('crud.wards.inputs.ward_description')</h5>
                        <span>{{ $ward->ward_description ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.wards.inputs.ward_name')</h5>
                        <span>{{ $ward->ward_name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.wards.inputs.ward_location')</h5>
                        <span>{{ $ward->ward_location ?? '-' }}</span>
                    </div>
                    
              

            </div>
        </div>
    </div>
</div>
@endsection
