<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Writing;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $writings = Writing::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $latest = $writings->sortByDesc('published_at')->first();

        return view('public.landing', [
            'status'   => [
                'cadence' => 'ayda iki yazı',
                'last'    => $latest?->published_at?->translatedFormat('d F Y') ?? '—',
                'feed'    => '/feed.xml',
            ],
            'writings' => $writings,
            'credits'  => Publication::query()
                ->orderBy('sort_order')
                ->pluck('name')
                ->all(),
        ]);
    }
}
