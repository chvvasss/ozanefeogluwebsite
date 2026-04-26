<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Writing;
use App\Support\SettingsRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * Admin · Site settings.
 *
 * Group-scoped edit/update. Each group maps to a Blade partial and a
 * validation ruleset. Values are persisted via SettingsRepository which
 * automatically busts the shared cache.
 *
 * Groups handled here (Phase B.2): identity, contact, nav.
 * Hero / theme / features arrive in B.3 & D.
 */
class SettingsController extends Controller
{
    private const GROUPS = ['identity', 'contact', 'nav', 'hero', 'social', 'theme'];

    public const HERO_MODES = ['featured_photo', 'rotation', 'typographic', 'portrait'];

    public const DARK_MODES = ['light', 'dark'];

    public const THEME_PRESETS = ['dispatch']; // single preset for now — black/white/crimson locked

    public function index(): RedirectResponse
    {
        return redirect()->route('admin.settings.edit', ['group' => 'identity']);
    }

    public function edit(string $group): View|RedirectResponse
    {
        if (! in_array($group, self::GROUPS, true)) {
            return redirect()->route('admin.settings.index');
        }

        return view("admin.settings.{$group}", [
            'group' => $group,
            'groups' => self::GROUPS,
            'values' => $this->currentValues($group),
            'heroModes' => self::HERO_MODES,
            'heroCandidates' => $group === 'hero' ? $this->heroCandidates() : collect(),
        ]);
    }

    /**
     * Hero picker options: published + hero_eligible, most recent first.
     *
     * @return Collection<int, Writing>
     */
    private function heroCandidates(): Collection
    {
        return Writing::query()
            ->where('hero_eligible', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'published_at']);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        if (! in_array($group, self::GROUPS, true)) {
            return redirect()->route('admin.settings.index');
        }

        $validated = Validator::make(
            $request->all(),
            $this->rulesFor($group),
            [],
            $this->attributesFor($group)
        )->validate();

        // Forms submit bracket-notation (identity[name]) which validator sees
        // as nested. Flatten back to dot keys (identity.name) for storage.
        $flat = Arr::dot($validated);

        foreach ($flat as $key => $value) {
            SettingsRepository::set($key, $this->normalize($key, $value), $this->groupOf($key, $group));
        }

        return redirect()
            ->route('admin.settings.edit', ['group' => $group])
            ->with('status', __('Ayarlar kaydedildi.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function currentValues(string $group): array
    {
        $keys = array_keys($this->rulesFor($group));
        $out = [];

        foreach ($keys as $key) {
            $out[$key] = SettingsRepository::get($key);
        }

        return $out;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    private function rulesFor(string $group): array
    {
        return match ($group) {
            'identity' => [
                'identity.name' => ['required', 'string', 'max:120'],
                'identity.role_primary' => ['required', 'string', 'max:120'],
                'identity.role_secondary' => ['nullable', 'string', 'max:160'],
                'identity.role_tertiary' => ['nullable', 'string', 'max:200'],
                'identity.base' => ['nullable', 'string', 'max:80'],
                'identity.affiliation' => ['nullable', 'string', 'max:200'],
                'identity.affiliation_approved' => ['required', 'boolean'],
                'identity.description' => ['nullable', 'string', 'max:400'],
                'identity.manifesto_quote' => ['nullable', 'string', 'max:200'],
                'identity.current_context' => ['nullable', 'string', 'max:120'],
            ],
            'contact' => [
                'contact.email' => ['required', 'email:rfc', 'max:160'],
                'contact.signal_url' => ['nullable', 'url', 'max:200'],
                'contact.pgp_fingerprint' => ['nullable', 'string', 'max:80'],
                'contact.pgp_key_id' => ['nullable', 'string', 'max:40'],
                'contact.pgp_download' => ['nullable', 'url', 'max:200'],
                'contact.retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            ],
            'nav' => [
                'nav.show_visuals' => ['required', 'boolean'],
                'hero.eyebrow' => ['nullable', 'string', 'max:120'],
                'hero.cta_primary_label' => ['nullable', 'string', 'max:40'],
                'hero.cta_primary_url' => ['nullable', 'string', 'max:200'],
                'hero.cta_secondary_label' => ['nullable', 'string', 'max:40'],
                'hero.cta_secondary_url' => ['nullable', 'string', 'max:200'],
            ],
            'hero' => [
                'hero.mode' => ['required', 'string', 'in:'.implode(',', self::HERO_MODES)],
                'hero.featured_writing_id' => ['nullable', 'integer', 'exists:writings,id'],
            ],
            'social' => [
                'social.mastodon_url' => ['nullable', 'url', 'max:200'],
                'social.bluesky_url' => ['nullable', 'url', 'max:200'],
                'social.x_url' => ['nullable', 'url', 'max:200'],
                'social.instagram_url' => ['nullable', 'url', 'max:200'],
                'social.linkedin_url' => ['nullable', 'url', 'max:200'],
                'social.github_url' => ['nullable', 'url', 'max:200'],
            ],
            'theme' => [
                'theme.preset' => ['required', 'string', 'in:'.implode(',', self::THEME_PRESETS)],
                'theme.dark_mode' => ['required', 'string', 'in:'.implode(',', self::DARK_MODES)],
                'features.feed_enabled' => ['required', 'boolean'],
                'features.newsletter_enabled' => ['required', 'boolean'],
                'features.search_enabled' => ['required', 'boolean'],
                'features.demo_content_banner' => ['required', 'boolean'],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private function attributesFor(string $group): array
    {
        return match ($group) {
            'identity' => [
                'identity.name' => 'isim',
                'identity.role_primary' => 'birincil rol',
                'identity.role_secondary' => 'ikincil rol',
                'identity.role_tertiary' => 'üçüncül rol',
                'identity.base' => 'merkez',
                'identity.affiliation' => 'bağlı kurum',
                'identity.affiliation_approved' => 'kurum onayı',
                'identity.description' => 'meta açıklama',
                'identity.manifesto_quote' => 'manifesto cümlesi',
                'identity.current_context' => 'şu an',
            ],
            'contact' => [
                'contact.email' => 'e-posta',
                'contact.signal_url' => 'signal URL',
                'contact.pgp_fingerprint' => 'PGP parmak izi',
                'contact.pgp_key_id' => 'PGP key ID',
                'contact.pgp_download' => 'PGP indirme adresi',
                'contact.retention_days' => 'saklama süresi',
            ],
            'nav' => [
                'nav.show_visuals' => 'görseller',
                'hero.eyebrow' => 'hero eyebrow',
                'hero.cta_primary_label' => 'birincil buton metni',
                'hero.cta_primary_url' => 'birincil buton URL',
                'hero.cta_secondary_label' => 'ikincil buton metni',
                'hero.cta_secondary_url' => 'ikincil buton URL',
            ],
            'hero' => [
                'hero.mode' => 'hero modu',
                'hero.featured_writing_id' => 'öne çıkan yazı',
            ],
            'social' => [
                'social.mastodon_url' => 'Mastodon',
                'social.bluesky_url' => 'Bluesky',
                'social.x_url' => 'X',
                'social.instagram_url' => 'Instagram',
                'social.linkedin_url' => 'LinkedIn',
                'social.github_url' => 'GitHub',
            ],
            'theme' => [
                'theme.preset' => 'tema',
                'theme.dark_mode' => 'karanlık mod',
                'features.feed_enabled' => 'RSS yayını',
                'features.newsletter_enabled' => 'bülten',
                'features.search_enabled' => 'arama',
                'features.demo_content_banner' => 'demo içerik uyarısı',
            ],
            default => [],
        };
    }

    /**
     * Resolve the persistence group for a given dotted key.
     * Nav tab mixes nav.* + hero.* so we trust the prefix, not the tab.
     */
    private function groupOf(string $key, string $fallback): string
    {
        $prefix = strtok($key, '.');

        return is_string($prefix) && $prefix !== '' ? $prefix : $fallback;
    }

    /**
     * Light coercion: boolean strings → bool, numeric-typed fields → int.
     */
    private function normalize(string $key, mixed $value): mixed
    {
        $booleans = [
            'identity.affiliation_approved',
            'nav.show_visuals',
            'features.feed_enabled',
            'features.newsletter_enabled',
            'features.search_enabled',
            'features.demo_content_banner',
        ];
        if (in_array($key, $booleans, true)) {
            return (bool) $value;
        }

        if (in_array($key, ['contact.retention_days', 'hero.featured_writing_id'], true)) {
            if ($value === null || $value === '' || $value === '0') {
                return $key === 'contact.retention_days' ? (int) $value : null;
            }

            return (int) $value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return $value;
    }
}
