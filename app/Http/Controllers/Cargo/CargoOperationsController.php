<?php

namespace App\Http\Controllers\Cargo;

use App\Models\CargoPhoto;
use App\Models\Loadpackage;
use App\Services\Cargo\CargoStatusService;
use App\Services\Cargo\QrCodeService;
use App\Services\Cargo\ReleaseControlService;
use App\Services\Cargo\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * The cargo hub: everything about one package in one place - the journey, the photos, the QR
 * code, the invoice, the storage balance and the actions the warehouse needs.
 */
class CargoOperationsController extends CargoController
{
    public function __construct(
        private CargoStatusService $status,
        private StorageService $storage,
        private QrCodeService $qr,
        private ReleaseControlService $release,
    ) {
    }

    public function index(Request $request): View
    {
        $this->allow();

        $clientId = $this->clientId();
        $search = trim((string) $request->get('search', ''));
        $statusFilter = $request->get('status');

        $loads = Loadpackage::where('client_id', $clientId)
            ->with(['customer', 'unittype', 'storage'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('load_no', 'like', "%{$search}%")
                        ->orWhere('load_name', 'like', "%{$search}%")
                        ->orWhere('china_location', 'like', "%{$search}%")
                        ->orWhere('batch_no', 'like', "%{$search}%");
                });
            })
            ->when($statusFilter, fn ($query) => $query->where('cargo_status', $statusFilter))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        foreach ($loads as $load) {
            $this->status->ensureToken($load);
            $load->storage_balance = $load->storage ? $load->storage->balance() : 0.0;
        }

        return view('dashboards.cargo.index', [
            'loads' => $loads,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'statuses' => Loadpackage::STATUSES,
        ]);
    }

    public function show(Loadpackage $loadpackage): View
    {
        $this->allow();
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);

        $this->status->ensureToken($loadpackage);

        $invoice = $this->release->invoiceFor($loadpackage);
        $controls = $this->release->checks($loadpackage);
        $storage = $loadpackage->storage;

        return view('dashboards.cargo.show', [
            'load' => $loadpackage->load(['customer', 'unittype', 'loadpackagetype', 'photos', 'statusHistories.user', 'containers']),
            'timeline' => $this->status->timeline($loadpackage),
            'qr' => $this->qr->dataUri($this->qr->trackingUrl($loadpackage->qr_token)),
            'trackingUrl' => $this->qr->trackingUrl($loadpackage->qr_token),
            'invoice' => $invoice,
            'storage' => $storage,
            'storageBalance' => $storage ? $storage->balance() : 0.0,
            'controls' => $controls,
            'statuses' => Loadpackage::STATUSES,
            'note' => $controls['note'],
        ]);
    }

    /** Registration details recorded in China: location, batch, photos. */
    public function updateDetails(Request $request, Loadpackage $loadpackage): RedirectResponse
    {
        $this->allow('update loadpackages');
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);

        $data = $request->validate([
            'china_location' => ['nullable', 'string', 'max:120'],
            'batch_no' => ['nullable', 'string', 'max:60'],
            'load_description' => ['nullable', 'string', 'max:255'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'max:5120'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:120'],
        ]);

        $loadpackage->update([
            'china_location' => $data['china_location'] ?? null,
            'batch_no' => $data['batch_no'] ?? null,
            'load_description' => $data['load_description'] ?? $loadpackage->load_description,
        ]);

        foreach ($request->file('photos', []) as $index => $file) {
            $path = $file->store('cargo/' . $loadpackage->client_id . '/' . $loadpackage->id, 'public');

            CargoPhoto::create([
                'client_id' => $loadpackage->client_id,
                'loadpackage_id' => $loadpackage->id,
                'path' => $path,
                'caption' => $data['captions'][$index] ?? null,
                'uploaded_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Cargo details saved.');
    }

    public function deletePhoto(Loadpackage $loadpackage, CargoPhoto $photo): RedirectResponse
    {
        $this->allow('update loadpackages');
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);
        abort_unless((int) $photo->loadpackage_id === (int) $loadpackage->id, 404);

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Photo removed.');
    }

    /** Move the cargo along the journey by hand (China -> In transit -> Arrived ...). */
    public function changeStatus(Request $request, Loadpackage $loadpackage): RedirectResponse
    {
        $this->allow('update loadpackages');
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);

        $data = $request->validate([
            'cargo_status' => ['required', 'in:' . implode(',', array_keys(Loadpackage::STATUSES))],
            'note' => ['nullable', 'string', 'max:250'],
        ]);

        // Only the physical journey is set by hand. Everything after arrival has a screen that
        // does its own checks, and moving backwards would contradict what already happened.
        $manual = ['Received', 'In Transit', 'Arrived'];

        if (! in_array($data['cargo_status'], $manual, true)) {
            $screen = match ($data['cargo_status']) {
                'Verified' => 'Warehouse Verification',
                'Invoiced', 'Paid' => 'the invoice (a payment)',
                default => 'Release Control',
            };

            return back()->with(
                'error',
                'Status "' . $data['cargo_status'] . '" is set from ' . $screen . ', not by hand.'
            );
        }

        $order = array_keys(Loadpackage::STATUSES);
        $current = array_search($loadpackage->cargo_status, $order, true);
        $target = array_search($data['cargo_status'], $order, true);

        if ($current !== false && $target !== false && $target < $current) {
            return back()->with(
                'error',
                'Cargo ' . $loadpackage->load_no . ' is already at "' . $loadpackage->cargo_status . '" and cannot move back to "' . $data['cargo_status'] . '".'
            );
        }

        $loadpackage->forceFill([
            'cargo_status' => $data['cargo_status'],
            'arrived_at' => $data['cargo_status'] === 'Arrived' ? ($loadpackage->arrived_at ?? now()->toDateString()) : $loadpackage->arrived_at,
        ])->save();

        $this->status->set($loadpackage->fresh(), $data['cargo_status'], $data['note'] ?? null);

        // Arriving in Dar starts the storage clock (the free period begins here).
        if ($data['cargo_status'] === 'Arrived') {
            $this->storage->start($loadpackage->fresh());
        }

        return back()->with('success', 'Cargo moved to: ' . $data['cargo_status']);
    }
}
