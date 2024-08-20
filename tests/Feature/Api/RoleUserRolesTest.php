<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleUserRolesTest extends TestCase
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
    public function it_gets_role_user_roles(): void
    {
        $role = Role::factory()->create();
        $userRoles = UserRole::factory()
            ->count(2)
            ->create([
                'role_id' => $role->id,
            ]);

        $response = $this->getJson(route('api.roles.user-roles.index', $role));

        $response->assertOk()->assertSee($userRoles[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_role_user_roles(): void
    {
        $role = Role::factory()->create();
        $data = UserRole::factory()
            ->make([
                'role_id' => $role->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.roles.user-roles.store', $role),
            $data
        );

        $this->assertDatabaseHas('user_roles', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $userRole = UserRole::latest('id')->first();

        $this->assertEquals($role->id, $userRole->role_id);
    }
}
