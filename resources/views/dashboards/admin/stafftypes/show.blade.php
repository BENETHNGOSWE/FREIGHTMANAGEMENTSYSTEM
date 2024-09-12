@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Staff Types</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Staff Type
                        Show</a></span></p>
        </div>

    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('list.stafftypes') }}" class="mr-4"
                        ><i class="icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.stafftypes.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.stafftypes.inputs.staff_type_name')</h5>
                        <span>{{ $stafftype->staff_type_name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.stafftypes.inputs.staff_type_description')</h5>
                        <span>{{ $stafftype->staff_type_description ?? '-' }}</span>
                    </div>
                <div class="mt-4">
                    <a href="{{ route('list.stafftypes') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\StaffType::class)
                    <a href="{{ route('create.stafftypes') }}" class="btn btn-light">
                        <i class="icon ion-md-add"></i> @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
