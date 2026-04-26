<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * WritingSeeder — Phase A blocker (2026-04-23).
 *
 * Positioning correction: owner is a press photographer + editor at
 * Anadolu Ajansı (NOT a war correspondent). Previous demo entries
 * (Gazze / Harkiv / Artsakh / Kahire) removed; fake publications
 * (Reuters / Guardian / Le Monde / Foreign Policy / BBC Türkçe / TRT
 * World) completely dropped — only "Anadolu" attribution is real.
 *
 * Six new İstanbul-/Adana-based entries modelled on Content Agent's
 * recommendation — editoryal + foto_notu + analiz + deneme + not kinds.
 * All entries marked `is_demo => true`; admin can flip this via the
 * Writings editor (to be wired in Phase B.2). On public surfaces a
 * site-wide banner ("Bu sayfa tasarım demosudur") can be rendered when
 * any demo writing is visible (wired in Phase E).
 */
class WritingSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super-admin'))->first()
            ?? User::query()->first();

        $anadoluId = Publication::query()->where('name', 'Anadolu')->value('id');

        foreach ($this->entries() as $row) {
            // Pre-compute read_minutes because DatabaseSeeder uses
            // WithoutModelEvents which suppresses the saving() observer.
            $plain = trim(strip_tags($row['body']));
            $words = preg_split('/\s+/u', $plain) ?: [];
            $readMinutes = max(1, (int) ceil(count($words) / 220));

            $writing = Writing::query()->updateOrCreate(
                ['slug->tr' => $row['slug']],
                [
                    'author_id' => $author?->id,
                    'kind' => $row['kind'],
                    'status' => 'published',
                    'published_at' => Carbon::parse($row['date'])->setTimezone(config('app.timezone')),
                    'location' => $row['location'],
                    'title' => ['tr' => $row['title']],
                    'slug' => ['tr' => $row['slug']],
                    'excerpt' => ['tr' => $row['excerpt']],
                    'body' => ['tr' => $row['body']],
                    'is_featured' => $row['featured'] ?? false,
                    'is_demo' => true, // All seed content is demo until owner reviews
                    'hero_eligible' => $row['hero_eligible'] ?? false,
                    'photo_credit' => $row['photo_credit'] ?? null, // null → config default "Foto: Ozan Efeoğlu / AA"
                    'sort_order' => $row['order'] ?? 0,
                    'read_minutes' => $readMinutes,
                ]
            );

            // Only real publication: Anadolu (AA) when applicable
            if (! empty($row['aa_published']) && $anadoluId !== null) {
                $writing->publications()->sync([$anadoluId]);
            } else {
                $writing->publications()->sync([]);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function entries(): array
    {
        return [
            [
                'slug' => 'haber-masasinda-bir-kare',
                'title' => 'Haber masasında bir kare — seçimin gramerleri',
                'kind' => 'editoryal',
                'location' => 'İstanbul',
                'date' => '2026-04-08 09:00',
                'excerpt' => 'Wire servisine giden tek kareyi seçmek, düzinelerce başka kareye "şimdi değil" demek demektir. Editör masasında geçen on dakikanın notları.',
                'body' => <<<'HTML'
<p>Ajansa düşen her hikâyenin arkasında, yayıma girmeyen düzinelerce kare vardır. Foto muhabirin masaya bıraktığı kart bir dizi imkândır; editörün işi bu dizinin içinden bir tane seçmek — ve geri kalanını, şu an için, yayın dışında tutmaktır. Masa başında geçen her on dakika bir terbiye alıştırmasıdır.</p>

<p>Seçim sanıldığı kadar estetik bir karar değildir. Haberin söylediği şey, kadrajın söylediği şey ve yayının taşıyabileceği şey üçü birden hesaba katılır. Bir karede konu net ama bağlam zayıfsa, başka kareye geçilir. Bir karede bağlam güçlü ama konu yoğunlaşmamışsa aynı şekilde. Masa bu üçünü aynı anda tartmaya alışır.</p>

<blockquote>
    <p>"Ajans için en iyi kare, hem haberi taşıyan hem de ertesi gün arşivde bir kez daha konuşan karedir. İkincisi yoksa, seçim zayıftır."</p>
    <footer>— Haber masası ilkesi</footer>
</blockquote>

<p>Bu yazının konusu kare seçimi değil; seçim yapan gözün yetişmesi. Bir karar her seferinde birden çok şeyi dışarıda bırakır — ve bırakılan şeylerin önemi, yıllar sonra görünür. Bu yüzden masa hafızası tutar. Geri alınan kare, geri çekilen başlık, basılmayan köşe — hepsinin ayrı bir not defteri vardır.</p>

<p>Editörün foto muhabirine verebileceği en iyi geri bildirim "bunu kullanmadık ama gördüm" demektir. Kullanılmayan karenin görüldüğü söylenmezse, muhabir bir dahaki karesini yazı masasına göre değil, sessizliğe göre seçmeye başlar. Sessizlik, haber masalarının en gizli düşmanıdır.</p>
HTML,
                'featured' => true,
                'hero_eligible' => true,
                'order' => 10,
                'aa_published' => true,
            ],
            [
                'slug' => 'drone-hattinda-mesafe-insanda-olcek',
                'title' => 'Drone hattında mesafe, insanda ölçek',
                'kind' => 'analiz',
                'location' => 'İstanbul',
                'date' => '2026-03-24 14:30',
                'excerpt' => 'İnsansız hava aracının yükseldiği her yüz metre, izleyicinin özdeşleşmesini bir miktar kırar. Görsel göstergebilim çerçevesinde bir not.',
                'body' => <<<'HTML'
<p>Drone haberciliği üzerine yüksek lisans çalışmamın ikinci yılında, kendime en çok sorduğum soru şu: Aracın yüksekliği, izleyicinin karakterle kurduğu bağı nasıl etkiliyor? Cevap kısa değil; ama görsel göstergebilim çerçevesinden bakıldığında bir hat çizmek mümkün.</p>

<p>İnsan gözü yere yakın kadrajları "kendine benzer" okuma eğilimindedir. Bir protest, bir yürüyüş, bir enkaz — 1.7 metre yükseklikten çekilmiş karelerde, izleyici kolayca çerçeveye girer, "ben de orada olabilirdim" duygusu kurulur. Bu, habercilik için değerli bir özdeşleşme aracıdır.</p>

<p>Drone ise bu ilişkiyi değiştirir. 30 metre yükseklikten çekilen bir kalabalık artık sayılabilir bir geometri oluşturur; 100 metreden bakıldığında ise bir örüntü, bir harita parçası olur. İzleyicinin pozisyonu "içeride" olmaktan "yukarıdan" olmaya geçer. Bu geçiş, bazı haberler için bilgi taşır (etki alanını göstermek) ama bazıları için özdeşleşmeyi koparan, neredeyse yabancılaştırıcı bir etki yaratır.</p>

<p>Sorunun tek cevabı yok: drone yanlış bir araç değildir. Ama drone kadrajının her kullanımı, izleyicinin habere duyduğu yakınlığı bir miktar esnetir. Editör masasında drone karesini "güzellik için" değil, "bağlam için" seçmek gerekir. Aksi halde haber izleyene değil, izleyenin üstüne çıkmış olur.</p>

<p>Saha pratiklerimi kitap formatında derlerken bu bölümü en çok yazıp en çok sildiğim bölüm oldu; çünkü hem teknik hem etik, hem göstergebilim hem haber disiplini. Üçü birden tek bir kareye sığmıyor; ama bir muhabir o karenin yazılmamış protokolünü bilmeli.</p>
HTML,
                'hero_eligible' => true,
                'order' => 9,
            ],
            [
                'slug' => 'protokol-fotografinda-gorunmeyen',
                'title' => 'Protokol fotoğrafında görünmeyen',
                'kind' => 'foto_notu',
                'location' => 'İstanbul',
                'date' => '2026-02-14 11:00',
                'excerpt' => 'Devlet törenlerinin standart sıralanışı içinde, muhabirin seçmek zorunda olduğu tek kare: kimin omzu, kimin profili, hangi saniye.',
                'body' => <<<'HTML'
<p>Protokol haberciliğinde kadraj hür seçimin değil, disiplinin ürünüdür. Herkes aynı anda aynı sıradadır; foto muhabirinin sırası, kolyesi, kart numarası — hepsi önceden belirlenmiştir. Bu kısıtlama çoğu zaman bir dezavantaj gibi okunur; hâlbuki görmeyi terbiye eden en sağlam zemindir.</p>

<p>Tören karelerinin hepsinin birbirine benzediği doğru değildir. Aynı tokalaşma, aynı yan yana duruş, aynı salon — ama bir karede kimin omzu öndeyse, hangi saniyede göz teması kurulmuşsa, hangi mikronlarda bir el küçük bir duraksama yapmışsa, haber oradadır. Bu farkı yakalamak için saatlerce izlemek gerekir.</p>

<p>Yeni başlayan foto muhabirlerine hep şu tavsiyeyi veririm: ilk protokol görevinizde mümkün olan kareyi değil, mümkün olmayan kareyi arayın. Yani herkesin çektiği karenin bir sonraki saniyesini. Çünkü haber o bir sonraki saniyededir; standart poz değil, standart pozun kırılması.</p>

<p>Ajans içinde bu tür kareler çoğu zaman wire'a düşmez — "protokol dışı görünür" diye. Kişisel arşive düşer. Yıllar sonra o arşivden çıkan kare, aynı törenin wire karesinden çok daha çok şey anlatır. Muhabirin gerçek mesleki birikimi, wire'a giden değil, wire'a gitmeyen karelerdir.</p>
HTML,
                'hero_eligible' => true,
                'order' => 8,
            ],
            [
                'slug' => 'sabah-dosyasi-bir-ajans-gunu',
                'title' => 'Sabah dosyası: bir ajans gününün açılışı',
                'kind' => 'editoryal',
                'location' => 'İstanbul',
                'date' => '2026-01-20 08:30',
                'excerpt' => 'Sabah 06:30\'da wire alertlerinin nasıl sıralandığı, foto servisinin hangi kareyi neden önce çıkardığı — bir günün kısa anatomisi.',
                'body' => <<<'HTML'
<p>İstanbul'da ajans sabahı erken başlar. Saat 06:30'da ilk editör masasına oturur; 07:00'de yabancı büroların önceki gecedeki wire'ları geçmiş olur. Bu yarım saat, günün planlandığı, önceliklerin yeniden hizalandığı yarım saattir. Foto servisinin sabahı ayrıca bir disiplin ister: yerel gelişmeler, yabancı kaynaklı görseller, bölge ajansların beslemeleri üç ayrı kanaldan akar.</p>

<p>Bir günün açılışında foto editörünün yapacağı ilk iş, "dün gece ne gördük" sorusuna cevap vermektir. Gece boyunca gelen görsellerin önceliklendirilmesi, haber masasının ritmini belirler. Bu yüzden foto muhabiri olarak sabah masaya ilk düşen kareyi takip etmek, o günün haber dilini anlamak demektir.</p>

<p>Akşamki uzun dosya, sabahki ilk alertin ikinci adımıdır. Muhabir sahada iken editörün masasındaki ilk kareyi hayal edebilmeli; aksi halde kendine sorduğu "bu kare ne anlatır" sorusu havada kalır. Ajans içinde foto servisi ile haber masası arasındaki ritim birbirine denktir.</p>

<p>Sabah dosyasının yazısız kuralı şudur: bir gecenin görselleri bir günün hafızasını kurar. İçeride veya dışarıda, çalışan herkes bu hafızaya katılır. Muhabir gördüğünü, editör seçtiğini, masa çıkardığını hatırlar. Bu üç hafıza üst üste binmezse, ajans gazeteciliği zayıflar.</p>
HTML,
                'hero_eligible' => true,
                'order' => 7,
                'aa_published' => true,
            ],
            [
                'slug' => 'adana-bolgesi-eski-masa-yeni-liste',
                'title' => 'Adana Bölgesi — eski masa, yeni liste',
                'kind' => 'foto_notu',
                'location' => 'Adana',
                'date' => '2025-11-12 16:00',
                'excerpt' => 'Dört yıl Adana Bölge Müdürlüğü\'nde çalıştım. Bu sene bir günlüğüne döndüm; eski sokaklar yeni foto listesi.',
                'body' => <<<'HTML'
<p>Hatay'da başlayan muhabirlik görevim, sonraki dört yıl Adana Bölge Müdürlüğü'nün sorumluluk alanında sürdü — Adana, Hatay, Mersin, Osmaniye. Bu dört şehir her biri farklı bir foto diline sahip. Adana'nın günü erken başlıyor, Mersin'in ışığı öğleden sonra dönüyor, Osmaniye'nin ritmi sessiz ama net, Hatay her seferinde başka bir hal alıyor.</p>

<p>Geçen ayın sonunda Adana'ya bir günlüğüne gittim. Bir toplantı bahanesi; ama asıl iş eski sokakları bir kez daha dolaşmak, o zamanki çekimlerin nereden yapıldığını hatırlamaktı. Aynı balkondan aynı manzara bambaşka bir haberin içine düşmüş — bu, foto arşivinin hafıza katmanı. Arşiv sadece görüntü tutmaz, bir sokağa nereden bakıldığını da tutar.</p>

<p>Dört yıl önce sıradan bir cuma öğleden sonrası çektiğim pazar yeri karesi, o günkü haber değeri düşük bir görüntüydü. Bugün aynı pazar yerine bakınca, o kare bir bölgenin tarihini tutan bir belge niteliğine yakınlaşıyor. Ajans foto muhabirliği için zaman her zaman çalışır; bugünün sıradan karesi, yarının nadirliğidir.</p>

<p>Adana gezimin sonunda yeni bir foto listesi çıkardım — eski alanlardan yeni bakış açılarıyla. Bu liste bir sonraki saha için hazır duruyor. Muhabirin en sade becerilerinden biri: bir kenti ziyaret etmeyi bir alıştırma olarak sürdürmek. Her gidiş, kayıt olarak kalır.</p>
HTML,
                'hero_eligible' => true,
                'order' => 6,
            ],
            [
                'slug' => 'kaynak-koruma-dijital-temiz-masa',
                'title' => 'Kaynak koruma — dijital temiz masa',
                'kind' => 'not',
                'location' => 'İstanbul',
                'date' => '2025-10-03 12:00',
                'excerpt' => 'Bir muhabirin cihaz hijyeni üzerine kısa liste: sinyal, yedek, şifreleme, silme. Abartısız, uygulanabilir.',
                'body' => <<<'HTML'
<p>Gazetecilikte kaynak koruma teknik bir iş olmakla birlikte disipliner bir iştir. En iyi şifreleme bile kötü bir alışkanlıkla çalınır. Bu kısa liste, yıllar içinde oluşturduğum "dijital temiz masa" kurallarıdır. Herkesin listesi farklı olabilir; kural olarak değil, örnek olarak paylaşıyorum.</p>

<p><strong>1. Mesajlaşma.</strong> Hassas görüşmeler Signal üzerinden, uçtan uca şifreli. Hiçbir zaman normal SMS'ten ilk temas kurma. Signal'da "disappearing messages" 24 saat. Yedek almıyorsun; yedek mesajı aldığında artık ephemeral değildir.</p>

<p><strong>2. Dosya transferi.</strong> Dosyaları paylaşırken Signal veya uçtan uca şifreli servis (Proton Drive, Tresorit). E-posta ekleri hiçbir zaman. WeTransfer hiçbir zaman — sunucularda saklanır.</p>

<p><strong>3. Notlar.</strong> Sahada aldığın notlar telefonda plaintext durmamalı. Standard Notes (E2EE) veya offline bir not defteri kullan. Kaynak isimleri kod adıyla tut; gerçek ad ayrı bir şifrelenmiş ZIP'te, farklı bir yerde.</p>

<p><strong>4. Cihaz.</strong> Telefonunun disk şifrelemesi aktif mi? Güçlü PIN mi kullanıyorsun? Ekran kilidi iki dakika içinde devreye giriyor mu? Bilgisayarınd full disk encryption (FileVault/BitLocker) açık mı? Üç evet yoksa, önce bunu hallet.</p>

<p><strong>5. Silme.</strong> "Silinmiş" dosya silinmiş değildir. Güvenli silme için BleachBit, eraser veya cihazın sıfırlama özelliği. Eski telefonları atmadan önce sıfırla + iki kez üzerine yaz.</p>

<p><strong>6. Yedek.</strong> Tüm önemli işlerin iki kopyası olsun — biri yerel (şifreli disk), biri uzak (şifreli cloud). Tek kopya kaybolduğunda yıllık bir çalışma kaybolur. İki kopya yoksa, ilk önce yedek yap.</p>

<p>Bu kurallar paranoya değil; sıradan meslek hijyeni. Üç ay uygulayınca dengesi bulunur. Kaynakların güvenliği ilk önce senin alışkanlıklarındadır.</p>
HTML,
                'order' => 5,
            ],
        ];
    }
}
