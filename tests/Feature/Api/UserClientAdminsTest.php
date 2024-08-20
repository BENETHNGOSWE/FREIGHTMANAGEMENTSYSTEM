<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ClientAdmin;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserClientAdminsTest extends TestCase
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
    public function it_gets_user_client_admins(): void
    {
        $user = User::factory()->create();
        $clientAdmins = ClientAdmin::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(
            route('api.users.client-admins.index', $user)
        );

        $response->assertOk()->assertSee($clientAdmins[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_user_client_admins(): void
    {
        $user = User::factory()->create();
        $data = ClientAdmin::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.client-admins.store', $user),
            $data
        );

        $this->assertDatabaseHas('client_admins', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $clientAdmin = ClientAdmin::latest('id')->first();

        $this->assertEquals($user->id, $clientAdmin->user_id);
    }
}
