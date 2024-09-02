@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">{{ $hospital->hospital_name ?? '-' }}</h2>
                
            </div>
    
            <div>
               <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('dashboard.index') }}">View hospital</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('list.hospitals') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.hospitals.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_name')</h5>
                        <span>{{ $hospital->hospital_name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_logo')</h5>
                        <span>{{ $hospital->hospital_logo ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_location')</h5>
                        <span>{{ $hospital->hospital_location ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_address_one')</h5>
                        <span>{{ $hospital->hospital_address_one ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_address_two')</h5>
                        <span>{{ $hospital->hospital_address_two ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_tinnumber')</h5>
                        <span>{{ $hospital->hospital_tinnumber ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_phonenumber')</h5>
                        <span>{{ $hospital->hospital_phonenumber ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_email')</h5>
                        <span>{{ $hospital->hospital_email ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_city')</h5>
                        <span>{{ $hospital->hospital_city ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_country')</h5>
                        <span>{{ $hospital->hospital_country ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_zipcode')</h5>
                        <span>{{ $hospital->hospital_zipcode ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_fax')</h5>
                        <span>{{ $hospital->hospital_fax ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>
                            @lang('crud.hospitals.inputs.hospital_website_link')
                        </h5>
                        <span>{{ $hospital->hospital_website_link ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.hospitals.inputs.hospital_type_id')</h5>
                        <span>{{$hospital->hospitalType->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('list.hospitals') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Hospital::class)
                        <a href="{{ route('create.hospitals') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
