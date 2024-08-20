<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\UserRole;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUserRolesTest extends TestCase
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
    public function it_gets_user_user_roles(): void
    {
        $user = User::factory()->create();
        $userRoles = UserRole::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(route('api.users.user-roles.index', $user));

        $response->assertOk()->assertSee($userRoles[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_user_user_roles(): void
    {
        $user = User::factory()->create();
        $data = UserRole::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.user-roles.store', $user),
            $data
        );

        $this->assertDatabaseHas('user_roles', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $userRole = UserRole::latest('id')->first();

        $this->assertEquals($user->id, $userRole->user_id);
    }
}
