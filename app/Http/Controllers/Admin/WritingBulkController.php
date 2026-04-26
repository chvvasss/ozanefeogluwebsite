<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Writing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Bulk actions for admin writings list.
 *
 * Payload:
 *   - ids[]: array of writing ids (required, min 1)
 *   - action: one of self::ACTIONS
 *
 * Each id is authorized per-policy; entries the user cannot touch are
 * silently skipped so a partial batch still succeeds for what's allowed.
 */
class WritingBulkController extends Controller
{
    public const ACTION_PUBLISH = 'publish';

    public const ACTION_UNPUBLISH = 'unpublish';

    public const ACTION_FEATURE = 'feature';

    public const ACTION_UNFEATURE = 'unfeature';

    public const ACTION_DELETE = 'delete';

    public const ACTIONS = [
        self::ACTION_PUBLISH,
        self::ACTION_UNPUBLISH,
        self::ACTION_FEATURE,
        self::ACTION_UNFEATURE,
        self::ACTION_DELETE,
    ];

    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'action' => ['required', 'string'],
        ]);

        $action = (string) $data['action'];
        if (! in_array($action, self::ACTIONS, true)) {
            throw ValidationException::withMessages([
                'action' => __('Geçersiz toplu aksiyon.'),
            ]);
        }

        $ids = array_values(array_unique(array_map('intval', (array) $data['ids'])));

        $affected = DB::transaction(function () use ($ids, $action, $request): int {
            $writings = Writing::query()->whereIn('id', $ids)->get();
            $count = 0;

            foreach ($writings as $writing) {
                $ability = $this->abilityFor($action);

                if (! $request->user()->can($ability, $writing)) {
                    continue;
                }

                $this->applyAction($writing, $action);
                $count++;
            }

            return $count;
        });

        return back()->with('status', $this->flashFor($action, $affected));
    }

    private function abilityFor(string $action): string
    {
        return match ($action) {
            self::ACTION_PUBLISH => 'publish',
            self::ACTION_UNPUBLISH => 'unpublish',
            self::ACTION_DELETE => 'delete',
            default => 'update',
        };
    }

    private function applyAction(Writing $writing, string $action): void
    {
        match ($action) {
            self::ACTION_PUBLISH => $writing->forceFill([
                'status' => 'published',
                'published_at' => $writing->published_at ?? now(),
            ])->save(),
            self::ACTION_UNPUBLISH => $writing->forceFill(['status' => 'draft'])->save(),
            self::ACTION_FEATURE => $writing->forceFill(['is_featured' => true])->save(),
            self::ACTION_UNFEATURE => $writing->forceFill(['is_featured' => false])->save(),
            self::ACTION_DELETE => $writing->delete(),
        };
    }

    private function flashFor(string $action, int $count): string
    {
        return match ($action) {
            self::ACTION_PUBLISH => __(':count yazı yayımlandı.', ['count' => $count]),
            self::ACTION_UNPUBLISH => __(':count yazı taslağa alındı.', ['count' => $count]),
            self::ACTION_FEATURE => __(':count yazı öne çıkarıldı.', ['count' => $count]),
            self::ACTION_UNFEATURE => __(':count yazı öne çıkarılmadan kaldırıldı.', ['count' => $count]),
            self::ACTION_DELETE => __(':count yazı çöpe alındı.', ['count' => $count]),
            default => __(':count kayıt güncellendi.', ['count' => $count]),
        };
    }
}
