<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\Platform\SubscriptionService;

/**
 * "How much SMS do I have left?" - the question every bulk-SMS reseller asks first.
 * Read-only: an owner, not the client, decides the allowance.
 */
class SmsBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'set_freight']);
    }

    public function index(SubscriptionService $subscriptions)
    {
        $client = auth()->user()->client;
        $subscription = $subscriptions->current($client->id ?? null);

        $quota = $subscriptions->smsQuota($client);
        $used = $subscriptions->smsUsed($client);
        $left = $subscriptions->smsLeft($client);

        return view('dashboards.sms_balance', [
            'client' => $client,
            'subscription' => $subscription,
            'quota' => $quota,
            'used' => $used,
            'left' => $left,
            'status' => $subscriptions->status($client),
            'daysLeft' => $subscriptions->daysLeft($client),
            'expires' => optional($subscription)->expire_date,
            'product' => $client->account_type ?? 'freight',
            'sentToday' => Message::where('client_id', $client->id ?? null)->whereDate('created_at', today())->count(),
            'failed' => Message::where('client_id', $client->id ?? null)->where('status', 'failed')->count(),
            'lastMessage' => Message::where('client_id', $client->id ?? null)->latest()->first(),
        ]);
    }
}
