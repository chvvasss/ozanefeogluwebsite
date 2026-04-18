<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

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
                'kind'     => 'system',
                'template' => 'about',
                'title'    => ['tr' => 'Hakkında'],
                'intro'    => ['tr' => 'Ozan Efeoğlu — saha muhabiri, yazar. On yıldır haberin kör noktalarından yazıyor.'],
                'body'     => ['tr' => <<<'HTML'
<p>Saha muhabiriyim. 2015'ten bu yana Ortadoğu, Kafkasya ve Doğu Avrupa'dan çatışma bölgelerinde, felaket sonralarında ve haberin kör noktalarında yazı yazıyorum. İşin büyük kısmı yürümek, beklemek, doğru cümleyi seçmek — geri kalanı, iletişim kurabildiğin insanlara borçlu.</p>

<p>Fotoğrafı yazıyla eşdeğer bir araç sayıyorum. Hiçbir kare yalnızca illüstrasyon değil; her biri ayrı bir kaynak. Uzun-form denemeler de bu ikisinin arasındaki gerilimden çıkıyor.</p>

<h2>Önce zanaat</h2>

<p>İşe ajanslarda başladım — Reuters bureau desk'i, sonra saha. İlk uzun röportajımı 2017'de Suriye-Türkiye sınırında yazdım. O yazı bana şunu öğretti: bir muhabirin mesleki biyografisi, aslında mesafelerinin biyografisidir. Hangi olaya kaç metre yakın durduk, hangi soruyu soramadık, hangi kareyi almamayı tercih ettik — hepsi öğrendiğimiz şeylerin önsözüdür.</p>

<p>Akredite olduğum ilk yıldan beri IFJ kartımı taşıyorum. Son dört yıldır bağımsız çalışıyorum; işlerimi The Guardian, Le Monde, Reuters, Foreign Policy, TRT World, Anadolu ve BBC Türkçe ile paylaştım.</p>

<h2>Şimdi ne yazıyor</h2>

<p>Bu siteyi açmamın sebebi: wire'ın dışında kalanları yazmak. Ajansın iki cümlelik başlıklarının arkasındaki on iki saatin hikâyesini. Yayınlar için de yazmaya devam ediyorum, ama burada — Türkçe, sansürsüz, editorial filtresiz — en özgür hâlim.</p>

<blockquote>
    <p>"Yakınlık bilgiyi verir; uzaklık onu anlamlı kılar. İkisini karıştırırsan ne muhabir ne yazar olursun."</p>
    <cite>— 'Yakınlığın etiği üstüne'nden</cite>
</blockquote>

<p>Ayda iki yazı hedefim var. Bazen saha, bazen masa. Her ikisini de size ulaştırmak için bir <a href="/feed.xml">RSS</a> ve yakında bir bülten olacak.</p>
HTML],
                'meta_title'       => ['tr' => 'Hakkında — Ozan Efeoğlu'],
                'meta_description' => ['tr' => 'Saha muhabiri ve yazar Ozan Efeoğlu\'nun özgeçmişi, yayın hayatı ve uzun-form yazı uygulaması.'],
                'extras'           => [
                    'credentials' => [
                        ['label' => 'başlangıç',  'value' => '2015'],
                        ['label' => 'üs',         'value' => 'İstanbul, Türkiye'],
                        ['label' => 'saha',       'value' => 'MENA · Kafkasya · Doğu Avrupa'],
                        ['label' => 'dil',        'value' => 'Türkçe, İngilizce (akıcı); Arapça (orta); Ukraynaca (orta)'],
                        ['label' => 'tempo',      'value' => 'ayda iki yazı'],
                        ['label' => 'basın',      'value' => 'akredite · IFJ kart no. ████'],
                        ['label' => 'güvenlik',   'value' => 'HEFAT eğitimli · first-aid sertifikalı'],
                    ],
                    'timeline' => [
                        ['year' => '2015', 'text' => 'Reuters Istanbul bureau desk — ajans gazeteciliğine giriş.'],
                        ['year' => '2017', 'text' => 'İlk uzun saha röportajı: Suriye-Türkiye sınır hattı (yayın: Le Monde).'],
                        ['year' => '2020', 'text' => 'HEFAT (Hostile Environment) eğitimi tamamlandı.'],
                        ['year' => '2022', 'text' => 'Harkiv dönüşümü — tam ölçekli savaşın ilk kışı, altı portre.'],
                        ['year' => '2023', 'text' => 'Kahramanmaraş depremleri — ilk üç hafta sahada, AP/Anadolu ortak yayın.'],
                        ['year' => '2023', 'text' => 'Laçın koridoru evakuasyonu — Le Monde + Guardian kapak.'],
                        ['year' => '2025', 'text' => 'Gazze embed — acil servis on gün (Reuters/TRT World).'],
                        ['year' => '2026', 'text' => 'ozanefeoglu.com — bağımsız yayın açık.'],
                    ],
                    'awards' => [
                        ['year' => '2023', 'title' => 'European Press Prize — Distinguished Reporting (nominee)'],
                        ['year' => '2024', 'title' => 'Turkish Journalists\' Association — Feature of the Year'],
                    ],
                    'cv_url'       => '#',
                    'bylines_list' => '/hakkimda#yayinlar',
                ],
                'is_published' => true,
            ]
        );
    }

    private function seedContact(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'iletisim'],
            [
                'kind'     => 'system',
                'template' => 'contact',
                'title'    => ['tr' => 'İletişim'],
                'intro'    => ['tr' => 'İş teklifi, lisanslama, basın soruları veya sadece bir mesaj. Üç güvenli kanal.'],
                'body'     => ['tr' => <<<'HTML'
<p>Tüm mesajlar en geç 3 iş günü içinde cevaplanır. Hassas kaynaklar için <strong>Signal</strong> veya <strong>PGP-şifreli e-posta</strong> tercih edilir — aşağıdaki adresler tam uçtan uca şifrelenmiş kanallar kurar.</p>
<p>Kurumsal basın talepleri için PR ajansım ya da editörlerimin bağlantı bilgilerini paylaşabilirim; lütfen ilk mesajda kurumunu ve aciliyeti belirt.</p>
HTML],
                'meta_title'       => ['tr' => 'İletişim — Ozan Efeoğlu'],
                'meta_description' => ['tr' => 'Ozan Efeoğlu ile güvenli iletişim kanalları: e-posta, Signal, PGP-şifreli kanal.'],
                'extras'           => [
                    'channels' => [
                        [
                            'label'     => 'Editöryal e-posta',
                            'handle'    => 'press@ozanefeoglu.com',
                            'note'      => 'Genel sorular, lisans talepleri, yayın önerileri.',
                            'type'      => 'email',
                            'primary'   => true,
                        ],
                        [
                            'label'     => 'Signal',
                            'handle'    => '@ozanefeoglu.42',
                            'note'      => 'Tercih edilen kanal — uçtan uca şifrelenir, mesajlar 1 haftada kaybolur.',
                            'type'      => 'signal',
                            'primary'   => false,
                        ],
                        [
                            'label'     => 'PGP şifreli e-posta',
                            'handle'    => 'secure@ozanefeoglu.com',
                            'note'      => 'Hassas kaynaklar için. Anahtar: parmak izi aşağıda.',
                            'type'      => 'pgp',
                            'primary'   => false,
                        ],
                    ],
                    'pgp' => [
                        'fingerprint' => '9A4C 8B3E  F2D1 6A57  1C8B E4A0  3F9D B205  7E6C 1D48',
                        'key_id'      => '0x7E6C1D48',
                        'download'    => '/pgp/ozanefeoglu.asc',
                    ],
                    'disclosure' => [
                        'Kaynakların güvenliği için: isim, lokasyon ve zamanları mesajına koyma. İlk temasta yalnız konuyu ve güvenli kanalda nasıl konuşabileceğimizi belirt.',
                        'Gazeteci-kaynak ilişkisi bu sitede Türkiye Basın Yasası §19 ve IFJ etik kuralları çerçevesinde korunur.',
                    ],
                    'response_time_hours' => 72,
                    'accreditation'       => 'IFJ kart no. ████ · press@ e-postası akreditasyon doğrulaması için reply-all yapılabilir.',
                ],
                'is_published' => true,
            ]
        );
    }
}
