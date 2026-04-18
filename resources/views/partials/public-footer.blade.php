<footer class="mt-24 border-t border-[var(--color-rule)]">
    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-14 grid gap-10 md:grid-cols-[2fr_1fr_1fr_1fr] items-start">
        <div>
            <div class="flex items-baseline gap-2 mb-3">
                <span class="inline-block w-2 h-2 rounded-full bg-[var(--color-accent)]"></span>
                <span class="display-fraunces text-lg">Ozan Efeoğlu</span>
            </div>
            <p class="text-sm text-[var(--color-ink-muted)] max-w-[38ch] leading-relaxed">
                Saha muhabiri ve yazar. İstanbul'dan.
            </p>
        </div>

        <div>
            <p class="eyebrow mb-3">Gezin</p>
            <ul class="space-y-1.5 text-sm">
                <li><a href="{{ route('writing.index') }}" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">Yazılar</a></li>
                <li><a href="{{ route('about') }}" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">Hakkında</a></li>
                <li><a href="{{ route('contact') }}" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">İletişim</a></li>
                <li><a href="/feed.xml" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">RSS</a></li>
            </ul>
        </div>

        <div>
            <p class="eyebrow mb-3">İletişim</p>
            <ul class="space-y-1.5 text-sm">
                <li><a href="mailto:press@ozanefeoglu.com" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">E-posta</a></li>
                <li><a href="#" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">Signal</a></li>
                <li><a href="#" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">PGP</a></li>
            </ul>
        </div>

        <div>
            <p class="eyebrow mb-3">Sosyal</p>
            <ul class="space-y-1.5 text-sm">
                <li><a href="https://x.com/" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">X / Twitter</a></li>
                <li><a href="https://instagram.com/" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">Instagram</a></li>
                <li><a href="https://linkedin.com/" class="no-underline text-[var(--color-ink-muted)] hover:text-[var(--color-ink)]">LinkedIn</a></li>
            </ul>
        </div>
    </div>

    <div class="max-w-[var(--container-wide)] mx-auto px-[clamp(1rem,4vw,3rem)] py-5 flex flex-wrap items-center justify-between gap-3 text-xs text-[var(--color-ink-subtle)] font-mono border-t border-[var(--color-rule)]">
        <span>© {{ now()->format('Y') }} Ozan Efeoğlu</span>
        <span>İstanbul</span>
    </div>
</footer>
