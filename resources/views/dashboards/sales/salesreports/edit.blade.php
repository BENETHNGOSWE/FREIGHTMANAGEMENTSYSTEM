@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Sales Report</h2>
            
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('sales.list.salesreport') }}">Edit Sales Report</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('sales.update.salesreport', $salesreport ) }}"
                class="mt-4"
            >
            @include('dashboards.sales.salesreports.form-inputs')

                <div class="mt-4">
                    <a
                        href="{{ route('sales.list.salesreport') }}"
                        class="btn btn-light"
                    >
                        <i class="fa-solid fa-backward"></i>
                        @lang('crud.common.back')
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
