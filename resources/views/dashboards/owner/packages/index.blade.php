@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">package List</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('owner.list.package') }}">package List</a></span></p>
            </div>
            <div>
                <a href="{{ route('owner.create.package') }}" class="btn btn-primary me-3">+ Add new</a>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="EmployeesTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        S/N
                                    </th>
                                    <th>
                                        Employee Name
                                    </th>
                                    <th>
                                        Role
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientadmins as $package)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        
                                        
                                        <td  style="width: 40%;">{{ $package->package_firstname . " " . $package->package_middlename . " " . $package->package_lastname  ?? '-' }}</td>
                                        <td  style="width: 20%;">{{ $package->user->user_type ?? '-' }}</td>
                                        <td>
                                            {!! user_status($package->user->status)!!}
                                        </td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">

                                                <a href="{{ route('owner.edit.package', $package) }}">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>

                                                <a href="{{ route('owner.show.package', $package) }}">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>

                                                <form class="delete_form"
                                                    action="{{ route('owner.delete.package', $package) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger delete_button">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="5">
                                            @lang('crud.common.no_items_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
        


    </div>
    @push('scripts')
    <script>
        (function($) {
            var table = $("#labtypeTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        })(jQuery);
    </script>

    <script>
        const deleteButtons = document.querySelectorAll(".delete_button");

        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                swal({
                    title: "Are you sure to delete ?",
                    text: "You will not be able to recover deleted data !!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it !!",
                    cancelButtonText: "No, cancel it !!",
                    closeOnConfirm: !1,
                    closeOnCancel: !1
                }).then((result) => {
                    if (result.value) {
                        const form = button.closest(".delete_form");
                        form.submit();
                    } else {
                        swal("Cancelled", "Your data is safe", "success");
                    }
                });
            });
        });
    </script>
@endpush
@endsection
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
