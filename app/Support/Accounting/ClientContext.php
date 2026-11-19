<?php

namespace App\Support\Accounting;

use App\Models\Client;

/**
 * Every accounting table is tenant-scoped by `client_id`, exactly like the rest of the app.
 */
class ClientContext
{
    public static function id(): int
    {
        $user = auth()->user();

        if ($user && $user->client_id) {
            return (int) $user->client_id;
        }

        // Platform staff (owner / admin without a client) may look at one client at a time.
        if ($user && ($user->hasRole('owner') || $user->hasRole('admin'))) {
            $requested = request()->integer('client_id');
            if ($requested && Client::whereKey($requested)->exists()) {
                return $requested;
            }

            $first = Client::orderBy('id')->value('id');
            if ($first) {
                return (int) $first;
            }
        }

        abort(403, 'Your user account is not linked to a client, so it has no books.');
    }

    public static function client()
    {
        return Client::find(self::id());
    }
}
