<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\Branch;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientBranchesTest extends TestCase
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
    public function it_gets_client_branches(): void
    {
        $client = Client::factory()->create();
        $branches = Branch::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.branches.index', $client)
        );

        $response->assertOk()->assertSee($branches[0]->branch_name);
    }

    /**
     * @test
     */
    public function it_stores_the_client_branches(): void
    {
        $client = Client::factory()->create();
        $data = Branch::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.branches.store', $client),
            $data
        );

        $this->assertDatabaseHas('branches', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $branch = Branch::latest('id')->first();

        $this->assertEquals($client->id, $branch->client_id);
    }
}
