<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Branch;

use App\Models\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchControllerTest extends TestCase
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
    public function it_displays_index_view_with_branches(): void
    {
        $branches = Branch::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('branches.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.branches.index')
            ->assertViewHas('branches');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_branch(): void
    {
        $response = $this->get(route('branches.create'));

        $response->assertOk()->assertViewIs('app.branches.create');
    }

    /**
     * @test
     */
    public function it_stores_the_branch(): void
    {
        $data = Branch::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('branches.store'), $data);

        $this->assertDatabaseHas('branches', $data);

        $branch = Branch::latest('id')->first();

        $response->assertRedirect(route('branches.edit', $branch));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_branch(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->get(route('branches.show', $branch));

        $response
            ->assertOk()
            ->assertViewIs('app.branches.show')
            ->assertViewHas('branch');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_branch(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->get(route('branches.edit', $branch));

        $response
            ->assertOk()
            ->assertViewIs('app.branches.edit')
            ->assertViewHas('branch');
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

        $response = $this->put(route('branches.update', $branch), $data);

        $data['id'] = $branch->id;

        $this->assertDatabaseHas('branches', $data);

        $response->assertRedirect(route('branches.edit', $branch));
    }

    /**
     * @test
     */
    public function it_deletes_the_branch(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->delete(route('branches.destroy', $branch));

        $response->assertRedirect(route('branches.index'));

        $this->assertModelMissing($branch);
    }
}
