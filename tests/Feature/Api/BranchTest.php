<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Branch;

use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchTest extends TestCase
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
    public function it_gets_branches_list(): void
    {
        $branches = Branch::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.branches.index'));

        $response->assertOk()->assertSee($branches[0]->branch_name);
    }

    /**
     * @test
     */
    public function it_stores_the_branch(): void
    {
        $data = Branch::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.branches.store'), $data);

        $this->assertDatabaseHas('branches', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_branch(): void
    {
        $branch = Branch::factory()->create();

        $client = Client::factory()->create();

        $data = [
            'branch_name' => $this->faker->name(),
            'client_id' => $client->id,
        ];

        $response = $this->putJson(
            route('api.branches.update', $branch),
            $data
        );

        $data['id'] = $branch->id;

        $this->assertDatabaseHas('branches', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_branch(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->deleteJson(route('api.branches.destroy', $branch));

        $this->assertModelMissing($branch);

        $response->assertNoContent();
    }
}
