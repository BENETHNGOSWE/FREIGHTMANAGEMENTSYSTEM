@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('clients.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.clients.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.name')</h5>
                    <span>{{ $client->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.logo')</h5>
                    <span>{{ $client->logo ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.address')</h5>
                    <span>{{ $client->address ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.country_id')</h5>
                    <span>{{ optional($client->country)->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.city')</h5>
                    <span>{{ $client->city ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.registration_number')</h5>
                    <span>{{ $client->registration_number ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.subdomain')</h5>
                    <span>{{ $client->subdomain ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.clients.inputs.client_type_id')</h5>
                    <span
                        >{{ optional($client->clientType)->name ?? '-' }}</span
                    >
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('clients.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Client::class)
                <a href="{{ route('clients.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
