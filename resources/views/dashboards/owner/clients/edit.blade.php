@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Add Hospital</h2>
                
            </div>

            <div>
               <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('list.hospitals') }}">Add Hospital</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <x-form method="PUT" action="{{ route('update.hospitals',$hospital) }}" has-files class="mt-4">
                    @include('dashboards.owner.hospitals.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('list.hospitals') }}" class="btn btn-light">
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
