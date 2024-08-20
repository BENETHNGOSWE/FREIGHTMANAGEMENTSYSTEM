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
                @can('create', App\Models\SubscriptionToken::class)
                <a
                    href="{{ route('subscription-tokens.create') }}"
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
                    @lang('crud.subscription_tokens.index_title')
                </h4>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.subscription_tokens.inputs.subscription_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_tokens.inputs.subscription_invoice_id')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_tokens.inputs.token_')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_tokens.inputs.expire_date')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_tokens.inputs.status')
                            </th>
                            <th class="text-left">
                                @lang('crud.subscription_tokens.inputs.client_id')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptionTokens as $subscriptionToken)
                        <tr>
                            <td>
                                {{
                                optional($subscriptionToken->subscription)->start_date
                                ?? '-' }}
                            </td>
                            <td>
                                {{
                                optional($subscriptionToken->subscriptionInvoice)->bill_date
                                ?? '-' }}
                            </td>
                            <td>{{ $subscriptionToken->token_ ?? '-' }}</td>
                            <td>
                                {{ $subscriptionToken->expire_date ?? '-' }}
                            </td>
                            <td>{{ $subscriptionToken->status ?? '-' }}</td>
                            <td>
                                {{ optional($subscriptionToken->client)->name ??
                                '-' }}
                            </td>
                            <td class="text-center" style="width: 134px;">
                                <div
                                    role="group"
                                    aria-label="Row Actions"
                                    class="btn-group"
                                >
                                    @can('update', $subscriptionToken)
                                    <a
                                        href="{{ route('subscription-tokens.edit', $subscriptionToken) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-create"></i>
                                        </button>
                                    </a>
                                    @endcan @can('view', $subscriptionToken)
                                    <a
                                        href="{{ route('subscription-tokens.show', $subscriptionToken) }}"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                        >
                                            <i class="icon ion-md-eye"></i>
                                        </button>
                                    </a>
                                    @endcan @can('delete', $subscriptionToken)
                                    <form
                                        action="{{ route('subscription-tokens.destroy', $subscriptionToken) }}"
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
                            <td colspan="7">
                                @lang('crud.common.no_items_found')
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                {!! $subscriptionTokens->render() !!}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
