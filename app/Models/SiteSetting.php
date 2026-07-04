<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    /**
     * Récupère une valeur par clé
     */
    public static function get(string $key, string $default = ''): string
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? ($setting->value ?? $default) : $default;
        });
    }

    /**
     * Met à jour une valeur et vide le cache
     */
    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * Récupère toutes les settings groupées
     */
    public static function getAllGrouped(): array
    {
        return static::all()->groupBy('group')->toArray();
    }

    /**
     * Retourne l'URL WhatsApp complète
     */
    public static function whatsappUrl(string $message = ''): string
    {
        $number = static::get('whatsapp', '22663952032');
        $url = "https://wa.me/{$number}";
        if ($message) {
            $url .= '?text=' . urlencode($message);
        }
        return $url;
    }
}