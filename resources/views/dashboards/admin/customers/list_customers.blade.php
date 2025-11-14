@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Customer List</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Customer
                            List</a></span>
                </p>
            </div>
            <div class="d-flex">
                <!-- Download Template -->
                <a href="{{ route('customers.template') }}" class="btn btn-success me-3">
                    <i class="fa fa-download"></i> Download Template
                </a>

                <!-- Import Excel -->
                <button type="button" data-bs-toggle="modal" data-bs-target="#importModal"
                    class="btn btn-info me-3">
                    <i class="fa fa-upload"></i> Import Excel
                </button>

                <!-- Add Customer -->
                <a href="{{ route('admin.create.customer') }}" class="btn btn-primary">+ Add Customer</a>
            </div>

        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.customers.index_title')</h4>
                        </div>
                        <table id="customerTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        s/n
                                    </th>
                                    <th class="text-center">
                                        image
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.customers.inputs.customer_firstname')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.customers.inputs.customer_no')
                                    </th>
                                    <th class="text-left">
                                        Add load
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="text-center" style="width: 10%;">
                                            @if ($customer->image)
                                                <img src="{{ asset('storage/' . $customer->image) }}" alt=""
                                                    width="50px" height="50px">
                                            @else
                                                <img src="{{ asset('images/default-user.png') }}" alt=""
                                                    width="50px" height="50px">
                                            @endif
                                        </td>
                                        <td>{{ strtoupper($customer->customer_firstname . ' ' . $customer->customer_middlename . ' ' . $customer->customer_lastname) ?? '-' }}
                                        </td>
                                        <td>{{ strtoupper($customer->phone) ?? '-' }}</td>
                                        <td>
                                            <a
                                                href="{{ route('admin.create.loadpackage', ['customer_id' => $customer->id]) }}">
                                                <button type="button" class="btn btn-outline-primary mx-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </a>
                                        </td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @if ($canSeeActionButtons)
                                                    <a href="{{ route('admin.edit.customer', $customer) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>

                                                    <a href="{{ route('admin.show.customer', $customer) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </a>

                                                    <form class="delete_form"
                                                        action="{{ route('admin.delete.customer', $customer) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger delete_button">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
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
    <!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data"
            class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Customers & Loads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>Upload an Excel file that follows the downloaded template format.</p>

                <div class="form-group">
                    <label for="file">Choose Excel File (.xlsx or .csv)</label>
                    <input type="file" name="file" class="form-control mt-2" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-upload"></i> Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        (function($) {
            var table = $("#customerTable").DataTable({
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
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
