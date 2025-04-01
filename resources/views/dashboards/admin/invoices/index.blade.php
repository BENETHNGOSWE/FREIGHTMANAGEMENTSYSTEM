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
                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3">Home</a>
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
                                    <th>s/n</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.invoice_no')</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.prefix')</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.total_amount')</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.total_balance')</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.status')</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="color: green; font-weight:700;">
                                            {{ strtoupper($invoice->invoice_no) ?? '-' }}
                                        </td>
                                        <td>{{ strtoupper($invoice->customer->customer_firstname) ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->total_amount) ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->total_amount) ?? '-' }}</td>
                                        <td class="invoice-status">{!! invoice_status($invoice->status) !!}</td>
                                        <td class="text-center" style="width: 200px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @if ($canPrintReleaseOrder)
                                                    <a href="{{ route('admin.edit.invoice', $invoice) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.show.invoice', $invoice) }}">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>
                                                @if ($canPrintReleaseOrder)
                                                    <button type="button"
                                                        class="btn btn-outline-secondary print-release-order mx-2" 
                                                        data-invoice-id="{{ $invoice->id }}">
                                                        Print Release Order
                                                    </button>
                                                    <button type="button"
                                                    class="btn btn-outline-secondary print-final-invoice"
                                                    data-invoice-id="{{ $invoice->id }}">
                                                    Print Final Invoice
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="7">
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
        document.addEventListener('DOMContentLoaded', function() {
            const printButtons = document.querySelectorAll('.print-release-order');

            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const invoiceId = button.dataset.invoiceId;
                    window.location.href = `/release/pdf/${invoiceId}`;
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const printButtons = document.querySelectorAll('.print-final-invoice');

            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const invoiceId = button.dataset.invoiceId;
                    window.location.href = `/finalInvoice/pdf/${invoiceId}`;
                });
            });
        });
    </script>
@endpush
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
