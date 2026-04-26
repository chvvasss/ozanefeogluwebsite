@php
    /** @var \App\Models\Writing $heroItem */
    /** @var string|null $intro */
    /** @var string|null $eyebrow */
    /** @var string $primaryLabel */
    /** @var string $primaryUrl */
    /** @var string $secondaryLabel */
    /** @var string $secondaryUrl */

    $rolePrimary   = site_setting('identity.role_primary');
    $roleSecondary = site_setting('identity.role_secondary');
    $roleTertiary  = site_setting('identity.role_tertiary');
    $affApproved   = site_setting('identity.affiliation_approved');
    $affiliation   = site_setting('identity.affiliation');
@endphp

{{-- ════════════════════════════════════════════════════════════════════════
     HERO · MASTHEAD
     Full-bleed photo + floating editorial plate.
     The photo carries the emotional weight; the plate carries the identity.
     ════════════════════════════════════════════════════════════════════════ --}}
<section class="scene scene--hero-masthead" aria-labelledby="hero-headline">

    {{-- Full-bleed image layer --}}
    <figure class="masthead-figure">
        <img class="masthead-img"
             src="{{ $heroItem->coverUrl('w1920') ?? $heroItem->coverUrl() }}"
             srcset="{{ $heroItem->coverSrcset() }}"
             sizes="100vw"
             alt="{{ $heroItem->title }}"
             width="1920" height="1280"
             loading="eager"
             fetchpriority="high">
        <span class="masthead-scrim" aria-hidden="true"></span>
    </figure>

    {{-- Floating editorial plate — sits on top of bottom-left of the photo --}}
    <div class="page-wrap masthead-wrap">
        <div class="masthead-plate">
            @if ($eyebrow)
                <p class="eyebrow masthead-eyebrow">{{ $eyebrow }}</p>
            @endif

            <h1 id="hero-headline" class="masthead-headline">{{ site_setting('identity.name') }}</h1>

            <ul class="masthead-roles" aria-label="Kimlik">
                @if ($rolePrimary)
                    <li class="masthead-role--primary">{{ $rolePrimary }}</li>
                @endif
                @if ($roleSecondary)
                    <li class="masthead-role--secondary">{{ $roleSecondary }}</li>
                @endif
                @if ($roleTertiary)
                    <li class="masthead-role--tertiary">{{ $roleTertiary }}</li>
                @endif
            </ul>

            @if ($affApproved && $affiliation)
                <p class="masthead-affiliation">
                    <span class="masthead-affiliation-rule" aria-hidden="true"></span>
                    {{ $affiliation }}
                </p>
            @endif

            <div class="masthead-ctas">
                @if ($primaryLabel && $primaryUrl)
                    <a href="{{ $primaryUrl }}" class="btn btn--masthead">
                        {{ $primaryLabel }} <span aria-hidden="true">→</span>
                    </a>
                @endif
                @if ($secondaryLabel && $secondaryUrl)
                    <a href="{{ $secondaryUrl }}" class="masthead-link-quiet">
                        {{ $secondaryLabel }} →
                    </a>
                @endif
            </div>
        </div>

        {{-- Lead caption box — floats bottom-right, mirrors a magazine pull-tag --}}
        <a href="{{ $heroItem->url() }}" class="masthead-leadtag" aria-label="{{ $heroItem->title }}">
            <span class="masthead-leadtag-kicker">Son kare</span>
            <span class="masthead-leadtag-title">{{ $heroItem->title }}</span>
            <span class="masthead-leadtag-cta">Dosyayı aç →</span>
        </a>
    </div>
</section>
