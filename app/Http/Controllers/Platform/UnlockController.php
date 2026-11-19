<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\Platform\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The screen a client sees when the subscription has ended (or the owner locked them).
 * They log in normally, then paste the token the provider sent them.
 */
class UnlockController extends Controller
{
    public function __construct(private SubscriptionService $subscriptions)
    {
        $this->middleware('auth');
    }

    public function show(): View
    {
        $client = auth()->user()->client;
        $current = $this->subscriptions->current(auth()->user()->client_id);

        return view('platform.unlock', [
            'status' => $this->subscriptions->status($client),
            'client' => $client,
            'subscription' => $current,
            'daysLeft' => $this->subscriptions->daysLeft($client),
            'graceDays' => $this->subscriptions->graceDays(),
            'smsLeft' => $this->subscriptions->smsLeft($client),
            'smsQuota' => $this->subscriptions->smsQuota($client),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'min:6', 'max:64'],
        ]);

        $result = $this->subscriptions->redeem($data['token'], auth()->user()->client_id);

        if (! $result['ok']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()->route('home')->with('success', $result['message']);
    }
}
