<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;


class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail, // Use safeEmail to ensure it's valid
            'phone' => $this->faker->phoneNumber, // Nullable, so this can be left out if desired
            'photo' => null, // Default to null, or use $this->faker->imageUrl() if you want a placeholder
            'email_verified_at' => now(), // Automatically verify for seeding
            'password' => Hash::make('password'), // Using Hash facade to hash the password
            'remember_token' => Str::random(10), // Random string for remember token
            'client_id' => \App\Models\Client::factory(), // Link to a Client model
            'user_type' => 'smallclient', // Default value as per your schema
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
