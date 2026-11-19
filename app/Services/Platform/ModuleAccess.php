<?php

namespace App\Services\Platform;

use App\Models\Client;
use App\Models\Kodapackage;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Which modules a client bought, and whether the sidebar / a route may show them.
 *
 * Access is the intersection of two things:
 *   1. what the client's current subscription package includes (package_modules),
 *   2. the role permissions (spatie), which are unchanged.
 * The owner role sees everything; that is the platform side of the product.
 */
class ModuleAccess
{
    private array $cache = [];

    public function isOwner($user = null): bool
    {
        $user = $user ?: auth()->user();

        return $user && $user->hasRole('owner');
    }

    /** Module keys included in the client's current package. */
    public function modulesForClient(?int $clientId): array
    {
        if (! $clientId) {
            return [];
        }

        if (array_key_exists($clientId, $this->cache)) {
            return $this->cache[$clientId];
        }

        if (! Schema::hasTable('package_modules')) {
            return $this->cache[$clientId] = ProductCatalog::allKeys();
        }

        $subscription = app(SubscriptionService::class)->current($clientId);

        // No subscription on file: keep the client working with the full product
        // (grandfathered, same rule as SubscriptionService::isUsable).
        if (! $subscription) {
            if (! app(SubscriptionService::class)->isUsable(Client::find($clientId))) {
                return $this->cache[$clientId] = [];   // locked: no modules at all
            }

            return $this->cache[$clientId] = ProductCatalog::modulesFor(
                Client::whereKey($clientId)->value('account_type') ?: 'freight'
            );
        }

        $keys = DB::table('package_modules')->where('package_id', $subscription->package_id)->pluck('module_key')->all();

        // A package with nothing configured behaves like the old system: everything the
        // product allows, so existing clients never lose screens after the upgrade.
        if (empty($keys)) {
            $product = $subscription->product ?: 'freight';
            $keys = ProductCatalog::modulesFor($product);
        }

        return $this->cache[$clientId] = $keys;
    }

    public function allows(?string $module, $user = null): bool
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return false;
        }

        if ($this->isOwner($user)) {
            return true;
        }

        if ($module === null || $module === '') {
            return true;
        }

        return in_array($module, $this->modulesForClient($user->client_id), true);
    }

    public function packageSummary(?int $packageId): array
    {
        if (! $packageId || ! Schema::hasTable('package_modules')) {
            return [];
        }

        return DB::table('package_modules')->where('package_id', $packageId)->pluck('module_key')->all();
    }

    public function syncPackage(int $packageId, array $moduleKeys): void
    {
        if (! Schema::hasTable('package_modules')) {
            return;
        }

        DB::table('package_modules')->where('package_id', $packageId)->delete();

        foreach (array_unique($moduleKeys) as $key) {
            if (isset(ProductCatalog::MODULES[$key])) {
                DB::table('package_modules')->insert([
                    'package_id' => $packageId,
                    'module_key' => $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /** Products a client is allowed to see on their own screens. */
    public function productOf(?Subscription $subscription): string
    {
        if (! $subscription) {
            return 'freight';
        }

        $product = $subscription->product ?: 'freight';

        return isset(ProductCatalog::PRODUCTS[$product]) ? $product : 'freight';
    }
}
