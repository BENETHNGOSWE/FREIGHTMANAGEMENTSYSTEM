<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\SubscriptionInvoice;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientSubscriptionInvoicesTest extends TestCase
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
    public function it_gets_client_subscription_invoices(): void
    {
        $client = Client::factory()->create();
        $subscriptionInvoices = SubscriptionInvoice::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.subscription-invoices.index', $client)
        );

        $response->assertOk()->assertSee($subscriptionInvoices[0]->bill_date);
    }

    /**
     * @test
     */
    public function it_stores_the_client_subscription_invoices(): void
    {
        $client = Client::factory()->create();
        $data = SubscriptionInvoice::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.subscription-invoices.store', $client),
            $data
        );

        $this->assertDatabaseHas('subscription_invoices', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $subscriptionInvoice = SubscriptionInvoice::latest('id')->first();

        $this->assertEquals($client->id, $subscriptionInvoice->client_id);
    }
}
