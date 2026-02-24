<?php

namespace Modules\Settings\Services;

use Modules\Settings\Models\Setting;

class SettingsService
{
    protected static ?array $cache = null;

    public static function get(string $key, $default = null)
    {
        if (static::$cache === null) {
            static::loadCache();
        }
        return static::$cache[$key] ?? $default;
    }

    public static function set(string $key, $value, string $group = 'general'): void
    {
        Setting::setValue($key, $value, $group);
        if (static::$cache !== null) {
            static::$cache[$key] = $value;
        }
    }

    public static function loadCache(): void
    {
        try {
            static::$cache = Setting::pluck('value', 'key')->toArray();
        } catch (\Throwable $e) {
            static::$cache = [];
        }
    }

    public static function clearCache(): void
    {
        static::$cache = null;
    }

    /** Font key => label for dropdown. Keys are stored in DB (no quotes in HTML). */
    public static function allowedFontFamilies(): array
    {
        return [
            'system-ui' => 'System UI',
            'inter' => 'Inter',
            'roboto' => 'Roboto',
            'open-sans' => 'Open Sans',
            'lato' => 'Lato',
            'source-sans-3' => 'Source Sans 3',
            'nunito' => 'Nunito',
            'poppins' => 'Poppins',
            'merriweather' => 'Merriweather',
            'playfair-display' => 'Playfair Display',
            'dm-sans' => 'DM Sans',
            'work-sans' => 'Work Sans',
            'georgia' => 'Georgia',
        ];
    }

    /** Map font key to full CSS font-family value for :root. */
    public static function fontKeyToCss(string $key): string
    {
        $map = [
            'system-ui' => 'system-ui, sans-serif',
            'inter' => "'Inter', sans-serif",
            'roboto' => "'Roboto', sans-serif",
            'open-sans' => "'Open Sans', sans-serif",
            'lato' => "'Lato', sans-serif",
            'source-sans-3' => "'Source Sans 3', sans-serif",
            'nunito' => "'Nunito', sans-serif",
            'poppins' => "'Poppins', sans-serif",
            'merriweather' => "'Merriweather', serif",
            'playfair-display' => "'Playfair Display', serif",
            'dm-sans' => "'DM Sans', sans-serif",
            'work-sans' => "'Work Sans', sans-serif",
            'georgia' => 'Georgia, serif',
        ];
        return $map[$key] ?? 'system-ui, sans-serif';
    }

    /** Return font key safe for storage; default if invalid. */
    public static function allowedFontValue(string $value): string
    {
        $allowed = array_keys(static::allowedFontFamilies());
        return in_array($value, $allowed, true) ? $value : 'system-ui';
    }
}
