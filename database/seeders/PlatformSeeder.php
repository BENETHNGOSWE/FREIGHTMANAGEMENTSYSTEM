<?php

namespace Database\Seeders;

use App\Models\Kodapackage;
use App\Services\Platform\ModuleAccess;
use App\Services\Platform\ProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The two packages this business sells, and the modules inside them.
 * Safe to re-run: it only creates what is missing.
 */
class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------------- module catalogue
        foreach (ProductCatalog::MODULES as $key => [$name, $group, $description]) {
            DB::table('platform_modules')->updateOrInsert(
                ['key' => $key],
                ['name' => $name, 'group' => $group, 'description' => $description, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // ---------------------------------------------------------------- packages
        // staff_type_id / client_admin_id are legacy NOT NULL columns; zero keeps the
        // packages platform-wide (the old per-staff-type split is not used any more).
        $freight = Kodapackage::firstOrCreate(
            ['name' => 'KodaFreight'],
            ['monthly_fee' => 50000, 'yearly_fee' => 500000, 'staff_type_id' => null, 'client_admin_id' => null]
        );

        $sms = Kodapackage::firstOrCreate(
            ['name' => 'KodaSMS'],
            ['monthly_fee' => 30000, 'yearly_fee' => 300000, 'staff_type_id' => null, 'client_admin_id' => null]
        );

        $both = Kodapackage::firstOrCreate(
            ['name' => 'KodaFreight + KodaSMS'],
            ['monthly_fee' => 70000, 'yearly_fee' => 700000, 'staff_type_id' => null, 'client_admin_id' => null]
        );

        // Leave the packages as a sensible starting point; the owner can narrow them later
        // under Products -> Modules. A package with nothing ticked = "everything the product allows", so a
        // client on this package is never blocked. The owner can narrow it under
        // Products -> Modules whenever they want.
        $access = app(ModuleAccess::class);
        foreach ([$freight, $sms, $both] as $package) {
            if (empty($access->packageSummary($package->id))) {
                $product = $package->name === 'KodaSMS' ? 'sms' : ($package->name === 'KodaFreight' ? 'freight' : 'both');
                $access->syncPackage($package->id, ProductCatalog::modulesFor($product));
            }
        }

        // ---------------------------------------------------------------- client types
        foreach (['Freight company', 'SMS / bulk messaging', 'Freight + SMS'] as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type]);
        }
    }
}
