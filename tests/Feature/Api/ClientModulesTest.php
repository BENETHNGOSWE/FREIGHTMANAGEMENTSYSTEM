<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\Module;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientModulesTest extends TestCase
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
    public function it_gets_client_modules(): void
    {
        $client = Client::factory()->create();
        $modules = Module::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(route('api.clients.modules.index', $client));

        $response->assertOk()->assertSee($modules[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_client_modules(): void
    {
        $client = Client::factory()->create();
        $data = Module::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.modules.store', $client),
            $data
        );

        $this->assertDatabaseHas('modules', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $module = Module::latest('id')->first();

        $this->assertEquals($client->id, $module->client_id);
    }
}
