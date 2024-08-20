<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ClientPayment;

use App\Models\PaymentType;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientPaymentTest extends TestCase
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
    public function it_gets_client_payments_list(): void
    {
        $clientPayments = ClientPayment::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.client-payments.index'));

        $response->assertOk()->assertSee($clientPayments[0]->date);
    }

    /**
     * @test
     */
    public function it_stores_the_client_payment(): void
    {
        $data = ClientPayment::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.client-payments.store'), $data);

        $this->assertDatabaseHas('client_payments', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.client-payments.update', $clientPayment),
            $data
        );

        $data['id'] = $clientPayment->id;

        $this->assertDatabaseHas('client_payments', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_client_payment(): void
    {
        $clientPayment = ClientPayment::factory()->create();

        $response = $this->deleteJson(
            route('api.client-payments.destroy', $clientPayment)
        );

        $this->assertModelMissing($clientPayment);

        $response->assertNoContent();
    }
}
