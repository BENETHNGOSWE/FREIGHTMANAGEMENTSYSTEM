<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all client IDs
        $clientIds = DB::table('clients')->pluck('id')->toArray();

        // Create container records for each client
        foreach ($clientIds as $clientId) {
            DB::table('containers')->insert([
                [
                    'client_id' => $clientId,
                    'container_number' => 'AIRBOX-CONTAINER' . rand(100, 999),
                    'container_size' => '0000',
                    'container_volume' => '0000',
                    'container_status' => 'closed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
