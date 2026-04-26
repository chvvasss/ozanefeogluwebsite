<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Photo;
use App\Models\Writing;
use Illuminate\Http\Response;

/**
 * Public sitemap.xml — single-file XML for crawlers.
 *
 * Includes:
 *   - core landing pages (home, /yazilar, /goruntu, /hakkimda, /iletisim)
 *   - all published writings (/yazilar/{slug})
 *   - all published photos (/goruntu/{slug})
 *   - legal pages (kvkk/gizlilik/kunye)
 *
 * `lastmod` from updated_at when available, else now.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $urls[] = $this->entry(route('home'), null, 'weekly', '1.0');
        $urls[] = $this->entry(route('writing.index'), null, 'weekly', '0.9');
        $urls[] = $this->entry(route('about'), null, 'monthly', '0.8');
        $urls[] = $this->entry(route('contact'), null, 'monthly', '0.5');

        if (\Route::has('visuals.index')) {
            $urls[] = $this->entry(route('visuals.index'), null, 'weekly', '0.9');
        }

        Writing::query()
            ->published()
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Writing $w) use (&$urls): void {
                $loc = $w->url();
                if (! str_starts_with($loc, 'http')) {
                    $loc = url($loc);
                }
                $urls[] = $this->entry($loc, $w->updated_at, 'monthly', '0.7');
            });

        if (\Route::has('visuals.show')) {
            Photo::query()
                ->published()
                ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'))
                ->get(['id', 'slug', 'updated_at'])
                ->each(function (Photo $p) use (&$urls): void {
                    $urls[] = $this->entry($p->url(), $p->updated_at, 'monthly', '0.6');
                });
        }

        Page::query()
            ->where('template', 'legal')
            ->where('is_published', true)
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Page $p) use (&$urls): void {
                $urls[] = $this->entry(url('/hukuksal/'.$p->slug), $p->updated_at, 'yearly', '0.3');
            });

        $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $body .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            $body .= "  <url>\n";
            $body .= '    <loc>'.htmlspecialchars($u['loc'], ENT_XML1)."</loc>\n";
            if ($u['lastmod']) {
                $body .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            }
            $body .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $body .= "    <priority>{$u['priority']}</priority>\n";
            $body .= "  </url>\n";
        }
        $body .= "</urlset>\n";

        return response($body, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * @return array{loc: string, lastmod: ?string, changefreq: string, priority: string}
     */
    private function entry(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod ? $lastmod->format('Y-m-d') : null,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
