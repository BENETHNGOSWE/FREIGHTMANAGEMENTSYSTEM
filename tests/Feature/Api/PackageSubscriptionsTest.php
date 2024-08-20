<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PackageSubscriptionsTest extends TestCase
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
    public function it_gets_package_subscriptions(): void
    {
        $package = Package::factory()->create();
        $subscriptions = Subscription::factory()
            ->count(2)
            ->create([
                'package_id' => $package->id,
            ]);

        $response = $this->getJson(
            route('api.packages.subscriptions.index', $package)
        );

        $response->assertOk()->assertSee($subscriptions[0]->start_date);
    }

    /**
     * @test
     */
    public function it_stores_the_package_subscriptions(): void
    {
        $package = Package::factory()->create();
        $data = Subscription::factory()
            ->make([
                'package_id' => $package->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.packages.subscriptions.store', $package),
            $data
        );

        $this->assertDatabaseHas('subscriptions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $subscription = Subscription::latest('id')->first();

        $this->assertEquals($package->id, $subscription->package_id);
    }
}
