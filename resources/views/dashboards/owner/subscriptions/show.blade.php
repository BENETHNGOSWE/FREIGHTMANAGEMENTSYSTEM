@extends('layouts._master')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('list.subscriptions') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.subscriptions.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.onboardhospital_name')</h5>
                    <span>{{ $subscription->onboardhospital_name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.hospital_id')</h5>
                    <span>{{ $subscription->hospital_id ?? '-' }}</span>
                </div>

                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.start_date')</h5>
                    <span>{{ $subscription->start_date ?? '-' }}</span>
                </div>

                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.expire_date')</h5>
                    <span>{{ $subscription->expire_date ?? '-' }}</span>
                </div>

                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.package_id')</h5>
                    <span>{{ $subscription->package_id ?? '-' }}</span>
                </div>
                
            </div>

            <div class="mt-4">
                <a href="{{ route('list.subscriptions') }}" class="btn btn-ght">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Subscription::class)
                <a href="{{ route('create.subscriptions') }}" class="btn btn-primary">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
