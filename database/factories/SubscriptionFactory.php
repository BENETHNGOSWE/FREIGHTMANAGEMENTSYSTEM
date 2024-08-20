<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_date' => $this->faker->date(),
            'expire_date' => $this->faker->date(),
            'status' => '0',
            'client_id' => \App\Models\Client::factory(),
            'package_id' => \App\Models\Package::factory(),
        ];
    }
}
