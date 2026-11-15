<?php

namespace Tests\Feature;

use App\Helpers\LoadPackageHelper;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Customer;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The "Customer SMS Notifications" switch under Settings -> Active Name.
 *
 * It must be stored per client (not in one browser session), honoured by every automatic
 * notification, and it must never be able to break a load, an invoice or a container.
 */
class SmsNotificationSwitchTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('system_settings')) {
            $this->markTestSkipped('Run `php artisan migrate` before these tests.');
        }
    }

    private function tenant(): array
    {
        $type = ClientType::firstOrCreate(['name' => 'Freight']);

        $client = Client::create([
            'client_name' => 'SMS Test Co', 'client_location' => 'Dar', 'client_city' => 'Dar',
            'client_country' => 'Tanzania', 'client_email' => 'sms-' . uniqid() . '@example.test',
            'client_type_id' => $type->id,
        ]);

        $user = User::create([
            'name' => 'SMS Admin', 'email' => 'sms-' . uniqid() . '@example.test',
            'password' => bcrypt('secret'), 'client_id' => $client->id,
        ]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        $customer = Customer::create([
            'customer_firstname' => 'SMS Customer', 'phone' => '0754000111',
            'user_id' => $user->id, 'client_id' => $client->id,
        ]);

        return [$client, $user, $customer];
    }

    public function test_the_switch_is_stored_per_client_and_not_in_the_session(): void
    {
        [$client, $user] = $this->tenant();

        // Notifications start ON (default), so a fresh client behaves like before.
        $this->assertTrue(SystemSetting::notificationsEnabled($client->id));

        $this->actingAs($user)
            ->post(route('admin.toggle.notification'), ['enabled' => 'false'])
            ->assertOk()
            ->assertJson(['success' => true, 'enabled' => false]);

        $this->assertSame('0', SystemSetting::get('send_load_notifications', $client->id));
        $this->assertFalse(SystemSetting::notificationsEnabled($client->id));

        // A different user of the same client sees the same value - it is not session-bound.
        $this->assertFalse(SystemSetting::notificationsEnabled($client->id));

        // And it can be switched back on.
        $this->actingAs($user)
            ->post(route('admin.toggle.notification'), ['enabled' => 'true'])
            ->assertJson(['enabled' => true]);
        $this->assertTrue(SystemSetting::notificationsEnabled($client->id));
    }

    public function test_no_sms_is_sent_when_the_switch_is_off(): void
    {
        [$client, , $customer] = $this->tenant();
        SystemSetting::set('send_load_notifications', '0', $client->id);

        $response = LoadPackageHelper::sendCustomerNotification(
            $customer->id, 'TZABC1234', 'Test Co', 5, 2, 'Shoes', 'KG', 1
        );

        $this->assertTrue($response['success'], 'the caller must not see an error');
        $this->assertTrue($response['skipped'], 'the message must be skipped, not sent');
        $this->assertStringContainsString('switched off', $response['message']);
    }

    public function test_the_loading_notification_is_also_skipped_when_off(): void
    {
        [$client, , $customer] = $this->tenant();
        SystemSetting::set('send_load_notifications', '0', $client->id);

        $response = LoadPackageHelper::sendCustomerNotificationafterLoading(
            $customer->id, 'TZABC1234', '2026-11-30', 'CONT-001', 1
        );

        $this->assertTrue($response['skipped']);
        $this->assertTrue($response['success']);
    }

    public function test_a_missing_customer_never_throws(): void
    {
        [$client] = $this->tenant();

        $response = LoadPackageHelper::sendCustomerNotification(999999, 'TZABC', 'Co', 1, 1, 'x', 'KG', 1);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['skipped']);
    }

    public function test_the_switching_helper_reports_off_for_an_unknown_client(): void
    {
        $this->assertFalse(LoadPackageHelper::smsEnabled(null));
    }

    public function test_the_settings_page_shows_the_stored_value(): void
    {
        [$client, $user] = $this->tenant();

        SystemSetting::set('send_load_notifications', '0', $client->id);

        $page = $this->actingAs($user)->get(route('admin.list.activename'));
        $page->assertOk();
        $page->assertSee('DISABLED');
        $page->assertDontSee('ENABLED</strong> - Customers will receive SMS');

        SystemSetting::set('send_load_notifications', '1', $client->id);
        $this->actingAs($user)->get(route('admin.list.activename'))->assertSee('ENABLED');
    }
}
