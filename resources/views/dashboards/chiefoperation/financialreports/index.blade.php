@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Invoice Report</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="#">Invoice List</a></span></p>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('chiefoperation.list.financialreport') }}" class="mb-4">
                        <div class="d-flex flex-column flex-md-row gap-2 align-items-end">
                            <div class="flex-grow-1 w-100">
                                <label for="customer_name">Customer Name</label>
                                <input type="name" name="customer_name" id="customer_name" class="form-control"
                                       value="{{ request('customer_name') }}" placeholder="eg.  Beneth Emmanuel">
                            </div>

                            <div class="flex-grow-1 w-100">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                       value="{{ request('start_date') }}">
                            </div>
                    
                            <!-- End Date -->
                            <div class="flex-grow-1 w-100">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                       value="{{ request('end_date') }}">
                            </div>

                            <div class="d-flex gap-2 flex-grow-1 w-100">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('chiefoperation.list.financialreport') }}"
                                    class="btn btn-secondary btn-sm flex-grow-1">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                                <a href="{{ route('chiefoperation.excel.financialreport', [
                                    'customer_name' => request('customer_name'),
                                    'start_date' => request('start_date'),
                                    'end_date' => request('end_date'),
                                ]) }}"
                                    class="btn btn-success btn-sm flex-grow-1">
                                    <i class="fas fa-download"></i> Excel
                                </a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <h4 class="card-title">Invoice Report</h4>
                        <table id="invoiceTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Customer Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice_no }}</td>
                                        <td>{{ strtoupper($invoice->customer->customer_firstname) ?? '-' }}</td>
                                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="invoice-status">{!! invoice_status($invoice->status) !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="7">No invoices found</td>
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
            var table = $("#invoiceTable").DataTable({
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
                        button.closest(".delete_form").submit();
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
