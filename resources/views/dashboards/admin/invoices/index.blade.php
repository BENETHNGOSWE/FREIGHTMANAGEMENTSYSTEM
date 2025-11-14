@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Invoice</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="#">Invoice List</a></span>
                </p>
            </div>
            <div class="d-flex align-items-center">
                {{-- NEW MERGE BUTTON --}}
                <button id="merge-invoices-btn"
                    class="btn btn-warning me-3 shadow-lg disabled:opacity-50"
                    disabled
                    title="Select invoices using the checkboxes below to enable merging.">
                    <i class="fas fa-file-pdf me-2"></i> Merge Selected Invoices
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3">Home</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    {{-- Hidden Form for Submitting IDs to the Controller --}}
                    <form id="merge-form" action="{{ route('admin.invoices.mergepdf') }}" method="POST">
                        @csrf
                        {{-- The JS below will dynamically add hidden inputs here --}}
                    </form>

                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.invoices.index_title')</h4>
                            {{-- Add a "Select All" checkbox/toggle if needed --}}
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    {{-- NEW: Checkbox Header --}}
                                    <th></th>
                                    <th>s/n</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.invoice_no')</th>
                                    <th class="text-left">Customer</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.total_amount')</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.total_balance')</th>
                                    <th class="text-left">@lang('crud.invoices.inputs.status')</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        {{-- NEW: Checkbox Column --}}
                                        <td>
                                            <input type="checkbox" name="invoice_ids_check"
                                                value="{{ $invoice->id }}"
                                                class="form-check-input merge-checkbox" />
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="color: green; font-weight:700;">
                                            {{ strtoupper($invoice->invoice_no) ?? '-' }}
                                        </td>
                                        {{-- Updated customer display based on your controller --}}
                                        <td>{{ strtoupper($invoice->customer->customer_firstname ?? '-') }}</td>
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
                                                @endif
                                                @if ($canPrintFinalInvoice)
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
                                        <td class="text-center py-5" colspan="8">
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
        $('#loadtypeTable').DataTable({
            searching: true,
            paging: true,
            select: false,
            lengthChange: false,
        });

        // Existing JS for single prints
        document.addEventListener('DOMContentLoaded', function() {
            // ... (Your existing print-release-order and print-final-invoice logic here) ...

            // ----------------------------------------------------
            // NEW MERGE LOGIC
            // ----------------------------------------------------

            const mergeForm = document.getElementById('merge-form');
            const mergeButton = document.getElementById('merge-invoices-btn');
            const checkboxes = document.querySelectorAll('.merge-checkbox');

            function updateMergeButtonState() {
                const checkedCount = document.querySelectorAll('.merge-checkbox:checked').length;
                if (checkedCount > 0) {
                    mergeButton.disabled = false;
                    mergeButton.classList.remove('disabled:opacity-50');
                } else {
                    mergeButton.disabled = true;
                    mergeButton.classList.add('disabled:opacity-50');
                }
            }

            // Listen for changes on all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateMergeButtonState);
            });

            // Initial check on load
            updateMergeButtonState();


            mergeButton.addEventListener('click', function() {
                // Clear any previous inputs
                mergeForm.innerHTML = `@csrf`;

                const selectedInvoiceIds = [];
                const checkedBoxes = document.querySelectorAll('.merge-checkbox:checked');

                if (checkedBoxes.length === 0) {
                    // Use a simple alert-like solution since window.alert is discouraged.
                    // If you have a toast library (like SweetAlert, already included), use that.
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select at least one invoice to merge.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                checkedBoxes.forEach(checkbox => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'invoice_ids[]';
                    hiddenInput.value = checkbox.value;
                    mergeForm.appendChild(hiddenInput);
                });

                // Submit the form
                mergeForm.submit();
            });
            
            // Re-adding your existing print listeners here for completeness
            const printROButtons = document.querySelectorAll('.print-release-order');
            printROButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const invoiceId = button.dataset.invoiceId;
                    window.location.href = `/release/pdf/${invoiceId}`;
                });
            });

            const printFIButtons = document.querySelectorAll('.print-final-invoice');
            printFIButtons.forEach(button => {
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
