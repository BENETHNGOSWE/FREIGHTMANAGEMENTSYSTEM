@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Add Load</h2>
        </div>
        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.create.loadpackage') }}">Add Load</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <x-form method="POST" action="{{ route('admin.store.loadpackage') }}" class="mt-4">
                <input type="hidden" name="customer_id" value="{{ request()->customer_id }}">

                <div id="load-packages-container">
                    @include('dashboards.admin.loadpackages.form-inputs', ['index' => 0])
                </div>


                <div class="mt-4">
                    <a href="{{ route('admin.list.loadpackage') }}" class="btn btn-light">
                        <i class="fa-solid fa-backLoadpackage"></i>
                        @lang('crud.common.back')
                    </a>
                    {{-- <button type="button" class="btn btn-secondary" id="add-loadpackage-btn">
                        Add Another Load 
                    </button> --}}
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="icon ion-md-save"></i>
                        @lang('crud.common.create')
                    </button>
                </div>
            </x-form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let loadPackageIndex = 1;

        document.getElementById('add-loadpackage-btn').addEventListener('click', function() {
            const container = document.getElementById('load-packages-container');
            const firstLoadPackage = container.querySelector('.load-package-row');
            const newLoadPackage = firstLoadPackage.cloneNode(true);

            newLoadPackage.querySelectorAll('input, select').forEach(function(input) {
                input.name = input.name.replace('[0]', `[${loadPackageIndex}]`);
                input.value = '';
            });

            container.appendChild(newLoadPackage);
            loadPackageIndex++;
            attachRemoveListener();
        });

        function attachRemoveListener() {
            const removeButtons = document.querySelectorAll('.remove-loadpackage-btn');
            removeButtons.forEach(function(button) {
                button.removeEventListener('click', removeLoadPackage);
                button.addEventListener('click', removeLoadPackage);
            });
        }

        function removeLoadPackage(event) {
            const allLoadPackages = document.querySelectorAll('.load-package-row');
            if (allLoadPackages.length > 1) {
                event.target.closest('.load-package-row').remove();
            }
        }

        attachRemoveListener();
    });
</script>
@endsection
