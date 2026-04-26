<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * LegalPageSeeder — Batalyon-H (Legal CMS).
 *
 * Static Blade'den DB-backed Page'e taşıma. Seed sahibinin onayladığı KVKK
 * metnini idempotent biçimde kurar; müşteri admin'den editoryal revizyon
 * yapabilir. Gizlilik + Künye kısa başlangıç taslakları — sahip onayına açık.
 *
 * NO FAKE DATA: Signal/PGP/IFJ vb. fake kanal üretilmez. İletişim e-postası
 * view katmanında `site_setting('contact.email')` üzerinden gelir.
 */
class LegalPageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedKvkk();
        $this->seedPrivacy();
        $this->seedImprint();
    }

    private function seedKvkk(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'kvkk'],
            [
                'kind' => 'system',
                'template' => 'legal',
                'title' => ['tr' => 'KVKK Aydınlatma Metni'],
                'intro' => ['tr' => '6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında bu sitenin ziyaretçileri olarak sizleri işlenen veriler, işleme amaçları ve haklarınız konusunda bilgilendirmek amacıyla hazırlanmıştır.'],
                'body' => ['tr' => <<<'HTML'
<h2>Veri Sorumlusu</h2>
<p>Ozan Efeoğlu (gerçek kişi) — İstanbul, Türkiye. Bu site kişisel yayın kanalıdır; Anadolu Ajansı ile doğrudan bir kurumsal veri paylaşımı yapılmamaktadır.</p>

<h2>İşlenen Kişisel Veriler</h2>
<p>Yalnızca iletişim formundan gönderdiğiniz bilgiler:</p>
<ul>
    <li><strong>Ad</strong> (serbest metin)</li>
    <li><strong>E-posta adresi</strong></li>
    <li><strong>Mesaj konusu</strong> (opsiyonel)</li>
    <li><strong>Mesaj içeriği</strong></li>
</ul>
<p><strong>IP adresi ve tarayıcı imzası (user agent) kaydedilmez.</strong> Spam önleme için yalnızca geçici, hash'lenmiş ve birkaç dakika içinde silinen bir sayaç tutulur.</p>

<h2>İşleme Amacı</h2>
<p>Kişisel verileriniz yalnızca gönderdiğiniz mesaja yanıt verebilmek amacıyla işlenir. Pazarlama, profilleme, üçüncü taraflara aktarım yoktur.</p>

<h2>Hukuki Sebep</h2>
<p>KVKK m.5/2-e (bir hakkın tesisi, kullanılması veya korunması) ve m.5/2-f (ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla meşru menfaat) kapsamında, açık rıza gerektirmeyen hukuki sebebe dayalı olarak işlenmektedir.</p>

<h2>Saklama Süresi</h2>
<p>Mesajınız yanıtlandıktan 90 gün sonra otomatik olarak silinir. Daha erken silinme talep ederseniz, talebinizi takiben derhal silinir.</p>

<h2>Haklarınız (KVKK m.11)</h2>
<p>Kanunun 11'inci maddesi uyarınca; kişisel verilerinizin işlenip işlenmediğini öğrenme, işlenmişse bilgi talep etme, işlenme amacını öğrenme, düzeltilmesini isteme, silinmesini isteme, işlemeye itiraz etme haklarına sahipsiniz. Başvurularınıza en geç 30 gün içinde yanıt verilecektir.</p>

<h2>Güvenlik Tedbirleri (KVKK m.12)</h2>
<ul>
    <li>Tüm bağlantı TLS 1.3 üzerinden şifrelenir.</li>
    <li>Veritabanı disk üzerinde şifrelenir (encryption at rest).</li>
    <li>Admin paneline erişim tek kullanıcılı, iki-faktör kimlik doğrulama (TOTP) zorunludur.</li>
    <li>Uygulama sunucusu erişim kayıtları 24 saat içinde rotasyona girer.</li>
</ul>

<h2>Kanallar ve Hassas Kaynaklar</h2>
<p>İletişim formu uçtan uca şifrelenmiş değildir. Hassas konular için iletişim sayfasında listelenen güvenli kanalları kullanın. Gazeteci — kaynak ilişkisi bu sitede Türk basın etiği ve Basın Konseyi ilkeleri çerçevesinde korunur.</p>
HTML],
                'meta_title' => ['tr' => 'KVKK Aydınlatma Metni'],
                'meta_description' => ['tr' => 'Kişisel Verilerin Korunması Kanunu kapsamında aydınlatma metni — işlenen veriler, işleme amaçları ve haklarınız.'],
                'extras' => [
                    'eyebrow' => 'Hukuk',
                ],
                'is_published' => true,
                'sort_order' => 10,
            ]
        );
    }

    private function seedPrivacy(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'gizlilik'],
            [
                'kind' => 'system',
                'template' => 'legal',
                'title' => ['tr' => 'Gizlilik Politikası'],
                'intro' => ['tr' => 'Bu sitede hangi verilerin tutulduğu, hangilerinin tutulmadığı ve neden tutulmadığı hakkında kısa bir açıklama.'],
                'body' => ['tr' => <<<'HTML'
<h2>Çerezler</h2>
<p>Bu site yalnızca oturum yönetimi ve CSRF koruması için teknik olarak zorunlu çerezleri kullanır. Reklam, izleme veya profilleme çerezi yoktur.</p>

<h2>Analitik ve Üçüncü Taraflar</h2>
<p>Google Analytics, Facebook Pixel veya benzeri üçüncü taraf izleyici entegre edilmemiştir. Sunucu erişim kayıtları 24 saat içinde rotasyona girer ve pazarlama amacıyla kullanılmaz.</p>

<h2>Gömülü İçerik</h2>
<p>Yazılara gömülü video veya embed kullanıldığında (örneğin YouTube), bu platformların kendi gizlilik politikaları geçerlidir. Sayfa gömülü içerik olmadan ziyaret edildiğinde hiçbir üçüncü taraf isteği yapılmaz.</p>

<h2>İletişim</h2>
<p>İletişim formundan gönderilen verilerin işlenmesi ve saklama süreleri için <a href="/hukuksal/kvkk">KVKK Aydınlatma Metni</a> geçerlidir.</p>
HTML],
                'meta_title' => ['tr' => 'Gizlilik Politikası'],
                'meta_description' => ['tr' => 'Bu sitenin çerez, analitik ve üçüncü taraf izleyici politikası.'],
                'extras' => [
                    'eyebrow' => 'Hukuk',
                ],
                'is_published' => true,
                'sort_order' => 20,
            ]
        );
    }

    private function seedImprint(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'kunye'],
            [
                'kind' => 'system',
                'template' => 'legal',
                'title' => ['tr' => 'Künye'],
                'intro' => ['tr' => 'Bu sitenin sahibi ve içerik sorumlusu hakkında.'],
                'body' => ['tr' => <<<'HTML'
<h2>Site Sahibi</h2>
<p>Ozan Efeoğlu — İstanbul, Türkiye. Foto muhabir ve yayıncı.</p>

<h2>İçerik Sorumlusu</h2>
<p>Bu sitede yayımlanan tüm yazı, fotoğraf ve diğer materyalin içerik sorumluluğu Ozan Efeoğlu'na aittir. Anadolu Ajansı'nın kurumsal yayınlarından bağımsız, kişisel bir yayın kanalıdır.</p>

<h2>İletişim</h2>
<p>Editöryal ve hukuki bildirimler için iletişim sayfasındaki e-posta adresini kullanın.</p>
HTML],
                'meta_title' => ['tr' => 'Künye'],
                'meta_description' => ['tr' => 'Ozan Efeoğlu kişisel yayın sitesi — sahibi, içerik sorumlusu ve iletişim.'],
                'extras' => [
                    'eyebrow' => 'Hukuk',
                ],
                'is_published' => true,
                'sort_order' => 30,
            ]
        );
    }
}
