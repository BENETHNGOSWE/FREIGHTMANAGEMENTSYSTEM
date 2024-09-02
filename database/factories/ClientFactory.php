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
            'client_name' => $this->faker->company,
            'client_logo' => $this->faker->imageUrl(), // Or use text if it's a string
            'client_location' => $this->faker->address,
            'client_address_one' => $this->faker->secondaryAddress,
            'client_address_two' => $this->faker->secondaryAddress,
            'client_tinnumber' => $this->faker->numerify('TIN#######'),
            'client_phonenumber' => $this->faker->phoneNumber,
            'client_email' => $this->faker->unique()->safeEmail,
            'client_city' => $this->faker->city,
            'client_country' => $this->faker->country,
            'client_zipcode' => $this->faker->postcode,
            'client_fax' => $this->faker->phoneNumber,
            'client_website_link' => $this->faker->url,
            'client_type_id' => \App\Models\ClientType::factory(),
        ];
    }
}
