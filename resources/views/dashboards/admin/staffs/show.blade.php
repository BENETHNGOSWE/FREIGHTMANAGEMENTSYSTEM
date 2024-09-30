@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">View Staff</h2>
            </div>
            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.list.staff') }}">View Staff</a></span></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $staff->staff_firstname . ' ' . $staff->staff_middlename . ' ' . $staff->staff_lastname ?? '-' }}</h4>
            </div>

            <div class="card-body">
                <div class="col-xl-12">
                    <!-- Nav tabs -->
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#staff_info" aria-selected="true" role="tab">
                                    <i class="la la-home me-2"></i>Staff Details
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- Staff Information Tab -->
                            <div class="tab-pane fade active show" id="staff_info" role="tabpanel">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-responsive-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 20%">First Name</td>
                                                            <td>{{ $staff->staff_firstname ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Middle Name</td>
                                                            <td>{{ $staff->staff_middlename ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Last Name</td>
                                                            <td>{{ $staff->staff_lastname ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Gender</td>
                                                            <td>{{ $staff->staff_gender ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Email</td>
                                                            <td>{{ $staff->staff_email ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Phone</td>
                                                            <td>{{ $staff->phone ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Staff Type</td>
                                                            <td>{{ $staff->staffType->name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address</td>
                                                            <td>{{ $staff->staff_address ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Staff Information Tab -->
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex">
                    <a href="{{ route('admin.edit.staff', $staff) }}" class="btn btn-outline-success">
                        <i class="icon fa fa-edit"></i> Edit
                    </a>

                    @can('delete', $staff)
                        <form class="delete_form mx-3" action="{{ route('admin.delete.staff', ['staff' => $staff->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger delete_button">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const deleteButton = document.querySelector(".delete_button");

    deleteButton.addEventListener("click", function(e) {
        e.preventDefault();
        swal({
            title: "Are you sure to delete?",
            text: "You will not be able to recover deleted data!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then((result) => {
            if (result.value) {
                const form = deleteButton.closest(".delete_form");
                form.submit();
            } else {
                swal("Cancelled", "Your data is safe", "success");
            }
        });
    });
</script>
@endpush

@include('layouts.partials.sweetalert')
