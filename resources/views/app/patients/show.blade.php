@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('patients.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.patients.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.patients.inputs.user_id')</h5>
                    <span>{{ optional($patient->user)->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.patients.inputs.patientID')</h5>
                    <span>{{ $patient->patientID ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.patients.inputs.has_insurance')</h5>
                    <span>{{ $patient->has_insurance ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.patients.inputs.city')</h5>
                    <span>{{ $patient->city ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('patients.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Patient::class)
                <a href="{{ route('patients.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
