<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public renderer for admin-created custom pages.
 *
 * System pages (about/contact/legal) have their own dedicated routes so
 * they can carry per-page templates and view-models. Everything ELSE that
 * the admin creates — `kind=custom` or `template=default` — lands here at
 * `/sayfa/{slug}` and is rendered via a single `public.pages.custom`
 * blade with the page's body + intro + meta.
 *
 * Slug is whitelisted by route regex; controller additionally enforces
 * `is_published` and refuses to serve system pages through this surface
 * (those have their own routes).
 */
class CustomPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        if (! $page) {
            throw new NotFoundHttpException;
        }

        // System templates have dedicated routes — refuse double-serving here.
        if (in_array($page->template, ['about', 'contact', 'legal'], true)) {
            throw new NotFoundHttpException;
        }

        return view('public.pages.custom', [
            'page' => $page,
        ]);
    }
}
