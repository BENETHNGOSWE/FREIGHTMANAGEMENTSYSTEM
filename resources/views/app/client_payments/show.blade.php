@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('client-payments.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.client_payments.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.client_payments.inputs.amount')</h5>
                    <span>{{ $clientPayment->amount ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.client_payments.inputs.date')</h5>
                    <span>{{ $clientPayment->date ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>
                        @lang('crud.client_payments.inputs.payment_type_id')
                    </h5>
                    <span
                        >{{ optional($clientPayment->paymentType)->name ?? '-'
                        }}</span
                    >
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('client-payments.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\ClientPayment::class)
                <a
                    href="{{ route('client-payments.create') }}"
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
