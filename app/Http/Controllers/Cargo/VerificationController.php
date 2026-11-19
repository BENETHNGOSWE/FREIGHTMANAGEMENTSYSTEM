<?php

namespace App\Http\Controllers\Cargo;

use App\Models\Loadpackage;
use App\Services\Cargo\StorageService;
use App\Services\Cargo\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Dar warehouse: confirm what physically arrived, correct the weight, flag problems.
 * The invoice re-prices itself when the weight changes.
 */
class VerificationController extends CargoController
{
    public function __construct(
        private VerificationService $verification,
        private StorageService $storage,
    ) {
    }

    public function index(): View
    {
        $this->allow();

        return view('dashboards.cargo.verification', [
            'loads' => Loadpackage::where('client_id', $this->clientId())
                ->whereNull('verified_at')
                ->whereIn('cargo_status', ['Received', 'In Transit', 'Arrived'])
                ->with(['customer', 'unittype'])
                ->orderBy('created_at')
                ->get(),
            'recent' => Loadpackage::where('client_id', $this->clientId())
                ->whereNotNull('verified_at')
                ->with(['customer', 'unittype'])
                ->orderByDesc('verified_at')
                ->limit(15)
                ->get(),
        ]);
    }

    public function store(Request $request, Loadpackage $loadpackage): RedirectResponse
    {
        $this->allow('update loadpackages');
        abort_unless((int) $loadpackage->client_id === $this->clientId(), 404);

        $data = $request->validate([
            'weight' => ['required', 'numeric', 'min:0'],
            'has_damage' => ['nullable', 'boolean'],
            'has_missing' => ['nullable', 'boolean'],
            'has_wrong_item' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data['has_damage'] = $request->boolean('has_damage');
        $data['has_missing'] = $request->boolean('has_missing');
        $data['has_wrong_item'] = $request->boolean('has_wrong_item');

        $result = $this->verification->verify($loadpackage, $data);

        // Verification is when the storage clock officially starts.
        $this->storage->start($loadpackage->fresh());

        $message = sprintf(
            'Cargo %s verified: weight %s → %s.',
            $loadpackage->load_no,
            $result['old_weight'],
            $result['new_weight']
        );

        if (abs($result['impact']) > 0.004) {
            $message .= ' Price affected by ' . number_format($result['impact'], 2) . '.';
        }

        return redirect()->route('cargo.show', $loadpackage)->with('success', $message);
    }
}
