<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\SubscriptionInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriptionInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomNumber(1),
            'balance' => $this->faker->randomNumber(1),
            'bill_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'status' => '0',
            'subscription_id' => \App\Models\Subscription::factory(),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
