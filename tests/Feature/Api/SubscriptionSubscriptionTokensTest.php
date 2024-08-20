<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionToken;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionSubscriptionTokensTest extends TestCase
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
    public function it_gets_subscription_subscription_tokens(): void
    {
        $subscription = Subscription::factory()->create();
        $subscriptionTokens = SubscriptionToken::factory()
            ->count(2)
            ->create([
                'subscription_id' => $subscription->id,
            ]);

        $response = $this->getJson(
            route('api.subscriptions.subscription-tokens.index', $subscription)
        );

        $response->assertOk()->assertSee($subscriptionTokens[0]->token_);
    }

    /**
     * @test
     */
    public function it_stores_the_subscription_subscription_tokens(): void
    {
        $subscription = Subscription::factory()->create();
        $data = SubscriptionToken::factory()
            ->make([
                'subscription_id' => $subscription->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.subscriptions.subscription-tokens.store', $subscription),
            $data
        );

        $this->assertDatabaseHas('subscription_tokens', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $subscriptionToken = SubscriptionToken::latest('id')->first();

        $this->assertEquals(
            $subscription->id,
            $subscriptionToken->subscription_id
        );
    }
}
