@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">View Hospital</h2>

            </div>

            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('dashboard.index') }}">View
                            hospital</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $hospital->hospital_name ?? '-' }}</h4>
            </div>
            <div class="card-body">

                <div class="col-xl-12">

                    <!-- Nav tabs -->
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#hospital_info" aria-selected="true"
                                    role="tab"><i class="la la-home me-2"></i>Hospital Details</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#admin_details" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-user me-2"></i> Admin Details</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#invoices" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-dollar me-2"></i> Invoices </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#subscriptions" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-file me-2"></i> Subscriptions</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#message" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-envelope me-2"></i> Message</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="hospital_info" role="tabpanel">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-responsive-sm">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%">Hospital Name</th>
                                                            <th>{{ $hospital->hospital_name ?? '-' }}</th>
                                                        </tr>


                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Hospital Type</td>
                                                            <td>{{ $hospital->hospitalType() ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address</td>
                                                            <td>{{ $hospital->hospital_address_one ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address 2</td>
                                                            <td>{{ $hospital->hospital_address_two ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tax/Tin number</td>
                                                            <td>{{ $hospital->hospital_tinnumber ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Location</td>
                                                            <td>{{ $hospital->hospital_location ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Location</td>
                                                            <td>{{ $hospital->hospital_location ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>City</td>
                                                            <td>{{ $hospital->hospital_city ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Country</td>
                                                            <td>{{ $hospital->country() ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Zipcode</td>
                                                            <td>{{ $hospital->hospital_zipcode ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Fax</td>
                                                            <td>{{ $hospital->hospital_fax ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Website</td>
                                                            <td>{{ $hospital->hospital_website_link ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Hospital Logo</td>
                                                            <td>
                                                                <img src="{{ asset('storage/uploads/' . $hospital->hospital_logo) }}"
                                                                    alt="Hospital Logo"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="admin_details" role="tabpanel">
                               <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-responsive-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%" class="text-capitalize">Admin Name</th>
                                                        <th>{{ $admin->user->name ?? '-' }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Admin Email</td>
                                                        <td>{{ $admin->email ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Admin Phone</td>
                                                        <td>{{ $admin->phone ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Admin Address</td>
                                                        <td>{{ $admin->address ?? '-' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                               </div>
                            </div>
                            <div class="tab-pane fade" id="invoices" role="tabpanel">
                                <div class="pt-4">
                                    <h4>Invoices</h4>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="subscriptions" role="tabpanel">
                                <div class="pt-4">
                                    <h4>subscriptions</h4>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="message" role="tabpanel">
                                <div class="pt-4">
                                    <h4>This is message title</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex ">
                    <a href="{{ route('edit.hospitals', $hospital) }}" class="btn btn-outline-success">
                        <i class="icon fa fa-add"></i>
                        Edit
                    </a>

                    @can('delete', $hospital)
                        <form class="delete_form mx-3" action="{{ route('delete.hospitals', ['hospital' => $hospital->id]) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger delete_button">
                                <i class="fas fa-trash"></i>delete
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
