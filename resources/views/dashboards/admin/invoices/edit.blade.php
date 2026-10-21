@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Invoice Status</h2>
        </div>
        <div>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.list.invoice') }}">Edit Invoice</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <x-form method="PUT" action="{{ route('admin.update.invoice', $invoice) }}" class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="invoice_no">Invoice Number</label>
                            <input type="text" value="{{ $invoice->invoice_no }}" name="invoice_no" class="form-control" placeholder="Invoice Number" readonly>
                            @error('invoice_no') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                   <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Invoice Status</label>
                        <select name="status" class="form-control" disabled>
                            <option value="0" {{ $invoice->status == 0 ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ $invoice->status == 2 ? 'selected' : '' }}>Partial Paid</option>
                            <option value="1" {{ $invoice->status == 1 ? 'selected' : '' }}>Full Paid</option>
                        </select>
                        <small class="text-muted">Status is automatic — it follows payments received below.</small>
                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>


                    <!-- Display load_weight_value for each related load -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Load Weight Values</label>
                            @if($invoice->invoiceLoads->isNotEmpty())
                                <ul class="list-group">
                                    @foreach($invoice->invoiceLoads as $invoiceLoad)
                                        <li class="list-group-item">
                                            
                                            {{ rtrim(rtrim(number_format((float)($invoiceLoad->loadPackage->load_weight_value ?? 0), 10, '.', ''), '0'), '.') }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No loads associated with this invoice.</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cbm_price">CBM Price</label>
                            <input type="number" step="0.01" name="cbm_price" value="{{ old('cbm_price', $invoice->cbm_price ?? '') }}" class="form-control" placeholder="Enter CBM Price">
                            @error('cbm_price') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exchange_rate">Exchange Rate</label>
                            <input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', $invoice->exchange_rate ?? '') }}" class="form-control" placeholder="Enter Exchange Rate">
                            @error('exchange_rate') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                   
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.list.invoice') }}" class="btn btn-light">
                        <i class="fa-solid fa-backward"></i> @lang('crud.common.back')
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="icon ion-md-save"></i> @lang('crud.common.update')
                    </button>
                </div>
            </x-form>
            <hr>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Amount to Pay (Total + Transport)</label>
                    <input type="text" class="form-control" value="{{ number_format($amountDue, 2) }}" readonly></div></div>
                <div class="col-md-4"><div class="form-group"><label>Total Received</label>
                    <input type="text" class="form-control" value="{{ number_format($totalReceived, 2) }}" readonly></div></div>
                <div class="col-md-4"><div class="form-group"><label>Balance Due</label>
                    <input type="text" class="form-control" value="{{ number_format($balanceDue, 2) }}" readonly></div></div>
            </div>

            @if($balanceDue > 0)
            <form method="POST" action="{{ route('admin.invoice.payments.store', $invoice) }}" class="mt-3">
                @csrf
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Amount Received *</label>
                        <input type="number" step="0.01" min="0.01" name="amount_received" class="form-control" required></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Payment Date *</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="CASH">Cash</option><option value="BANK">Bank</option>
                            <option value="MOBILE_MONEY">Mobile Money</option><option value="CHEQUE">Cheque</option>
                            <option value="OTHER">Other</option>
                        </select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Reference No</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Receipt / transaction no"></div></div>
                    <div class="col-md-12"><div class="form-group"><label>Notes</label>
                        <input type="text" name="notes" class="form-control"></div></div>
                </div>
                <button type="submit" class="btn btn-success float-right">Record Payment</button>
            </form>
            @else
                <div class="alert alert-success mt-3">This invoice is fully paid.</div>
            @endif

            <div class="table-responsive mt-4">
                <h5>Payment History</h5>
                <table class="table table-bordered">
                    <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Received By</th><th>Notes</th></tr></thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>{{ number_format($payment->amount_received, 2) }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->reference_no ?? '-' }}</td>
                                <td>{{ $payment->receivedBy->name ?? '-' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No payments recorded yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection