<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StaffType;

class StafftypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stafftypes = [
            ['staff_type_name' => 'receptionist', 'staff_type_description' => 'Receptionist', 'is_system' => true, 'is_admin' => false],
            ['staff_type_name' => 'operator', 'staff_type_description' => 'staff', 'is_system' => true, 'is_admin' => false],
            ['staff_type_name'=>'manager','staff_type_description'=>'Manager','is_system'=>true,'is_admin'=>true],
            ['staff_type_name'=>'admin','staff_type_description'=>'Admin','is_system'=>true,'is_admin'=>true]
            // ['staff_type_name' => 'student', 'staff_type_description' => 'Student', 'is_system' => true, 'is_admin' => false],
            // ['staff_type_name' => 'parent', 'staff_type_description' => 'Parent', 'is_system' => true, 'is_admin' => false],
            // ['staff_type_name' => 'lab_technician', 'staff_type_description' => 'Lab Technician', 'is_system' => true, 'is_admin' => false],
            // ['staff_type_name' => 'accountant', 'staff_type_description' => 'Accountant', 'is_system' => true, 'is_admin' => false],
            // ['staff_type_name' => 'hr', 'staff_type_description' => 'Human Resources', 'is_system' => true, 'is_admin' => false],
            // ['staff_type_name' => 'procurement', 'staff_type_description' => 'Procurement Officer',  'is_system'=>true, 	'is_admin'=>false],
            // ['staff_type_name'=>'storekeeper','staff_type_description'=>'Store Keeper','is_system'=>true,'is_admin'=>false],
            
        ];
        
        foreach ($stafftypes as $type) {
            Stafftype::create($type);
        }
    }
}
