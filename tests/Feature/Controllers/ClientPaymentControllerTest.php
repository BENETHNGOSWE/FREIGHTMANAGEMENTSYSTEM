<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ClientPayment;

use App\Models\PaymentType;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientPaymentControllerTest extends TestCase
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
    public function it_displays_index_view_with_client_payments(): void
    {
        $clientPayments = ClientPayment::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('client-payments.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.client_payments.index')
            ->assertViewHas('clientPayments');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_client_payment(): void
    {
        $response = $this->get(route('client-payments.create'));

        $response->assertOk()->assertViewIs('app.client_payments.create');
    }

    /**
     * @test
     */
    public function it_stores_the_client_payment(): void
    {
        $data = ClientPayment::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('client-payments.store'), $data);

        $this->assertDatabaseHas('client_payments', $data);

        $clientPayment = ClientPayment::latest('id')->first();

        $response->assertRedirect(
            route('client-payments.edit', $clientPayment)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_client_payment(): void
    {
        $clientPayment = ClientPayment::factory()->create();

        $response = $this->get(route('client-payments.show', $clientPayment));

        $response
            ->assertOk()
            ->assertViewIs('app.client_payments.show')
            ->assertViewHas('clientPayment');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_client_payment(): void
    {
        $clientPayment = ClientPayment::factory()->create();

        $response = $this->get(route('client-payments.edit', $clientPayment));

        $response
            ->assertOk()
            ->assertViewIs('app.client_payments.edit')
            ->assertViewHas('clientPayment');
    }

    /**
     * @test
     */
    public function it_updates_the_client_payment(): void
    {
        $clientPayment = ClientPayment::factory()->create();

        $paymentType = PaymentType::factory()->create();

        $data = [
            'amount' => $this->faker->randomNumber(1),
            'date' => $this->faker->date(),
            'payment_type_id' => $paymentType->id,
        ];

        $response = $this->put(
            route('client-payments.update', $clientPayment),
            $data
        );

        $data['id'] = $clientPayment->id;

        $this->assertDatabaseHas('client_payments', $data);

        $response->assertRedirect(
            route('client-payments.edit', $clientPayment)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_client_payment(): void
    {
        $clientPayment = ClientPayment::factory()->create();

        $response = $this->delete(
            route('client-payments.destroy', $clientPayment)
        );

        $response->assertRedirect(route('client-payments.index'));

        $this->assertModelMissing($clientPayment);
    }
}
