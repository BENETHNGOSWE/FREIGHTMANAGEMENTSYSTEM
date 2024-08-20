<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\SubscriptionInvoice;

use App\Models\Client;
use App\Models\Subscription;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionInvoiceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_subscription_invoices(): void
    {
        $subscriptionInvoices = SubscriptionInvoice::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('subscription-invoices.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.subscription_invoices.index')
            ->assertViewHas('subscriptionInvoices');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_subscription_invoice(): void
    {
        $response = $this->get(route('subscription-invoices.create'));

        $response->assertOk()->assertViewIs('app.subscription_invoices.create');
    }

    /**
     * @test
     */
    public function it_stores_the_subscription_invoice(): void
    {
        $data = SubscriptionInvoice::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('subscription-invoices.store'), $data);

        $this->assertDatabaseHas('subscription_invoices', $data);

        $subscriptionInvoice = SubscriptionInvoice::latest('id')->first();

        $response->assertRedirect(
            route('subscription-invoices.edit', $subscriptionInvoice)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_subscription_invoice(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();

        $response = $this->get(
            route('subscription-invoices.show', $subscriptionInvoice)
        );

        $response
            ->assertOk()
            ->assertViewIs('app.subscription_invoices.show')
            ->assertViewHas('subscriptionInvoice');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_subscription_invoice(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();

        $response = $this->get(
            route('subscription-invoices.edit', $subscriptionInvoice)
        );

        $response
            ->assertOk()
            ->assertViewIs('app.subscription_invoices.edit')
            ->assertViewHas('subscriptionInvoice');
    }

    /**
     * @test
     */
    public function it_updates_the_subscription_invoice(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();

        $subscription = Subscription::factory()->create();
        $client = Client::factory()->create();

        $data = [
            'amount' => $this->faker->randomNumber(1),
            'balance' => $this->faker->randomNumber(1),
            'bill_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'status' => '0',
            'subscription_id' => $subscription->id,
            'client_id' => $client->id,
        ];

        $response = $this->put(
            route('subscription-invoices.update', $subscriptionInvoice),
            $data
        );

        $data['id'] = $subscriptionInvoice->id;

        $this->assertDatabaseHas('subscription_invoices', $data);

        $response->assertRedirect(
            route('subscription-invoices.edit', $subscriptionInvoice)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_subscription_invoice(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();

        $response = $this->delete(
            route('subscription-invoices.destroy', $subscriptionInvoice)
        );

        $response->assertRedirect(route('subscription-invoices.index'));

        $this->assertModelMissing($subscriptionInvoice);
    }
}
