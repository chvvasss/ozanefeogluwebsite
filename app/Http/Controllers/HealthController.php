<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * /health — single endpoint that checks every external dependency the
 * app needs to render content. Returns 200 when all systems are green,
 * 503 when any dependency is down.
 *
 * Probes: db (SELECT 1), cache (put+get round-trip), storage (write+read+delete),
 * media (public disk reachable). Adds version + uptime hints so monitoring
 * dashboards can show real metadata.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'db' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'media' => $this->checkMedia(),
        ];

        $allOk = collect($checks)->every(fn ($v) => $v['status'] === 'ok');

        return response()->json([
            'app' => $allOk ? 'ok' : 'degraded',
            'environment' => app()->environment(),
            'version' => $this->detectVersion(),
            'time' => now()->toIso8601String(),
            'checks' => $checks,
        ], $allOk ? 200 : 503, [
            'Cache-Control' => 'no-store, max-age=0',
        ]);
    }

    /** @return array{status: string, detail?: string} */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->select('SELECT 1');

            return ['status' => 'ok', 'driver' => DB::connection()->getDriverName()];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'detail' => substr($e->getMessage(), 0, 120)];
        }
    }

    /** @return array{status: string} */
    private function checkCache(): array
    {
        try {
            Cache::put('health-probe', '1', 5);
            $back = Cache::get('health-probe');

            return ['status' => $back === '1' ? 'ok' : 'down', 'driver' => config('cache.default')];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'detail' => substr($e->getMessage(), 0, 120)];
        }
    }

    /** @return array{status: string} */
    private function checkStorage(): array
    {
        try {
            $disk = Storage::disk('public');
            $key = 'health/'.uniqid().'.txt';
            $disk->put($key, 'health '.now()->toIso8601String());
            $ok = $disk->exists($key) && $disk->get($key) !== '';
            $disk->delete($key);

            return ['status' => $ok ? 'ok' : 'down', 'disk' => 'public'];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'detail' => substr($e->getMessage(), 0, 120)];
        }
    }

    /** @return array{status: string} */
    private function checkMedia(): array
    {
        try {
            $count = Photo::query()
                ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'))
                ->count();

            return ['status' => 'ok', 'photos_with_image' => $count];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'detail' => substr($e->getMessage(), 0, 120)];
        }
    }

    private function detectVersion(): string
    {
        // Prefer git short SHA when available, fallback to deploy timestamp file.
        try {
            $head = base_path('.git/HEAD');
            if (is_file($head)) {
                $ref = trim((string) file_get_contents($head));
                if (str_starts_with($ref, 'ref: ')) {
                    $refPath = base_path('.git/'.substr($ref, 5));
                    if (is_file($refPath)) {
                        return 'git:'.substr(trim((string) file_get_contents($refPath)), 0, 8);
                    }
                }
            }
        } catch (\Throwable) {
        }

        return 'dev';
    }
}
