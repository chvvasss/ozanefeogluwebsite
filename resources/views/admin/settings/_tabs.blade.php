@php
    /** @var string $group */
    /** @var array<int, string> $groups */
    $labels = [
        'identity' => 'Kimlik',
        'contact'  => 'İletişim',
        'nav'      => 'Gezinme',
        'hero'     => 'Hero',
        'social'   => 'Sosyal',
        'theme'    => 'Tema & Özellikler',
    ];
@endphp

<nav class="admin-tabs" aria-label="Ayar grupları">
    @foreach ($groups as $g)
        <a href="{{ route('admin.settings.edit', ['group' => $g]) }}"
           class="admin-tab"
           aria-current="{{ $g === $group ? 'page' : 'false' }}">
            {{ $labels[$g] ?? ucfirst($g) }}
        </a>
    @endforeach
</nav>
