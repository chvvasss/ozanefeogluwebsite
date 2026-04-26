<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Photo;
use App\Models\Publication;
use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Homepage — V.3 (Dynamic hero + documentary-portfolio body, 6 scenes).
 *
 * Hero mode is driven by admin setting `hero.mode`:
 *   • featured_photo — selected hero-eligible writing with cover; falls back
 *                      to typographic if the chosen item has no cover yet.
 *   • rotation       — daily pick among hero_eligible items (stable within a day).
 *   • typographic    — editorial plate only; no image.
 *   • portrait       — legacy portrait-led treatment.
 *
 * Scene body (2–6) is unchanged in structure; SCENE 1 is the variable axis.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        [$heroMode, $heroItem] = $this->resolveHero();

        // The hero item (if any) is visually the lead; SCENE 2 should be the
        // next dispatch, not a duplicate framing of the same piece.
        $heroItemId = ($heroMode === 'featured_photo' || $heroMode === 'rotation') && $heroItem
            ? $heroItem->id
            : null;

        // Scene 2 — featured lead (prefer is_featured, fallback latest, excl. hero)
        $leadItem = Writing::query()
            ->published()
            ->when($heroItemId, fn ($q) => $q->where('id', '!=', $heroItemId))
            ->where('is_featured', true)
            ->latest('published_at')
            ->first()
            ?: Writing::query()
                ->published()
                ->when($heroItemId, fn ($q) => $q->where('id', '!=', $heroItemId))
                ->latest('published_at')
                ->first();

        // Scene 3 — constellation (up to 4 more; prefer featured, then latest).
        // Eager-load media so each card's coverUrl() doesn't hit DB.
        $excludeIds = array_filter([$heroItemId, $leadItem?->id]);
        $constellationItems = Writing::query()
            ->published()
            ->whereNotIn('id', $excludeIds)
            ->with('media')
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get();

        // Scene 5 — recent text-row list (exclude already shown above)
        $usedIds = Collection::make($excludeIds)
            ->merge($constellationItems->pluck('id'))
            ->filter()
            ->all();

        $recent = Writing::query()
            ->published()
            ->whereNotIn('id', $usedIds)
            ->with('media')
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Photo contact sheet — 6 featured photos for the visual scene.
        // Eager-load media; the strip iterates 6 photos and each calls imageUrl().
        $photoStrip = Photo::query()
            ->published()
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'))
            ->with('media')
            ->orderByDesc('is_featured')
            ->orderByDesc('captured_at')
            ->limit(6)
            ->get();

        // About page = single source of truth for profile (workareas, intro)
        $aboutPage = Page::query()->where('slug', 'hakkimda')->first();

        return view('public.landing', [
            'heroMode' => $heroMode,
            'heroItem' => $heroItem,
            'heroEyebrow' => site_setting('hero.eyebrow'),
            'ctaPrimaryLabel' => site_setting('hero.cta_primary_label') ?: 'Dosyalar',
            'ctaPrimaryUrl' => site_setting('hero.cta_primary_url') ?: route('writing.index'),
            'ctaSecondaryLabel' => site_setting('hero.cta_secondary_label') ?: 'Hakkında',
            'ctaSecondaryUrl' => site_setting('hero.cta_secondary_url') ?: route('about'),
            'leadItem' => $leadItem,
            'constellationItems' => $constellationItems,
            'photoStrip' => $photoStrip,
            'recent' => $recent,
            'workareas' => $aboutPage?->extra('workareas', []) ?? [],
            'intro' => $aboutPage?->intro,
            'manifestoQuote' => trim((string) site_setting('identity.manifesto_quote')) ?: null,
            'credits' => Publication::query()
                ->orderBy('sort_order')
                ->pluck('name')
                ->all(),
            'portraitUrl' => site_setting('identity.portrait_url'),
            'portraitCredit' => site_setting('identity.portrait_credit'),
        ]);
    }

    /**
     * Resolve hero mode + the Writing to show (if any), with graceful
     * fallback: featured_photo without a cover → typographic.
     *
     * @return array{0: string, 1: ?Writing}
     */
    private function resolveHero(): array
    {
        $mode = (string) (site_setting('hero.mode') ?: 'featured_photo');

        if ($mode === 'typographic' || $mode === 'portrait') {
            return [$mode, null];
        }

        $item = null;

        if ($mode === 'featured_photo') {
            $id = site_setting('hero.featured_writing_id');

            if ($id) {
                $item = Writing::query()
                    ->published()
                    ->where('id', (int) $id)
                    ->first();
            }

            $item ??= Writing::query()
                ->published()
                ->where('hero_eligible', true)
                ->latest('published_at')
                ->first();
        }

        if ($mode === 'rotation') {
            $candidates = Writing::query()
                ->published()
                ->where('hero_eligible', true)
                ->orderBy('id')
                ->get();

            if ($candidates->isNotEmpty()) {
                // Stable daily rotation: day-of-year modulo count.
                $idx = (int) now()->dayOfYear % $candidates->count();
                $item = $candidates[$idx];
            }
        }

        // Graceful fallback: no item or no cover → typographic.
        if (! $item || ! $item->hasCover()) {
            return ['typographic', $item];
        }

        return [$mode, $item];
    }
}
