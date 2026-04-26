<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublicationRequest;
use App\Models\Publication;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Publication::class);

        $query = Publication::query()
            ->withCount('writings')
            ->orderBy('sort_order')
            ->orderBy('name');

        $search = trim((string) $request->string('q')->value());
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $publications = $query->paginate(20)->withQueryString();

        return view('admin.publications.index', [
            'publications' => $publications,
            'filters' => ['q' => $search],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Publication::class);

        $publication = new Publication([
            'sort_order' => 0,
        ]);

        return view('admin.publications.create', [
            'publication' => $publication,
        ]);
    }

    public function store(PublicationRequest $request): RedirectResponse
    {
        $this->authorize('create', Publication::class);

        $publication = new Publication;
        $this->fill($publication, $request->validated());
        $publication->save();

        return redirect()
            ->route('admin.publications.index')
            ->with('status', __('Yayın oluşturuldu.'));
    }

    public function edit(Publication $publication): View
    {
        $this->authorize('update', $publication);

        return view('admin.publications.edit', [
            'publication' => $publication,
        ]);
    }

    public function update(PublicationRequest $request, Publication $publication): RedirectResponse
    {
        $this->authorize('update', $publication);

        $this->fill($publication, $request->validated());
        $publication->save();

        return redirect()
            ->route('admin.publications.edit', $publication)
            ->with('status', __('Yayın güncellendi.'));
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        $this->authorize('delete', $publication);

        // Hard delete — Publication model does not use SoftDeletes.
        // The pivot `publication_writing` rows are removed automatically
        // when the publications row is gone (FK cascade on that pivot).
        $publication->writings()->detach();
        $publication->delete();

        return redirect()
            ->route('admin.publications.index')
            ->with('status', __('Yayın silindi.'));
    }

    // -----------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $data
     */
    private function fill(Publication $publication, array $data): void
    {
        $name = trim((string) $data['name']);
        $slugInput = trim((string) ($data['slug'] ?? ''));
        $slug = $slugInput !== '' ? $slugInput : Str::slug($name);

        // Guarantee uniqueness if user left slug blank and auto-slug collides.
        if ($slugInput === '') {
            $slug = $this->uniqueSlug($slug, $publication->id);
        }

        $publication->name = $name;
        $publication->slug = $slug;
        $publication->url = $data['url'] ?? null;
        $publication->sort_order = (int) ($data['sort_order'] ?? 0);
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $candidate = $base !== '' ? $base : 'yayin';
        $suffix = 2;

        while (
            Publication::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
