@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Invoice</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Invoice List</a></span>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.list.invoice') }}" class="btn btn-primary me-3">+ Add Load</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.invoices.index_title')</h4>
                        </div>
                        <table id="labtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        s/n
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.invoices.inputs.invoice_no')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.invoices.inputs.prefix')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.invoices.inputs.total_amount')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.invoices.inputs.total_balance')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.invoices.inputs.status')
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice as $invoice)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td style="color: green; font-weight:700;">
                                            {{ strtoupper($invoice->invoice_no) ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->customer->customer_firstname) ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->total_amount) ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->total_balance) ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->status) ?? '-' }}</td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                <a href="{{ route('admin.edit.invoice', $invoice) }}">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>

                                                <a href="{{ route('admin.show.invoice', $invoice) }}">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>
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
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
