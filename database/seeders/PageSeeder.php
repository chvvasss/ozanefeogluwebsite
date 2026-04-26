<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * PageSeeder — Field Dossier v3 (Emergency Rebuild, Batch 4).
 *
 * Doctrine: NO FAKE DATA. Bu seeder sahibinin gerçek özgeçmişinden + doğrulanan
 * kurumsal afiliyasyonundan beslenir. Eski PageSeeder'da uydurulmuş Reuters
 * bureau, IFJ kart no, European Press Prize, fake Signal handle ve fake PGP
 * fingerprint TAMAMEN kaldırıldı. Channel listesi `null` default — gerçek
 * iletişim kanalları config('site.contact.*') üzerinden render olur, view
 * fake bir şey BASMAZ.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAbout();
        $this->seedContact();
    }

    private function seedAbout(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'hakkimda'],
            [
                'kind' => 'system',
                'template' => 'about',
                'title' => ['tr' => 'Hakkında'],
                'intro' => ['tr' => 'İstanbul tabanlı foto muhabir, yayıncı ve drone haberciliği üzerine yüksek lisans araştırmacısı. Anadolu Ajansı Uluslararası Haber Merkezi\'nde görüntü ve haber üretiyor.'],
                'body' => ['tr' => <<<'HTML'
<p>Yazılım eğitimiyle başladım. Yalova Aksa Anadolu Teknik Lisesi'nde bilgisayar yazılımı, Uludağ Üniversitesi'nde bilgisayar programcılığı okudum. Mesleğin teknik temeli buradan; düşünme biçimimin geri kalanı görüntüden ve sahadan geldi.</p>

<p>Görselin gücü beni içine çekti. İstanbul Aydın Üniversitesi'nde Fotoğrafçılık ve Kameramanlık eğitimi aldım, freelance ürün ve reklam fotoğrafçılığı yaparak ışığın ve açının dilini stüdyoda öğrendim. Ama hikâyeyi sadece çekmek yetmiyordu — aktarmak istedim.</p>

<p>Kocaeli Üniversitesi'nde Gazetecilik bölümüne adım attım, eğitimim sürerken Anadolu Ajansı bünyesinde Savaş Muhabirliği eğitimi aldım. İlk görevim Hatay'da muhabir olarak başladı; Zeytin Dalı Harekâtı sırasında sınır hattındaki kritik gelişmeleri yerinden takip ettim. Adana Bölge Müdürlüğünün sorumluluk alanı — Adana, Hatay, Mersin, Osmaniye — saha tecrübemin esas zeminini kurdu.</p>

<p>Bugün İstanbul'dayım. Anadolu Ajansı Uluslararası Haber Merkezi'nde foto muhabiri ve yayıncı olarak çalışıyorum. Yazılım kökenim, saha tecrübem ve akademik birikimim beni Hasan Kalyoncu Üniversitesi'nde Drone Haberciliği ve Görsel Göstergebilim üzerine yüksek lisans çalışmasına yönlendirdi. Tezimde insansız hava araçlarının haber anlatısındaki rolünü bilimsel bir çerçeveye oturtmaya çalıştım; akademik çalışmamdan da faydalanarak saha pratiklerini kitaplaştırdım.</p>

<p>Bu site bir not defteri. Ajansa giden haberlerin arkasındaki on iki saatin notları, akademik ilgi alanlarım ve yayımladığım dosyalar burada — Türkçe, açık, tek elden.</p>
HTML],
                'meta_title' => ['tr' => 'Hakkında'],
                'meta_description' => ['tr' => 'Foto muhabir Ozan Efeoğlu — Anadolu Ajansı Uluslararası Haber Merkezi. Drone haberciliği ve görsel göstergebilim üzerine yüksek lisans araştırmacısı.'],
                'extras' => [
                    // Subhead pipe identities (Janine di Giovanni pattern)
                    'identities' => [
                        'foto muhabir',
                        'yayıncı',
                        'araştırmacı',
                    ],
                    // Affiliation banner (single line, real)
                    'affiliation' => 'Anadolu Ajansı, Uluslararası Haber Merkezi · İstanbul',

                    // Künye — sadece doğrulanmış bilgiler
                    'credentials' => [
                        ['label' => 'üs',         'value' => 'İstanbul'],
                        ['label' => 'kurum',      'value' => 'Anadolu Ajansı Uluslararası Haber Merkezi'],
                        ['label' => 'rol',        'value' => 'Foto muhabir ve yayıncı'],
                        ['label' => 'saha',       'value' => 'Hatay · Adana bölgesi · İstanbul'],
                        ['label' => 'akademi',    'value' => 'Hasan Kalyoncu Üni. (yüksek lisans)'],
                        ['label' => 'dil',        'value' => 'Türkçe'],
                    ],

                    // Work areas — 4 sütun strip
                    'workareas' => [
                        [
                            'label' => 'saha',
                            'title' => 'Saha muhabirliği',
                            'lines' => [
                                'Zeytin Dalı Harekâtı (Hatay sınır hattı)',
                                'Adana Bölgesi (Adana · Hatay · Mersin · Osmaniye)',
                                'İstanbul, Uluslararası Haber Merkezi',
                            ],
                        ],
                        [
                            'label' => 'görsel',
                            'title' => 'Görsel üretim',
                            'lines' => [
                                'Foto muhabirlik (AA bünyesinde)',
                                'Drone görüntüleme',
                                'Reklam ve ürün fotoğrafçılığı (geçmiş)',
                            ],
                        ],
                        [
                            'label' => 'araştırma',
                            'title' => 'Akademik araştırma',
                            'lines' => [
                                'Drone haberciliği (yüksek lisans tezi)',
                                'Görsel göstergebilim',
                                'Saha pratikleri (kitap çalışması)',
                            ],
                        ],
                        [
                            'label' => 'yayıncılık',
                            'title' => 'Yayıncılık',
                            'lines' => [
                                'AA Uluslararası — wire + foto servisi',
                                'Bağımsız yayın (bu site)',
                            ],
                        ],
                    ],

                    // Timeline — gerçek tarihler özgeçmişten
                    'timeline' => [
                        ['year' => '2010', 'text' => 'Yalova Aksa Anadolu Teknik Lisesi (Bilgisayar Yazılımı).'],
                        ['year' => '2012', 'text' => 'Uludağ Üniversitesi, Bilgisayar Programcılığı.'],
                        ['year' => '2014', 'text' => 'İstanbul Aydın Üniversitesi, Fotoğrafçılık ve Kameramanlık. Freelance ürün ve reklam fotoğrafçılığı.'],
                        ['year' => '2016', 'text' => 'Kocaeli Üniversitesi, Gazetecilik.'],
                        ['year' => '2017', 'text' => 'Anadolu Ajansı Savaş Muhabirliği eğitimi.'],
                        ['year' => '2018', 'text' => 'Hatay\'da muhabir olarak göreve başlama. Zeytin Dalı Harekâtı sırasında sınır hattı.'],
                        ['year' => '2019', 'text' => 'Adana Bölge Müdürlüğü — Adana · Hatay · Mersin · Osmaniye.'],
                        ['year' => '2024', 'text' => 'İstanbul, Anadolu Ajansı Uluslararası Haber Merkezi (foto muhabir ve yayıncı).'],
                        ['year' => '2024', 'text' => 'Hasan Kalyoncu Üniversitesi, yüksek lisans (Drone Haberciliği · Görsel Göstergebilim).'],
                    ],

                    // Methodology — kısa, gerçek pratik
                    'methodology' => 'Akredite muhabirim — Anadolu Ajansı bünyesinde saha izinleri kurumsal süreçle alınır. Kaynak güvenliği önce gelir; isim, lokasyon ve zaman bilgisini kaynaklar onaylamadan paylaşmam. Görüntü editing müdahalesini doğrulanabilir minimuma indiririm — RAW + EXIF korunur, post-prod yalnızca tonal düzeltme. Drone çalışmalarında havacılık otoritesi bildirimleri ve sivil mahremiyet kuralları doğrulanır.',

                    // Research highlight (akademik kart)
                    'research' => [
                        'title' => 'Drone haberciliği ve görsel göstergebilim',
                        'place' => 'Hasan Kalyoncu Üniversitesi · Yüksek lisans',
                        'note' => 'İnsansız hava araçlarının haber anlatısındaki rolünü göstergebilim çerçevesinde inceleyen tez. Akademik bulgulardan saha pratikleri kitabı çıktı.',
                    ],

                    // CV PDF — Session 4'te generate edilecek; şimdilik null = link gizli
                    'cv_url' => null,
                ],
                'is_published' => true,
            ]
        );
    }

    private function seedContact(): void
    {
        // Contact page is now config-driven (config/site.php). Page extras
        // burada SADECE fake olmayan, sahibinin onayladığı veriyi taşır.
        // Signal/PGP gibi gerçek olmayan kanallar `null` — view bunları
        // render ETMEZ.
        Page::query()->updateOrCreate(
            ['slug' => 'iletisim'],
            [
                'kind' => 'system',
                'template' => 'contact',
                'title' => ['tr' => 'Yazışma'],
                'intro' => ['tr' => 'Editör, basın ve kaynak temasları için. Cevap genelde 3 iş günü içinde gelir.'],
                'body' => ['tr' => <<<'HTML'
<p>Anadolu Ajansı bünyesindeki kurumsal talepler için lütfen ajansın editör masasına yazın. Bu sayfadaki kanallar bağımsız iletişim içindir — yayımladığım dosyalar, bu sitedeki yazılar veya akademik çalışma soruları.</p>
<p>Hassas kaynaklar için yalnızca üzerinde uzlaşmalı bir kanal üzerinden konuşurum; ilk temasta lütfen yalnızca konuyu ve aciliyet düzeyini belirtin.</p>
HTML],
                'meta_title' => ['tr' => 'Yazışma'],
                'meta_description' => ['tr' => 'Foto muhabir Ozan Efeoğlu ile iletişim — editör, basın ve kaynak temasları.'],
                'extras' => [
                    // Channels: SADECE doğrulanmış olanlar. View ayrıca
                    // config('site.contact.*')'den ek gerçek kanallar varsa
                    // (signal_url, pgp_fingerprint) onları da render eder.
                    // Bu listede HİÇBİR FAKE handle yok.
                    'channels' => [
                        [
                            'type' => 'email',
                            'label' => 'Editöryal e-posta',
                            'handle' => 'press@ozanefeoglu.com',
                            'note' => 'Genel sorular, lisans talepleri, yayın önerileri.',
                            'primary' => true,
                        ],
                    ],
                    'response_note' => '~3 iş günü içinde yanıt',
                ],
                'is_published' => true,
            ]
        );
    }
}
