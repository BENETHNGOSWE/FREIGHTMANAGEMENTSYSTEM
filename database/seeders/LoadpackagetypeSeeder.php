<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoadpackagetypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loadpackagetypes')->insert([
            ['load_type_name' => 'General Cargo'],
            ['load_type_name' => 'Perishable Goods'],
            ['load_type_name' => 'High-Value Goods'],
            ['load_type_name' => 'Bulk Cargo'],
            ['load_type_name' => 'Liquid Bulk Cargo'],
            ['load_type_name' => 'Refrigerated Cargo'],
            ['load_type_name' => 'Hazardous Goods'],
            ['load_type_name' => 'Oversized Cargo'],
            ['load_type_name' => 'Livestock'],
            ['load_type_name' => 'Automobiles'],
        ]);
    }
}
