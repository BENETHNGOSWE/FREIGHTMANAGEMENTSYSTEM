@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Customer Leads List</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="#">Customer Leads
                            List</a></span>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.create.customerlead') }}" class="btn btn-primary me-3">+ Add Leads</a>
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
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customerleads as $customer)
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
                                        <td>{{ strtoupper($customer->customer_no) ?? '-' }}</td>
                                       
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">

                                                <a href="{{ route('admin.edit.customerlead', $customer) }}">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>

                                                <a href="{{ route('admin.show.customerlead', $customer) }}">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>

                                                <form class="delete_form"
                                                    action="{{ route('admin.delete.customerlead', $customer) }}" method="POST">
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
