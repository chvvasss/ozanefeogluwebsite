<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Checks a password against the Have I Been Pwned k-anonymity API.
 * The plaintext password never leaves this process; only the first 5
 * hex chars of its SHA-1 hash are sent, and the full hash is compared
 * locally against the returned suffixes.
 */
class HibpService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly int $cacheTtl,
        private readonly bool $enabled,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: (string) config('security.hibp.base_url', 'https://api.pwnedpasswords.com'),
            timeout: (int) config('security.hibp.timeout', 3),
            cacheTtl: (int) config('security.hibp.cache_ttl', 60 * 60 * 24 * 30),
            enabled: (bool) config('security.hibp.enabled', true),
        );
    }

    /**
     * @return int occurrence count (0 means safe / unknown).
     */
    public function occurrencesOf(string $password): int
    {
        if (! $this->enabled || $password === '') {
            return 0;
        }

        $sha1 = strtoupper(sha1($password));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5);

        $payload = $this->fetchRange($prefix);
        if ($payload === null) {
            return 0;
        }

        foreach (preg_split('/\r?\n/', $payload) ?: [] as $line) {
            if ($line === '') {
                continue;
            }
            [$candidate, $count] = array_pad(explode(':', $line, 2), 2, '0');
            if (hash_equals($suffix, trim($candidate))) {
                return (int) trim($count);
            }
        }

        return 0;
    }

    public function isCompromised(string $password): bool
    {
        return $this->occurrencesOf($password) > 0;
    }

    private function fetchRange(string $prefix): ?string
    {
        return Cache::remember(
            key: "hibp:{$prefix}",
            ttl: $this->cacheTtl,
            callback: function () use ($prefix): ?string {
                try {
                    $response = Http::timeout($this->timeout)
                        ->withHeaders(['Add-Padding' => 'true'])
                        ->get("{$this->baseUrl}/range/{$prefix}");

                    return $response->successful() ? (string) $response->body() : null;
                } catch (\Throwable $e) {
                    Log::warning('hibp.range_fetch_failed', ['prefix' => $prefix, 'error' => $e->getMessage()]);

                    return null;
                }
            }
        );
    }
}
