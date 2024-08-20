<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ClientPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomNumber(1),
            'date' => $this->faker->date(),
            'payment_type_id' => \App\Models\PaymentType::factory(),
        ];
    }
}
