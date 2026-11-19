@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Pickup Note {{ $note->note_no }}</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('cargo.release.index') }}">Release Control</a> / Pickup note</p>
        </div>
        <a href="{{ route('cargo.release.index') }}" class="btn btn-outline-primary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if ($qr)
                        <img src="{{ $qr }}" alt="Pickup note QR" style="width:220px;height:220px">
                    @else
                        <p class="text-muted">QR library not installed.</p>
                    @endif
                    <p class="small text-muted mt-2">{{ $url }}</p>
                    <span class="badge {{ $note->isValid() ? 'bg-success' : 'bg-secondary' }}">{{ strtoupper($note->status) }}</span>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><th>Note number</th><td>{{ $note->note_no }}</td></tr>
                        <tr><th>Invoice</th><td>{{ $invoice->invoice_no }}</td></tr>
                        <tr><th>Customer</th><td>{{ $invoice->customer->customer_firstname ?? '-' }}</td></tr>
                        <tr><th>Amount due on invoice</th><td>{{ number_format($invoice->balanceDue(), 2) }}</td></tr>
                        <tr><th>Issued</th><td>{{ optional($note->issued_at)->format('d M Y H:i') }}</td></tr>
                        <tr><th>Used</th><td>{{ optional($note->used_at)->format('d M Y H:i') ?? '—' }}</td></tr>
                    </table>

                    <h5 class="mt-3">Cargo on this note</h5>
                    <ul class="list-group">
                        @foreach ($cargo as $load)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $load->load_no }} — {{ $load->load_name }}</span>
                                <span class="badge bg-primary">{{ $load->statusLabel() }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($note->isValid())
                        <form method="POST" action="{{ route('cargo.pickup.cancel', $note) }}" class="mt-3">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">Cancel this pickup note</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
