<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Module;

use App\Models\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleControllerTest extends TestCase
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
    public function it_displays_index_view_with_modules(): void
    {
        $modules = Module::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('modules.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.modules.index')
            ->assertViewHas('modules');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_module(): void
    {
        $response = $this->get(route('modules.create'));

        $response->assertOk()->assertViewIs('app.modules.create');
    }

    /**
     * @test
     */
    public function it_stores_the_module(): void
    {
        $data = Module::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('modules.store'), $data);

        $this->assertDatabaseHas('modules', $data);

        $module = Module::latest('id')->first();

        $response->assertRedirect(route('modules.edit', $module));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_module(): void
    {
        $module = Module::factory()->create();

        $response = $this->get(route('modules.show', $module));

        $response
            ->assertOk()
            ->assertViewIs('app.modules.show')
            ->assertViewHas('module');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_module(): void
    {
        $module = Module::factory()->create();

        $response = $this->get(route('modules.edit', $module));

        $response
            ->assertOk()
            ->assertViewIs('app.modules.edit')
            ->assertViewHas('module');
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

        $response = $this->put(route('modules.update', $module), $data);

        $data['id'] = $module->id;

        $this->assertDatabaseHas('modules', $data);

        $response->assertRedirect(route('modules.edit', $module));
    }

    /**
     * @test
     */
    public function it_deletes_the_module(): void
    {
        $module = Module::factory()->create();

        $response = $this->delete(route('modules.destroy', $module));

        $response->assertRedirect(route('modules.index'));

        $this->assertModelMissing($module);
    }
}
