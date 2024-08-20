<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Module;
use App\Models\Package;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PackageModulesTest extends TestCase
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
    public function it_gets_package_modules(): void
    {
        $package = Package::factory()->create();
        $module = Module::factory()->create();

        $package->modules()->attach($module);

        $response = $this->getJson(
            route('api.packages.modules.index', $package)
        );

        $response->assertOk()->assertSee($module->name);
    }

    /**
     * @test
     */
    public function it_can_attach_modules_to_package(): void
    {
        $package = Package::factory()->create();
        $module = Module::factory()->create();

        $response = $this->postJson(
            route('api.packages.modules.store', [$package, $module])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $package
                ->modules()
                ->where('modules.id', $module->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_modules_from_package(): void
    {
        $package = Package::factory()->create();
        $module = Module::factory()->create();

        $response = $this->deleteJson(
            route('api.packages.modules.store', [$package, $module])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $package
                ->modules()
                ->where('modules.id', $module->id)
                ->exists()
        );
    }
}
