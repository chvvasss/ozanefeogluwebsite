@php
    /** Reusable editorial-plate for the hero scene. */
    /** @var string|null $intro */
    /** @var string|null $eyebrow */
    /** @var string $primaryLabel */
    /** @var string $primaryUrl */
    /** @var string $secondaryLabel */
    /** @var string $secondaryUrl */
@endphp

<div class="editorial-plate">
    <p class="eyebrow">{{ $eyebrow ?: 'Portfolyo · arşiv · saha' }}</p>

    <h1 class="display-statuesque">{{ site_setting('identity.name') }}</h1>

    <ul class="plate-roles" aria-label="Kimlik">
        @php
            $rolePrimary   = site_setting('identity.role_primary');
            $roleSecondary = site_setting('identity.role_secondary');
            $roleTertiary  = site_setting('identity.role_tertiary');
        @endphp
        @if ($rolePrimary)
            <li class="plate-role--primary">{{ $rolePrimary }}</li>
        @endif
        @if ($roleSecondary)
            <li class="plate-role--secondary">{{ $roleSecondary }}</li>
        @endif
        @if ($roleTertiary)
            <li class="plate-role--tertiary">{{ $roleTertiary }}</li>
        @endif
    </ul>

    @if (site_setting('identity.affiliation_approved') && site_setting('identity.affiliation'))
        <p class="plate-affiliation">
            {{ site_setting('identity.affiliation') }}
        </p>
    @endif

    @if (! empty($intro))
        <p class="text-[var(--text-md)] leading-[1.7] text-[var(--color-ink-muted)] max-w-[46ch]">
            {{ $intro }}
        </p>
    @endif

    <div class="flex flex-wrap gap-3 pt-2">
        @if ($primaryLabel && $primaryUrl)
            <a href="{{ $primaryUrl }}" class="btn">
                {{ $primaryLabel }} <span aria-hidden="true">→</span>
            </a>
        @endif
        @if ($secondaryLabel && $secondaryUrl)
            <a href="{{ $secondaryUrl }}" class="btn btn--secondary">
                {{ $secondaryLabel }}
            </a>
        @endif
    </div>
</div>
