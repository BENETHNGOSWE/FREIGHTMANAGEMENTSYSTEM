<?php

namespace App\Services\Platform;

use App\Models\Client;
use App\Models\Kodapackage;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionToken;
use Illuminate\Support\Facades\DB;

/**
 * One place that answers "may this client use the system, and how much SMS is left".
 *
 * Rules of the house:
 *  - a client must have a subscription whose expire_date is today or later,
 *  - a grace period (system setting, default 3 days) keeps them working just after expiry,
 *  - the owner can lock a client by hand at any time (clients.subscription_status = locked),
 *  - SMS is capped by subscriptions.sms_quota, counted from the messages table.
 */
class SubscriptionService
{
    public const STATUSES = [
        'active' => 'Active',
        'expired' => 'Expired',
        'grace' => 'Grace period',
        'locked' => 'Locked by owner',
        'none' => 'No subscription',
    ];

    /** The subscription that rules today (latest not-expired first, then the latest overall). */
    public function current(?int $clientId): ?Subscription
    {
        if (! $clientId) {
            return null;
        }

        return Subscription::where('client_id', $clientId)
            ->orderByRaw('case when expire_date >= ? then 0 else 1 end', [now()->toDateString()])
            ->orderByDesc('expire_date')
            ->orderByDesc('id')
            ->first();
    }

    public function graceDays(): int
    {
        $value = \App\Models\PlatformSetting::get('subscription_grace_days');

        return $value === null ? 3 : (int) $value;
    }

    public function setGraceDays(int $days): void
    {
        \App\Models\PlatformSetting::set('subscription_grace_days', (string) max($days, 0));
    }

    /** active | expired | grace | locked | none */
    public function status(?Client $client): string
    {
        if (! $client) {
            return 'none';
        }

        if ($client->subscription_status === 'locked') {
            return 'locked';
        }

        $subscription = $this->current($client->id);

        if (! $subscription) {
            return 'none';
        }

        $expiry = $subscription->expire_date ? $subscription->expire_date->copy()->endOfDay() : null;

        if ($expiry && $expiry->isPast()) {
            $graceEnd = $expiry->copy()->addDays($this->graceDays());

            return now()->lessThanOrEqualTo($graceEnd) ? 'grace' : 'expired';
        }

        return 'active';
    }

    public function isUsable(?Client $client): bool
    {
        $status = $this->status($client);

        if (in_array($status, ['active', 'grace'], true)) {
            return true;
        }

        // Clients that never had a subscription are grandfathered: the upgrade must not lock
        // an existing business out. The owner can switch enforcement on in Platform settings
        // once every client has a subscription.
        if ($status === 'none' && ! $this->enforceForEveryone()) {
            return true;
        }

        return false;
    }

    /** Should a client with no subscription at all be locked too? (off by default) */
    public function enforceForEveryone(): bool
    {
        return (string) \App\Models\PlatformSetting::get('subscription_enforce_all', '0') === '1';
    }

    public function setEnforceForEveryone(bool $on): void
    {
        \App\Models\PlatformSetting::set('subscription_enforce_all', $on ? '1' : '0');
    }

    public function daysLeft(?Client $client): ?int
    {
        $subscription = $this->current($client?->id);

        if (! $subscription || ! $subscription->expire_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($subscription->expire_date->startOfDay(), false);
    }

    // ------------------------------------------------------------------ SMS allowance

    public function smsQuota(?Client $client): int
    {
        $subscription = $this->current($client?->id);

        return $subscription ? (int) $subscription->sms_quota : 0;
    }

    public function smsUsed(?Client $client): int
    {
        if (! $client) {
            return 0;
        }

        // Counted live from the messages table so it can never drift.
        return (int) Message::where('client_id', $client->id)->count();
    }

    public function smsLeft(?Client $client): int
    {
        return max($this->smsQuota($client) - $this->smsUsed($client), 0);
    }

    public function canSendSms(?Client $client, int $howMany = 1): bool
    {
        if (! $this->isUsable($client) || $this->smsQuota($client) <= 0) {
            return false;
        }

        return $this->smsLeft($client) >= $howMany;
    }

    // ------------------------------------------------------------------ pricing

    /** Months → money, using the package fees (yearly fee for whole years). */
    public function priceFor(?int $packageId, int $months): float
    {
        $package = $packageId ? Kodapackage::find($packageId) : null;

        if (! $package) {
            return 0.0;
        }

        $yearly = intdiv($months, 12);
        $monthly = $months % 12;
        $total = $yearly * (float) $package->yearly_fee + $monthly * (float) $package->monthly_fee;

        // A full year should never cost more than 12 months of the monthly fee.
        if ($yearly === 0 && $monthly === 12) {
            return (float) $package->monthly_fee * 12;
        }

        return round($total, 2);
    }

    // ------------------------------------------------------------------ locking

    public function lock(Client $client, string $reason = 'Locked by owner'): void
    {
        $client->update(['subscription_status' => 'locked']);
        $this->current($client->id)?->update(['locked_at' => now()]);

        $this->log($client, 'lock', $reason);
    }

    public function unlock(Client $client): void
    {
        $client->update(['subscription_status' => 'active']);
        $this->current($client->id)?->update(['locked_at' => null]);

        $this->log($client, 'unlock', 'Unlocked by owner');
    }

    /** Renew / extend: moves the expiry date without losing the history. */
    public function renew(Subscription $subscription, int $months, ?float $amount = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $months, $amount) {
            $from = $subscription->expire_date && $subscription->expire_date->isFuture()
                ? $subscription->expire_date->copy()
                : now()->startOfDay();

            $subscription->update([
                'duration_in_month' => $subscription->duration_in_month + $months,
                'expire_date' => $from->copy()->addMonths($months),
                'amount' => $amount ?? ($subscription->amount + $this->priceFor($subscription->package_id, $months)),
            ]);

            return $subscription->fresh();
        });
    }

    // ------------------------------------------------------------------ tokens

    /** The token the owner sends to unlock an expired install. One active token per subscription. */
    public function issueToken(Subscription $subscription, ?string $expiry = null): SubscriptionToken
    {
        SubscriptionToken::where('subscription_id', $subscription->id)
            ->where('status', 1)
            ->update(['status' => 0]);

        $invoice = SubscriptionInvoice::where('subscription_id', $subscription->id)->latest('id')->first()
            ?? SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'client_id' => $subscription->client_id,
                'amount' => (float) $subscription->amount,
                'balance' => 0,
                'bill_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'status' => '0',
            ]);

        return SubscriptionToken::create([
            'subscription_id' => $subscription->id,
            'subscription_invoice_id' => $invoice?->id,
            'token_' => strtoupper(generateSubscriptionToken(20)),
            'expire_date' => $expiry ?? $subscription->expire_date ?? now()->addYear()->toDateString(),
            'status' => 1,
            'client_id' => $subscription->client_id,
        ]);
    }

    /**
     * The client types the token into the locked screen. A valid token unlocks the client
     * and extends the subscription up to the token's expiry date.
     */
    public function redeem(string $token, ?int $clientId = null): array
    {
        $clean = strtoupper(preg_replace('/\s+/', '', $token));

        $row = SubscriptionToken::whereRaw('upper(token_) = ?', [$clean])->first();

        if (! $row) {
            return ['ok' => false, 'message' => 'That token is not recognised.'];
        }

        if (! $row->status) {
            if ($row->used_at) {
                return ['ok' => false, 'message' => 'That token has already been used on ' . $row->used_at->format('d M Y') . '.'];
            }

            return ['ok' => false, 'message' => 'That token is no longer active. Ask for a new one.'];
        }

        if ($row->expire_date && $row->expire_date->isPast()) {
            return ['ok' => false, 'message' => 'That token expired on ' . $row->expire_date->format('d M Y') . '.'];
        }

        if ($clientId && (int) $row->client_id !== (int) $clientId) {
            return ['ok' => false, 'message' => 'That token belongs to another account.'];
        }

        $subscription = $row->subscription;

        if (! $subscription) {
            return ['ok' => false, 'message' => 'The subscription behind this token no longer exists.'];
        }

        DB::transaction(function () use ($row, $subscription) {
            $row->update(['status' => 0, 'used_at' => now(), 'used_by' => auth()->id()]);

            $newExpiry = $row->expire_date;
            if ($newExpiry && (! $subscription->expire_date || $newExpiry->greaterThan($subscription->expire_date))) {
                $subscription->update([
                    'expire_date' => $newExpiry,
                    'duration_in_month' => max((int) $subscription->duration_in_month, (int) now()->startOfDay()->diffInMonths($newExpiry->copy())),
                ]);
            }

            $subscription->update(['locked_at' => null]);
            $subscription->client?->update(['subscription_status' => 'active']);

            $this->log($subscription->client, 'token', 'Unlocked with token ' . $row->token_);
        });

        $row->refresh();

        return [
            'ok' => true,
            'message' => 'Token accepted — the system is unlocked until ' . ($row->expire_date?->format('d M Y') ?? 'the new expiry date') . '.',
            'subscription' => $subscription->fresh(),
        ];
    }

    /** Small audit trail in the settings table, per client. */
    private function log(?Client $client, string $action, string $note): void
    {
        if (! $client) {
            return;
        }

        DB::table('system_settings')->insert([
            'client_id' => $client->id,
            'key' => 'subscription_' . $action . '_' . now()->format('YmdHis'),
            'value' => $note . ' by ' . (auth()->user()->name ?? 'system'),
            'updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
