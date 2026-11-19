<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('email', 'owner@fms.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'owner@fms.com',
                'password' => Hash::make('12345678'),
                'client_id' => null,
                'user_type' => 'owner',
            ]);
        }

        $this->call([
            PermissionsSeeder::class,
            StafftypeSeeder::class,
            ClientTypeSeeder::class,
            PlatformSeeder::class,
        ]);
    }
}
