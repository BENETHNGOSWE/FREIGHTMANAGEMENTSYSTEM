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
                    {{-- Customer Statement Summary --}}
                    <div class="table-responsive mb-4">
                        <h4 class="card-title">Customer Statement Summary</h4>
                        <table id="customerStatementTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Invoices</th>
                                    <th>Total Invoiced</th>
                                    <th>Total Received</th>
                                    <th>Balance Outstanding</th>
                                    <th>Last Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customerStatements as $statement)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper(($statement->customer->customer_firstname ?? '') . ' ' . ($statement->customer->customer_lastname ?? '')) ?: '-' }}</td>
                                        <td>{{ $statement->invoice_count }}</td>
                                        <td>{{ number_format($statement->total_due, 2) }}</td>
                                        <td>{{ number_format($statement->total_received, 2) }}</td>
                                        <td>{{ number_format($statement->outstanding_amount, 2) }}</td>
                                        <td>{{ $statement->last_invoice_date ? \Carbon\Carbon::parse($statement->last_invoice_date)->format('d M Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No customer statements found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($customerStatements->isNotEmpty())
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th>{{ $customerStatements->sum('invoice_count') }}</th>
                                        <th>{{ number_format($customerStatements->sum('total_due'), 2) }}</th>
                                        <th>{{ number_format($customerStatements->sum('total_received'), 2) }}</th>
                                        <th>{{ number_format($customerStatements->sum('outstanding_amount'), 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
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
                                    <th>Action</th>
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
                                        <td>
                                            <a href="{{ route('admin.show.invoice', $invoice) }}" class="btn btn-outline-primary btn-sm" title="View invoice details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.pdf.finalinvoice', $invoice) }}" class="btn btn-outline-secondary btn-sm" title="Download final invoice PDF">
                                                <i class="fas fa-print"></i> Print
                                            </a>
                                        </td>
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
           $("#customerStatementTable").DataTable({
            searching: true,
            paging: true,
            select: false,
            lengthChange: false,
            pageLength: 10,
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // Helper: remove commas like "2,352,304,838.00" -> 2352304838.00
                function clean(value) {
                    return parseFloat(String(value).replace(/,/g, '')) || 0;
                }

                // Helper: sum one column using ONLY the rows matching the current search
                function sumColumn(index) {
                    return api.column(index, { search: 'applied' }).data()
                        .reduce(function (a, b) { return clean(a) + clean(b); }, 0);
                }

                function fmt(value) {
                    return Number(value).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                // Column numbers: 2=Invoices, 3=Total Invoiced, 4=Full Paid, 5=Outstanding
                $(api.column(2).footer()).html(sumColumn(2));
                $(api.column(3).footer()).html(fmt(sumColumn(3)));
                $(api.column(4).footer()).html(fmt(sumColumn(4)));
                $(api.column(5).footer()).html(fmt(sumColumn(5)));
            }
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
