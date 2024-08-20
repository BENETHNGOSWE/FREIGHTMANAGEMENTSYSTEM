@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('messages.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.messages.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.messages.inputs.sender_id')</h5>
                    <span>{{ $message->sender_id ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.messages.inputs.receiver_id')</h5>
                    <span>{{ $message->receiver_id ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.messages.inputs.body')</h5>
                    <span>{{ $message->body ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.messages.inputs.status')</h5>
                    <span>{{ $message->status ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.messages.inputs.opened')</h5>
                    <span>{{ $message->opened ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.messages.inputs.client_id')</h5>
                    <span>{{ optional($message->client)->name ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('messages.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Message::class)
                <a href="{{ route('messages.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
