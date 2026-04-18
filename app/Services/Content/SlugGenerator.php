<?php

declare(strict_types=1);

namespace App\Services\Content;

use App\Models\Writing;
use Illuminate\Support\Str;

/**
 * Turkish-aware slug generator for Writing records.
 * Keeps slug uniqueness per-locale (see ADR-011 + ADR-015).
 */
class SlugGenerator
{
    /**
     * Build a URL-safe slug from Turkish input (İ/ş/ğ handled), then
     * guarantee uniqueness within the TR locale of `writings.slug`.
     */
    public static function uniqueForWriting(string $source, ?int $ignoreId = null): string
    {
        $base = self::make($source);
        if ($base === '') {
            $base = 'yazi';
        }

        $slug = $base;
        $suffix = 2;

        while (self::exists($slug, $ignoreId)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public static function make(string $source): string
    {
        return Str::slug(self::transliterate($source));
    }

    private static function transliterate(string $text): string
    {
        return strtr($text, [
            'İ' => 'i', 'ı' => 'i',
            'Ş' => 's', 'ş' => 's',
            'Ğ' => 'g', 'ğ' => 'g',
            'Ü' => 'u', 'ü' => 'u',
            'Ö' => 'o', 'ö' => 'o',
            'Ç' => 'c', 'ç' => 'c',
        ]);
    }

    private static function exists(string $slug, ?int $ignoreId): bool
    {
        $q = Writing::query()->where('slug->tr', $slug);
        if ($ignoreId !== null) {
            $q->where('id', '!=', $ignoreId);
        }

        return $q->exists();
    }
}
