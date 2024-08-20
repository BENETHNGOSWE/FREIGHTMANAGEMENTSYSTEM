<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\ClientType;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTypeClientsTest extends TestCase
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
    public function it_gets_client_type_clients(): void
    {
        $clientType = ClientType::factory()->create();
        $clients = Client::factory()
            ->count(2)
            ->create([
                'client_type_id' => $clientType->id,
            ]);

        $response = $this->getJson(
            route('api.client-types.clients.index', $clientType)
        );

        $response->assertOk()->assertSee($clients[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_client_type_clients(): void
    {
        $clientType = ClientType::factory()->create();
        $data = Client::factory()
            ->make([
                'client_type_id' => $clientType->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.client-types.clients.store', $clientType),
            $data
        );

        $this->assertDatabaseHas('clients', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $client = Client::latest('id')->first();

        $this->assertEquals($clientType->id, $client->client_type_id);
    }
}
