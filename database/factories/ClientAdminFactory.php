<?php

namespace Database\Factories;

use App\Models\ClientAdmin;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ClientAdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientAdmin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName,    
            'middlename' => $this->faker->firstName,   
            'lastname' => $this->faker->lastName,     
            'username' => $this->faker->userName,      
            'email' => $this->faker->unique()->safeEmail,  
            'phone' => $this->faker->phoneNumber,      
            'password' => Hash::make('password'),     
            'user_id' => \App\Models\User::factory(),  
            'client_id' => \App\Models\Client::factory(),  
            'created_at' => now(),                   
            'updated_at' => now(),                     
        ];
    }
}
