<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PhotoRequest;
use App\Models\Photo;
use App\Models\Writing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Photo::class);

        $query = Photo::query()
            ->withTrashed()
            ->with('writing:id,title,slug')
            ->orderByDesc('updated_at');

        $kind = $request->string('kind')->value() ?: null;
        if (in_array($kind, Photo::KINDS, true)) {
            $query->where('kind', $kind);
        }

        $status = $request->string('status')->value() ?: null;
        if ($status === 'published') {
            $query->where('is_published', true);
        } elseif ($status === 'draft') {
            $query->where('is_published', false);
        } elseif ($status === 'hero') {
            $query->where('hero_eligible', true);
        }

        $search = trim((string) $request->string('q')->value());
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('slug->tr', 'like', "%{$search}%")
                    ->orWhere('title->tr', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return view('admin.photos.index', [
            'photos' => $query->paginate(24)->withQueryString(),
            'filters' => [
                'kind' => $kind,
                'status' => $status,
                'q' => $search,
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Photo::class);

        $photo = new Photo([
            'kind' => 'reportage',
            'license' => 'editorial-only',
            'source' => 'AA',
            'is_published' => false,
        ]);

        return view('admin.photos.create', [
            'photo' => $photo,
            'writings' => $this->writingOptions(),
        ]);
    }

    public function store(PhotoRequest $request): RedirectResponse
    {
        $this->authorize('create', Photo::class);

        $photo = new Photo;
        $this->fill($photo, $request->validated());
        $photo->created_by = $request->user()?->id;
        $photo->save();

        $this->handleImageUpload($photo, $request);

        return redirect()
            ->route('admin.photos.edit', $photo)
            ->with('status', __('Fotoğraf eklendi.'));
    }

    public function edit(Photo $photo): View
    {
        $this->authorize('update', $photo);

        return view('admin.photos.edit', [
            'photo' => $photo,
            'writings' => $this->writingOptions(),
        ]);
    }

    public function update(PhotoRequest $request, Photo $photo): RedirectResponse
    {
        $this->authorize('update', $photo);

        $this->fill($photo, $request->validated());
        $photo->save();

        $this->handleImageUpload($photo, $request);

        return redirect()
            ->route('admin.photos.edit', $photo)
            ->with('status', __('Fotoğraf güncellendi.'));
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $this->authorize('delete', $photo);

        $photo->delete();

        return redirect()
            ->route('admin.photos.index')
            ->with('status', __('Fotoğraf çöpe alındı. 30 gün içinde geri getirilebilir.'));
    }

    public function publish(Photo $photo): RedirectResponse
    {
        $this->authorize('update', $photo);

        $photo->forceFill(['is_published' => true])->save();

        return back()->with('status', __('Fotoğraf yayımlandı.'));
    }

    public function unpublish(Photo $photo): RedirectResponse
    {
        $this->authorize('update', $photo);

        $photo->forceFill(['is_published' => false])->save();

        return back()->with('status', __('Fotoğraf taslağa alındı.'));
    }

    public function removeImage(Photo $photo): RedirectResponse
    {
        $this->authorize('update', $photo);

        $photo->clearMediaCollection('image');

        return back()->with('status', __('Görsel kaldırıldı.'));
    }

    /**
     * Bulk upload — accepts up to 20 images, creates a draft Photo row
     * per file with title derived from filename. Admin then edits each
     * to add caption, credit, etc.
     */
    public function bulkUpload(Request $request): RedirectResponse
    {
        $this->authorize('create', Photo::class);

        $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:20'],
            'images.*' => ['image', 'max:20480', 'mimes:jpg,jpeg,png,webp,avif'],
        ], [], [
            'images' => 'fotoğraflar',
            'images.*' => 'fotoğraf',
        ]);

        $created = 0;
        $defaultCredit = (string) site_setting('photo.default_credit', 'Foto: Ozan Efeoğlu');

        foreach ((array) $request->file('images', []) as $file) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $title = trim(preg_replace('/[_\-]+/', ' ', $name)) ?: 'Yüklenen fotoğraf';

            $photo = new Photo([
                'title' => ['tr' => $title],
                'kind' => 'reportage',
                'credit' => $defaultCredit,
                'source' => 'AA',
                'license' => 'editorial-only',
                'is_published' => false,
                'captured_at' => now(),
            ]);
            $photo->created_by = $request->user()?->id;
            $photo->save();

            $photo->addMedia($file->getPathname())
                ->usingName($name)
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('image');

            $created++;
        }

        return redirect()
            ->route('admin.photos.index', ['status' => 'draft'])
            ->with('status', __(':count fotoğraf yüklendi. Her birine künye, alt yazı ve konum eklemek için düzenle.', ['count' => $created]));
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $data
     */
    private function fill(Photo $photo, array $data): void
    {
        $title = trim((string) ($data['title_tr'] ?? ''));

        $slug = trim((string) ($data['slug_tr'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($title);
        }

        $photo->title = ['tr' => $title];
        $photo->slug = ['tr' => $slug];
        $photo->caption = ['tr' => (string) ($data['caption_tr'] ?? '')];
        $photo->alt_text = ['tr' => (string) ($data['alt_text_tr'] ?? '')];

        $photo->kind = (string) $data['kind'];
        $photo->credit = $data['credit'] ?? null;
        $photo->source = $data['source'] ?? null;
        $photo->license = $data['license'] ?? null;
        $photo->rights_notes = $data['rights_notes'] ?? null;
        $photo->location = $data['location'] ?? null;
        $photo->captured_at = ! empty($data['captured_at']) ? Carbon::parse($data['captured_at']) : null;

        $photo->is_published = (bool) ($data['is_published'] ?? false);
        $photo->is_featured = (bool) ($data['is_featured'] ?? false);
        $photo->hero_eligible = (bool) ($data['hero_eligible'] ?? false);

        $photo->writing_id = ! empty($data['writing_id']) ? (int) $data['writing_id'] : null;
    }

    private function handleImageUpload(Photo $photo, PhotoRequest $request): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        $photo->clearMediaCollection('image');
        $photo->addMediaFromRequest('image')->toMediaCollection('image');
    }

    /**
     * @return Collection<int, Writing>
     */
    private function writingOptions(): Collection
    {
        return Writing::query()
            ->select(['id', 'title', 'slug', 'published_at'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
    }
}
