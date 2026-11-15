<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pickup note check</title>
    <style>
        body { margin:0; font-family:-apple-system,"Segoe UI",Roboto,Arial,sans-serif; background:#f4f6fb; color:#1f2a44;
               display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
        .card { background:#fff; border-radius:16px; padding:34px 26px; text-align:center; max-width:420px;
                box-shadow:0 8px 30px rgba(31,42,68,.10); }
        .icon { width:74px; height:74px; border-radius:50%; margin:0 auto 16px; display:flex; align-items:center;
                justify-content:center; font-size:36px; color:#fff; }
        .ok { background:#12b76a; } .no { background:#f04438; }
        h1 { font-size:20px; margin:0 0 8px; }
        p { color:#667085; font-size:14px; margin:0 0 6px; }
        .ref { font-family:monospace; font-size:13px; color:#98a2b3; margin-top:14px; }
    </style>
</head>
<body>
<div class="card">
    @if ($valid)
        <div class="icon ok">✓</div>
        <h1>Valid pickup note</h1>
        <p>This note authorises the customer to collect the cargo.</p>
        <p><strong>{{ $note->note_no }}</strong></p>
        <p>Issued {{ optional($note->issued_at)->format('d M Y H:i') }}</p>
    @elseif ($note)
        <div class="icon no">!</div>
        <h1>Pickup note not valid</h1>
        <p>This note is <strong>{{ $note->status }}</strong>. Do not release the cargo.</p>
        <p><strong>{{ $note->note_no }}</strong></p>
    @else
        <div class="icon no">✕</div>
        <h1>Unknown pickup note</h1>
        <p>This code does not belong to any pickup note in our system.</p>
    @endif
    <div class="ref">Checked {{ now()->format('d M Y H:i') }}</div>
</div>
</body>
</html>
