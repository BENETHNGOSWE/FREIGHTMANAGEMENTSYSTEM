<?php

namespace App\Http\Controllers\Cargo;

use App\Models\Loadpackage;
use App\Models\PickupNote;
use App\Services\Cargo\CargoStatusService;
use App\Services\Cargo\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public tracking - no login. The customer types a tracking number (or scans the cargo QR)
 * and sees the journey of their own cargo, nothing else: no costs, no internal notes.
 */
class TrackingController
{
    public function __construct(private CargoStatusService $status)
    {
    }

    public function form(Request $request): View
    {
        $load = null;
        $tracking = trim((string) $request->get('tracking', ''));
        $error = null;

        if ($tracking !== '') {
            $load = Loadpackage::where('load_no', $tracking)->first();

            if (! $load) {
                $error = 'No cargo found with tracking number ' . $tracking . '.';
            }
        }

        return view('public.track', [
            'tracking' => $tracking,
            'load' => $load,
            'error' => $error,
            'timeline' => $load ? $this->status->timeline($load) : [],
        ]);
    }

    public function show(string $token): View
    {
        $load = Loadpackage::where('qr_token', $token)->firstOrFail();

        return view('public.track', [
            'tracking' => $load->load_no,
            'load' => $load,
            'error' => null,
            'timeline' => $this->status->timeline($load),
        ]);
    }

    /** What the warehouse sees when it scans a pickup note. No amounts, only validity. */
    public function pickup(string $token): View
    {
        $note = PickupNote::where('qr_token', $token)->first();

        return view('public.pickup', [
            'note' => $note,
            'valid' => $note && $note->isValid(),
        ]);
    }
}
