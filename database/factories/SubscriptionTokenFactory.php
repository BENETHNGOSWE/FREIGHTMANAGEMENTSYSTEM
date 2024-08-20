<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\SubscriptionToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriptionToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token_' => $this->faker->text(255),
            'expire_date' => $this->faker->date(),
            'status' => $this->faker->boolean(),
            'subscription_id' => \App\Models\Subscription::factory(),
            'subscription_invoice_id' => \App\Models\SubscriptionInvoice::factory(),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
