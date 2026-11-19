@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">What each package includes</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Platform</span> / Modules</p>
        </div>
        <a href="{{ route('owner.list.package') }}" class="btn btn-outline-primary">Packages &amp; prices</a>
    </div>

    <div class="alert alert-info">
        Tick the modules a package unlocks. A client only sees the menus they paid for — and only the ones
        their role has permission to use. Leave a package empty and the client gets the whole product
        (that is how old clients keep working after this upgrade).
    </div>

    @forelse ($packages as $package)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ $package->name }}</h4>
                <span class="badge bg-light text-dark">{{ number_format((float) $package->monthly_fee, 0) }}/mo · {{ number_format((float) $package->yearly_fee, 0) }}/yr</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('platform.modules.sync', $package) }}">
                    @csrf
                    @foreach (['freight' => 'KodaFreight modules', 'sms' => 'KodaSMS modules', 'both' => 'Always available'] as $group => $label)
                        <h6 class="text-muted mt-2">{{ $label }}</h6>
                        <div class="row">
                            @foreach ($modules as $key => $module)
                                @if ($module[1] === $group)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="modules[]" value="{{ $key }}"
                                                   id="{{ $package->id }}-{{ $key }}"
                                                   @checked(in_array($key, $assigned[$package->id] ?? []))>
                                            <label class="form-check-label" for="{{ $package->id }}-{{ $key }}">
                                                <strong>{{ $module[0] }}</strong>
                                                <div class="small text-muted">{{ $module[2] }}</div>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                    <button class="btn btn-primary mt-3">Save modules for {{ $package->name }}</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-muted">
            No package yet. Create one under Packages, then come back to choose its modules.
        </div></div>
    @endforelse
</div>
@endsection
