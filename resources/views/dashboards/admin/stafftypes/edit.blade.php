@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Staff Type</h2>
            
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('dashboard.index') }}">Edit Staff Type</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="POST"
                action="{{ route('update.stafftypes', $stafftype) }}"
                class="mt-4"
            >
                @include('dashboards.admin.stafftypes.form-inputs')

                <div class="mt-4">
                    <a
                        href="{{ route('list.stafftypes') }}"
                        class="btn btn-light"
                    >
                        <i class="fa-solid fa-backward"></i>
                        @lang('crud.common.back')
                    </a>

                    <a
                        href="{{ route('create.stafftypes') }}"
                        class="btn btn-light"
                    >
                        <i class="icon ion-md-add text-primary"></i>
                        @lang('crud.common.create')
                    </a>

                    <button type="submit" class="btn btn-primary float-right">
                        <i class="icon ion-md-save"></i>
                        @lang('crud.common.update')
                    </button>
                </div>
            </x-form>
        </div>
    </div>
</div>
@endsection
