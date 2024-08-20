<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ClientType;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTypeTest extends TestCase
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
    public function it_gets_client_types_list(): void
    {
        $clientTypes = ClientType::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.client-types.index'));

        $response->assertOk()->assertSee($clientTypes[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_client_type(): void
    {
        $data = ClientType::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.client-types.store'), $data);

        $this->assertDatabaseHas('client_types', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_client_type(): void
    {
        $clientType = ClientType::factory()->create();

        $data = [
            'name' => $this->faker->name(),
        ];

        $response = $this->putJson(
            route('api.client-types.update', $clientType),
            $data
        );

        $data['id'] = $clientType->id;

        $this->assertDatabaseHas('client_types', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_client_type(): void
    {
        $clientType = ClientType::factory()->create();

        $response = $this->deleteJson(
            route('api.client-types.destroy', $clientType)
        );

        $this->assertModelMissing($clientType);

        $response->assertNoContent();
    }
}
