<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ClientAdmin;

use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientAdminTest extends TestCase
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
    public function it_gets_client_admins_list(): void
    {
        $clientAdmins = ClientAdmin::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.client-admins.index'));

        $response->assertOk()->assertSee($clientAdmins[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_client_admin(): void
    {
        $data = ClientAdmin::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.client-admins.store'), $data);

        $this->assertDatabaseHas('client_admins', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_client_admin(): void
    {
        $clientAdmin = ClientAdmin::factory()->create();

        $user = User::factory()->create();
        $client = Client::factory()->create();

        $data = [
            'user_id' => $user->id,
            'client_id' => $client->id,
        ];

        $response = $this->putJson(
            route('api.client-admins.update', $clientAdmin),
            $data
        );

        $data['id'] = $clientAdmin->id;

        $this->assertDatabaseHas('client_admins', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_client_admin(): void
    {
        $clientAdmin = ClientAdmin::factory()->create();

        $response = $this->deleteJson(
            route('api.client-admins.destroy', $clientAdmin)
        );

        $this->assertModelMissing($clientAdmin);

        $response->assertNoContent();
    }
}
