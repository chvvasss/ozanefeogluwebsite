# Bilgi Mimarisi

> Sayfa hiyerarşisi, URL yapısı, kullanıcı yolculukları (public visitor, blog reader, admin operatör).

## 1. Top-level navigasyon (public)

```
Header nav (left logo, right items):
  Works   →  /works
  Writing →  /writing
  About   →  /about
  Contact →  /contact

Footer:
  - Sosyal linkler (GitHub, X/Mastodon, LinkedIn, RSS)
  - Newsletter signup
  - Sitemap link, Privacy, Colophon
  - © year + dil değiştirici (TR/EN)
```

## 2. URL şeması

| Kaynak | URL deseni | Notlar |
|---|---|---|
| Anasayfa | `/` | Hero + 3-4 öne çıkan iş + son 3 yazı + about snippet + CTA |
| İşler listesi | `/works` | Filtre: `?tag=ux&year=2025` |
| İş detayı | `/works/{slug}` | Slug değişirse 301 redirect otomatik |
| Yazılar listesi | `/writing` | Sayfalama `?page=2`, filtre `?tag=php&category=essays` |
| Yazı detayı | `/writing/{slug}` | Aynı slug stratejisi |
| Etiket arşivi | `/writing/tag/{slug}` | Tag bazlı yazı listesi |
| Kategori arşivi | `/writing/category/{slug}` | |
| Yıl arşivi | `/writing/{year}` | `/writing/2025` → o yılın yazıları |
| Hakkında | `/about` | Statik sayfa (admin'den editable) |
| İletişim | `/contact` | Form + alt bilgi |
| Custom sayfalar | `/{slug}` | "Now", "Colophon", "Uses" gibi statik sayfalar |
| RSS feed | `/feed.xml`, `/writing/feed.xml` | Atom 1.0 |
| Sitemap | `/sitemap.xml` | Dinamik |
| Robots | `/robots.txt` | Dinamik (admin'den editable) |
| Search | `/search?q=...` | Meilisearch backed |
| Health | `/health` | JSON: db, cache, queue status |

### i18n URL stratejisi
- Default locale: `tr`. Path-based: `/en/works`, `/en/writing/...`. TR root'ta (kanonik).
- `<link rel="alternate" hreflang="tr" />` her sayfada.
- Switcher cookie + localStorage yerine query string-free, doğrudan path. SEO-friendly.

### Admin URL'leri
| Kaynak | URL |
|---|---|
| Login | `/admin/login` |
| Dashboard | `/admin` |
| Projeler | `/admin/works`, `/admin/works/create`, `/admin/works/{id}/edit` |
| Yazılar | `/admin/writing`, `/admin/writing/create`, `/admin/writing/{id}/edit` |
| Sayfalar | `/admin/pages`, `/admin/pages/create`, `/admin/pages/{id}/edit` |
| Kategoriler | `/admin/categories` |
| Etiketler | `/admin/tags` |
| Medya | `/admin/media` |
| Yorumlar | `/admin/comments` |
| Mesajlar | `/admin/messages` (iletişim form gelenleri) |
| Menüler | `/admin/menus` |
| Tema | `/admin/theme` |
| SEO | `/admin/seo`, `/admin/redirects`, `/admin/robots` |
| Kullanıcılar | `/admin/users`, `/admin/users/{id}/edit` |
| Audit log | `/admin/audit-log` |
| Yedekleme | `/admin/backups` |
| Ayarlar | `/admin/settings` |
| Profil | `/admin/profile`, `/admin/profile/2fa` |
| Logout | `POST /admin/logout` |

## 3. Veri modeli (high-level ER)

```
users
  id, name, email, password_hash, role, two_factor_secret, two_factor_recovery_codes (encrypted),
  email_verified_at, created_at, updated_at, deleted_at

projects
  id, slug, title (translatable JSON), summary (tr JSON), body (tr JSON),
  status (draft|scheduled|published), published_at,
  cover_media_id, year, client, role,
  external_url, repo_url,
  is_featured, sort_order,
  meta_title (tr JSON), meta_description (tr JSON), og_image_id,
  schema_overrides JSON,
  author_id, created_at, updated_at, deleted_at

posts
  id, slug, title (tr JSON), excerpt (tr JSON), body (tr JSON), reading_time_min,
  status, published_at, scheduled_at,
  cover_media_id, og_image_id,
  meta_title, meta_description, canonical_url, robots_directive,
  is_featured, comments_enabled,
  author_id, created_at, updated_at, deleted_at

post_versions     ← revision history
  id, post_id, body (tr JSON), saved_by_user_id, created_at

pages             ← static pages (Hakkında, Now, Colophon...)
  id, slug, title (tr JSON), body (tr JSON), meta_title, meta_description,
  template (default|wide|landing), status,
  created_at, updated_at, deleted_at

categories
  id, slug, name (tr JSON), description (tr JSON), parent_id (tree?),
  sort_order, applies_to (post|project|both)

tags
  id, slug, name (tr JSON)

taggables / categorizables    ← polymorphic pivot
  taggable_type, taggable_id, tag_id

technologies   ← portfolio için ek bir taxonomy
  id, slug, name, icon_media_id

project_technology  ← pivot
  project_id, technology_id

media
  id, file_name (original), disk, path,
  mime, size, width, height,
  alt_text (tr JSON), caption (tr JSON), credit,
  variants JSON (responsive sizes/formats),
  uploaded_by_user_id, created_at, deleted_at

media_collections   ← grouping
  id, name, description

media_collection_items
  collection_id, media_id, sort_order

menus
  id, location (header|footer|...), items JSON   ← veya menu_items table

menu_items
  id, menu_id, parent_id, label (tr JSON),
  link_type (internal|external),
  linkable_type, linkable_id (polymorphic for internal),
  external_url, sort_order, is_visible

settings   ← key-value store, JSON value
  key, value, updated_at

theme_config   ← settings'in özel alt kümesi (renkler, fontlar, logo media id)

redirects
  id, from_path, to_path, status_code (301|302), hits, last_hit_at, created_at

audit_logs
  id, user_id, action, auditable_type, auditable_id,
  changes JSON (before/after diff), ip, user_agent, created_at

contact_messages
  id, name, email, subject, body, ip, user_agent, status (new|read|replied|spam),
  created_at

newsletter_subscribers
  id, email, confirmed_at, confirmation_token, unsubscribe_token, locale, created_at

comments  (Faz 5)
  id, post_id, parent_id, author_name, author_email, author_url, body,
  status (pending|approved|spam|trash), ip, user_agent, created_at

sessions  ← Laravel default session driver (DB or Redis)

password_resets, personal_access_tokens, jobs, failed_jobs  ← Laravel standard
```

### Çeviri stratejisi (Spatie Translatable yaklaşımı)
JSON kolonlarda locale-keyed: `{"tr": "Başlık", "en": "Title"}`. Eloquent accessor app locale'i otomatik döner. Fallback locale TR.

### Slug stratejisi
- Slug locale-aware. Aynı yazı TR ve EN için farklı slug olabilir: `/writing/sade-yazilim-uzerine` vs `/en/writing/on-simple-software`.
- Slug değişirse: eski → yeni redirect tablosuna otomatik insert (status 301).

## 4. Kullanıcı yolculukları

### 4.1 First-time visitor
1. Anasayfaya gelir → hero + öne çıkan işler.
2. İşlerden birine tıklar → case study okur.
3. Sondaki "next project" linkiyle bir sonraki işe geçer veya yazılara döner.
4. Yazılardan birini okur → ToC ile gezinir.
5. Yazı sonunda "related posts" → ek tüketim.
6. About veya Contact'a yönlenir → e-mail veya social.
**Optimal yol:** 0 friction, hızlı sayfalar, içeriğin önünde durmayan UI.

### 4.2 Returning blog reader
1. Doğrudan `/writing` veya RSS ile gelir.
2. Yazıyı okur. Belki yorum bırakır (Faz 5) veya newsletter'a katılır.
3. Etiket/kategori filtrelerle keşif yapar.
**Optimal:** RSS-first, keyboard shortcut (j/k navigation, / search), quick search.

### 4.3 Admin operatör (sahibi, gayri-teknik)
1. `/admin/login` → email + parola → 2FA kod.
2. Dashboard'da: bekleyen yorumlar (varsa), okunmamış mesajlar, son aktiviteler.
3. **Yeni yazı:** "Writing" → "+ New post" → editor → tipografi-öncelikli, distraction-free yazma → "Save draft" / "Publish" / "Schedule".
4. **Medya:** Editör içinden veya doğrudan media library; drag-drop upload, alt text zorlu.
5. **Tema:** "Theme" → renk preset seç veya hex gir → preview → kaydet → public site güncellenir.
6. **Yedekleme:** "Backups" → "Download latest" veya schedule.
**Optimal:** Hiçbir terim teknik değil. Kaybetme riski yok (autosave + revision).

## 5. İçerik durumları (content status machine)

```
draft ──────────────► scheduled ──────────────► published
  │                       │                          │
  │                       └──────► published         │
  │                                                  │
  └────────► trash ◄─────── unpublished ◄────────────┘
```

- **draft:** sadece editör/admin görür.
- **scheduled:** `published_at` gelecekte; cron `posts:publish-scheduled` her dakika çalışır.
- **published:** public.
- **unpublished:** önceden yayınlanmış, geri çekilmiş; URL 404 (ama redirect kayıtlıysa onu izle).
- **trash:** soft delete, 30 gün sonra purge cron.

## 6. Yetki matrisi

| Rol | Yapabilir | Yapamaz |
|---|---|---|
| **Super Admin** | Her şey + kullanıcı CRUD + audit log + yedekleme + tema + ayarlar | — |
| **Admin** | İçerik tüm CRUD + medya + menü + SEO | Kullanıcı management, sistem ayarları, yedekleme |
| **Editor** | Tüm yazı/proje/sayfa CRUD + medya | Kategori/etiket silme, kullanıcı, ayarlar |
| **Contributor** | Kendi yazılarını taslak + submit-for-review | Publish, başkasının yazısı, medya silme |
| **Viewer** | Read-only admin | Hiçbir yazma |

Multi-rol değil — her kullanıcı tek rol.

## 7. Sitemap çıktısı (örnek)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
  <url>
    <loc>https://ozanefeoglu.com/</loc>
    <xhtml:link rel="alternate" hreflang="tr" href="https://ozanefeoglu.com/"/>
    <xhtml:link rel="alternate" hreflang="en" href="https://ozanefeoglu.com/en"/>
    <lastmod>2026-04-18T12:00:00Z</lastmod>
  </url>
  <!-- works, writing, pages -->
</urlset>
```

## 8. 404 strategy

- Custom 404 sayfası: yararlı (arama kutusu, popüler içerik linkleri, "did you mean" via Meili).
- 404 logged: hangi URL kaç kez vuruldu. Admin "Suggested redirects" panelinde.
- Slug değişikliği → otomatik 301 (model observer).
