<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\SubscriptionToken;
use App\Models\SubscriptionInvoice;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionInvoiceSubscriptionTokensTest extends TestCase
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
    public function it_gets_subscription_invoice_subscription_tokens(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();
        $subscriptionTokens = SubscriptionToken::factory()
            ->count(2)
            ->create([
                'subscription_invoice_id' => $subscriptionInvoice->id,
            ]);

        $response = $this->getJson(
            route(
                'api.subscription-invoices.subscription-tokens.index',
                $subscriptionInvoice
            )
        );

        $response->assertOk()->assertSee($subscriptionTokens[0]->token_);
    }

    /**
     * @test
     */
    public function it_stores_the_subscription_invoice_subscription_tokens(): void
    {
        $subscriptionInvoice = SubscriptionInvoice::factory()->create();
        $data = SubscriptionToken::factory()
            ->make([
                'subscription_invoice_id' => $subscriptionInvoice->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route(
                'api.subscription-invoices.subscription-tokens.store',
                $subscriptionInvoice
            ),
            $data
        );

        $this->assertDatabaseHas('subscription_tokens', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $subscriptionToken = SubscriptionToken::latest('id')->first();

        $this->assertEquals(
            $subscriptionInvoice->id,
            $subscriptionToken->subscription_invoice_id
        );
    }
}
