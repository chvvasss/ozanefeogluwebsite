<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\Content\BodySanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::query()
            ->orderBy('kind')
            ->orderBy('sort_order')
            ->orderBy('slug')
            ->paginate(20);

        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function create(): View
    {
        $this->authorize('create', Page::class);

        $page = new Page([
            'kind'         => 'custom',
            'template'     => 'default',
            'is_published' => true,
        ]);

        return view('admin.pages.edit', ['page' => $page]);
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $data = $request->validated();
        $page = new Page;
        $this->fill($page, $data, $request);
        $page->kind = 'custom';
        $page->save();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', __('Sayfa oluşturuldu.'));
    }

    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $data = $request->validated();
        $this->fill($page, $data, $request);

        // System pages keep their kind + slug locked
        if ($page->kind === 'system') {
            $page->slug = $page->getOriginal('slug');
            $page->template = $page->getOriginal('template');
        }

        $page->save();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', __('Sayfa güncellendi.'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('status', __('Sayfa silindi.'));
    }

    // -----------------------------------------------------------------

    private function fill(Page $page, array $data, Request $request): void
    {
        $page->title = ['tr' => trim((string) $data['title_tr'])];
        $page->intro = ['tr' => (string) ($data['intro_tr'] ?? '')];
        $page->body = ['tr' => BodySanitizer::clean((string) ($data['body_tr'] ?? ''))];
        $page->meta_title = ['tr' => (string) ($data['meta_title_tr'] ?? '')];
        $page->meta_description = ['tr' => (string) ($data['meta_desc_tr'] ?? '')];

        if ($page->kind !== 'system') {
            $page->slug = (string) $data['slug'];
            $page->template = (string) $data['template'];
        }

        $extras = $request instanceof PageRequest ? $request->extras() : null;
        if ($extras !== null) {
            $page->extras = $extras;
        }

        $page->is_published = (bool) ($data['is_published'] ?? false);
    }
}
