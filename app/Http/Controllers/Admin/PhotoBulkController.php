<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Bulk actions for admin photos list.
 *
 * Payload:
 *   - ids[]: array of photo ids (required, min 1)
 *   - action: one of self::ACTIONS
 */
class PhotoBulkController extends Controller
{
    public const ACTION_PUBLISH = 'publish';

    public const ACTION_UNPUBLISH = 'unpublish';

    public const ACTION_FEATURE = 'feature';

    public const ACTION_UNFEATURE = 'unfeature';

    public const ACTION_HERO = 'hero';

    public const ACTION_UNHERO = 'unhero';

    public const ACTION_DELETE = 'delete';

    public const ACTIONS = [
        self::ACTION_PUBLISH,
        self::ACTION_UNPUBLISH,
        self::ACTION_FEATURE,
        self::ACTION_UNFEATURE,
        self::ACTION_HERO,
        self::ACTION_UNHERO,
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
            $photos = Photo::query()->whereIn('id', $ids)->get();
            $count = 0;

            foreach ($photos as $photo) {
                $ability = $action === self::ACTION_DELETE ? 'delete' : 'update';

                if (! $request->user()->can($ability, $photo)) {
                    continue;
                }

                $this->applyAction($photo, $action);
                $count++;
            }

            return $count;
        });

        return back()->with('status', $this->flashFor($action, $affected));
    }

    private function applyAction(Photo $photo, string $action): void
    {
        match ($action) {
            self::ACTION_PUBLISH => $photo->forceFill(['is_published' => true])->save(),
            self::ACTION_UNPUBLISH => $photo->forceFill(['is_published' => false])->save(),
            self::ACTION_FEATURE => $photo->forceFill(['is_featured' => true])->save(),
            self::ACTION_UNFEATURE => $photo->forceFill(['is_featured' => false])->save(),
            self::ACTION_HERO => $photo->forceFill(['hero_eligible' => true])->save(),
            self::ACTION_UNHERO => $photo->forceFill(['hero_eligible' => false])->save(),
            self::ACTION_DELETE => $photo->delete(),
        };
    }

    private function flashFor(string $action, int $count): string
    {
        return match ($action) {
            self::ACTION_PUBLISH => __(':count fotoğraf yayımlandı.', ['count' => $count]),
            self::ACTION_UNPUBLISH => __(':count fotoğraf taslağa alındı.', ['count' => $count]),
            self::ACTION_FEATURE => __(':count fotoğraf öne çıkarıldı.', ['count' => $count]),
            self::ACTION_UNFEATURE => __(':count fotoğraf öne çıkarılmadan kaldırıldı.', ['count' => $count]),
            self::ACTION_HERO => __(':count fotoğraf hero adayı olarak işaretlendi.', ['count' => $count]),
            self::ACTION_UNHERO => __(':count fotoğraf hero havuzundan çıkarıldı.', ['count' => $count]),
            self::ACTION_DELETE => __(':count fotoğraf çöpe alındı.', ['count' => $count]),
            default => __(':count kayıt güncellendi.', ['count' => $count]),
        };
    }
}
