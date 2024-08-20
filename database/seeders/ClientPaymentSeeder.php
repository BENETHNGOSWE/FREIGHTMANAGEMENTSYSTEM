<?php

namespace Database\Seeders;

use App\Models\ClientPayment;
use Illuminate\Database\Seeder;

class ClientPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClientPayment::factory()
            ->count(5)
            ->create();
    }
}
