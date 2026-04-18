<aside class="admin-sidebar" :data-open="sidebarOpen">
    <a href="{{ route('admin.dashboard') }}" class="admin-brand no-underline">
        <span class="admin-brand-dot"></span>
        Ozan Efeoğlu<span class="text-[var(--color-ink-subtle)] font-[var(--font-sans)] text-xs ml-1 tracking-[0.2em] uppercase">yazı masası</span>
    </a>

    <p class="admin-nav-group">Yayın</p>
    <a href="{{ route('admin.dashboard') }}"
       aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">▤</span> Masa
    </a>
    <a href="{{ route('admin.writings.index') }}"
       aria-current="{{ request()->routeIs('admin.writings.*') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">✎</span> Yazılar
    </a>
    <a href="{{ route('admin.pages.index') }}"
       aria-current="{{ request()->routeIs('admin.pages.*') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">☰</span> Sayfalar
    </a>

    <p class="admin-nav-group">Gelen kutusu</p>
    @php
        $unreadContacts = class_exists(App\Models\ContactMessage::class)
            ? App\Models\ContactMessage::query()->where('status', 'new')->count()
            : 0;
    @endphp
    <a href="{{ route('admin.contact.index') }}"
       aria-current="{{ request()->routeIs('admin.contact.*') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">✉</span>
        Mesajlar
        @if ($unreadContacts > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-[1.3rem] h-5 px-1.5 rounded-full text-[0.65rem] font-mono bg-[var(--color-accent)] text-[var(--color-accent-fg)]">
                {{ $unreadContacts }}
            </span>
        @endif
    </a>

    <p class="admin-nav-group">Hesap</p>
    <a href="{{ route('admin.profile.show') }}"
       aria-current="{{ request()->routeIs('admin.profile.*') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">◉</span> Profil
    </a>
    <a href="{{ route('admin.sessions.index') }}"
       aria-current="{{ request()->routeIs('admin.sessions.*') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">⎙</span> Oturumlar
    </a>
    <a href="{{ route('admin.two-factor.setup') }}"
       aria-current="{{ request()->routeIs('admin.two-factor.*') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">⚿</span> İki faktör
    </a>
    @role('super-admin|admin')
    <a href="{{ route('admin.audit-log') }}"
       aria-current="{{ request()->routeIs('admin.audit-log') ? 'page' : 'false' }}"
       class="admin-nav-item">
        <span aria-hidden="true">⌇</span> Denetim kaydı
    </a>
    @endrole

    <div class="mt-auto pt-6 px-3 text-xs text-[var(--color-ink-subtle)] font-mono">
        <span>v0.1 · {{ now()->format('Y.m.d') }}</span>
    </div>
</aside>
