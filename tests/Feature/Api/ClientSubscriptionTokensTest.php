<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\SubscriptionToken;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientSubscriptionTokensTest extends TestCase
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
    public function it_gets_client_subscription_tokens(): void
    {
        $client = Client::factory()->create();
        $subscriptionTokens = SubscriptionToken::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.subscription-tokens.index', $client)
        );

        $response->assertOk()->assertSee($subscriptionTokens[0]->token_);
    }

    /**
     * @test
     */
    public function it_stores_the_client_subscription_tokens(): void
    {
        $client = Client::factory()->create();
        $data = SubscriptionToken::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.subscription-tokens.store', $client),
            $data
        );

        $this->assertDatabaseHas('subscription_tokens', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $subscriptionToken = SubscriptionToken::latest('id')->first();

        $this->assertEquals($client->id, $subscriptionToken->client_id);
    }
}
