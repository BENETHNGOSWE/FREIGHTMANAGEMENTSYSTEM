<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\Customer;
use App\Models\Kodapackage;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Platform\ModuleAccess;
use App\Services\Platform\ProductCatalog;
use App\Services\Platform\SubscriptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The owner flow: create client → assign package/product → subscribe → token → SMS allowance
 * → the client only sees what they paid for.
 */
class PlatformSubscriptionTest extends TestCase
{
    use DatabaseTransactions;

    private Client $client;
    private User $clientUser;
    private User $owner;
    private Kodapackage $freightPackage;
    private Kodapackage $smsPackage;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('package_modules')) {
            $this->markTestSkipped('Run `php artisan migrate --seed --seeder=PlatformSeeder` first.');
        }

        $type = ClientType::firstOrCreate(['name' => 'Freight company']);

        $this->client = Client::create([
            'client_name' => 'SMS Buyer ' . uniqid(),
            'client_location' => 'Dar', 'client_city' => 'Dar', 'client_country' => 'TZ',
            'client_email' => 'buyer-' . uniqid() . '@example.test',
            'client_type_id' => $type->id,
            'account_type' => 'sms',
        ]);

        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web'], ['is_super_admin' => true]);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->owner = User::create([
            'name' => 'Platform Owner', 'email' => 'owner-' . uniqid() . '@example.test',
            'password' => bcrypt('secret'), 'client_id' => null,
        ]);
        $this->owner->assignRole('owner');

        $this->clientUser = User::create([
            'name' => 'SMS Buyer Admin', 'email' => 'admin-' . uniqid() . '@example.test',
            'password' => bcrypt('secret'), 'client_id' => $this->client->id,
        ]);
        $this->clientUser->assignRole('admin');

        $this->freightPackage = Kodapackage::firstOrCreate(['name' => 'KodaFreight'], ['monthly_fee' => 50000, 'yearly_fee' => 500000]);
        $this->smsPackage = Kodapackage::firstOrCreate(['name' => 'KodaSMS'], ['monthly_fee' => 30000, 'yearly_fee' => 300000]);

        app(ModuleAccess::class)->syncPackage($this->smsPackage->id, ProductCatalog::modulesFor('sms'));
        app(ModuleAccess::class)->syncPackage($this->freightPackage->id, ProductCatalog::modulesFor('freight'));
    }

    private function subscribe(Client $client, Kodapackage $package, string $product, array $overrides = []): Subscription
    {
        return Subscription::create(array_merge([
            'client_id' => $client->id,
            'package_id' => $package->id,
            'product' => $product,
            'start_date' => now()->subMonth()->toDateString(),
            'expire_date' => now()->addMonths(11)->toDateString(),
            'duration_in_month' => 12,
            'status' => '0',
            'sms_quota' => 100,
            'amount' => 300000,
        ], $overrides));
    }

    // ---------------------------------------------------------------- status & lock

    public function test_a_client_without_subscription_keeps_working_until_enforced(): void
    {
        $service = app(SubscriptionService::class);

        $this->assertSame('none', $service->status($this->client));
        $this->assertTrue($service->isUsable($this->client), 'grandfathered on purpose');

        $service->setEnforceForEveryone(true);
        $this->assertFalse($service->isUsable($this->client->fresh()));
    }

    public function test_an_expired_subscription_gets_a_grace_period_then_locks(): void
    {
        $service = app(SubscriptionService::class);
        $service->setGraceDays(3);

        $this->subscribe($this->client, $this->smsPackage, 'sms', ['expire_date' => now()->subDay()->toDateString()]);

        $this->assertSame('grace', $service->status($this->client->fresh()));
        $this->assertTrue($service->isUsable($this->client->fresh()));

        Subscription::where('client_id', $this->client->id)->update(['expire_date' => now()->subDays(10)->toDateString()]);

        $this->assertSame('expired', $service->status($this->client->fresh()));
        $this->assertFalse($service->isUsable($this->client->fresh()));
    }

    public function test_an_expired_client_is_redirected_to_the_unlock_screen(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms', ['expire_date' => now()->subDays(30)->toDateString()]);
        app(SubscriptionService::class)->setGraceDays(0);

        $this->actingAs($this->clientUser)
            ->get('/dashboard')
            ->assertRedirect(route('subscription.unlock'));
    }

    public function test_the_owner_can_lock_and_unlock_a_client(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms');

        $this->actingAs($this->owner)
            ->post(route('platform.clients.lock', $this->client))
            ->assertRedirect();

        $this->assertSame('locked', app(SubscriptionService::class)->status($this->client->fresh()));

        $this->actingAs($this->clientUser)->get('/dashboard')->assertRedirect(route('subscription.unlock'));

        $this->actingAs($this->owner)
            ->post(route('platform.clients.unlock', $this->client))
            ->assertRedirect();

        $this->assertSame('active', app(SubscriptionService::class)->status($this->client->fresh()));
    }

    // ---------------------------------------------------------------- token

    public function test_a_token_unlocks_an_expired_client_and_extends_the_subscription(): void
    {
        $subscription = $this->subscribe($this->client, $this->smsPackage, 'sms', ['expire_date' => now()->subDays(30)->toDateString()]);
        app(SubscriptionService::class)->setGraceDays(0);
        $this->client->update(['subscription_status' => 'locked']);

        $token = app(SubscriptionService::class)->issueToken($subscription, now()->addMonths(6)->toDateString());

        $this->actingAs($this->clientUser)
            ->post(route('subscription.unlock.post'), ['token' => strtolower($token->token_)])
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $this->assertSame('active', app(SubscriptionService::class)->status($this->client->fresh()));
        // Read it the way a human would: find the token row by its value.
        $used = \App\Models\SubscriptionToken::query()
            ->whereRaw('upper(token_) = ?', [strtoupper($token->token_)])
            ->first();
        $this->assertNotNull($used, 'the token row still exists');
        $this->assertNotNull($used->used_at, 'the token is marked as used');
        $this->assertSame(0, (int) $used->status, 'a token can be used once');
        $this->assertTrue($subscription->fresh()->expire_date->isFuture());
    }

    public function test_a_used_token_cannot_be_reused_and_a_foreign_token_is_refused(): void
    {
        $subscription = $this->subscribe($this->client, $this->smsPackage, 'sms');
        $service = app(SubscriptionService::class);
        $token = $service->issueToken($subscription);

        $this->assertTrue($service->redeem($token->token_, $this->client->id)['ok']);
        $this->assertFalse($service->redeem($token->token_, $this->client->id)['ok'], 'second use refused');

        // a token of another client
        $otherClient = Client::create([
            'client_name' => 'Other ' . uniqid(), 'client_location' => 'X', 'client_city' => 'X',
            'client_country' => 'TZ', 'client_email' => 'o-' . uniqid() . '@example.test',
            'client_type_id' => $this->client->client_type_id,
        ]);
        $otherSub = $this->subscribe($otherClient, $this->smsPackage, 'sms');
        $otherToken = $service->issueToken($otherSub);

        $result = $service->redeem($otherToken->token_, $this->client->id);
        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('another account', $result['message']);
    }

    // ---------------------------------------------------------------- modules / sidebar

    public function test_an_sms_client_sees_the_sms_modules_and_not_the_freight_ones(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms');

        $access = app(ModuleAccess::class);
        $modules = $access->modulesForClient($this->client->id);

        $this->assertContains('sms_bulk', $modules);
        $this->assertNotContains('cargo', $modules);
        $this->assertFalse($access->allows('cargo', $this->clientUser));
        $this->assertFalse($access->allows('storage', $this->clientUser));
        $this->assertTrue($access->allows('sms_bulk', $this->clientUser));

        // The cargo screens are closed too, not just the menu.
        $this->actingAs($this->clientUser)->get(route('cargo.index'))->assertForbidden();
        $this->actingAs($this->clientUser)->get(route('storage.index'))->assertForbidden();
    }

    public function test_a_freight_client_sees_cargo_and_not_the_sms_modules(): void
    {
        $this->subscribe($this->client, $this->freightPackage, 'freight');

        $access = app(ModuleAccess::class);

        $this->assertTrue($access->allows('cargo', $this->clientUser));
        $this->assertTrue($access->allows('storage', $this->clientUser));
        $this->assertFalse($access->allows('sms_bulk', $this->clientUser));

        $this->actingAs($this->clientUser)->get(route('cargo.index'))->assertOk();
    }

    public function test_the_sidebar_only_shows_what_the_package_includes(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms');

        $menu = app(\App\Services\Platform\SidebarService::class)->menu($this->clientUser);
        $titles = collect($menu)->pluck('title')->all();

        $this->assertContains('Message', $titles);
        $this->assertNotContains('Cargo Operations', $titles);
        $this->assertNotContains('Storage', $titles);
        $this->assertNotContains('Accounts', $titles);
        $this->assertNotContains('Clients', $titles, 'client management is owner only');
    }

    public function test_the_owner_gets_the_platform_menu_not_a_client_menu(): void
    {
        $menu = app(\App\Services\Platform\SidebarService::class)->menu($this->owner);
        $titles = collect($menu)->pluck('title')->all();

        $this->assertContains('Clients', $titles);
        $this->assertContains('Subscriptions', $titles);
        $this->assertContains('Products', $titles);
        $this->assertNotContains('Cargo Operations', $titles);
    }

    // ---------------------------------------------------------------- SMS allowance

    public function test_sending_stops_when_the_sms_balance_is_finished(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms', ['sms_quota' => 1]);

        $customer = Customer::create([
            'customer_firstname' => 'A', 'phone' => '0712000001',
            'user_id' => $this->clientUser->id, 'client_id' => $this->client->id,
        ]);

        // quota 1, nothing used yet
        $service = app(SubscriptionService::class);
        $this->assertSame(1, $service->smsLeft($this->client->fresh()));
        $this->assertTrue($service->canSendSms($this->client->fresh(), 1));

        // one message recorded = balance finished
        Message::create([
            'customer_number' => '0712000001', 'content' => 'hello',
            'status' => 'delivered', 'client_id' => $this->client->id,
        ]);

        $this->assertSame(0, $service->smsLeft($this->client->fresh()));
        $this->assertFalse($service->canSendSms($this->client->fresh(), 1));

        // the controller refuses the batch before talking to the gateway
        $this->actingAs($this->clientUser)
            ->post(route('admin.sendmessage.multiple'), [
                'send_to_all' => 1, 'message' => 'Second batch', 'sender_id' => 'KODA',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, Message::where('client_id', $this->client->id)->count(), 'nothing was sent');
    }

    public function test_an_expired_account_cannot_send_sms_at_all(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms', [
            'expire_date' => now()->subDays(40)->toDateString(), 'sms_quota' => 500,
        ]);
        app(SubscriptionService::class)->setGraceDays(0);

        Customer::create([
            'customer_firstname' => 'B', 'phone' => '0712000002',
            'user_id' => $this->clientUser->id, 'client_id' => $this->client->id,
        ]);

        $this->assertFalse(app(SubscriptionService::class)->canSendSms($this->client->fresh(), 1));
    }

    // ---------------------------------------------------------------- owner screens

    public function test_the_owner_platform_screens_open(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms');

        foreach ([
            route('platform.clients'),
            route('platform.subscriptions'),
            route('platform.tokens'),
            route('platform.modules'),
            route('platform.settings'),
            route('list.clients'),
        ] as $url) {
            $this->actingAs($this->owner)->get($url)->assertOk();
        }
    }

    public function test_a_client_cannot_open_the_platform_screens(): void
    {
        $this->subscribe($this->client, $this->smsPackage, 'sms');

        $this->actingAs($this->clientUser)->get(route('platform.clients'))->assertForbidden();
        $this->actingAs($this->clientUser)->get(route('platform.tokens'))->assertForbidden();
    }

    public function test_the_owner_can_create_a_subscription_for_a_client(): void
    {
        $this->actingAs($this->owner)
            ->post(route('platform.subscriptions.store'), [
                'client_id' => $this->client->id,
                'package_id' => $this->smsPackage->id,
                'product' => 'sms',
                'start_date' => now()->toDateString(),
                'duration_in_month' => 12,
                'sms_quota' => 5000,
            ])
            ->assertRedirect(route('platform.clients'));

        $subscription = Subscription::where('client_id', $this->client->id)->first();

        $this->assertNotNull($subscription);
        $this->assertSame(5000, (int) $subscription->sms_quota);
        $this->assertSame('active', app(SubscriptionService::class)->status($this->client->fresh()));
        $this->assertEquals(300000.0, (float) $subscription->amount, 'yearly price of the SMS package');
    }

    public function test_the_owner_renews_from_the_overview_in_one_click(): void
    {
        $subscription = $this->subscribe($this->client, $this->smsPackage, 'sms', [
            'start_date' => now()->subMonths(13)->toDateString(),
            'expire_date' => now()->subMonth()->toDateString(),
        ]);
        $this->client->update(['subscription_status' => 'locked']);

        $this->actingAs($this->owner)
            ->post(route('platform.subscriptions.store'), [
                'renew_client_id' => $this->client->id,
                'months' => 12,
            ])
            ->assertRedirect(route('platform.clients'))
            ->assertSessionHas('success');

        $this->assertSame('active', app(SubscriptionService::class)->status($this->client->fresh()));
        $this->assertTrue($subscription->fresh()->expire_date->isFuture());
        // the renewal is billed automatically, so the invoice trail stays complete
        $this->assertTrue(\App\Models\SubscriptionInvoice::where('client_id', $this->client->id)->exists());
    }
}
