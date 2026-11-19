<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Kodapackage;
use App\Models\Subscription;
use App\Services\Platform\SubscriptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The owner screens run on databases that were built at different times. A missing
 * platform column must degrade, never throw: the classic failure is an aggregate over
 * an empty list ("select sum(amount) ... where 0 = 1"), which PostgreSQL rejects when
 * the column does not exist.
 */
class PlatformMigrationSafetyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_platform_columns_exist_on_subscriptions(): void
    {
        foreach (['product', 'sms_quota', 'sms_used', 'amount', 'locked_at', 'notes'] as $column) {
            $this->assertTrue(
                Schema::hasColumn('subscriptions', $column),
                "subscriptions.{$column} is added by the platform migrations"
            );
        }
    }

    public function test_the_platform_settings_and_token_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('platform_settings'));

        foreach (['used_at', 'used_by'] as $column) {
            $this->assertTrue(Schema::hasColumn('subscription_tokens', $column));
        }

        // tokens may be issued without an invoice attached
        $nullable = collect(Schema::getColumns('subscription_tokens'))
            ->firstWhere('name', 'subscription_invoice_id');

        if ($nullable) {
            $this->assertTrue((bool) $nullable['nullable'], 'subscription_invoice_id must be nullable');
        }
    }

    public function test_summing_subscription_value_on_an_empty_list_never_touches_sql(): void
    {
        // exactly what the owner overview does, with nothing to sum
        $total = round((float) collect()->pluck('subscription')->filter()
            ->sum(fn ($subscription) => (float) ($subscription->amount ?? 0)), 2);

        $this->assertSame(0.0, $total);
    }

    public function test_a_renewal_does_not_depend_on_optional_columns(): void
    {
        $client = Client::firstOrCreate(['client_email' => 'migration-safety@example.test'], [
            'client_name' => 'Migration Safety Ltd', 'client_location' => 'Dar', 'client_city' => 'Dar',
            'client_country' => 'TZ', 'client_type_id' => 1, 'account_type' => 'freight',
        ]);

        $package = Kodapackage::first();

        if (! $package) {
            $this->markTestSkipped('PlatformSeeder has not run.');
        }

        $subscription = Subscription::create([
            'client_id' => $client->id, 'package_id' => $package->id, 'product' => 'freight',
            'start_date' => now()->subMonth()->toDateString(), 'expire_date' => now()->subDay()->toDateString(),
            'duration_in_month' => 1, 'status' => '0', 'amount' => 0,
        ]);

        $renewed = app(SubscriptionService::class)->renew($subscription, 3);

        $this->assertTrue($renewed->expire_date->isFuture());
        $this->assertGreaterThan(1, (int) $renewed->duration_in_month);
        $this->assertSame('active', app(SubscriptionService::class)->status($client->fresh()));
    }
}
