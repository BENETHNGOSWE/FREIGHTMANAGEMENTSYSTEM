@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a
                    href="{{ route('subscription-invoices.index') }}"
                    class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.subscription_invoices.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.subscription_invoices.inputs.amount')</h5>
                    <span>{{ $subscriptionInvoice->amount ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscription_invoices.inputs.balance')</h5>
                    <span>{{ $subscriptionInvoice->balance ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>
                        @lang('crud.subscription_invoices.inputs.bill_date')
                    </h5>
                    <span>{{ $subscriptionInvoice->bill_date ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscription_invoices.inputs.due_date')</h5>
                    <span>{{ $subscriptionInvoice->due_date ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.subscription_invoices.inputs.status')</h5>
                    <span>{{ $subscriptionInvoice->status ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>
                        @lang('crud.subscription_invoices.inputs.subscription_id')
                    </h5>
                    <span
                        >{{
                        optional($subscriptionInvoice->subscription)->start_date
                        ?? '-' }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>
                        @lang('crud.subscription_invoices.inputs.client_id')
                    </h5>
                    <span
                        >{{ optional($subscriptionInvoice->client)->name ?? '-'
                        }}</span
                    >
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('subscription-invoices.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\SubscriptionInvoice::class)
                <a
                    href="{{ route('subscription-invoices.create') }}"
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
