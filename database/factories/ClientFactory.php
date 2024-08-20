<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'logo' => $this->faker->text(255),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'registration_number' => $this->faker->text(255),
            'subdomain' => $this->faker->text(255),
            'country_id' => \App\Models\Country::factory(),
            'client_type_id' => \App\Models\ClientType::factory(),
        ];
    }
}
