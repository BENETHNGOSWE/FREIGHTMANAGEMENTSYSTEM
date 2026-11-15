<?php

namespace App\Http\Controllers\Cargo;

use App\Models\CargoStorage;
use App\Models\Customer;
use App\Models\CustomerStorageRate;
use App\Models\Loadpackage;
use App\Models\StoragePayment;
use App\Models\StorageSetting;
use App\Services\Cargo\QrCodeService;
use App\Services\Cargo\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorageController extends CargoController
{
    public function __construct(private StorageService $storage)
    {
    }

    public function index(Request $request): View
    {
        $this->allow();

        $clientId = $this->clientId();
        $search = trim((string) $request->get('search', ''));

        $summary = $this->storage->summary($clientId);

        if ($search !== '') {
            $summary['rows'] = $summary['rows']->filter(function (CargoStorage $row) use ($search) {
                $load = $row->loadpackage;

                return $load && (
                    str_contains(mb_strtolower((string) $load->load_no), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) optional($load->customer)->customer_firstname), mb_strtolower($search))
                );
            })->values();
        }

        return view('dashboards.storage.index', [
            'summary' => $summary,
            'search' => $search,
            'settings' => $this->storage->settings($clientId),
        ]);
    }

    public function show(CargoStorage $storage): View
    {
        $this->allow();
        abort_unless((int) $storage->client_id === $this->clientId(), 404);

        return view('dashboards.storage.show', [
            'storage' => $storage->load(['loadpackage.customer', 'loadpackage.unittype', 'charges', 'payments', 'waivers']),
            'balance' => $storage->balance(),
            'charged' => $storage->chargedTotal(),
            'paid' => $storage->paidTotal(),
            'waived' => $storage->waivedTotal(),
        ]);
    }

    /** Add today's (or a given day's) charges now, instead of waiting for the nightly job. */
    public function accrue(Request $request): RedirectResponse
    {
        $this->allow('update loadpackages');

        $data = $request->validate(['date' => ['nullable', 'date']]);
        $result = $this->storage->accrue($this->clientId(), $data['date'] ?? null);

        return back()->with('success', sprintf(
            'Storage updated: %d charge(s) on %d cargo, %s added.',
            $result['added'],
            $result['cargo'],
            number_format($result['amount'], 2)
        ));
    }

    public function pay(Request $request, CargoStorage $storage): RedirectResponse
    {
        $this->allow('update invoices');
        abort_unless((int) $storage->client_id === $this->clientId(), 404);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:CASH,BANK,MOBILE_MONEY,CHEQUE,OTHER'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:250'],
        ]);

        if ((float) $data['amount'] > $storage->balance() + 0.004) {
            return back()->with('error', 'The amount is more than the storage balance (' . number_format($storage->balance(), 2) . ').');
        }

        $this->storage->pay($storage, $data);

        return back()->with('success', 'Storage payment recorded.');
    }

    public function waive(Request $request, CargoStorage $storage): RedirectResponse
    {
        $this->allow('update invoices');
        abort_unless((int) $storage->client_id === $this->clientId(), 404);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:200'],
        ]);

        if ((float) $data['amount'] > $storage->balance() + 0.004) {
            return back()->with('error', 'You cannot waive more than the outstanding storage.');
        }

        $this->storage->waive($storage, (float) $data['amount'], $data['reason']);

        return back()->with('success', 'Storage waived and recorded in the ledger.');
    }

    public function payments(Request $request): View
    {
        $this->allow();

        $clientId = $this->clientId();
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $payments = StoragePayment::where('client_id', $clientId)
            ->with(['loadpackage.customer', 'storage'])
            ->whereDate('payment_date', '>=', $from)
            ->whereDate('payment_date', '<=', $to)
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        return view('dashboards.storage.payments', [
            'payments' => $payments,
            'total' => round((float) $payments->sum('amount'), 2),
            'from' => $from,
            'to' => $to,
        ]);
    }

    // ------------------------------------------------------------------ settings

    public function settings(): View
    {
        $this->allow('manage accounting settings');

        $clientId = $this->clientId();

        return view('dashboards.storage.settings', [
            'settings' => $this->storage->settings($clientId),
            'customers' => Customer::where('client_id', $clientId)->orderBy('customer_firstname')->get(),
            'overrides' => CustomerStorageRate::where('client_id', $clientId)->get()->keyBy('customer_id'),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $this->allow('manage accounting settings');

        $data = $request->validate([
            'free_days' => ['required', 'integer', 'min:0', 'max:365'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'charge_basis' => ['nullable', 'in:per_cargo,per_cbm'],
        ]);

        StorageSetting::updateOrCreate(
            ['client_id' => $this->clientId()],
            $data + [
                'auto_accrue' => $request->boolean('auto_accrue'),
                'currency' => 'TZS',
            ]
        );

        return back()->with('success', 'Storage settings saved. New cargo use these values.');
    }

    public function updateCustomerRate(Request $request): RedirectResponse
    {
        $this->allow('manage accounting settings');

        $clientId = $this->clientId();

        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'free_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:200'],
        ]);

        abort_unless(Customer::where('client_id', $clientId)->whereKey($data['customer_id'])->exists(), 404);

        $rate = CustomerStorageRate::firstOrNew([
            'client_id' => $clientId,
            'customer_id' => $data['customer_id'],
        ]);

        $rate->fill([
            'free_days' => $data['free_days'] ?? null,
            'daily_rate' => $data['daily_rate'] ?? null,
            'notes' => $data['notes'] ?? null,
        ])->save();

        return back()->with('success', 'Customer storage rate saved.');
    }

    /** Start the clock for cargo that arrived before this module existed. */
    public function startForLoads(Request $request): RedirectResponse
    {
        $this->allow('update loadpackages');

        $clientId = $this->clientId();
        $started = 0;

        $loads = Loadpackage::where('client_id', $clientId)
            ->whereNull('collected_at')
            ->whereIn('cargo_status', ['Arrived', 'Verified', 'Invoiced', 'Paid', 'Ready for Pickup'])
            ->doesntHave('storage')
            ->get();

        foreach ($loads as $load) {
            $this->storage->start($load);
            $started++;
        }

        return back()->with('success', $started . ' cargo added to storage tracking.');
    }
}
