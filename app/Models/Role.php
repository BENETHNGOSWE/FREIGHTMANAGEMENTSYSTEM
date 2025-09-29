<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'guard_name', 'client_id'];

    protected $searchableFields = ['*'];

    public static function findByNameForClient(string $name, $clientId = null, $guardName = null)
    {
        $guardName = $guardName ?? config('auth.defaults.guard');

        $query = static::where('name', $name)
            ->where('guard_name', $guardName)
            ->where(function ($q) use ($clientId) {
                $q->whereNull('client_id');       // Global role
                if ($clientId !== null) {
                    $q->orWhere('client_id', $clientId);  // Client-specific role
                }
            });

        return $query->firstOrFail();
    }

    // Rename this method to avoid conflict with Spatie's users() method
    public function roleUsers()
    {
        return $this->hasMany(User::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }
}