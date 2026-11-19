<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cargo tracking{{ $load ? ' — ' . $load->load_no : '' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; background: #f4f6fb; color: #1f2a44; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 28px 18px 60px; }
        .brand { font-size: 15px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #2f6bff; margin-bottom: 18px; }
        .card { background: #fff; border-radius: 14px; padding: 22px; box-shadow: 0 6px 24px rgba(31,42,68,.08); margin-bottom: 18px; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        p.sub { margin: 0 0 18px; color: #667085; font-size: 14px; }
        form.search { display: flex; gap: 10px; }
        input[type=text] { flex: 1; padding: 12px 14px; border: 1px solid #d0d5dd; border-radius: 10px; font-size: 15px; }
        button { padding: 12px 20px; background: #2f6bff; color: #fff; border: 0; border-radius: 10px; font-size: 15px; cursor: pointer; }
        .alert { background: #fef3f2; color: #b42318; border-radius: 10px; padding: 12px 14px; font-size: 14px; }
        .meta { display: flex; flex-wrap: wrap; gap: 14px 28px; margin-bottom: 6px; }
        .meta div span { display: block; font-size: 12px; color: #98a2b3; text-transform: uppercase; letter-spacing: .04em; }
        .meta div strong { font-size: 15px; }
        ul.timeline { list-style: none; padding: 0; margin: 18px 0 0; }
        ul.timeline li { display: flex; gap: 14px; padding: 10px 0; border-bottom: 1px solid #f0f1f4; }
        ul.timeline li:last-child { border-bottom: 0; }
        .dot { width: 22px; height: 22px; border-radius: 50%; background: #eaecf0; color: #98a2b3; display: flex; align-items: center; justify-content: center; font-size: 12px; flex: 0 0 22px; }
        .done .dot { background: #12b76a; color: #fff; }
        .current { font-weight: 700; color: #2f6bff; }
        .when { margin-left: auto; font-size: 12px; color: #98a2b3; white-space: nowrap; }
        .foot { text-align: center; font-size: 12px; color: #98a2b3; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="brand">Cargo Tracking</div>

    <div class="card">
        <h1>Where is my cargo?</h1>
        <p class="sub">Enter your tracking number (it is on your receipt) or scan the QR code on your cargo.</p>
        <form class="search" method="GET" action="{{ url('track') }}">
            <input type="text" name="tracking" value="{{ $tracking }}" placeholder="e.g. TZAB12CD3" required>
            <button type="submit">Track</button>
        </form>
    </div>

    @if ($error)
        <div class="card"><div class="alert">{{ $error }}</div></div>
    @endif

    @if ($load)
        <div class="card">
            <h1>{{ $load->load_no }}</h1>
            <p class="sub">{{ $load->load_name }}</p>

            <div class="meta">
                <div><span>Status</span><strong>{{ $load->statusLabel() }}</strong></div>
                <div><span>Weight</span><strong>{{ $load->billableWeight() }} {{ optional($load->unittype)->unittype_name }}</strong></div>
                <div><span>Customer</span><strong>{{ $load->customer->customer_firstname ?? '-' }}</strong></div>
            </div>

            <ul class="timeline">
                @foreach ($timeline as $step)
                    <li class="{{ $step['done'] ? 'done' : '' }}">
                        <div class="dot">{{ $step['done'] ? '✓' : '•' }}</div>
                        <div class="{{ $step['current'] ? 'current' : '' }}">{{ $step['label'] }}</div>
                        <div class="when">
                            @if ($step['date'])
                                {{ $step['date'] instanceof \Carbon\Carbon ? $step['date']->format('d M Y') : \Illuminate\Support\Carbon::parse($step['date'])->format('d M Y') }}
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="foot">Keep your tracking number safe. For any question, contact our office.</p>
</div>
</body>
</html>
