@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Load Package Types</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Load Package Type
                        Show</a></span></p>
        </div>

        <div>
            <a href="{{ route('admin.create.loadpackagetpye') }}" class="btn btn-primary me-3">+ Add Load Package Type</a>
        </div>

    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('admin.list.loadpackagetpye') }}" class="mr-4"
                        ><i class="icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.loadpackagetypes.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.loadpackagetypes.inputs.load_type_name')</h5>
                        <span>{{ $Loadpackagetype->load_type_name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.loadpackagetypes.inputs.load_type_description')</h5>
                        <span>{{ $Loadpackagetype->load_type_description ?? '-' }}</span>
                    </div>
                <div class="mt-4">
                    <a href="{{ route('admin.list.loadpackagetpye') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Loadpackagetype::class)
                    <a href="{{ route('admin.create.loadpackagetpye') }}" class="btn btn-light">
                        <i class="icon ion-md-add"></i> @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
