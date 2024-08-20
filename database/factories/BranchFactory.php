<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_name' => $this->faker->name(),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
