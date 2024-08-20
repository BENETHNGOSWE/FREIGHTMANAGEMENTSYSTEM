<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\ClientAdmin;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientClientAdminsTest extends TestCase
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
    public function it_gets_client_client_admins(): void
    {
        $client = Client::factory()->create();
        $clientAdmins = ClientAdmin::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.client-admins.index', $client)
        );

        $response->assertOk()->assertSee($clientAdmins[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_client_client_admins(): void
    {
        $client = Client::factory()->create();
        $data = ClientAdmin::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.client-admins.store', $client),
            $data
        );

        $this->assertDatabaseHas('client_admins', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $clientAdmin = ClientAdmin::latest('id')->first();

        $this->assertEquals($client->id, $clientAdmin->client_id);
    }
}
