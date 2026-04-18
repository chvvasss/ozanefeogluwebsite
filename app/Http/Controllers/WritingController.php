<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WritingController extends Controller
{
    public function index(Request $request): View
    {
        $kind = $request->string('tur')->value() ?: null;
        $kind = in_array($kind, Writing::KINDS, true) ? $kind : null;

        /** @var LengthAwarePaginator $writings */
        $writings = Writing::query()
            ->published()
            ->ofKind($kind)
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.writing.index', [
            'writings' => $writings,
            'filter'   => $kind,
            'kinds'    => $this->kindOptions(),
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
            'prev'    => $prev,
            'next'    => $next,
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
