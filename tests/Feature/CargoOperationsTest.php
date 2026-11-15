<?php

namespace Tests\Feature;

use App\Models\CargoStorage;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLoad;
use App\Models\InvoicePayment;
use App\Models\Loadpackage;
use App\Models\PickupNote;
use App\Models\StorageCharge;
use App\Models\StoragePayment;
use App\Models\UnitType;
use App\Models\User;
use App\Services\Cargo\CargoStatusService;
use App\Services\Cargo\MergePaymentService;
use App\Services\Cargo\ReleaseControlService;
use App\Services\Cargo\StorageService;
use App\Services\Cargo\VerificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CargoOperationsTest extends TestCase
{
    use DatabaseTransactions;

    private Client $client;
    private User $user;
    private Customer $customer;
    private UnitType $kg;
    private \App\Models\Loadpackagetype $packageType;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('cargo_storages')) {
            $this->markTestSkipped('Run `php artisan migrate` before the cargo tests.');
        }

        $type = ClientType::firstOrCreate(['name' => 'Freight']);
        $this->client = Client::create([
            'client_name' => 'Cargo Test Co', 'client_location' => 'Dar', 'client_city' => 'Dar',
            'client_country' => 'TZ', 'client_email' => 'cargo-' . uniqid() . '@example.test',
            'client_type_id' => $type->id,
        ]);

        $this->user = User::create([
            'name' => 'Cargo Admin', 'email' => 'cargo-' . uniqid() . '@example.test',
            'password' => bcrypt('secret'), 'client_id' => $this->client->id,
        ]);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user->assignRole('admin');
        $this->actingAs($this->user);

        $this->customer = Customer::create([
            'customer_firstname' => 'Storage Customer', 'phone' => '0712000111',
            'user_id' => $this->user->id, 'client_id' => $this->client->id,
        ]);

        $this->kg = UnitType::firstOrCreate(
            ['unittype_name' => 'KG'],
            ['unitytype_initial' => 'kg', 'client_id' => $this->client->id]
        );

        $this->packageType = \App\Models\Loadpackagetype::firstOrCreate(
            ['load_type_name' => 'Carton', 'client_id' => $this->client->id],
            ['load_type_description' => 'Carton']
        );
    }

    private function load(array $overrides = []): Loadpackage
    {
        $load = Loadpackage::create(array_merge([
            'load_no' => 'TZ' . strtoupper(uniqid()),
            'load_name' => 'Shoes',
            'load_price' => 0,
            'load_weight_value' => 2,
            'load_description' => 'Test cargo',
            'load_transport_date' => now()->toDateString(),
            'load_delivery_date' => now()->addDays(20)->toDateString(),
            'load_trucking_number' => 'TRK-1',
            'client_id' => $this->client->id,
            'customer_id' => $this->customer->id,
            'unittype_id' => $this->kg->id,
            'loadpackagetype_id' => $this->packageType->id,
            'user_id' => $this->user->id,
            'cargo_status' => 'Arrived',
        ], $overrides));

        app(CargoStatusService::class)->ensureToken($load);

        return $load;
    }

    private function invoiceWithLoad(Loadpackage $load, float $price = 1000, float $fx = 1): Invoice
    {
        $qty = (float) $load->load_weight_value;
        $invoice = Invoice::create([
            'invoice_no' => 'INV-' . strtoupper(uniqid()),
            'prefix' => 'INV',
            'total_amount' => $qty * $price * $fx,
            'total_transportfee' => 0,
            'total_balance' => $qty * $price * $fx,
            'status' => 0,
            'customer_id' => $this->customer->id,
            'client_id' => $this->client->id,
            'cbm_price' => $price,
            'exchange_rate' => $fx,
        ]);

        InvoiceLoad::create([
            'invoice_id' => $invoice->id,
            'loadpackage_id' => $load->id,
            'client_id' => $this->client->id,
            'load_price' => $invoice->total_amount,
        ]);

        return $invoice;
    }

    // ------------------------------------------------------------------ storage

    public function test_storage_starts_after_the_free_period_and_never_duplicates(): void
    {
        $storage = app(StorageService::class);
        $storage->settings($this->client->id)->update(['free_days' => 3, 'daily_rate' => 500]);

        $load = $this->load();
        $record = $storage->start($load, Carbon::today()->subDays(6));
        $record->update(['daily_rate' => 500, 'free_days' => 3]);

        // Free period: today-6 .. today-4 (3 days) → charges start at today-3.
        $result = $storage->accrue($this->client->id, now()->toDateString());
        $this->assertSame(4, $result['added'], 'four chargeable days');
        $this->assertSame(2000.0, (float) StorageCharge::where('cargo_storage_id', $record->id)->sum('amount'));

        // Running again on the same day must not duplicate anything.
        $again = $storage->accrue($this->client->id, now()->toDateString());
        $this->assertSame(0, $again['added']);
        $this->assertSame(4, StorageCharge::where('cargo_storage_id', $record->id)->count());
        $this->assertSame(2000.0, $record->fresh()->balance());
    }

    public function test_a_customer_rate_overrides_the_default(): void
    {
        $storage = app(StorageService::class);
        $storage->settings($this->client->id)->update(['free_days' => 7, 'daily_rate' => 1000]);

        \App\Models\CustomerStorageRate::create([
            'client_id' => $this->client->id, 'customer_id' => $this->customer->id,
            'free_days' => 2, 'daily_rate' => 300,
        ]);

        $load = $this->load();
        $record = $storage->start($load);

        $this->assertSame(2, (int) $record->free_days);
        $this->assertSame(300.0, (float) $record->daily_rate);
    }

    public function test_storage_payment_and_waiver_clear_the_balance(): void
    {
        $storage = app(StorageService::class);
        $load = $this->load();
        $record = $storage->start($load, Carbon::today()->subDays(5));
        $record->update(['free_days' => 1, 'daily_rate' => 1000]);

        $storage->accrue($this->client->id, now()->toDateString());
        $this->assertGreaterThan(0, $record->fresh()->balance());

        $storage->pay($record, ['amount' => 1000, 'payment_method' => 'CASH']);
        $remaining = $record->fresh()->balance();

        $storage->waive($record, $remaining, 'Agreed with the customer');

        $this->assertSame(0.0, $record->fresh()->balance());
        $this->assertSame(1, StoragePayment::where('cargo_storage_id', $record->id)->count());
    }

    public function test_storage_stops_when_the_cargo_is_collected(): void
    {
        $storage = app(StorageService::class);
        $load = $this->load();
        $record = $storage->start($load, Carbon::today()->subDays(10));
        $record->update(['free_days' => 1, 'daily_rate' => 100]);

        $storage->stop($load);

        $this->assertSame('collected', $record->fresh()->status);
        $days = StorageCharge::where('cargo_storage_id', $record->id)->count();
        $this->assertSame(0, $storage->accrue($this->client->id, now()->addDay()->toDateString())['added']);
        $this->assertSame($days, StorageCharge::where('cargo_storage_id', $record->id)->count());
    }

    public function test_the_daily_command_runs(): void
    {
        $load = $this->load();
        $storage = app(StorageService::class);
        $record = $storage->start($load, Carbon::today()->subDays(8));
        $record->update(['free_days' => 2, 'daily_rate' => 250]);

        $this->artisan('storage:accrue')->assertSuccessful();
        $this->assertGreaterThan(0, StorageCharge::where('cargo_storage_id', $record->id)->count());
    }

    // ------------------------------------------------------------------ verification

    public function test_a_weight_change_reprices_the_invoice_and_keeps_the_old_weight(): void
    {
        $load = $this->load(['load_weight_value' => 5]);
        $invoice = $this->invoiceWithLoad($load, price: 10, fx: 1);

        $this->assertSame('50.00', number_format((float) $invoice->total_amount, 2));

        app(VerificationService::class)->verify($load, ['weight' => 5.4]);

        $invoice->refresh();
        $load->refresh();

        $this->assertSame(5.0, (float) $load->original_weight_value, 'China weight is history');
        $this->assertSame(5.4, (float) $load->verified_weight_value);
        $this->assertSame('54.00', number_format((float) $invoice->total_amount, 2), 'price followed the weight');
        $this->assertSame(4.0, (float) $load->weight_price_impact);
        $this->assertSame('Verified', $load->cargo_status);
    }

    public function test_verification_records_damage_and_missing_flags(): void
    {
        $load = $this->load();

        app(VerificationService::class)->verify($load, [
            'weight' => 2, 'has_damage' => true, 'has_missing' => true, 'notes' => 'One carton crushed',
        ]);

        $load->refresh();
        $this->assertTrue($load->has_damage);
        $this->assertTrue($load->has_missing);
        $this->assertSame('One carton crushed', $load->verification_notes);
    }

    // ------------------------------------------------------------------ release control

    public function test_release_is_blocked_until_invoice_storage_and_pickup_note_are_clear(): void
    {
        $release = app(ReleaseControlService::class);
        $load = $this->load();
        $invoice = $this->invoiceWithLoad($load, price: 1000, fx: 1);

        app(VerificationService::class)->verify($load, ['weight' => 2]);

        $checks = $release->checks($load->fresh());
        $this->assertFalse($checks['can_release'], 'unpaid invoice blocks the release');

        // Paid, but storage still owing.
        InvoicePayment::create([
            'invoice_id' => $invoice->id, 'client_id' => $this->client->id, 'amount_received' => 2000,
            'payment_date' => now()->toDateString(), 'payment_method' => 'CASH', 'received_by' => $this->user->id,
        ]);

        $storage = app(StorageService::class);
        $record = $storage->start($load->fresh(), Carbon::today()->subDays(9));
        $record->update(['free_days' => 1, 'daily_rate' => 500]);
        $storage->accrue($this->client->id, now()->toDateString());

        $checks = $release->checks($load->fresh());
        $this->assertFalse($checks['can_release']);
        $this->assertContains('Storage cleared', $checks['blocked_by']);

        // Clear the storage and issue the pickup note.
        $storage->waive($record, $record->balance(), 'First customer');
        app(CargoStatusService::class)->pickupNoteFor($invoice);

        $checks = $release->checks($load->fresh());
        $this->assertTrue($checks['can_release'], 'blocked by: ' . implode(', ', $checks['blocked_by']));

        $result = $release->release($load->fresh());
        $this->assertTrue($result['ok']);
        $this->assertSame('Ready for Pickup', $load->fresh()->cargo_status);

        $collected = $release->collect($load->fresh());
        $this->assertTrue($collected['ok']);
        $this->assertSame('Collected', $load->fresh()->cargo_status);
        $this->assertSame('collected', $record->fresh()->status, 'storage stops on collection');
    }

    public function test_a_cancelled_pickup_note_blocks_the_release(): void
    {
        $release = app(ReleaseControlService::class);
        $load = $this->load();
        $invoice = $this->invoiceWithLoad($load, price: 100, fx: 1);
        app(VerificationService::class)->verify($load, ['weight' => 2]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id, 'client_id' => $this->client->id, 'amount_received' => 200,
            'payment_date' => now()->toDateString(), 'payment_method' => 'CASH', 'received_by' => $this->user->id,
        ]);

        $note = app(CargoStatusService::class)->pickupNoteFor($invoice);
        $note->forceFill(['status' => 'cancelled'])->save();

        $checks = $release->checks($load->fresh());
        $this->assertFalse($checks['can_release']);
        $this->assertContains('Pickup note issued', $checks['blocked_by']);
    }

    // ------------------------------------------------------------------ merge payment + 1 KG

    public function test_the_one_kg_minimum_applies_to_the_combined_weight(): void
    {
        $merge = app(MergePaymentService::class);

        $a = $this->load(['load_weight_value' => 0.3, 'verified_weight_value' => 0.3]);
        $b = $this->load(['load_weight_value' => 0.4, 'verified_weight_value' => 0.4]);
        $invoiceA = $this->invoiceWithLoad($a, price: 1000, fx: 1);
        $invoiceB = $this->invoiceWithLoad($b, price: 1000, fx: 1);

        $preview = $merge->preview($this->client->id, [$invoiceA->id, $invoiceB->id]);

        $this->assertSame(0.7, (float) $preview['combined_weight']);
        $this->assertSame(1.0, (float) $preview['billable_weight']);
        $this->assertTrue($preview['minimum_applied']);
        // 1 KG billable instead of 0.7 → 300 extra billed.
        $this->assertSame(300.0, (float) $preview['difference']);
    }

    public function test_no_minimum_when_the_combined_weight_is_above_one_kg(): void
    {
        $merge = app(MergePaymentService::class);

        $a = $this->load(['load_weight_value' => 0.6, 'verified_weight_value' => 0.6]);
        $b = $this->load(['load_weight_value' => 0.8, 'verified_weight_value' => 0.8]);
        $invoiceA = $this->invoiceWithLoad($a, price: 1000, fx: 1);
        $invoiceB = $this->invoiceWithLoad($b, price: 1000, fx: 1);

        $preview = $merge->preview($this->client->id, [$invoiceA->id, $invoiceB->id]);

        $this->assertSame(1.4, (float) $preview['combined_weight']);
        $this->assertSame(1.4, (float) $preview['billable_weight']);
        $this->assertFalse($preview['minimum_applied']);
        $this->assertSame(0.0, (float) $preview['difference']);
    }

    public function test_a_merged_payment_settles_several_invoices(): void
    {
        $a = $this->load(['load_weight_value' => 2]);
        $b = $this->load(['load_weight_value' => 3]);
        $invoiceA = $this->invoiceWithLoad($a, price: 1000, fx: 1);   // 2,000
        $invoiceB = $this->invoiceWithLoad($b, price: 1000, fx: 1);   // 3,000

        $payment = app(MergePaymentService::class)->apply($this->client->id, [$invoiceA->id, $invoiceB->id], [
            'customer_id' => $this->customer->id,
            'amount' => 5000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'CASH',
        ]);

        $this->assertSame(2, $payment->lines->count());
        $this->assertSame(0.0, (float) max($invoiceA->fresh()->balanceDue(), 0));
        $this->assertSame(0.0, (float) max($invoiceB->fresh()->balanceDue(), 0));
        $this->assertSame(2, InvoicePayment::whereIn('invoice_id', [$invoiceA->id, $invoiceB->id])->count());
        $this->assertSame('Paid', $a->fresh()->cargo_status);
    }

    // ------------------------------------------------------------------ tracking + pages

    public function test_public_tracking_shows_the_journey_without_money(): void
    {
        $load = $this->load();
        $this->invoiceWithLoad($load, price: 4500, fx: 1);

        $page = $this->get(route('track.form', ['tracking' => $load->load_no]));
        $page->assertOk();
        $page->assertSee($load->load_no);
        $page->assertSee('Arrived Tanzania');
        $page->assertDontSee('4,500');

        $byQr = $this->get(route('track.show', $load->qr_token));
        $byQr->assertOk()->assertSee($load->load_no);
    }

    public function test_an_unknown_tracking_number_shows_a_friendly_error(): void
    {
        $this->get(route('track.form', ['tracking' => 'NOPE123']))
            ->assertOk()
            ->assertSee('No cargo found');
    }

    public function test_the_cargo_pages_open(): void
    {
        $load = $this->load();
        $invoice = $this->invoiceWithLoad($load, price: 1000, fx: 1);
        app(CargoStatusService::class)->pickupNoteFor($invoice);

        foreach ([
            route('cargo.index'),
            route('cargo.show', $load),
            route('cargo.verification'),
            route('cargo.release.index'),
            route('cargo.pickup.note', $invoice),
            route('storage.index'),
            route('storage.show', app(StorageService::class)->start($load)),
            route('storage.payments'),
            route('storage.settings'),
            route('invoices.merge.create'),
        ] as $url) {
            $response = $this->get($url);
            $this->assertSame(200, $response->getStatusCode(), 'Failed page: ' . $url . ' -> ' . $response->getStatusCode());
        }
    }

    public function test_cargo_details_and_photos_can_be_saved(): void
    {
        $load = $this->load();

        $this->post(route('cargo.details', $load), [
            'china_location' => 'Guangzhou warehouse B',
            'batch_no' => 'BATCH-04',
            'load_description' => 'Two cartons of shoes',
        ])->assertRedirect();

        $load->refresh();
        $this->assertSame('Guangzhou warehouse B', $load->china_location);
        $this->assertSame('BATCH-04', $load->batch_no);
    }

    public function test_status_changes_are_written_to_the_history(): void
    {
        $load = $this->load(['cargo_status' => 'Received']);

        $this->post(route('cargo.status', $load), ['cargo_status' => 'In Transit', 'note' => 'Left Guangzhou'])
            ->assertRedirect();

        $this->assertSame('In Transit', $load->fresh()->cargo_status);
        $this->assertDatabaseHas('cargo_status_histories', [
            'loadpackage_id' => $load->id, 'status' => 'In Transit', 'note' => 'Left Guangzhou',
        ]);
    }

    public function test_another_tenant_cannot_open_a_cargo_page(): void
    {
        $load = $this->load();
        $otherType = ClientType::firstOrCreate(['name' => 'Retail']);
        $other = Client::create([
            'client_name' => 'Other', 'client_location' => 'X', 'client_city' => 'X', 'client_country' => 'TZ',
            'client_email' => 'other-' . uniqid() . '@example.test', 'client_type_id' => $otherType->id,
        ]);
        $foreign = Loadpackage::create([
            'load_no' => 'TZFOREIGN', 'load_name' => 'x', 'load_price' => 0, 'load_weight_value' => 1,
            'load_description' => 'x', 'load_transport_date' => now()->toDateString(),
            'load_delivery_date' => now()->toDateString(), 'load_trucking_number' => 'x',
            'client_id' => $other->id, 'customer_id' => $this->customer->id,
            'unittype_id' => $this->kg->id, 'loadpackagetype_id' => $this->packageType->id,
            'user_id' => $this->user->id,
        ]);

        $this->get(route('cargo.show', $foreign))->assertNotFound();
    }

    // ------------------------------------------------------------------ air freight (no container)

    public function test_air_cargo_walks_the_whole_journey_without_a_container(): void
    {
        // Nothing here assigns a container: this is an air shipment.
        $load = $this->load(['cargo_status' => 'Received']);
        $this->assertNull($load->container_id);

        $this->post(route('cargo.status', $load), ['cargo_status' => 'In Transit'])->assertRedirect();
        $this->assertSame('In Transit', $load->fresh()->cargo_status);

        $this->post(route('cargo.status', $load), ['cargo_status' => 'Arrived'])->assertRedirect();
        $this->assertSame('Arrived', $load->fresh()->cargo_status);
        $this->assertNotNull($load->fresh()->arrived_at, 'arrival starts the storage clock');
        $this->assertNotNull($load->fresh()->storage, 'storage started when the cargo arrived');

        $invoice = $this->invoiceWithLoad($load, price: 1000, fx: 1);
        app(VerificationService::class)->verify($load->fresh(), ['weight' => 2]);
        $this->assertSame('Verified', $load->fresh()->cargo_status);

        InvoicePayment::create([
            'invoice_id' => $invoice->id, 'client_id' => $this->client->id, 'amount_received' => 2000,
            'payment_date' => now()->toDateString(), 'payment_method' => 'CASH', 'received_by' => $this->user->id,
        ]);

        $storage = app(StorageService::class);
        $record = $load->fresh()->storage;
        $storage->waive($record, $record->balance(), 'Free of charge');
        app(CargoStatusService::class)->pickupNoteFor($invoice);

        $result = app(ReleaseControlService::class)->release($load->fresh());
        $this->assertTrue($result['ok'], $result['message']);
        $this->assertSame('Ready for Pickup', $load->fresh()->cargo_status);

        $this->assertTrue(app(ReleaseControlService::class)->collect($load->fresh())['ok']);
        $this->assertSame('Collected', $load->fresh()->cargo_status);
    }

    public function test_only_the_physical_journey_can_be_set_by_hand(): void
    {
        $load = $this->load(['cargo_status' => 'Arrived']);

        // Verification, invoices and the release desk own the later steps.
        foreach (['Verified', 'Invoiced', 'Paid', 'Ready for Pickup', 'Collected'] as $status) {
            $this->post(route('cargo.status', $load), ['cargo_status' => $status])
                ->assertRedirect()
                ->assertSessionHas('error');
            $this->assertSame('Arrived', $load->fresh()->cargo_status, $status . ' must not be set by hand');
        }
    }

    public function test_the_journey_cannot_move_backwards(): void
    {
        $load = $this->load(['cargo_status' => 'Arrived']);

        $this->post(route('cargo.status', $load), ['cargo_status' => 'In Transit'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('Arrived', $load->fresh()->cargo_status);
    }

    public function test_invoice_weight_is_summed_in_php_not_in_sql(): void
    {
        // PostgreSQL rejects coalesce(numeric, varchar); the sum must never reach the database.
        $a = $this->load(['load_weight_value' => 4]);
        $b = $this->load(['load_weight_value' => 1.5]);
        $invoice = $this->invoiceWithLoad($a, price: 1000, fx: 1);

        InvoiceLoad::create([
            'invoice_id' => $invoice->id, 'loadpackage_id' => $b->id,
            'client_id' => $this->client->id, 'load_price' => 1500,
        ]);

        $this->assertSame(5.5, Loadpackage::weightForInvoice($invoice->id));

        // After verification the verified weight wins.
        app(VerificationService::class)->verify($b, ['weight' => 2.5]);
        $this->assertSame(6.5, Loadpackage::weightForInvoice($invoice->id));
    }
}
