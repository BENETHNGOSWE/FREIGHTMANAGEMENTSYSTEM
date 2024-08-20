<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ClientType;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTypeControllerTest extends TestCase
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
    public function it_displays_index_view_with_client_types(): void
    {
        $clientTypes = ClientType::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('client-types.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.client_types.index')
            ->assertViewHas('clientTypes');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_client_type(): void
    {
        $response = $this->get(route('client-types.create'));

        $response->assertOk()->assertViewIs('app.client_types.create');
    }

    /**
     * @test
     */
    public function it_stores_the_client_type(): void
    {
        $data = ClientType::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('client-types.store'), $data);

        $this->assertDatabaseHas('client_types', $data);

        $clientType = ClientType::latest('id')->first();

        $response->assertRedirect(route('client-types.edit', $clientType));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_client_type(): void
    {
        $clientType = ClientType::factory()->create();

        $response = $this->get(route('client-types.show', $clientType));

        $response
            ->assertOk()
            ->assertViewIs('app.client_types.show')
            ->assertViewHas('clientType');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_client_type(): void
    {
        $clientType = ClientType::factory()->create();

        $response = $this->get(route('client-types.edit', $clientType));

        $response
            ->assertOk()
            ->assertViewIs('app.client_types.edit')
            ->assertViewHas('clientType');
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

        $response = $this->put(
            route('client-types.update', $clientType),
            $data
        );

        $data['id'] = $clientType->id;

        $this->assertDatabaseHas('client_types', $data);

        $response->assertRedirect(route('client-types.edit', $clientType));
    }

    /**
     * @test
     */
    public function it_deletes_the_client_type(): void
    {
        $clientType = ClientType::factory()->create();

        $response = $this->delete(route('client-types.destroy', $clientType));

        $response->assertRedirect(route('client-types.index'));

        $this->assertModelMissing($clientType);
    }
}
