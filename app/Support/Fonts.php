<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Resolves Vite-hashed font asset URLs for `<link rel="preload">` tags.
 *
 * Fontsource paketleri @font-face kayıtlarını CSS'te tutuyor; Vite font
 * dosyalarını content-hash ile public/build/assets/ altına kopyalıyor ama
 * manifest.json bunları explicit entry olarak taşımıyor. Bu yüzden
 * preload için dosya adının hash'ini glob ile yakalıyoruz; hash sabit
 * (content-based) — fontsource versiyonu değişmediği sürece tek seferlik
 * iş, üretimde cache'lenir.
 */
final class Fonts
{
    /**
     * Find the build URL for a font file matching the given needle.
     * Example needle: "source-serif-4-latin-wght-normal".
     *
     * Returns null if no match (e.g., assets not yet built).
     */
    public static function url(string $needle): ?string
    {
        static $cache = [];

        if (array_key_exists($needle, $cache)) {
            return $cache[$needle];
        }

        $assetDir = public_path('build/assets');
        if (! is_dir($assetDir)) {
            return $cache[$needle] = null;
        }

        $matches = glob($assetDir.DIRECTORY_SEPARATOR.'*'.$needle.'*.woff2') ?: [];
        if (! $matches) {
            return $cache[$needle] = null;
        }

        return $cache[$needle] = '/build/assets/'.basename($matches[0]);
    }
}
