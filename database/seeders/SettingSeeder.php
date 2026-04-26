<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * SettingSeeder — baseline defaults for the site.
 *
 * Idempotent: uses firstOrCreate so admin-set values are never overwritten
 * on subsequent `db:seed` runs. Fresh installs get the positioning-aligned
 * defaults that match the current identity (foto muhabir + editör at AA
 * Uluslararası Haber Merkezi, İstanbul).
 *
 * Groups:
 *   identity  — name, roles, base, affiliation, manifesto
 *   contact   — email, signal, pgp, retention
 *   photo     — default credit
 *   hero      — mode, eyebrow, cta labels
 *   nav       — visuals toggle
 *   seo       — meta defaults
 *   theme     — preset, dark mode
 *   features  — feature flags
 *   analytics — tracking ids (empty by default)
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // ── identity ─────────────────────────────────────────────
            ['key' => 'identity.name',                 'value' => 'Ozan Efeoğlu',                                                                    'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.role_primary',         'value' => 'Foto muhabir',                                                                    'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.role_secondary',       'value' => 'editör · yayıncı',                                                                'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.role_tertiary',        'value' => 'Drone haberciliği · Görsel göstergebilim',                                         'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.base',                 'value' => 'İstanbul',                                                                        'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.affiliation',          'value' => 'Anadolu Ajansı · Uluslararası Haber Merkezi · İstanbul',                          'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.affiliation_approved', 'value' => true,                                                                              'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.description',          'value' => 'Anadolu Ajansı Uluslararası Haber Merkezi\'nde foto muhabir ve editör. İstanbul merkezli saha kaydı, dron haberciliği, görsel göstergebilim.', 'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.manifesto_quote',      'value' => 'Kareyi değil kararı koruyorum.',                                                  'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.current_context',      'value' => 'Şu an · Haber Masası · İstanbul',                                                  'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.portrait_url',         'value' => null,                                                                              'group' => 'identity', 'is_public' => true],
            ['key' => 'identity.portrait_credit',      'value' => null,                                                                              'group' => 'identity', 'is_public' => true],

            // ── contact ──────────────────────────────────────────────
            ['key' => 'contact.email',           'value' => 'press@ozanefeoglu.com', 'group' => 'contact', 'is_public' => true],
            ['key' => 'contact.signal_url',      'value' => null,                    'group' => 'contact', 'is_public' => true],
            ['key' => 'contact.pgp_fingerprint', 'value' => null,                    'group' => 'contact', 'is_public' => true],
            ['key' => 'contact.pgp_key_id',      'value' => null,                    'group' => 'contact', 'is_public' => true],
            ['key' => 'contact.pgp_download',    'value' => null,                    'group' => 'contact', 'is_public' => true],
            ['key' => 'contact.retention_days',  'value' => 90,                      'group' => 'contact', 'is_public' => false],

            // ── photo ────────────────────────────────────────────────
            ['key' => 'photo.default_credit', 'value' => 'Foto: Ozan Efeoğlu / AA', 'group' => 'photo', 'is_public' => true],

            // ── hero ─────────────────────────────────────────────────
            ['key' => 'hero.mode',                'value' => 'featured_photo',                     'group' => 'hero', 'is_public' => true],
            ['key' => 'hero.eyebrow',             'value' => 'Haber Masası · İstanbul',            'group' => 'hero', 'is_public' => true],
            ['key' => 'hero.cta_primary_label',   'value' => 'Çalışmalar',                         'group' => 'hero', 'is_public' => true],
            ['key' => 'hero.cta_primary_url',     'value' => '/yazilar',                           'group' => 'hero', 'is_public' => true],
            ['key' => 'hero.cta_secondary_label', 'value' => 'Hakkımda',                           'group' => 'hero', 'is_public' => true],
            ['key' => 'hero.cta_secondary_url',   'value' => '/hakkimda',                          'group' => 'hero', 'is_public' => true],
            ['key' => 'hero.featured_writing_id', 'value' => null,                                 'group' => 'hero', 'is_public' => true],

            // ── nav ──────────────────────────────────────────────────
            ['key' => 'nav.show_visuals', 'value' => true, 'group' => 'nav', 'is_public' => true],

            // ── seo ──────────────────────────────────────────────────
            ['key' => 'seo.meta_title_suffix', 'value' => ' — Ozan Efeoğlu',                                                'group' => 'seo', 'is_public' => true],
            ['key' => 'seo.meta_description',  'value' => 'Foto muhabir ve editör. İstanbul merkezli saha kaydı, dron haberciliği.', 'group' => 'seo', 'is_public' => true],
            ['key' => 'seo.og_image_url',      'value' => null,                                                             'group' => 'seo', 'is_public' => true],

            // ── social ───────────────────────────────────────────────
            ['key' => 'social.mastodon_url',  'value' => null, 'group' => 'social', 'is_public' => true],
            ['key' => 'social.bluesky_url',   'value' => null, 'group' => 'social', 'is_public' => true],
            ['key' => 'social.x_url',         'value' => null, 'group' => 'social', 'is_public' => true],
            ['key' => 'social.instagram_url', 'value' => null, 'group' => 'social', 'is_public' => true],
            ['key' => 'social.linkedin_url',  'value' => null, 'group' => 'social', 'is_public' => true],
            ['key' => 'social.github_url',    'value' => null, 'group' => 'social', 'is_public' => true],

            // ── theme ────────────────────────────────────────────────
            ['key' => 'theme.preset',    'value' => 'dispatch', 'group' => 'theme', 'is_public' => true],
            ['key' => 'theme.dark_mode', 'value' => 'light',    'group' => 'theme', 'is_public' => true],

            // ── features ─────────────────────────────────────────────
            ['key' => 'features.feed_enabled',         'value' => false, 'group' => 'features', 'is_public' => true],
            ['key' => 'features.newsletter_enabled',   'value' => false, 'group' => 'features', 'is_public' => true],
            ['key' => 'features.search_enabled',       'value' => false, 'group' => 'features', 'is_public' => true],
            ['key' => 'features.comments_enabled',     'value' => false, 'group' => 'features', 'is_public' => true],
            ['key' => 'features.demo_content_banner',  'value' => true,  'group' => 'features', 'is_public' => true],

            // ── analytics ────────────────────────────────────────────
            ['key' => 'analytics.plausible_domain', 'value' => null, 'group' => 'analytics', 'is_public' => false],
            ['key' => 'analytics.umami_website_id', 'value' => null, 'group' => 'analytics', 'is_public' => false],
        ];

        foreach ($defaults as $row) {
            Setting::query()->firstOrCreate(
                ['key' => $row['key']],
                [
                    'value' => $row['value'],
                    'group' => $row['group'],
                    'is_public' => $row['is_public'],
                ]
            );
        }
    }
}
