<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Clientactivename;
use App\Models\ClientType;
use App\Models\Customer;
use App\Models\Kodapackage;
use App\Models\Message;
use App\Models\Sendername;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Services\Platform\SubscriptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Demo data for the platform flow, so the owner screens have something to show:
 * one freight client, one bulk-SMS client (a reseller) and one expired client
 * waiting for a token.
 *
 * Run: php artisan db:seed --class=PlatformDemoSeeder
 * Logins (all password `secret123` unless noted):
 *   owner@fms.com / 12345678   platform owner
 *   freight@demo.test          KodaFreight client
 *   sms@demo.test              KodaSMS client
 *   expired@demo.test          expired client - lands on the unlock screen
 */
class PlatformDemoSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = app(SubscriptionService::class);
        $type = ClientType::firstOrCreate(['name' => 'Freight company']);

        $owner = User::firstOrCreate(['email' => 'owner@fms.com'], [
            'name' => 'Platform Owner', 'password' => Hash::make('12345678'), 'client_id' => null,
        ]);
        $owner->assignRole(Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web'], ['is_super_admin' => true]));

        $freightPackage = Kodapackage::where('name', 'KodaFreight')->first();
        $smsPackage = Kodapackage::where('name', 'KodaSMS')->first();

        // ---------------------------------------------------------- freight client
        $freight = $this->client('freight@demo.test', 'Kilimanjaro Freight Ltd', 'Dar es Salaam', 'freight', $type->id);
        $this->user($freight, 'Freight Admin', 'admin');
        $this->subscription($freight, $freightPackage, 'freight', 10, 0, 500000, $subscriptions);

        // ------------------------------------------------- SMS reseller client
        $sms = $this->client('sms@demo.test', 'Pamoja Marketing (SMS)', 'Arusha', 'sms', $type->id);
        $smsUser = $this->user($sms, 'SMS Admin', 'admin', 'kodasmspackage');
        $this->subscription($sms, $smsPackage, 'sms', 10, 2000, 300000, $subscriptions);

        foreach ([['Neema', '0713000201'], ['Baraka', '0713000202'], ['Zawadi', '0713000203']] as [$name, $phone]) {
            Customer::firstOrCreate(['phone' => $phone, 'client_id' => $sms->id], [
                'customer_firstname' => $name, 'user_id' => $smsUser->id, 'client_id' => $sms->id,
            ]);
        }

        // a real used balance, so "SMS left" is not just the full quota
        for ($i = 1; $i <= 120; $i++) {
            Message::firstOrCreate(
                ['customer_number' => '0713000' . str_pad((string) $i, 4, '0', STR_PAD_LEFT), 'client_id' => $sms->id],
                ['content' => 'Promo sms ' . $i, 'status' => $i % 9 === 0 ? 'failed' : 'delivered', 'client_id' => $sms->id]
            );
        }

        $sender = Sendername::firstOrCreate(['name' => 'PAMOJA']);
        Clientactivename::firstOrCreate(['client_id' => $sms->id, 'sendername_id' => $sender->id]);

        // ------------------------------------------------- expired, needs a token
        $expired = $this->client('expired@demo.test', 'Coast Cargo (expired)', 'Mtwara', 'freight', $type->id);
        $expired->update(['subscription_status' => 'locked']);
        $this->user($expired, 'Coast Admin', 'admin');

        if (! Subscription::where('client_id', $expired->id)->exists()) {
            $subscription = Subscription::create([
                'client_id' => $expired->id,
                'package_id' => $freightPackage->id,
                'product' => 'freight',
                'start_date' => '2025-01-15',
                'expire_date' => '2026-09-30',
                'duration_in_month' => 12,
                'status' => '0',
                'sms_quota' => 0,
                'amount' => 500000,
            ]);

            SubscriptionInvoice::create([
                'subscription_id' => $subscription->id, 'client_id' => $expired->id,
                'amount' => 500000, 'balance' => 500000,
                'bill_date' => '2026-09-01', 'due_date' => '2026-09-30', 'status' => '0',
            ]);

            $token = $subscriptions->issueToken($subscription, '2027-09-30');
            $this->command?->info('Unlock token for the expired demo client: ' . $token->token_);
        }

        $this->command?->info('Demo logins: owner@fms.com/12345678, freight@demo.test, sms@demo.test, expired@demo.test (secret123).');
    }

    private function client(string $email, string $name, string $city, string $product, int $typeId): Client
    {
        return Client::firstOrCreate(['client_email' => $email], [
            'client_name' => $name, 'client_location' => $city, 'client_city' => $city,
            'client_country' => 'TZ', 'client_type_id' => $typeId, 'account_type' => $product,
        ]);
    }

    private function user(Client $client, string $name, string ...$roles): User
    {
        $user = User::firstOrCreate(['email' => $client->client_email], [
            'name' => $name, 'password' => Hash::make('secret123'),
            'client_id' => $client->id, 'user_type' => 'user',
        ]);

        foreach ($roles as $role) {
            if (Role::where('name', $role)->exists() && ! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        return $user;
    }

    private function subscription(Client $client, ?Kodapackage $package, string $product, int $months, int $quota, float $amount, SubscriptionService $service): void
    {
        if (! $package || Subscription::where('client_id', $client->id)->exists()) {
            return;
        }

        $subscription = Subscription::create([
            'client_id' => $client->id, 'package_id' => $package->id, 'product' => $product,
            'start_date' => now()->subMonths(2)->toDateString(),
            'expire_date' => now()->addMonths($months)->toDateString(),
            'duration_in_month' => $months, 'status' => '0', 'sms_quota' => $quota, 'amount' => $amount,
        ]);

        SubscriptionInvoice::create([
            'subscription_id' => $subscription->id, 'client_id' => $client->id,
            'amount' => $amount, 'balance' => 0,
            'bill_date' => now()->toDateString(), 'due_date' => now()->addDays(7)->toDateString(), 'status' => '1',
        ]);
    }
}
