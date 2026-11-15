<?php

namespace App\Http\Controllers\Cargo;

use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\PickupNote;
use App\Services\Cargo\CargoStatusService;
use App\Services\Cargo\QrCodeService;
use App\Services\Cargo\ReleaseControlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Warehouse release desk: every cargo still in the building with its checklist
 * (invoice paid, storage cleared, pickup note valid, verified, physically here).
 */
class ReleaseControlController extends CargoController
{
    public function __construct(
        private ReleaseControlService $release,
        private CargoStatusService $status,
        private QrCodeService $qr,
    ) {
    }

    public function index(Request $request): View
    {
        $this->allow();

        $loads = $this->release->pending($this->clientId());
        $filter = $request->get('filter');

        if ($filter === 'ready') {
            $loads = $loads->where('can_release', true)->values();
        } elseif ($filter === 'blocked') {
            $loads = $loads->where('can_release', false)->values();
        } elseif ($filter === 'storage') {
            $loads = $loads->where('storage_balance', '>', 0)->values();
        }

        return view('dashboards.cargo.release', [
            'loads' => $loads,
            'filter' => $filter,
            'notes' => PickupNote::where('client_id', $this->clientId())->latest('id')->limit(10)->with('invoice')->get(),
        ]);
    }

    public function release(Loadpackage $loadpackage): RedirectResponse
    {
        $this->allow('print release order');
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);

        $result = $this->release->release($loadpackage);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function collect(Loadpackage $loadpackage): RedirectResponse
    {
        $this->allow('print release order');
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);

        $result = $this->release->collect($loadpackage);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    /** Issue (or show) the pickup note QR for an invoice. */
    public function pickupNote(Invoice $invoice): View
    {
        $this->allow();
        abort_unless((int) $invoice->client_id === $this->clientId(), 404);

        $note = $this->status->pickupNoteFor($invoice);

        return view('dashboards.cargo.pickup_note', [
            'invoice' => $invoice->load('customer'),
            'note' => $note,
            'qr' => $this->qr->dataUri(url('/pickup/' . $note->qr_token)),
            'url' => url('/pickup/' . $note->qr_token),
            'cargo' => $invoice->loadpackages()->get(),
        ]);
    }

    public function cancelNote(PickupNote $note): RedirectResponse
    {
        $this->allow('print release order');
        abort_unless((int) $note->client_id === $this->clientId(), 404);

        $note->forceFill(['status' => 'cancelled'])->save();

        return back()->with('success', 'Pickup note cancelled.');
    }
}
