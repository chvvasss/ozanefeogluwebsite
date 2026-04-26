<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Site identity & contact channels
|--------------------------------------------------------------------------
|
| ADR-016 doctrine: ANY channel that does not exist for real MUST stay null
| so that views render NOTHING. Placeholder PGP keys, dummy Signal numbers
| or "coming soon" social links are explicitly forbidden — see the user's
| Session 2 lock list, item 5 ("PGP yoksa tamamen gizle").
|
| Sahibi gerçek bir kanal yayına aldığında bu env değerlerini doldurur;
| header/footer/contact view'ları otomatik açılır.
|
*/

return [

    /*
    |----------------------------------------------------------------------
    | Identity
    |----------------------------------------------------------------------
    */

    'name' => 'Ozan Efeoğlu',
    'role' => 'Foto muhabir ve yayıncı',
    'base' => 'İstanbul',
    'description' => 'Anadolu Ajansı Uluslararası Haber Merkezi\'nde foto muhabir ve yayıncı. Haber fotoğrafı, editöryel notlar, drone haberciliği.',

    /*
    |----------------------------------------------------------------------
    | Default photo credit — every Writing falls back to this when its
    | own `photo_credit` field is null. Wire-service slash convention.
    |----------------------------------------------------------------------
    */

    'default_photo_credit' => env('SITE_DEFAULT_PHOTO_CREDIT', 'Foto: Ozan Efeoğlu / AA'),

    /*
    |----------------------------------------------------------------------
    | AA affiliation visibility — gated flag. Set true only after written
    | institutional approval from Anadolu Ajansı (İnsan Kaynakları +
    | Editöryal Koordinasyon) has been received for the specific banner
    | text used on this site. When false, the affiliation line is not
    | rendered on landing / about. Default true per owner's confirmation
    | that use is legally cleared.
    |----------------------------------------------------------------------
    */

    'affiliation_approved' => env('SITE_AA_AFFILIATION_APPROVED', true),

    /*
    |----------------------------------------------------------------------
    | Editor's desk — anasayfa "Şu sıralar" mikro-bültenine ne yazılacak.
    | Sahibi her hafta/ay bunu env'den günceller. Boş bırakırsa rail gizlenir.
    |----------------------------------------------------------------------
    */

    'current_context' => env('SITE_CURRENT_CONTEXT'),

    /*
    |----------------------------------------------------------------------
    | Manifesto quote — pull-quote text used in homepage Scene 4 (profile).
    | Single sentence. Null = scene 4 falls back to title-led composition.
    |----------------------------------------------------------------------
    */

    'manifesto_quote' => env('SITE_MANIFESTO_QUOTE'),

    /*
    |----------------------------------------------------------------------
    | Contact channels — null değerler view'larda gizlenir
    |----------------------------------------------------------------------
    */

    'contact' => [
        'email' => env('SITE_CONTACT_EMAIL', 'press@ozanefeoglu.com'),
        'signal_url' => env('SITE_SIGNAL_URL'),       // signal.me link
        'pgp_fingerprint' => env('SITE_PGP_FINGERPRINT'),  // boşsa kart tamamen gizlenir
        'pgp_key_id' => env('SITE_PGP_KEY_ID'),
        'pgp_download' => env('SITE_PGP_DOWNLOAD'),     // .asc URL
    ],

    /*
    |----------------------------------------------------------------------
    | Social — gerçek hesap yoksa link basılmaz
    |----------------------------------------------------------------------
    */

    'social' => array_filter([
        'twitter' => env('SITE_TWITTER_URL'),
        'instagram' => env('SITE_INSTAGRAM_URL'),
        'linkedin' => env('SITE_LINKEDIN_URL'),
    ]),

    /*
    |----------------------------------------------------------------------
    | Navigation flags — IA'da gizli/açık kontrolü
    |----------------------------------------------------------------------
    | "Görüntü" (foto seri arşivi) Faz 5'te ayrı PhotoStory modeliyle
    | gelene kadar nav'da görünmez (boş vaat yok).
    */

    'nav' => [
        'show_visuals' => env('SITE_SHOW_VISUALS_NAV', false),
    ],

    /*
    |----------------------------------------------------------------------
    | Feature flags — feed/newsletter vb. henüz implement edilmemiş olanlar
    | false default; true olunca view'lar ilgili linki gösterir.
    |----------------------------------------------------------------------
    */

    'features' => [
        'feed_enabled' => env('SITE_FEED_ENABLED', false),
        'newsletter_enabled' => env('SITE_NEWSLETTER_ENABLED', false),
    ],

    /*
    |----------------------------------------------------------------------
    | Portrait — gerçek portre dosyası yüklendiğinde doldurulur.
    | null ise About masthead portrait-frame render edilmez.
    |----------------------------------------------------------------------
    */

    'portrait' => [
        'url' => env('SITE_PORTRAIT_URL', '/storage/portrait/ozan.jpg'),
        'credit' => env('SITE_PORTRAIT_CREDIT'),       // "Foto: Ad Soyad" (opsiyonel)
    ],

];
