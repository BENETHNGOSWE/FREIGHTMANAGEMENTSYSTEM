@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit container</h2>
            
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('admin.list.companyinvoicedetail') }}">Edit container</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('admin.update.companyinvoicedetail', $companyinvoicedetail ) }}"
                class="mt-4"
            >
            @include('dashboards.admin.companyinvoicedetails.form-inputs')

                <div class="mt-4">
                    <a
                        href="{{ route('admin.list.companyinvoicedetail') }}"
                        class="btn btn-light"
                    >
                        <i class="fa-solid fa-backward"></i>
                        @lang('crud.common.back')
                    </a>

                    <a
                        href="{{ route('admin.create.companyinvoicedetail') }}"
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
