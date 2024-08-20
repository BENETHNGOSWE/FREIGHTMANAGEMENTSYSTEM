<?php

namespace Database\Seeders;

use App\Models\ClientAdmin;
use Illuminate\Database\Seeder;

class ClientAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClientAdmin::factory()
            ->count(5)
            ->create();
    }
}
