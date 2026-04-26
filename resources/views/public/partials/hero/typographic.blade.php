@php
    /** @var string|null $intro */
    /** @var string|null $eyebrow */
    /** @var string $primaryLabel */
    /** @var string $primaryUrl */
    /** @var string $secondaryLabel */
    /** @var string $secondaryUrl */
@endphp

<section class="scene scene--overture scene--typographic">
    <div class="page-wrap">
        <div class="dossier-grid items-center gap-y-12">
            <div class="dg-12 max-w-[68ch]">
                @include('public.partials.hero._plate', [
                    'intro'          => $intro,
                    'eyebrow'        => $eyebrow,
                    'primaryLabel'   => $primaryLabel,
                    'primaryUrl'     => $primaryUrl,
                    'secondaryLabel' => $secondaryLabel,
                    'secondaryUrl'   => $secondaryUrl,
                ])
            </div>
        </div>
    </div>
</section>
