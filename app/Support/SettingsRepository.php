<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * SettingsRepository — cached, group-aware read/write for the site.
 *
 * Reads hit an in-memory static cache first, then Laravel Cache (5 min
 * TTL, tagged "settings" / "settings.{group}"). Writes invalidate both
 * layers. Admin can toggle any value at runtime; public views see the
 * change on the next request.
 *
 * Helpers:
 *   site_setting('identity.name', 'Default')  — single key
 *   site_settings('identity')                  — whole group as array
 *
 * Blade directive:
 *
 *   @setting('identity.name', 'Default')       — echoes resolved value
 *
 * Fallback chain: static cache → DB (via Cache) → config('site.*') →
 * provided default. `config/site.php` remains the seed / fallback source
 * of truth so the site still renders on a fresh DB.
 */
final class SettingsRepository
{
    /** @var array<string, mixed> */
    private static array $memory = [];

    private const CACHE_TTL = 300; // 5 min

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$memory)) {
            return self::$memory[$key];
        }

        $value = Cache::remember(
            self::cacheKey($key),
            self::CACHE_TTL,
            fn () => Setting::query()->where('key', $key)->value('value')
        );

        if ($value === null) {
            // Fallback: config('site.*') — key transform:
            // 'identity.name' → 'site.name' (legacy compat)
            $value = self::configFallback($key, $default);
        }

        return self::$memory[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public static function group(string $group): array
    {
        $cacheKey = "settings.group.{$group}";

        if (array_key_exists($cacheKey, self::$memory)) {
            return self::$memory[$cacheKey];
        }

        $rows = Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => Setting::query()
                ->where('group', $group)
                ->pluck('value', 'key')
                ->all()
        );

        return self::$memory[$cacheKey] = $rows;
    }

    public static function set(string $key, mixed $value, ?string $group = null): Setting
    {
        $attributes = ['value' => $value];
        if ($group !== null) {
            $attributes['group'] = $group;
        }

        /** @var Setting $setting */
        $setting = Setting::query()->updateOrCreate(['key' => $key], $attributes);

        self::forget($key);

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $items
     */
    public static function bulkSet(array $items, ?string $group = null): void
    {
        foreach ($items as $key => $value) {
            self::set($key, $value, $group);
        }
    }

    public static function forget(string $key): void
    {
        unset(self::$memory[$key]);
        Cache::forget(self::cacheKey($key));

        // Also bust group cache if the key is a grouped dotted form
        if (str_contains($key, '.')) {
            $group = strtok($key, '.');
            Cache::forget("settings.group.{$group}");
            unset(self::$memory["settings.group.{$group}"]);
        }
    }

    public static function flush(): void
    {
        self::$memory = [];
        Cache::flush(); // blunt; refined tag-flush possible when Redis is wired
    }

    /**
     * Returns all public-visible settings — used by view composers
     * to inject frontend-facing toggles at layout level if needed.
     *
     * @return Collection<string, mixed>
     */
    public static function publicAll(): Collection
    {
        return Cache::remember(
            'settings.public.all',
            self::CACHE_TTL,
            fn () => Setting::query()
                ->where('is_public', true)
                ->pluck('value', 'key')
        );
    }

    private static function cacheKey(string $key): string
    {
        return "settings.key.{$key}";
    }

    /**
     * Legacy fallback mapping: settings keys usually begin with a domain
     * prefix (identity.*, contact.*, nav.*). The prior codebase used
     * config('site.*') directly; keep that as fallback so a missing
     * setting never breaks rendering.
     */
    private static function configFallback(string $key, mixed $default): mixed
    {
        $map = [
            'identity.name' => 'site.name',
            'identity.role' => 'site.role',
            'identity.base' => 'site.base',
            'identity.description' => 'site.description',
            'identity.manifesto_quote' => 'site.manifesto_quote',
            'identity.current_context' => 'site.current_context',
            'contact.email' => 'site.contact.email',
            'contact.signal_url' => 'site.contact.signal_url',
            'contact.pgp_fingerprint' => 'site.contact.pgp_fingerprint',
            'contact.pgp_key_id' => 'site.contact.pgp_key_id',
            'contact.pgp_download' => 'site.contact.pgp_download',
            'photo.default_credit' => 'site.default_photo_credit',
            'identity.portrait_url' => 'site.portrait.url',
            'identity.portrait_credit' => 'site.portrait.credit',
            'features.feed_enabled' => 'site.features.feed_enabled',
            'features.newsletter_enabled' => 'site.features.newsletter_enabled',
            'nav.show_visuals' => 'site.nav.show_visuals',
            'identity.affiliation_approved' => 'site.affiliation_approved',
        ];

        if (isset($map[$key])) {
            $configValue = config($map[$key]);
            if ($configValue !== null) {
                return $configValue;
            }
        }

        return $default;
    }
}
