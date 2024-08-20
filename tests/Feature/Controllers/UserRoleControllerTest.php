<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\UserRole;

use App\Models\Role;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRoleControllerTest extends TestCase
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
    public function it_displays_index_view_with_user_roles(): void
    {
        $userRoles = UserRole::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('user-roles.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.user_roles.index')
            ->assertViewHas('userRoles');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_user_role(): void
    {
        $response = $this->get(route('user-roles.create'));

        $response->assertOk()->assertViewIs('app.user_roles.create');
    }

    /**
     * @test
     */
    public function it_stores_the_user_role(): void
    {
        $data = UserRole::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('user-roles.store'), $data);

        $this->assertDatabaseHas('user_roles', $data);

        $userRole = UserRole::latest('id')->first();

        $response->assertRedirect(route('user-roles.edit', $userRole));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_user_role(): void
    {
        $userRole = UserRole::factory()->create();

        $response = $this->get(route('user-roles.show', $userRole));

        $response
            ->assertOk()
            ->assertViewIs('app.user_roles.show')
            ->assertViewHas('userRole');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_user_role(): void
    {
        $userRole = UserRole::factory()->create();

        $response = $this->get(route('user-roles.edit', $userRole));

        $response
            ->assertOk()
            ->assertViewIs('app.user_roles.edit')
            ->assertViewHas('userRole');
    }

    /**
     * @test
     */
    public function it_updates_the_user_role(): void
    {
        $userRole = UserRole::factory()->create();

        $user = User::factory()->create();
        $role = Role::factory()->create();

        $data = [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ];

        $response = $this->put(route('user-roles.update', $userRole), $data);

        $data['id'] = $userRole->id;

        $this->assertDatabaseHas('user_roles', $data);

        $response->assertRedirect(route('user-roles.edit', $userRole));
    }

    /**
     * @test
     */
    public function it_deletes_the_user_role(): void
    {
        $userRole = UserRole::factory()->create();

        $response = $this->delete(route('user-roles.destroy', $userRole));

        $response->assertRedirect(route('user-roles.index'));

        $this->assertModelMissing($userRole);
    }
}
