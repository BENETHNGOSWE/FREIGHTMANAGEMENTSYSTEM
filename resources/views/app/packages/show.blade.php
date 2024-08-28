@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('packages.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.packages.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.packages.inputs.name')</h5>
                    <span>{{ $package->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.packages.inputs.monthly_fee')</h5>
                    <span>{{ $package->monthly_fee ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.packages.inputs.yearly_fee')</h5>
                    <span>{{ $package->yearly_fee ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.packages.inputs.features')</h5>
                    <span>{{ $package->features ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.packages.inputs.client_id')</h5>
                    <span>{{ optional($package->client)->name ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('packages.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Package::class)
                <a href="{{ route('packages.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
