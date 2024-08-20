@extends('layouts.app')

@section('content')
<div class="container">
    <div class="searchbar mt-0 mb-4">
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="input-group">
                        <input
                            id="indexSearch"
                            type="text"
                            name="search"
                            placeholder="{{ __('crud.common.search') }}"
                            value="{{ $search ?? '' }}"
                            class="form-control"
                            autocomplete="off"
                        />
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon ion-md-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-right">
                @can('create', App\Models\SubscriptionInvoice::class)
                <a
                    href="{{ route('subscription-invoices.create') }}"
                    class="btn btn-primary"
                >
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between;">
                <h4 class="card-title">
                    @lang('crud.subscription_invoices.index_title')
                </h4>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th class="text-right">
                                @lang('crud.subscription_invoices.inputs.amount')
                            </th>
                            <th class="text-right">
                                @lang('crud.subscription_invoices.inputs.balance')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_invoices.inputs.bill_date')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_invoices.inputs.due_date')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_invoices.inputs.status')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_invoices.inputs.subscription_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_invoices.inputs.client_id')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptionInvoices as $subscriptionInvoice)
                        <tr>
                            <td>{{ $subscriptionInvoice->amount ?? '-' }}</td>
                            <td>{{ $subscriptionInvoice->balance ?? '-' }}</td>
                            <td>
                                {{ $subscriptionInvoice->bill_date ?? '-' }}
                            </td>
                            <td>{{ $subscriptionInvoice->due_date ?? '-' }}</td>
                            <td>{{ $subscriptionInvoice->status ?? '-' }}</td>
                            <td>
                                {{
                                optional($subscriptionInvoice->subscription)->start_date
                                ?? '-' }}
                            </td>
                            <td>
                                {{ optional($subscriptionInvoice->client)->name
                                ?? '-' }}
                            </td>
                            <td class="text-center" style="width: 134px;">
                                <div
                                    role="group"
                                    aria-label="Row Actions"
                                    class="btn-group"
                                >
                                    @can('update', $subscriptionInvoice)
                                    <a
                                        href="{{ route('subscription-invoices.edit', $subscriptionInvoice) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-create"></i>
                                        </button>
                                    </a>
                                    @endcan @can('view', $subscriptionInvoice)
                                    <a
                                        href="{{ route('subscription-invoices.show', $subscriptionInvoice) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-eye"></i>
                                        </button>
                                    </a>
                                    @endcan @can('delete', $subscriptionInvoice)
                                    <form
                                        action="{{ route('subscription-invoices.destroy', $subscriptionInvoice) }}"
                                        method="POST"
                                        onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                    >
                                        @csrf @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-light text-danger"
                                        >
                                            <i class="icon ion-md-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                @lang('crud.common.no_items_found')
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8">
                                {!! $subscriptionInvoices->render() !!}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
