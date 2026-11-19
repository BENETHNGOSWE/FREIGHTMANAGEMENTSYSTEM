<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/** Platform-wide key/value settings (grace days, enforcement switch). */
class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value', 'updated_by'];

    public static function get(string $key, $default = null)
    {
        if (! Schema::hasTable('platform_settings')) {
            return $default;
        }

        return Cache::rememberForever('platform_setting_' . $key, function () use ($key, $default) {
            $row = self::where('key', $key)->first();

            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        if (! Schema::hasTable('platform_settings')) {
            return;
        }

        Cache::forget('platform_setting_' . $key);

        self::updateOrCreate(['key' => $key], ['value' => $value, 'updated_by' => auth()->id()]);
    }
}
