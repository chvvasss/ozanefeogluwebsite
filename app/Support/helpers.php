<?php

declare(strict_types=1);

use App\Support\SettingsRepository;

if (! function_exists('site_setting')) {
    /**
     * Resolve a single setting value with config/default fallback.
     * Loaded into every request via composer autoload/files.
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return SettingsRepository::get($key, $default);
    }
}

if (! function_exists('site_settings')) {
    /**
     * Resolve all settings in a group as [key => value].
     *
     * @return array<string, mixed>
     */
    function site_settings(string $group): array
    {
        return SettingsRepository::group($group);
    }
}
