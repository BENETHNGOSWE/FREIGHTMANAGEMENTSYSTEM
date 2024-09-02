<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('client_types')->insert([
            ['name' => 'Individual'],
            ['name' => 'Company'],
            ['name' => 'Non-Profit'],
            ['name' => 'Government'],
            ['name' => 'Educational'],
        ]);
    }
}
