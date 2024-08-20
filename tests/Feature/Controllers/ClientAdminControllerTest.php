<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ClientAdmin;

use App\Models\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientAdminControllerTest extends TestCase
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
    public function it_displays_index_view_with_client_admins(): void
    {
        $clientAdmins = ClientAdmin::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('client-admins.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.client_admins.index')
            ->assertViewHas('clientAdmins');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_client_admin(): void
    {
        $response = $this->get(route('client-admins.create'));

        $response->assertOk()->assertViewIs('app.client_admins.create');
    }

    /**
     * @test
     */
    public function it_stores_the_client_admin(): void
    {
        $data = ClientAdmin::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('client-admins.store'), $data);

        $this->assertDatabaseHas('client_admins', $data);

        $clientAdmin = ClientAdmin::latest('id')->first();

        $response->assertRedirect(route('client-admins.edit', $clientAdmin));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_client_admin(): void
    {
        $clientAdmin = ClientAdmin::factory()->create();

        $response = $this->get(route('client-admins.show', $clientAdmin));

        $response
            ->assertOk()
            ->assertViewIs('app.client_admins.show')
            ->assertViewHas('clientAdmin');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_client_admin(): void
    {
        $clientAdmin = ClientAdmin::factory()->create();

        $response = $this->get(route('client-admins.edit', $clientAdmin));

        $response
            ->assertOk()
            ->assertViewIs('app.client_admins.edit')
            ->assertViewHas('clientAdmin');
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

        $response = $this->put(
            route('client-admins.update', $clientAdmin),
            $data
        );

        $data['id'] = $clientAdmin->id;

        $this->assertDatabaseHas('client_admins', $data);

        $response->assertRedirect(route('client-admins.edit', $clientAdmin));
    }

    /**
     * @test
     */
    public function it_deletes_the_client_admin(): void
    {
        $clientAdmin = ClientAdmin::factory()->create();

        $response = $this->delete(route('client-admins.destroy', $clientAdmin));

        $response->assertRedirect(route('client-admins.index'));

        $this->assertModelMissing($clientAdmin);
    }
}
