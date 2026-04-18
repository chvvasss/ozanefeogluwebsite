@extends('layouts.admin', ['title' => ($page->getTranslation('title', 'tr', false) ?: 'Sayfa') . ' düzenle'])

@section('content')

@php
    $isUpdate = $page->exists;
    $action = $isUpdate
        ? route('admin.pages.update', $page)
        : route('admin.pages.store');
    $method = $isUpdate ? 'PUT' : 'POST';
    $titleTr = old('title_tr', $page->getTranslation('title', 'tr', false));
    $introTr = old('intro_tr', $page->getTranslation('intro', 'tr', false));
    $bodyTr  = old('body_tr', $page->getTranslation('body', 'tr', false));
    $metaTitleTr = old('meta_title_tr', $page->getTranslation('meta_title', 'tr', false));
    $metaDescTr  = old('meta_desc_tr', $page->getTranslation('meta_description', 'tr', false));
    $extrasJson  = old('extras_json', $page->extras ? json_encode($page->extras, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '');
    $isSystem = $page->kind === 'system';
@endphp

<header class="admin-page-header">
    <div>
        <p class="eyebrow mb-2">
            <a href="{{ route('admin.pages.index') }}" class="no-underline border-b border-transparent hover:border-current pb-0.5">Sayfalar</a>
            &nbsp;·&nbsp; {{ $isUpdate ? 'düzenle' : 'yeni' }}
        </p>
        <h1 class="admin-page-title" style="font-size: var(--text-2xl);">
            {{ $titleTr ?: 'Yeni sayfa' }}
        </h1>
        @if ($isUpdate)
            <p class="admin-page-subtitle">
                /{{ $page->slug }}
                @if ($isSystem)
                    &nbsp;·&nbsp; <span class="text-[var(--color-accent)]">sistem sayfası</span>
                @endif
            </p>
        @endif
    </div>
</header>

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @method($method)

    @if ($errors->any())
        <div class="flash flash--danger">
            <ul class="list-disc pl-4 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[2.2fr_1fr] items-start">

        <div class="space-y-6">

            <div class="admin-card">
                <div class="field mb-5">
                    <label for="title_tr" class="field-label">Başlık</label>
                    <input id="title_tr" name="title_tr" type="text" required
                           value="{{ $titleTr }}" class="input"
                           style="font-family: var(--font-display); font-size: 1.5rem; line-height: 1.2;">
                </div>

                <div class="field">
                    <label for="slug" class="field-label">Slug</label>
                    <input id="slug" name="slug" type="text"
                           value="{{ old('slug', $page->slug) }}"
                           pattern="^[a-z0-9][a-z0-9\-]*$"
                           class="input font-mono text-sm"
                           @disabled($isSystem)>
                    <p class="field-hint">
                        @if ($isSystem)
                            Sistem sayfası — slug kilitli.
                        @else
                            Küçük harf · rakam · tire.
                        @endif
                    </p>
                </div>
            </div>

            <div class="admin-card">
                <p class="admin-card-title">Giriş cümlesi</p>
                <textarea id="intro_tr" name="intro_tr" rows="2" maxlength="500"
                          class="input resize-y"
                          placeholder="Sayfanın başındaki kısa açıklama.">{{ $introTr }}</textarea>
            </div>

            <div class="admin-card p-0 overflow-hidden">
                <div class="p-5 pb-3 border-b border-[var(--color-rule)]">
                    <p class="admin-card-title !mb-0">Gövde</p>
                </div>
                <div x-data="tiptapEditor(@js($bodyTr ?? ''))" x-init="init()" class="tiptap-wrap">
                    <div class="tiptap-toolbar" role="toolbar" aria-label="Biçim araçları">
                        <button type="button" @click="chain('toggleHeading', { level: 2 })" :class="active('heading', { level: 2 }) && 'is-active'">H2</button>
                        <button type="button" @click="chain('toggleHeading', { level: 3 })" :class="active('heading', { level: 3 }) && 'is-active'">H3</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="chain('toggleBold')" :class="active('bold') && 'is-active'"><strong>B</strong></button>
                        <button type="button" @click="chain('toggleItalic')" :class="active('italic') && 'is-active'"><em>I</em></button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="chain('toggleBulletList')" :class="active('bulletList') && 'is-active'">•</button>
                        <button type="button" @click="chain('toggleOrderedList')" :class="active('orderedList') && 'is-active'">1.</button>
                        <button type="button" @click="chain('toggleBlockquote')" :class="active('blockquote') && 'is-active'">"</button>
                        <button type="button" @click="chain('setHorizontalRule')">—</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="setLink()" :class="active('link') && 'is-active'">🔗</button>
                        <button type="button" @click="unsetLink()">⊘</button>
                        <span class="tiptap-divider" aria-hidden="true"></span>
                        <button type="button" @click="chain('undo')">↶</button>
                        <button type="button" @click="chain('redo')">↷</button>
                    </div>
                    <div x-ref="editor" class="tiptap-content"></div>
                    <textarea name="body_tr" x-ref="hidden" class="sr-only">{{ $bodyTr }}</textarea>
                </div>
            </div>

            <details class="admin-card" {{ $isUpdate ? '' : 'open' }}>
                <summary class="admin-card-title cursor-pointer select-none">
                    <span>Şablona özel veriler</span>
                    <span class="ml-2 text-[var(--color-ink-subtle)] font-normal normal-case tracking-normal text-xs">
                        (JSON — template: <code class="font-mono">{{ $page->template }}</code>)
                    </span>
                </summary>
                <div class="pt-4">
                    <textarea name="extras_json" rows="16"
                              class="input font-mono text-xs resize-y"
                              placeholder='{"key": "value"}'>{{ $extrasJson }}</textarea>
                    <p class="field-hint mt-2">
                        @if ($page->template === 'about')
                            <strong>about</strong> şablonu için: <code>credentials</code>, <code>timeline</code>, <code>awards</code>, <code>cv_url</code> alanları.
                        @elseif ($page->template === 'contact')
                            <strong>contact</strong> şablonu için: <code>channels</code>, <code>pgp</code>, <code>disclosure</code>, <code>response_time_hours</code> alanları.
                        @else
                            Özel sayfalar için opsiyonel JSON veri.
                        @endif
                    </p>
                </div>
            </details>

            <details class="admin-card">
                <summary class="admin-card-title cursor-pointer select-none">SEO meta (opsiyonel)</summary>
                <div class="pt-4 space-y-4">
                    <div class="field">
                        <label for="meta_title_tr" class="field-label">Meta başlık</label>
                        <input id="meta_title_tr" name="meta_title_tr" type="text" maxlength="255"
                               value="{{ $metaTitleTr }}" class="input">
                    </div>
                    <div class="field">
                        <label for="meta_desc_tr" class="field-label">Meta açıklama</label>
                        <textarea id="meta_desc_tr" name="meta_desc_tr" rows="2" maxlength="320"
                                  class="input resize-y">{{ $metaDescTr }}</textarea>
                    </div>
                </div>
            </details>
        </div>

        <div class="space-y-6">

            <div class="admin-card">
                <p class="admin-card-title">Durum</p>
                <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1"
                           class="accent-[var(--color-accent)]"
                           @checked(old('is_published', $page->is_published))>
                    <span>Yayında</span>
                </label>
                <p class="field-hint mt-2">
                    Yayından kaldırılırsa public'te 404 döner.
                </p>
            </div>

            <div class="admin-card">
                <p class="admin-card-title">Şablon</p>
                <select name="template" class="input" @disabled($isSystem)>
                    @foreach (App\Models\Page::TEMPLATES as $tmpl)
                        <option value="{{ $tmpl }}" @selected(old('template', $page->template) === $tmpl)>
                            {{ $tmpl }}
                        </option>
                    @endforeach
                </select>
                <p class="field-hint mt-2">
                    @if ($isSystem)
                        Sistem sayfası — şablon kilitli.
                    @else
                        Public'te hangi görsel şablon kullanılacağı.
                    @endif
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 sticky bottom-4">
                @if ($isUpdate)
                    <a href="{{ $page->url() }}" target="_blank" rel="noopener"
                       class="btn btn--ghost btn--sm">Public'te aç ↗</a>
                @endif
                <a href="{{ route('admin.pages.index') }}" class="btn btn--ghost btn--sm">İptal</a>
                <button type="submit" class="btn btn--accent">
                    {{ $isUpdate ? 'Güncelle' : 'Oluştur' }}
                </button>
            </div>
        </div>
    </div>
</form>

@endsection
