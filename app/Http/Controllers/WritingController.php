<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WritingController extends Controller
{
    public function index(Request $request): View
    {
        $kind = $request->string('tur')->value() ?: null;
        $kind = in_array($kind, Writing::KINDS, true) ? $kind : null;

        // Editor's archive: chronological list, grouped by year in the view.
        // Year-based archive routes (/yazilar/{year}) come in Faz 3 — no
        // artificial cap here; sahibinin gerçek yazı sayısı çoğalana kadar
        // tek sayfa kronolojik liste yeterli.
        $writings = Writing::query()
            ->published()
            ->ofKind($kind)
            ->with('media')
            ->latest('published_at')
            ->get()
            ->groupBy(fn (Writing $w) => optional($w->published_at)->format('Y') ?? '—');

        return view('public.writing.index', [
            'writingsByYear' => $writings,
            'totalCount' => Writing::query()->published()->ofKind($kind)->count(),
            'filter' => $kind,
            'kinds' => $this->kindOptions(),
        ]);
    }

    public function show(string $slug): View
    {
        $writing = Writing::query()
            ->published()
            ->bySlug($slug)
            ->with('publications')
            ->first();

        if (! $writing) {
            throw new NotFoundHttpException;
        }

        $prev = Writing::query()
            ->published()
            ->where('published_at', '<', $writing->published_at)
            ->orderByDesc('published_at')
            ->first();

        $next = Writing::query()
            ->published()
            ->where('published_at', '>', $writing->published_at)
            ->orderBy('published_at')
            ->first();

        $related = Writing::query()
            ->published()
            ->where('kind', $writing->kind)
            ->where('id', '!=', $writing->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.writing.show', [
            'writing' => $writing,
            'prev' => $prev,
            'next' => $next,
            'related' => $related,
        ]);
    }

    /** @return array<int, array{value: string|null, label: string}> */
    private function kindOptions(): array
    {
        return [
            ['value' => null,          'label' => 'hepsi'],
            ['value' => 'saha_yazisi', 'label' => 'saha yazısı'],
            ['value' => 'roportaj',    'label' => 'röportaj'],
            ['value' => 'deneme',      'label' => 'deneme'],
            ['value' => 'not',         'label' => 'not'],
        ];
    }
}
