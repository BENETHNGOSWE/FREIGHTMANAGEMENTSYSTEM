@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Customer Statement</h2>
            <p class="mb-1">
                Dashboard / <a class="text-primary" href="{{ route('accounts.reports.receivables') }}">Receivables</a> /
                {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
            </p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-end">
            <div>
                <label class="form-label mb-1">Customer</label>
                <select name="customer" class="form-control" onchange="window.location='/accounts/ledger/customer/'+this.value">
                    @foreach ($customers as $option)
                        <option value="{{ $option->id }}" @selected($option->id === $customer->id)>
                            {{ $option->customer_firstname }} {{ $option->customer_lastname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div>
                <label class="form-label mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <button class="btn btn-primary">Show</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">{{ $customer->customer_firstname }} {{ $customer->customer_lastname }}</h4>
                    <small class="text-muted">{{ $customer->customer_email }} · {{ $customer->customer_phone ?? '' }}</small>
                </div>
                <div class="text-end">
                    <div>Opening: <strong>{{ number_format($opening, 2) }}</strong></div>
                    <div>Balance due: <strong>{{ number_format($closing, 2) }}</strong></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th class="text-end">Invoice</th>
                            <th class="text-end">Payment</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('d M Y') }}</td>
                                <td>{{ $row['type'] }}</td>
                                <td>{{ $row['reference'] }}</td>
                                <td>{{ $row['description'] }}</td>
                                <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}</td>
                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No invoices or payments in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
