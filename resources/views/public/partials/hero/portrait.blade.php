@php
    /** @var string|null $portraitUrl */
    /** @var string|null $portraitCredit */
    /** @var string|null $intro */
    /** @var string|null $eyebrow */
    /** @var string $primaryLabel */
    /** @var string $primaryUrl */
    /** @var string $secondaryLabel */
    /** @var string $secondaryUrl */
@endphp

<section class="scene scene--overture scene--portrait">
    <div class="page-wrap">
        <div class="dossier-grid items-center gap-y-12">

            <div class="{{ $portraitUrl ? 'dg-5' : 'dg-12 max-w-[68ch]' }}">
                @include('public.partials.hero._plate', [
                    'intro'          => $intro,
                    'eyebrow'        => $eyebrow,
                    'primaryLabel'   => $primaryLabel,
                    'primaryUrl'     => $primaryUrl,
                    'secondaryLabel' => $secondaryLabel,
                    'secondaryUrl'   => $secondaryUrl,
                ])
            </div>

            @if ($portraitUrl)
                <figure class="dg-7 m-0">
                    <img class="overture-portrait"
                         src="{{ $portraitUrl }}"
                         alt="{{ site_setting('identity.name') }} — portre"
                         width="900" height="1200"
                         loading="eager"
                         fetchpriority="high">
                    @if ($portraitCredit)
                        <figcaption class="portrait-caption">{{ $portraitCredit }}</figcaption>
                    @endif
                </figure>
            @endif
        </div>
    </div>
</section>
