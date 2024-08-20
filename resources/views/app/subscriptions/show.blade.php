@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('subscriptions.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.subscriptions.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.client_id')</h5>
                    <span
                        >{{ optional($subscription->client)->name ?? '-'
                        }}</span
                    >
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
                    <span
                        >{{ optional($subscription->package)->name ?? '-'
                        }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscriptions.inputs.status')</h5>
                    <span>{{ $subscription->status ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('subscriptions.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Subscription::class)
                <a
                    href="{{ route('subscriptions.create') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
