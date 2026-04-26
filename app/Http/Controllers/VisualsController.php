<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public visual archive — /görüntü (routed at /goruntu for ASCII slug safety).
 *
 * Grid-first, editorial: filterable by kind, paginated. Individual photo
 * detail page carries caption, credit, location, captured_at and linked
 * writing (if any).
 */
class VisualsController extends Controller
{
    public function index(Request $request): View
    {
        $kind = $request->string('kind')->value() ?: null;

        $baseQuery = Photo::query()
            ->published()
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'));

        // Eager-load media to avoid N+1 inside the grid loop (each card calls
        // imageUrl() which would otherwise trigger one query per photo).
        // Sort by COALESCE(captured_at, created_at) so newly added photos
        // without an explicit captured date still appear at the top.
        $photos = (clone $baseQuery)
            ->kind($kind)
            ->with('media')
            ->orderByRaw('COALESCE(captured_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        // Per-kind counts for filter labels (adaptive: hide zero-count kinds).
        $counts = (clone $baseQuery)
            ->selectRaw('kind, count(*) as c')
            ->groupBy('kind')
            ->pluck('c', 'kind');

        $kindLabels = [
            'reportage' => 'röportaj',
            'portrait' => 'portre',
            'drone' => 'drone',
            'protocol' => 'protokol',
            'editorial' => 'editoryal',
            'archive' => 'arşiv',
        ];

        return view('public.visuals.index', [
            'photos' => $photos,
            'totalCount' => (int) (clone $baseQuery)->count(),
            'kind' => $kind,
            'kinds' => Photo::KINDS,
            'kindLabels' => $kindLabels,
            'counts' => $counts,
        ]);
    }

    public function show(string $slug): View
    {
        $photo = Photo::query()
            ->published()
            ->where(function ($q) use ($slug): void {
                $q->where('slug->tr', $slug)->orWhere('slug->en', $slug);
            })
            ->with('writing:id,title,slug,published_at')
            ->first();

        if (! $photo) {
            throw new NotFoundHttpException;
        }

        // Next / previous in captured_at order (published-only)
        $prev = Photo::query()->published()
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'))
            ->where('captured_at', '<', $photo->captured_at ?? $photo->created_at)
            ->orderByDesc('captured_at')
            ->first();

        $next = Photo::query()->published()
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'))
            ->where('captured_at', '>', $photo->captured_at ?? $photo->created_at)
            ->orderBy('captured_at')
            ->first();

        return view('public.visuals.show', [
            'photo' => $photo,
            'prev' => $prev,
            'next' => $next,
        ]);
    }
}
