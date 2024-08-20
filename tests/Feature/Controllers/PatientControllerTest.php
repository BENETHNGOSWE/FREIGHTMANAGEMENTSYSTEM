<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Patient;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientControllerTest extends TestCase
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
    public function it_displays_index_view_with_patients(): void
    {
        $patients = Patient::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('patients.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.patients.index')
            ->assertViewHas('patients');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_patient(): void
    {
        $response = $this->get(route('patients.create'));

        $response->assertOk()->assertViewIs('app.patients.create');
    }

    /**
     * @test
     */
    public function it_stores_the_patient(): void
    {
        $data = Patient::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('patients.store'), $data);

        $this->assertDatabaseHas('patients', $data);

        $patient = Patient::latest('id')->first();

        $response->assertRedirect(route('patients.edit', $patient));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->get(route('patients.show', $patient));

        $response
            ->assertOk()
            ->assertViewIs('app.patients.show')
            ->assertViewHas('patient');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->get(route('patients.edit', $patient));

        $response
            ->assertOk()
            ->assertViewIs('app.patients.edit')
            ->assertViewHas('patient');
    }

    /**
     * @test
     */
    public function it_updates_the_patient(): void
    {
        $patient = Patient::factory()->create();

        $user = User::factory()->create();

        $data = [
            'patientID' => $this->faker->text(255),
            'has_insurance' => $this->faker->boolean(),
            'city' => $this->faker->city(),
            'user_id' => $user->id,
        ];

        $response = $this->put(route('patients.update', $patient), $data);

        $data['id'] = $patient->id;

        $this->assertDatabaseHas('patients', $data);

        $response->assertRedirect(route('patients.edit', $patient));
    }

    /**
     * @test
     */
    public function it_deletes_the_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->delete(route('patients.destroy', $patient));

        $response->assertRedirect(route('patients.index'));

        $this->assertModelMissing($patient);
    }
}
