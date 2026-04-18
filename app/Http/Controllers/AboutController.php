<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', 'hakkimda')
            ->first();

        if (! $page) {
            throw new NotFoundHttpException;
        }

        $recentWritings = Writing::query()
            ->published()
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.pages.about', [
            'page'           => $page,
            'recentWritings' => $recentWritings,
        ]);
    }
}
