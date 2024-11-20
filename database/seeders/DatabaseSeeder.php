<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Adding an admin user
        $user = \App\Models\User::factory()
            ->count(1)
            ->create([
                'name' => 'Admin',
                'email' => 'owner@fms.com',
                'password' => Hash::make('12345678'),
                'client_id' => null,
                'user_type' => 'owner',
            ]);
        $this->call(PermissionsSeeder::class);

        // $this->call(BankSeeder::class);
        // $this->call(BranchSeeder::class);
        // $this->call(ClientSeeder::class);
        // $this->call(ClientAdminSeeder::class);
        // $this->call(ClientPaymentSeeder::class);
        // $this->call(ClientTypeSeeder::class);
        $this->call(CountrySeeder::class);
        // $this->call(MessageSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(PackageSeeder::class);
        // $this->call(PatientSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        // $this->call(RoleSeeder::class);
        // $this->call(SubscriptionSeeder::class);
        // $this->call(SubscriptionInvoiceSeeder::class);
        // $this->call(SubscriptionTokenSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(StafftypeSeeder::class);
    }
}
