@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Staff List</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('admin.list.staff') }}">Staff List</a></span></p>
            </div>
            <div>
                <a href="{{ route('admin.create.staff') }}" class="btn btn-primary me-3">+ Add new</a>
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
                                    <th class="text-center">
                                        image
                                    </th>
                                    <th>
                                        Employee ID
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
                                @forelse($employees as $staff)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="text-center"  style="width: 10%;">
                                            @if($staff->image)
                                            <img src="{{ asset('storage/'.$staff->image) }}" alt="" width="50px" height="50px">
                                            @else 
                                            <img src="{{ asset('images/default-user.png') }}" alt="" width="50px" height="50px">
                                            @endif
                                        </td>
                                        
                                        <td style="width: 10%;">{{ $staff->staff_no ?? '-' }}</td>
                                        <td  style="width: 40%;">{{ $staff->staff_firstname . " " . $staff->staff_middlename . " " . $staff->staff_lastname  ?? '-' }}</td>
                                        <td  style="width: 20%;">{{ $staff->user->role_name ?? '-' }}</td>
                                        <td>
                                            {!! user_status($staff->user->status)!!}
                                        </td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">

                                                <a href="{{ route('admin.edit.staff', $staff) }}">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>

                                                <a href="{{ route('admin.show.staff', $staff) }}">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>

                                                <form class="delete_form"
                                                    action="{{ route('admin.delete.staff', $staff) }}" method="POST">
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
