<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'monthly_fee' => $this->faker->randomNumber(2),
            'yearly_fee' => $this->faker->randomNumber(2),
            'features' => $this->faker->sentence(15),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
