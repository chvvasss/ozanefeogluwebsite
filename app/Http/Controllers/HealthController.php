<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $status = [
            'app' => 'ok',
            'db' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'time' => now()->toIso8601String(),
        ];

        $allOk = ! in_array('down', $status, true);

        return response()->json($status, $allOk ? 200 : 503);
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->select('SELECT 1');

            return 'ok';
        } catch (\Throwable) {
            return 'down';
        }
    }

    private function checkCache(): string
    {
        try {
            Cache::put('health-probe', '1', 5);
            Cache::get('health-probe');

            return 'ok';
        } catch (\Throwable) {
            return 'down';
        }
    }
}
