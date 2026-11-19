<?php

namespace App\Services\Platform;

/**
 * Builds the menu for the person who is logged in.
 *
 * Two filters, in this order:
 *   1. module  - did the client's package include this module? (ModuleAccess)
 *   2. permission - does the role have the permission? (spatie, unchanged)
 * The owner sees the platform menu instead of a client menu.
 */
class SidebarService
{
    public function __construct(private ModuleAccess $access)
    {
    }

    public function menu($user = null): array
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return [];
        }

        // The platform owner signs in to manage clients, packages and subscriptions,
        // never to look at one client's cargo.
        if ($this->access->isOwner($user)) {
            return $this->ownerMenu();
        }

        return $this->clientMenu($user);
    }

    private function clientMenu($user): array
    {
        $menu = config('sidebarmenu', []);
        $modules = $this->access->modulesForClient($user->client_id);

        return $this->filter($menu, $user, $modules);
    }

    private function ownerMenu(): array
    {
        return [
            ['title' => 'Platform', 'icon' => 'flaticon-381-networking', 'route' => 'home', 'permission' => null],
            [
                'title' => 'Clients', 'icon' => 'fa fa-building', 'permission' => null,
                'children' => [
                    ['title' => 'All clients', 'route' => 'list.clients', 'permission' => null],
                    ['title' => 'Add client', 'route' => 'create.clients', 'permission' => null],
                    ['title' => 'Demo requests', 'route' => 'owner.list.demorequest', 'permission' => null],
                ],
            ],
            [
                'title' => 'Subscriptions', 'icon' => 'fa fa-credit-card', 'permission' => null,
                'children' => [
                    ['title' => 'All subscriptions', 'route' => 'list.subscriptions', 'permission' => null],
                    ['title' => 'New subscription', 'route' => 'create.subscriptions', 'permission' => null],
                    ['title' => 'Unlock tokens', 'route' => 'platform.tokens', 'permission' => null],
                    ['title' => 'Platform settings', 'route' => 'platform.settings', 'permission' => null],
                ],
            ],
            [
                'title' => 'Products', 'icon' => 'fa fa-box', 'permission' => null,
                'children' => [
                    ['title' => 'Packages', 'route' => 'owner.list.package', 'permission' => null],
                    ['title' => 'Modules', 'route' => 'platform.modules', 'permission' => null],
                ],
            ],
            [
                'title' => 'Access', 'icon' => 'fa fa-key', 'permission' => null,
                'children' => [
                    ['title' => 'Users', 'route' => 'users.index', 'permission' => null],
                    ['title' => 'Roles', 'route' => 'roles.index', 'permission' => null],
                    ['title' => 'Permissions', 'route' => 'permissions.index', 'permission' => null],
                ],
            ],
            // the owner only has staff-profile screens, so let the old screen handle it
            ['title' => 'My profile', 'icon' => 'fa fa-user', 'route' => 'admin.list.profile', 'permission' => null],
        ];
    }

    /** The only menu a locked / expired client may see: their own account. */
    public function accountMenu($user = null): array
    {
        $user = $user ?: auth()->user();

        return array_values(array_filter(config('sidebarmenu', []), function ($item) use ($user) {
            if (! in_array($item['title'] ?? '', ['Manage Profile', 'My profile', 'Settings'], true)) {
                return false;
            }

            return empty($item['permission']) || ($user && $user->can($item['permission']));
        }));
    }

    /** Keep only what the package includes AND the role may use. */
    private function filter(array $items, $user, array $modules): array
    {
        $kept = [];

        foreach ($items as $item) {
            // platform-only sections never belong in a client menu
            if (! empty($item['owner_only'])) {
                continue;
            }

            $moduleOk = $this->access->allows($item['module'] ?? null, $user);
            $permissionOk = empty($item['permission']) || $user->can($item['permission']);

            if (! empty($item['children'])) {
                $children = [];

                foreach ($item['children'] as $child) {
                    if (! empty($child['owner_only'])) {
                        continue;
                    }

                    if (! $this->access->allows($child['module'] ?? ($item['module'] ?? null), $user)) {
                        continue;
                    }

                    if (! empty($child['permission']) && ! $user->can($child['permission'])) {
                        continue;
                    }

                    $children[] = $child;
                }

                if (empty($children)) {
                    continue;
                }

                $item['children'] = $children;
                $kept[] = $item;
                continue;
            }

            if ($moduleOk && $permissionOk) {
                $kept[] = $item;
            }
        }

        return $kept;
    }
}
