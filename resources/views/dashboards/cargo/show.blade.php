@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">{{ $load->load_no }}</h2>
            <p class="mb-1">
                Dashboard / <a class="text-primary" href="{{ route('cargo.index') }}">Cargo</a> / {{ $load->load_no }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cargo.index') }}" class="btn btn-outline-primary">Back</a>
            @if ($invoice)
                <a href="{{ route('cargo.pickup.note', $invoice) }}" class="btn btn-outline-success">Pickup note</a>
            @endif
        </div>
    </div>

    <div class="row align-items-start">
        {{-- ------------------------------------------------------------ left: cargo + photos --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Cargo</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>Customer:</strong> {{ $load->customer->customer_firstname ?? '-' }}</div>
                        <div class="col-md-6 mb-2"><strong>Product:</strong> {{ $load->load_name }}</div>
                        <div class="col-md-6 mb-2"><strong>Weight:</strong> {{ $load->load_weight_value }} {{ optional($load->unittype)->unittype_name }}
                            @if ($load->original_weight_value && (float) $load->original_weight_value !== (float) $load->verified_weight_value)
                                <small class="text-warning">(China: {{ $load->original_weight_value }})</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-2"><strong>Status:</strong> <span class="badge bg-primary">{{ $load->statusLabel() }}</span></div>
                        <div class="col-md-6 mb-2"><strong>China location:</strong> {{ $load->china_location ?: '-' }}</div>
                        <div class="col-md-6 mb-2"><strong>Batch:</strong> {{ $load->batch_no ?: '-' }}</div>
                        @if ($load->container->container_number ?? false)
                            <div class="col-md-6 mb-2"><strong>Container:</strong> {{ $load->container->container_number }}</div>
                        @endif
                        @if ((float) $load->weight_price_impact != 0)
                            <div class="col-12">
                                <div class="alert alert-warning py-2 mb-0">
                                    KG changed — price affected by {{ number_format((float) $load->weight_price_impact, 2) }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr>
                    <h5>Cargo photos</h5>
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        @forelse ($load->photos as $photo)
                            <div style="width:150px">
                                <img src="{{ $photo->url() }}" alt="{{ $photo->caption }}" class="img-fluid rounded"
                                     style="height:110px;object-fit:cover;width:100%">
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted text-truncate">{{ $photo->caption ?: 'photo' }}</small>
                                    <form method="POST" action="{{ route('cargo.photo.delete', [$load, $photo]) }}"
                                          onsubmit="return confirm('Remove this photo?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0">×</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No photos yet. Add them when the cargo is registered in China.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('cargo.details', $load) }}" enctype="multipart/form-data" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label mb-1">China location</label>
                            <input type="text" name="china_location" class="form-control" value="{{ old('china_location', $load->china_location) }}" placeholder="e.g. Guangzhou warehouse">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1">Batch / shipment</label>
                            <input type="text" name="batch_no" class="form-control" value="{{ old('batch_no', $load->batch_no) }}" placeholder="e.g. BATCH-04">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1">Description</label>
                            <input type="text" name="load_description" class="form-control" value="{{ old('load_description', $load->load_description) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Add photos</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1">Caption (first photo)</label>
                            <input type="text" name="captions[]" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ------------------------------------------------------------------ timeline --}}
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Journey</h4></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach ($timeline as $step)
                            <li class="d-flex mb-3">
                                <div class="me-3">
                                    <span class="badge {{ $step['done'] ? 'bg-success' : 'bg-light text-muted' }}" style="width:22px">
                                        {{ $step['done'] ? '✓' : '•' }}
                                    </span>
                                </div>
                                <div>
                                    <strong class="{{ $step['current'] ? 'text-primary' : '' }}">{{ $step['label'] }}</strong>
                                    @if ($step['date'])
                                        <small class="text-muted ms-2">{{ $step['date'] instanceof \Carbon\Carbon ? $step['date']->format('d M Y H:i') : \Illuminate\Support\Carbon::parse($step['date'])->format('d M Y') }}</small>
                                    @endif
                                    @if ($step['note'])
                                        <div><small class="text-muted">{{ $step['note'] }}</small></div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <form method="POST" action="{{ route('cargo.status', $load) }}" class="row g-2 mt-2">
                        @csrf
                        <div class="col-md-4">
                            <select name="cargo_status" class="form-control">
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}" @selected($load->cargo_status === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="note" class="form-control" placeholder="Note (optional)">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100">Update status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ------------------------------------------------------------ right: QR, money, checks --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Cargo QR</h4></div>
                <div class="card-body text-center">
                    @if ($qr)
                        <img src="{{ $qr }}" alt="Cargo QR" style="width:170px;height:170px">
                    @else
                        <p class="text-muted">QR library not installed — run <code>composer install</code>.</p>
                    @endif
                    <p class="small text-muted mt-2 mb-1">Customers scan this to see their cargo status.</p>
                    <a href="{{ $trackingUrl }}" target="_blank" class="small">{{ $trackingUrl }}</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Money</h4></div>
                <div class="card-body">
                    @if ($invoice)
                        <div class="d-flex justify-content-between"><span>Invoice</span><strong>{{ $invoice->invoice_no }}</strong></div>
                        <div class="d-flex justify-content-between"><span>Amount due</span><strong>{{ number_format($invoice->amountDue(), 2) }}</strong></div>
                        <div class="d-flex justify-content-between"><span>Balance</span><strong class="{{ $invoice->balanceDue() > 0 ? 'text-danger' : 'text-success' }}">{{ number_format(max($invoice->balanceDue(), 0), 2) }}</strong></div>
                    @else
                        <p class="text-muted mb-0">No invoice for this cargo yet.</p>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between"><span>Storage charged</span><strong>{{ number_format($storage ? $storage->chargedTotal() : 0, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Storage paid / waived</span><strong>{{ number_format($storage ? $storage->paidTotal() + $storage->waivedTotal() : 0, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Storage balance</span>
                        <strong class="{{ $storageBalance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($storageBalance, 2) }}</strong>
                    </div>
                    @if ($storage)
                        <a href="{{ route('storage.show', $storage) }}" class="btn btn-sm btn-outline-primary w-100 mt-3">Open storage</a>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Release checklist</h4></div>
                <div class="card-body">
                    @foreach ($controls['checks'] as $check)
                        <div class="mb-2">
                            <span class="badge {{ $check['ok'] ? 'bg-success' : 'bg-danger' }}">{{ $check['ok'] ? '✓' : '✕' }}</span>
                            <strong>{{ $check['label'] }}</strong>
                            <div><small class="text-muted">{{ $check['detail'] }}</small></div>
                        </div>
                    @endforeach
                    <div class="d-flex gap-2 mt-3">
                        <form method="POST" action="{{ route('cargo.release.release', $load) }}" class="w-100">
                            @csrf
                            <button class="btn btn-primary w-100" @disabled(! $controls['can_release'])>Release</button>
                        </form>
                        <form method="POST" action="{{ route('cargo.release.collect', $load) }}" class="w-100">
                            @csrf
                            <button class="btn btn-outline-success w-100" @disabled(! $controls['can_collect'])>Collected</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
