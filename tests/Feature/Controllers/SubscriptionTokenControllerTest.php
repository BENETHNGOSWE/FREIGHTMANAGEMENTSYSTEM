<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\SubscriptionToken;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionTokenControllerTest extends TestCase
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
    public function it_displays_index_view_with_subscription_tokens(): void
    {
        $subscriptionTokens = SubscriptionToken::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('subscription-tokens.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.subscription_tokens.index')
            ->assertViewHas('subscriptionTokens');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_subscription_token(): void
    {
        $response = $this->get(route('subscription-tokens.create'));

        $response->assertOk()->assertViewIs('app.subscription_tokens.create');
    }

    /**
     * @test
     */
    public function it_stores_the_subscription_token(): void
    {
        $data = SubscriptionToken::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('subscription-tokens.store'), $data);

        $this->assertDatabaseHas('subscription_tokens', $data);

        $subscriptionToken = SubscriptionToken::latest('id')->first();

        $response->assertRedirect(
            route('subscription-tokens.edit', $subscriptionToken)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_subscription_token(): void
    {
        $subscriptionToken = SubscriptionToken::factory()->create();

        $response = $this->get(
            route('subscription-tokens.show', $subscriptionToken)
        );

        $response
            ->assertOk()
            ->assertViewIs('app.subscription_tokens.show')
            ->assertViewHas('subscriptionToken');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_subscription_token(): void
    {
        $subscriptionToken = SubscriptionToken::factory()->create();

        $response = $this->get(
            route('subscription-tokens.edit', $subscriptionToken)
        );

        $response
            ->assertOk()
            ->assertViewIs('app.subscription_tokens.edit')
            ->assertViewHas('subscriptionToken');
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

        $response = $this->put(
            route('subscription-tokens.update', $subscriptionToken),
            $data
        );

        $data['id'] = $subscriptionToken->id;

        $this->assertDatabaseHas('subscription_tokens', $data);

        $response->assertRedirect(
            route('subscription-tokens.edit', $subscriptionToken)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_subscription_token(): void
    {
        $subscriptionToken = SubscriptionToken::factory()->create();

        $response = $this->delete(
            route('subscription-tokens.destroy', $subscriptionToken)
        );

        $response->assertRedirect(route('subscription-tokens.index'));

        $this->assertModelMissing($subscriptionToken);
    }
}
