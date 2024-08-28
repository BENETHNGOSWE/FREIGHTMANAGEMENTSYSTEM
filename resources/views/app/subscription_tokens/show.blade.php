@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('subscription-tokens.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.subscription_tokens.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>
                        @lang('crud.subscription_tokens.inputs.subscription_id')
                    </h5>
                    <span
                        >{{
                        optional($subscriptionToken->subscription)->start_date
                        ?? '-' }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>
                        @lang('crud.subscription_tokens.inputs.subscription_invoice_id')
                    </h5>
                    <span
                        >{{
                        optional($subscriptionToken->subscriptionInvoice)->bill_date
                        ?? '-' }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscription_tokens.inputs.token_')</h5>
                    <span>{{ $subscriptionToken->token_ ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>
                        @lang('crud.subscription_tokens.inputs.expire_date')
                    </h5>
                    <span>{{ $subscriptionToken->expire_date ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscription_tokens.inputs.status')</h5>
                    <span>{{ $subscriptionToken->status ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscription_tokens.inputs.client_id')</h5>
                    <span
                        >{{ optional($subscriptionToken->client)->name ?? '-'
                        }}</span
                    >
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('subscription-tokens.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\SubscriptionToken::class)
                <a
                    href="{{ route('subscription-tokens.create') }}"
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
