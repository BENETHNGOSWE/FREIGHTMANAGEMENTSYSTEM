@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('modules.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.modules.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.modules.inputs.name')</h5>
                    <span>{{ $module->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.modules.inputs.features')</h5>
                    <span>{{ $module->features ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.modules.inputs.client_id')</h5>
                    <span>{{ optional($module->client)->name ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('modules.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Module::class)
                <a href="{{ route('modules.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
