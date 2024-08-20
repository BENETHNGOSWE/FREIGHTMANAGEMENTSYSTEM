<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\SubscriptionInvoice;

use App\Models\Client;
use App\Models\Subscription;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionInvoiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_subscription_invoices_list(): void
    {
        $subscriptionInvoices = SubscriptionInvoice::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.subscription-invoices.index'));

        $response->assertOk()->assertSee($subscriptionInvoices[0]->bill_date);
    }

    /**
     * @test
     */
    public function it_stores_the_subscription_invoice(): void
    {
        $data = SubscriptionInvoice::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(
            route('api.subscription-invoices.store'),
            $data
        );

        $this->assertDatabaseHas('subscription_invoices', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.subscription-invoices.update', $subscriptionInvoice),
            $data
        );

        $data['id'] = $subscriptionInvoice->id;

        $this->assertDatabaseHas('subscription_invoices', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_subscription_invoice(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();

        $response = $this->deleteJson(
            route('api.subscription-invoices.destroy', $subscriptionInvoice)
        );

        $this->assertModelMissing($subscriptionInvoice);

        $response->assertNoContent();
    }
}
