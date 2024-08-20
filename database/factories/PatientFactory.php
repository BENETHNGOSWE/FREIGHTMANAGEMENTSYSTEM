<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patientID' => $this->faker->text(255),
            'has_insurance' => $this->faker->boolean(),
            'city' => $this->faker->city(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
