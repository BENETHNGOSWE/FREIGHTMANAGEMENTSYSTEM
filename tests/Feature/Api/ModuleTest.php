<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Module;

use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleTest extends TestCase
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
    public function it_gets_modules_list(): void
    {
        $modules = Module::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.modules.index'));

        $response->assertOk()->assertSee($modules[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_module(): void
    {
        $data = Module::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.modules.store'), $data);

        $this->assertDatabaseHas('modules', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_module(): void
    {
        $module = Module::factory()->create();

        $client = Client::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'features' => $this->faker->text(),
            'client_id' => $client->id,
        ];

        $response = $this->putJson(route('api.modules.update', $module), $data);

        $data['id'] = $module->id;

        $this->assertDatabaseHas('modules', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_module(): void
    {
        $module = Module::factory()->create();

        $response = $this->deleteJson(route('api.modules.destroy', $module));

        $this->assertModelMissing($module);

        $response->assertNoContent();
    }
}
