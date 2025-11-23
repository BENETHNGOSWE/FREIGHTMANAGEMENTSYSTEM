<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['client_id', 'key', 'value', 'updated_by'];

    /**
     * Get a setting value for specific client with caching
     */
    public static function get($key, $clientId, $default = null)
    {
        return Cache::remember("system_setting_{$clientId}_{$key}", 3600, function () use ($key, $clientId, $default) {
            $setting = self::where('client_id', $clientId)
                          ->where('key', $key)
                          ->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value for specific client and clear cache
     */
    public static function set($key, $value, $clientId, $userId = null)
    {
        Cache::forget("system_setting_{$clientId}_{$key}");
        
        return self::updateOrCreate(
            [
                'client_id' => $clientId,
                'key' => $key
            ],
            [
                'value' => $value,
                'updated_by' => $userId ?? auth()->id()
            ]
        );
    }

    /**
     * Check if notifications are enabled for specific client
     */
    public static function notificationsEnabled($clientId)
    {
        return self::get('send_load_notifications', $clientId, '1') === '1';
    }

    /**
     * Get who last updated the setting
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship to client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
