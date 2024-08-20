<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Module;
use App\Models\Package;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModulePackagesTest extends TestCase
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
    public function it_gets_module_packages(): void
    {
        $module = Module::factory()->create();
        $package = Package::factory()->create();

        $module->packages()->attach($package);

        $response = $this->getJson(
            route('api.modules.packages.index', $module)
        );

        $response->assertOk()->assertSee($package->name);
    }

    /**
     * @test
     */
    public function it_can_attach_packages_to_module(): void
    {
        $module = Module::factory()->create();
        $package = Package::factory()->create();

        $response = $this->postJson(
            route('api.modules.packages.store', [$module, $package])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $module
                ->packages()
                ->where('packages.id', $package->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_packages_from_module(): void
    {
        $module = Module::factory()->create();
        $package = Package::factory()->create();

        $response = $this->deleteJson(
            route('api.modules.packages.store', [$module, $package])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $module
                ->packages()
                ->where('packages.id', $package->id)
                ->exists()
        );
    }
}
