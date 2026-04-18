<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WritingRequest;
use App\Models\Publication;
use App\Models\Writing;
use App\Services\Content\BodySanitizer;
use App\Services\Content\SlugGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WritingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Writing::class);

        $query = Writing::query()
            ->with('author:id,name')
            ->withTrashed()
            ->orderByDesc('updated_at');

        if ($request->user()->hasRole('contributor') && ! $request->user()->hasAnyRole(['super-admin', 'admin', 'editor'])) {
            $query->where('author_id', $request->user()->id);
        }

        $status = $request->string('status')->value() ?: null;
        if (in_array($status, Writing::STATUSES, true)) {
            $query->where('status', $status);
        }

        $kind = $request->string('kind')->value() ?: null;
        if (in_array($kind, Writing::KINDS, true)) {
            $query->where('kind', $kind);
        }

        $search = trim((string) $request->string('q')->value());
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('slug->tr', 'like', "%{$search}%")
                    ->orWhere('title->tr', 'like', "%{$search}%");
            });
        }

        $writings = $query->paginate(20)->withQueryString();

        return view('admin.writings.index', [
            'writings' => $writings,
            'filters'  => [
                'status' => $status,
                'kind'   => $kind,
                'q'      => $search,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Writing::class);

        $writing = new Writing([
            'kind'        => 'deneme',
            'status'      => 'draft',
            'cover_hue_a' => 24,
            'cover_hue_b' => 200,
            'is_featured' => false,
        ]);

        return view('admin.writings.create', [
            'writing'      => $writing,
            'publications' => Publication::query()->orderBy('sort_order')->get(),
            'selectedPubs' => [],
        ]);
    }

    public function store(WritingRequest $request): RedirectResponse
    {
        $this->authorize('create', Writing::class);

        $data = $request->validated();
        $writing = new Writing;
        $this->fill($writing, $data, $request);
        $writing->author_id = $request->user()->id;
        $writing->save();

        $this->syncPublications($writing, $data['publication_ids'] ?? []);
        $this->handleCoverUpload($writing, $request);

        return redirect()
            ->route('admin.writings.edit', $writing)
            ->with('status', __('Yazı oluşturuldu.'));
    }

    public function edit(Request $request, Writing $writing): View
    {
        $this->authorize('update', $writing);

        return view('admin.writings.edit', [
            'writing'      => $writing,
            'publications' => Publication::query()->orderBy('sort_order')->get(),
            'selectedPubs' => $writing->publications()->pluck('publications.id')->all(),
        ]);
    }

    public function update(WritingRequest $request, Writing $writing): RedirectResponse
    {
        $this->authorize('update', $writing);

        $data = $request->validated();
        $this->fill($writing, $data, $request);
        $writing->save();

        $this->syncPublications($writing, $data['publication_ids'] ?? []);
        $this->handleCoverUpload($writing, $request);

        return redirect()
            ->route('admin.writings.edit', $writing)
            ->with('status', __('Yazı güncellendi.'));
    }

    public function uploadCover(Request $request, Writing $writing): RedirectResponse
    {
        $this->authorize('update', $writing);

        $request->validate([
            'cover' => ['required', 'image', 'max:8192', 'mimes:jpg,jpeg,png,webp,avif'],
        ]);

        $writing->clearMediaCollection('cover');
        $writing->addMediaFromRequest('cover')->toMediaCollection('cover');

        return back()->with('status', __('Kapak yüklendi.'));
    }

    public function removeCover(Request $request, Writing $writing): RedirectResponse
    {
        $this->authorize('update', $writing);

        $writing->clearMediaCollection('cover');

        return back()->with('status', __('Kapak kaldırıldı.'));
    }

    public function destroy(Request $request, Writing $writing): RedirectResponse
    {
        $this->authorize('delete', $writing);

        $writing->delete();

        return redirect()
            ->route('admin.writings.index')
            ->with('status', __('Yazı çöpe alındı. 30 gün içinde geri getirilebilir.'));
    }

    public function publish(Request $request, Writing $writing): RedirectResponse
    {
        $this->authorize('publish', $writing);

        $writing->forceFill([
            'status'       => 'published',
            'published_at' => $writing->published_at ?? now(),
        ])->save();

        return back()->with('status', __('Yazı yayımlandı.'));
    }

    public function unpublish(Request $request, Writing $writing): RedirectResponse
    {
        $this->authorize('unpublish', $writing);

        $writing->forceFill(['status' => 'draft'])->save();

        return back()->with('status', __('Yazı taslağa alındı.'));
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function fill(Writing $writing, array $data, Request $request): void
    {
        $title = trim((string) ($data['title_tr'] ?? ''));
        $slug  = $data['slug_tr'] ?? '';
        $slug  = $slug !== '' ? $slug : SlugGenerator::uniqueForWriting($title, $writing->id);

        $writing->title = ['tr' => $title];
        $writing->slug = ['tr' => $slug];
        $writing->excerpt = ['tr' => (string) ($data['excerpt_tr'] ?? '')];
        $writing->body = ['tr' => BodySanitizer::clean((string) ($data['body_tr'] ?? ''))];
        $writing->meta_title = ['tr' => (string) ($data['meta_title_tr'] ?? '')];
        $writing->meta_description = ['tr' => (string) ($data['meta_desc_tr'] ?? '')];

        $writing->kind = (string) $data['kind'];
        $writing->status = $this->resolveStatus($data, $request);
        $writing->published_at = $this->resolvePublishedAt($data, $writing);
        $writing->location = $data['location'] ?? null;
        $writing->cover_hue_a = (int) $data['cover_hue_a'];
        $writing->cover_hue_b = (int) $data['cover_hue_b'];
        $writing->is_featured = (bool) ($data['is_featured'] ?? false);
        $writing->canonical_url = $data['canonical_url'] ?? null;

        // Compute read time explicitly (observer may be suppressed in seeders).
        $plain = trim(strip_tags((string) $writing->getTranslation('body', 'tr', false)));
        $words = preg_split('/\s+/u', $plain) ?: [];
        $writing->read_minutes = max(1, (int) ceil(count($words) / 220));
    }

    private function resolveStatus(array $data, Request $request): string
    {
        $status = (string) $data['status'];

        // Contributors can never publish directly.
        if ($request->user()->hasRole('contributor') && ! $request->user()->hasAnyRole(['admin', 'editor', 'super-admin'])) {
            return 'draft';
        }

        return $status;
    }

    private function resolvePublishedAt(array $data, Writing $writing): ?Carbon
    {
        if (! empty($data['published_at'])) {
            return Carbon::parse($data['published_at']);
        }

        if (($data['status'] ?? null) === 'published') {
            return $writing->published_at ?? now();
        }

        return $writing->published_at;
    }

    /** @param array<int, int|string> $ids */
    private function syncPublications(Writing $writing, array $ids): void
    {
        $cleanIds = array_values(array_filter(array_map('intval', $ids)));
        $writing->publications()->sync($cleanIds);
    }

    private function handleCoverUpload(Writing $writing, Request $request): void
    {
        if (! $request->hasFile('cover')) {
            return;
        }

        $request->validate([
            'cover' => ['image', 'max:8192', 'mimes:jpg,jpeg,png,webp,avif'],
        ]);

        $writing->clearMediaCollection('cover');
        $writing->addMediaFromRequest('cover')->toMediaCollection('cover');
    }
}
