@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Add Client</h2>
                
            </div>

            <div>
               <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('list.clients') }}">Add Client</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('store.clients') }}" has-files class="mt-4">
                    @csrf
                    @include('dashboards.owner.clients.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('list.clients') }}" class="btn btn-light">
                            <i class="fa-solid fa-backward"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.create')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
