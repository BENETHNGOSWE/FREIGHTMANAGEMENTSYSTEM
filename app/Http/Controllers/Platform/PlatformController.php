<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Kodapackage;
use App\Models\Subscription;
use App\Models\SubscriptionToken;
use App\Services\Platform\ModuleAccess;
use App\Services\Platform\ProductCatalog;
use App\Services\Platform\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * The owner's platform desk: every client with where their subscription stands,
 * the modules each package unlocks, the unlock tokens and the settings.
 */
class PlatformController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptions,
        private ModuleAccess $access,
    ) {
        $this->middleware(['auth', 'owner']);
    }

    public function clients(): View
    {
        $clients = Client::with(['admin', 'clientTypeRelation'])
            ->orderBy('client_name')
            ->get();

        $rows = $clients->map(function (Client $client) {
            $subscription = $this->subscriptions->current($client->id);

            return (object) [
                'client' => $client,
                'subscription' => $subscription,
                'status' => $this->subscriptions->status($client),
                'days_left' => $this->subscriptions->daysLeft($client),
                'sms_left' => $this->subscriptions->smsLeft($client),
                'sms_quota' => $this->subscriptions->smsQuota($client),
                'modules' => $this->access->modulesForClient($client->id),
                'product' => $subscription->product ?? $client->account_type,
            ];
        });

        return view('platform.clients', [
            'rows' => $rows,
            'products' => ProductCatalog::PRODUCTS,
            'statusLabels' => SubscriptionService::STATUSES,
            'summary' => [
                'total' => $rows->count(),
                'active' => $rows->where('status', 'active')->count(),
                'grace' => $rows->where('status', 'grace')->count(),
                'expired' => $rows->whereIn('status', ['expired', 'none'])->count(),
                'locked' => $rows->where('status', 'locked')->count(),
                'sms' => $rows->whereIn('product', ['sms', 'both'])->count(),
            ],
        ]);
    }

    public function subscriptions(Request $request): View
    {
        $status = $request->get('status');

        $subscriptions = Subscription::with(['client', 'kodapackages'])
            ->when($status, function ($query) use ($status) {
                if ($status === 'expired') {
                    $query->whereDate('expire_date', '<', now()->toDateString());
                } else {
                    $query->where('status', $status);
                }
            })
            ->orderByDesc('id')
            ->get();

        return view('platform.subscriptions', [
            'subscriptions' => $subscriptions,
            'status' => $status,
            'products' => ProductCatalog::PRODUCTS,
            'statusLabels' => SubscriptionService::STATUSES,
        ]);
    }

    /** Create the subscription (this is what "assign package" really means). */
    public function storeSubscription(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'package_id' => ['required', 'exists:kodapackages,id'],
            'product' => ['required', 'in:' . implode(',', array_keys(ProductCatalog::PRODUCTS))],
            'start_date' => ['required', 'date'],
            'duration_in_month' => ['required', 'integer', 'min:1', 'max:120'],
            'sms_quota' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $months = (int) $data['duration_in_month'];
        $amount = $data['amount'] ?? $this->subscriptions->priceFor((int) $data['package_id'], $months);

        DB::transaction(function () use ($data, $months, $amount) {
            $subscription = Subscription::create([
                'client_id' => $data['client_id'],
                'package_id' => $data['package_id'],
                'product' => $data['product'],
                'start_date' => $data['start_date'],
                'expire_date' => \Illuminate\Support\Carbon::parse($data['start_date'])->addMonths($months),
                'duration_in_month' => $months,
                'status' => '0',
                'sms_quota' => $data['sms_quota'] ?? 0,
                'amount' => $amount,
                'notes' => $data['notes'] ?? null,
            ]);

            \App\Models\SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'client_id' => $subscription->client_id,
                'amount' => $amount,
                'balance' => $amount,
                'bill_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => '0',
            ]);

            Client::whereKey($subscription->client_id)->update([
                'subscription_status' => 'active',
                'account_type' => $data['product'] === 'both' ? 'freight' : $data['product'],
            ]);
        });

        return redirect()->route('platform.clients')
            ->with('success', 'Subscription created. The client now has access to the package modules.');
    }

    public function renew(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'months' => ['required', 'integer', 'min:1', 'max:120'],
            'sms_quota' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->subscriptions->renew($subscription, (int) $data['months']);

        if ($request->filled('sms_quota')) {
            $subscription->update(['sms_quota' => (int) $data['sms_quota']]);
        }

        $subscription->client?->update(['subscription_status' => 'active']);

        return back()->with('success', 'Subscription extended to ' . $subscription->fresh()->expire_date->format('d M Y') . '.');
    }

    public function lock(Client $client): RedirectResponse
    {
        $this->subscriptions->lock($client);

        return back()->with('success', $client->client_name . ' is locked. They can only reach the unlock screen.');
    }

    public function unlock(Client $client): RedirectResponse
    {
        $this->subscriptions->unlock($client);

        return back()->with('success', $client->client_name . ' is unlocked.');
    }

    // ------------------------------------------------------------------ tokens

    public function tokens(): View
    {
        return view('platform.tokens', [
            'tokens' => SubscriptionToken::with(['subscription', 'client'])->orderByDesc('id')->limit(100)->get(),
            'subscriptions' => Subscription::with('client')->orderByDesc('id')->get(),
        ]);
    }

    public function issueToken(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'expire_date' => ['nullable', 'date', 'after:today'],
        ]);

        $subscription = Subscription::findOrFail($data['subscription_id']);
        $token = $this->subscriptions->issueToken($subscription, $data['expire_date'] ?? null);

        return back()->with('success', 'Token ' . $token->token_ . ' created for ' . ($subscription->client->client_name ?? 'client') . ', valid until ' . $token->expire_date->format('d M Y') . '.');
    }

    public function revokeToken(SubscriptionToken $token): RedirectResponse
    {
        $token->update(['status' => 0]);

        return back()->with('success', 'Token revoked.');
    }

    // ------------------------------------------------------------------ modules / packages

    public function modules(): View
    {
        return view('platform.modules', [
            'modules' => ProductCatalog::MODULES,
            'packages' => Kodapackage::orderBy('name')->get(),
            'assigned' => Kodapackage::all()->mapWithKeys(fn ($package) => [$package->id => $this->access->packageSummary($package->id)]),
            'products' => ProductCatalog::PRODUCTS,
        ]);
    }

    public function syncModules(Request $request, Kodapackage $package): RedirectResponse
    {
        $data = $request->validate([
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string'],
        ]);

        $this->access->syncPackage($package->id, $data['modules'] ?? []);

        return back()->with('success', 'Modules for "' . $package->name . '" updated. Clients on this package see the change on their next page.');
    }

    public function settings(): View
    {
        return view('platform.settings', [
            'graceDays' => $this->subscriptions->graceDays(),
            'enforceForEveryone' => $this->subscriptions->enforceForEveryone(),
            'counts' => [
                'clients' => Client::count(),
                'freight' => Client::where('account_type', 'freight')->count(),
                'sms' => Client::where('account_type', 'sms')->count(),
                'subscriptions' => Subscription::count(),
                'expired' => Subscription::whereDate('expire_date', '<', now()->toDateString())->count(),
            ],
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'grace_days' => ['required', 'integer', 'min:0', 'max:60'],
            'enforce_all' => ['nullable', 'boolean'],
        ]);

        $this->subscriptions->setGraceDays((int) $data['grace_days']);
        $this->subscriptions->setEnforceForEveryone($request->boolean('enforce_all'));

        return back()->with('success', 'Platform settings saved.');
    }
}
