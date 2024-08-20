<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\Subscription;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientSubscriptionsTest extends TestCase
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
    public function it_gets_client_subscriptions(): void
    {
        $client = Client::factory()->create();
        $subscriptions = Subscription::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.subscriptions.index', $client)
        );

        $response->assertOk()->assertSee($subscriptions[0]->start_date);
    }

    /**
     * @test
     */
    public function it_stores_the_client_subscriptions(): void
    {
        $client = Client::factory()->create();
        $data = Subscription::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.subscriptions.store', $client),
            $data
        );

        $this->assertDatabaseHas('subscriptions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $subscription = Subscription::latest('id')->first();

        $this->assertEquals($client->id, $subscription->client_id);
    }
}
