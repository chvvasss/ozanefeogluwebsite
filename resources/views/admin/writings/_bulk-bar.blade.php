{{--
    Writings bulk action bar.

    Rendered once at the bottom of the index view, inside the Alpine
    scope that owns `selected` (array of selected writing ids). Shows
    only when at least one row is checked. Posts to the bulk endpoint
    with ids[] + action.
--}}
@php
    $bulkUrl = \Illuminate\Support\Facades\Route::has('admin.writings.bulk')
        ? route('admin.writings.bulk')
        : url('/admin/writings/bulk');
@endphp

<div x-show="selected.length > 0"
     x-transition.opacity
     x-cloak
     class="admin-bulk-bar-floating fixed left-1/2 bottom-6 z-40 -translate-x-1/2"
     style="max-width: calc(100% - 2rem);"
     role="region"
     aria-label="{{ __('Toplu işlem çubuğu') }}">
    <form method="POST" action="{{ $bulkUrl }}"
          class="admin-card flex flex-wrap items-center gap-2"
          style="box-shadow: 0 18px 40px -18px rgba(15,15,15,0.35); padding: 0.75rem 1rem;">
        @csrf
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
        <input type="hidden" name="action" value="" data-bulk-action>

        <span class="pill" x-text="`${selected.length} {{ __('seçili') }}`"></span>

        <div class="flex flex-wrap items-center gap-1.5 ml-2">
            <button type="submit" class="btn btn--sm"
                    @click="$el.form.querySelector('[data-bulk-action]').value = 'publish'">
                {{ __('Yayımla') }}
            </button>
            <button type="submit" class="btn btn--ghost btn--sm"
                    @click="$el.form.querySelector('[data-bulk-action]').value = 'unpublish'">
                {{ __('Taslağa al') }}
            </button>
            <button type="submit" class="btn btn--ghost btn--sm"
                    @click="$el.form.querySelector('[data-bulk-action]').value = 'feature'">
                {{ __('Öne çıkar') }}
            </button>
            <button type="submit" class="btn btn--ghost btn--sm"
                    @click="$el.form.querySelector('[data-bulk-action]').value = 'unfeature'">
                {{ __('Çıkarma') }}
            </button>
            <button type="submit" class="btn btn--ghost btn--sm"
                    @click="$el.form.querySelector('[data-bulk-action]').value = 'delete'"
                    onclick="if (!confirm('{{ __('Seçili yazıları çöpe al?') }}')) { event.preventDefault(); }">
                <span style="color: var(--color-danger)">{{ __('Sil') }}</span>
            </button>
        </div>

        <button type="button" class="btn btn--ghost btn--sm ml-auto"
                @click="selected = []">
            {{ __('Seçimi temizle') }}
        </button>
    </form>
</div>
