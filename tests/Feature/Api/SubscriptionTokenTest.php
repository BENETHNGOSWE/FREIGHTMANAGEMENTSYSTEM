<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\SubscriptionToken;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionTokenTest extends TestCase
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
    public function it_gets_subscription_tokens_list(): void
    {
        $subscriptionTokens = SubscriptionToken::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.subscription-tokens.index'));

        $response->assertOk()->assertSee($subscriptionTokens[0]->token_);
    }

    /**
     * @test
     */
    public function it_stores_the_subscription_token(): void
    {
        $data = SubscriptionToken::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(
            route('api.subscription-tokens.store'),
            $data
        );

        $this->assertDatabaseHas('subscription_tokens', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_subscription_token(): void
    {
        $subscriptionToken = SubscriptionToken::factory()->create();

        $subscription = Subscription::factory()->create();
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();
        $client = Client::factory()->create();

        $data = [
            'token_' => $this->faker->text(255),
            'expire_date' => $this->faker->date(),
            'status' => $this->faker->boolean(),
            'subscription_id' => $subscription->id,
            'subscription_invoice_id' => $subscriptionInvoice->id,
            'client_id' => $client->id,
        ];

        $response = $this->putJson(
            route('api.subscription-tokens.update', $subscriptionToken),
            $data
        );

        $data['id'] = $subscriptionToken->id;

        $this->assertDatabaseHas('subscription_tokens', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_subscription_token(): void
    {
        $subscriptionToken = SubscriptionToken::factory()->create();

        $response = $this->deleteJson(
            route('api.subscription-tokens.destroy', $subscriptionToken)
        );

        $this->assertModelMissing($subscriptionToken);

        $response->assertNoContent();
    }
}
