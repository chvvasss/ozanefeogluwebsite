<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public renderer for DB-backed legal pages (KVKK, Gizlilik, Künye).
 *
 * Slug whitelist enforced at route level (kvkk|gizlilik|kunye). Controller
 * additionally filters by template=legal + published scope so admin cannot
 * accidentally expose arbitrary pages through this surface.
 */
class LegalPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->where('template', 'legal')
            ->where('slug', $slug)
            ->first();

        if (! $page) {
            throw new NotFoundHttpException;
        }

        return view('public.pages.legal', [
            'page' => $page,
        ]);
    }
}
