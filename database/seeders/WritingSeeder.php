<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WritingSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super-admin'))->first()
            ?? User::query()->first();

        foreach ($this->entries() as $row) {
            // Pre-compute read_minutes because DatabaseSeeder uses
            // WithoutModelEvents which suppresses the saving() observer.
            $plain = trim(strip_tags($row['body']));
            $words = preg_split('/\s+/u', $plain) ?: [];
            $readMinutes = max(1, (int) ceil(count($words) / 220));

            $writing = Writing::query()->updateOrCreate(
                ['slug->tr' => $row['slug']],
                [
                    'author_id'    => $author?->id,
                    'kind'         => $row['kind'],
                    'status'       => 'published',
                    'published_at' => Carbon::parse($row['date'])->setTimezone(config('app.timezone')),
                    'location'     => $row['location'],
                    'title'        => ['tr' => $row['title']],
                    'slug'         => ['tr' => $row['slug']],
                    'excerpt'      => ['tr' => $row['excerpt']],
                    'body'         => ['tr' => $row['body']],
                    'cover_hue_a'  => $row['hueA'],
                    'cover_hue_b'  => $row['hueB'],
                    'is_featured'  => $row['featured'] ?? false,
                    'sort_order'   => $row['order'] ?? 0,
                    'read_minutes' => $readMinutes,
                ]
            );

            if (! empty($row['publications'])) {
                $ids = Publication::query()->whereIn('name', $row['publications'])->pluck('id')->all();
                $writing->publications()->sync($ids);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function entries(): array
    {
        return [
            [
                'slug'         => 'ayakta-kalan-son-hastane',
                'title'        => 'Ayakta kalan son hastanenin içinde',
                'kind'         => 'saha_yazisi',
                'location'     => 'Gazze',
                'date'         => '2025-09-12 06:00',
                'excerpt'      => 'Koridor çöktükten sonra açık kalmayı başaran acil servisinde on gün. Doktorların, hastaların ve eksik malzemelerin sessiz bir koreografisi.',
                'body'         => <<<'HTML'
<p>Acil servisin ana kapısı sabah altıdan beri kilit değil, tutkallıydı. Binanın kuzey koridoru geçen haftaki saldırıdan sonra çökmüş, içeri girmenin tek yolu bu kapıydı. Dr. Amal Hijazi kapının yanında bir portakalı dilimliyordu; sabah yemeği için değil, bir hastaya vermek için.</p>

<p>Hastanenin elektriği dönüşümlü çalışıyor. Jeneratör yakıt beklediği için güneş enerjisi panolarıyla idare ediliyor — panolar da vardiyaya giriyor. Sabah sekizden on ikiye kadar MRI, on ikiden üçe kadar yoğun bakım, üçten altıya kadar acil. "Takvim gibi yaşıyoruz" diyor Dr. Amal. "Ameliyat planlamak için değil, nefes almak için takvim."</p>

<blockquote>
    <p>"Burada kalan hiç kimse birer gönüllü değil. Başka bir yere gidilemediği için kalıyoruz. Bu, zannedildiği gibi bir cesaret meselesi değil."</p>
    <footer>— Dr. Amal Hijazi, genel cerrahi</footer>
</blockquote>

<p>Hastane çevresindeki mahalle üç gün içinde üç kez tahliye emri aldı. Her seferinde emir bir saatte geri alındı. Hasta taşımak için kullanılan tek ambulans — sekiz yaşında, üstünde eski Kızılay arması solmuş — haftanın beş günü arızalı. İki günde bir kez motor ısınmasından kaçarak çalışıyor.</p>

<p>Bir şey, üç hafta süren ziyarette en çok tekrarlandı: "Oradaki masa, benim masam değil." Hemşire odasında birinin kalem koymuş olduğu bir masa. Eski bir müdür yardımcısının ofisinde birinin koyduğu bir battaniye. Hastanede kimsenin kendi yeri yok; hepsi ödünç. Ne zaman bitecek ödünç, kimse sormuyor.</p>

<p>Son günüm cuma sabahıydı. Dr. Amal'ın vardiyası altıda bitti; ama yerine gelecek doktor, sabah saldırısında yolda kaldı. Ben ayrılırken hâlâ nöbetteydi. "Bir sonraki haberime kadar," dedi. Bu, Gazze'de veda etmenin yeni bir biçimi.</p>
HTML,
                'hueA'         => 22,
                'hueB'         => 12,
                'featured'     => true,
                'order'        => 10,
                'publications' => ['Reuters', 'TRT World'],
            ],
            [
                'slug'         => 'yakinligin-etigi',
                'title'        => 'Yakınlığın etiği üstüne',
                'kind'         => 'deneme',
                'location'     => 'İstanbul',
                'date'         => '2026-04-12 10:00',
                'excerpt'      => 'Kameranın kaç metre geride durduğu, röportajda hangi soruyu sormadığın — bir muhabirin en çok yazıyla değil, uzaklıkla kurduğu hikâye.',
                'body'         => <<<'HTML'
<p>Bir muhabirin mesleki biyografisi, aslında mesafelerinin biyografisidir. Hangi olaya kaç metre yakın durduk, hangi soruyu soramadık, hangi kareyi almamayı tercih ettik — hepsi öğrendiğimiz şeylerin önsözüdür. Bu yazının konusu bu önsöz: yakınlık etiği.</p>

<p>Saha yıllarının başında, yakınlık cesaret zannediliyor. Yaklaşıyorsun; karenin içine giriyor, seslerin karışıyor, nefeslerin denkleşiyor. Yayın ekranına bu yakınlık bir şey anlatıyor: "orada oldum." Ama orada olmak, orada kalmanın aksine çok kolay bir şey. Zor olan, gereken uzaklığı bulmak.</p>

<p>İsrailli fotoğrafçı Ron Haviv'in 1992 Bijeljina karesi, bir paramiliter askeri tekmeleyip öldürmeden önce bir kadına doğru yaklaşırken çekilmiştir. Haviv sonradan yazar: "O karede asker beni görmüş olsaydı, ben de hikâyenin parçası olacaktım. Uzaklık benim için değildi — hikâyenin kendisi için gerekliydi." Bu cümle, mesleğin kısa bir etiği.</p>

<p>Yazı masasında da aynı mesafe sorunu var. Hangi detayı yazıyorsun, hangisini bırakıyorsun? Bir kurbanın adını verirken, bir tanığı anonim bırakırken — her karar bir yakınlık ayarı. Gazetecinin dürüstlüğü, sadece yazdıklarında değil, yazmadıklarında da ölçülür.</p>

<p>Bir kuraluma uğradım yıllar içinde: sahada iken mümkün olan en yakında kalırsın; yazı masasında ise gereken kadar uzaktan bakarsın. Yakınlık bilgiyi verir; uzaklık onu anlamlı kılar. İkisini karıştırırsan ne muhabir ne yazar olursun.</p>
HTML,
                'hueA'         => 30,
                'hueB'         => 210,
                'order'        => 9,
                'publications' => ['Foreign Policy'],
            ],
            [
                'slug'         => 'lacin-koridorundan-bir-cikis',
                'title'        => 'Laçın koridorundan bir çıkış',
                'kind'         => 'roportaj',
                'location'     => 'Artsakh',
                'date'         => '2023-09-28 22:00',
                'excerpt'      => 'Dokuz aylık ablukanın ve son yirmi dört saatin belgelenmesi — tek bir gece, tek bir yolda, binlerce ışık.',
                'body'         => <<<'HTML'
<p>Stepanakert'ten Goris'e giden yolda, 28 Eylül gecesi, araçlar tek sıra halinde ilerledi. Sayılarını öğrenmek günler alacak; o gece, sadece ışıkların akışı vardı. Önden bakınca nehir gibiydi; arkadan bakınca tükenmeyen bir şehirdi.</p>

<p>Dokuz aydır kapalı olan koridor, cumartesi sabah 08:00'de açıldı. Askerler barikatı kaldırdı, işaretçiler rotayı gösterdi; ilk 24 saat içinde 30 binden fazla insanın geçtiği tahmin ediliyor. Rakam hâlâ güvenilir değil, çünkü geçenlerin büyük kısmı kayıt dışı. Rakam, belki hiçbir zaman güvenilir olmayacak.</p>

<blockquote>
    <p>"Evi kilitlemedim. Kapıyı sadece çektim. Kilitlersem, gittiğim belli olur."</p>
    <footer>— Araminé, 62, Martuni</footer>
</blockquote>

<p>Araminé üç gün önce kapatmıştı evin elektriğini. Buzdolabını boşalttı, kitapları salona yığdı, pencereleri çerçeveden ayırdı. "Belki dönerim" diye değil — "belki başkası bulur" diye. Bunu söylerken gülümsüyor. Saha muhabirliğinde en zor iş bu tür gülümsemeleri anlamak.</p>

<p>Koridor son kez 1988'de açılmıştı. Anne ve babaları o koridordan gelenler, bu sefer tersine geçiyorlardı. Zamanın simetrisi nadiren bu kadar açıktır. Arabalardan hiçbiri hızlı değildi; sanki kimse varmak istemiyordu. Bir yere değil, belki kendi tarihlerinin öbür ucuna varıyorlardı.</p>

<p>Goris'e ulaştığımda sabah 04:00 olmuştu. Gümrük çadırında sıcak çay, işaretli şeritler, gönüllüler. İnsanlar oturdu, su içti, kimliklerini değiştirdi. Yeni nüfus kağıtları yazılıyordu. Bu kelimeyi hiçbir mülteci sevmez — "yeni". Ama bu sabah yenilikten kaçınmak mümkün değildi.</p>
HTML,
                'hueA'         => 15,
                'hueB'         => 200,
                'order'        => 8,
                'publications' => ['Le Monde', 'The Guardian'],
            ],
            [
                'slug'         => 'basin-karti-bir-kalkan-degildir',
                'title'        => 'Basın kartı bir kalkan değildir',
                'kind'         => 'not',
                'location'     => 'Kahire',
                'date'         => '2026-03-28 14:30',
                'excerpt'      => 'Akreditasyonun bittiği yerde insan kalıyor. Sahada kendini ve kaynaklarını korumanın üzerine dersler.',
                'body'         => <<<'HTML'
<p>Genç bir muhabir arkadaşım geçen hafta beni aradı. Kahire'ye ilk kez gidecekti. "Basın kartım hazır," dedi. Ona şu cevabı verdim: "Kartın bir kalkan olduğunu zannetme."</p>

<p>Basın kartı, profesyonel bir kanıttır. Kaynaklarına güven verir, bir noktada seni bir kapıdan içeri sokar, bazen bir nöbetçiyi ikna eder. Ama bir mermiyi durdurmaz, bir gözaltıyı iptal etmez, bir yanlış anlamayı düzeltmez. Kart, taşınmalı; ama inanılmamalı.</p>

<p>Sahada kendini korumanın birkaç basit kuralı var. Birincisi: sigorta. İki türlü — biri fiziksel (travel kit, insurance), biri sosyal (editörün, ailene bildirim sistemi, meslektaş check-in'leri). İkincisi: çıkış planı. Girdiğin her yerin iki çıkışını bil. Üçüncüsü: kaynak koruma. Notlarını kripto et, cihazların temiz tut, isimlerden kod adı kullan.</p>

<p>Kaynaklarınla ilişkide, onların riskinin senin riskinden büyük olduğunu asla unutma. Sen gidiyorsun; onlar kalıyor. Bir yazıya bir cümle koymadan önce, o cümlenin maliyetini onun üstlenip üstlenemeyeceğini sor. Gazetecilik, en çok bu sorunun verdiği cevaplarla yapılır.</p>

<p>Son bir şey: basın kartı düşürülünce, kişisel olarak alınmaz. Tekrar alınır. İnsan düşünce, kolay kolay geri alınmaz. Önceliği hatırla.</p>
HTML,
                'hueA'         => 38,
                'hueB'         => 250,
                'order'        => 7,
                'publications' => ['Anadolu'],
            ],
            [
                'slug'         => 'bosalmayi-reddeden-bir-sehir',
                'title'        => 'Boşalmayı reddeden bir şehir',
                'kind'         => 'roportaj',
                'location'     => 'Harkiv',
                'date'         => '2022-12-04 08:00',
                'excerpt'      => 'Tam ölçekli savaşın ilk kışından altı portre. Metro sığınağında bir matematik öğretmeni, hastane bahçesinde bir ressam.',
                'body'         => <<<'HTML'
<p>Harkiv Metrosu'nun Peremohy istasyonu, Aralık 2022'de hâlâ bir sığınak. Üst katta dersler yapılıyor — matematik öğretmeni Svitlana haftada beş gün, aşağıya inen merdiven başında tahta kuruyor. Öğrencileri kum torbalarının üstünde ders dinliyor. Tahta kara, tebeşir beyaz, sayılar yüksek — "çarpım tablosu" diye başlıyor ders.</p>

<p>Şehir boşalmadı. İlk üç haftada nüfusun yarısı ayrıldı; Mayıs'tan itibaren bir kısmı döndü. Dönenlerin hepsi için sebep aynı değildi. Kimisi para bitti. Kimisi bir akrabayı bırakmadı. Kimisi, ne kadar uzun olursa olsun, bu savaşın 'ev'i değiştirmesine izin vermeyi reddetti. Reddetmek de bir askerlik biçimi.</p>

<blockquote>
    <p>"Evim buranın yirmi metre üstünde. Evim de, derslerim de. Gitmem, iki şeyi birden kaybederim."</p>
    <footer>— Svitlana, 47, matematik öğretmeni</footer>
</blockquote>

<p>Hastane bahçesinde tanıdığım ressam Oleksandr, her sabah saat yedide geliyor. Ateşli saldırılara rağmen. "Saat yedide ışık en iyi," diyor. "Hastane bahçesinin gölgeleri tam oluyor." Altmış sekiz yaşında. Bir sığınakta yaşıyor. Boyalarını taşıyor, fırçalarını taşıyor, bir tuvali taşıyor. Tuval dün akşam donmuş. Eritirken sabah geçiyor.</p>

<p>Metro sığınağının hemen dışında, Anna on dört yaşında, İngilizce çalışıyor. "Birgün New York'ta okumak istiyorum" diyor. "Ama New York için Harkiv'i bırakmak istemiyorum." Yan yana iki istek. Savaş, bu ikisini kavuşturmayı zorlaştırdı; ama imkansızlaştırmadı. Anna bu farkı anlıyor; on dört yaşında anladığı çok şey var.</p>

<p>Altı portre yazıya sığıyor; bir şehirin neden boşalmadığının cevabı sığmıyor. Belki de cevap sormakta değil, sormaya devam etmekte.</p>
HTML,
                'hueA'         => 200,
                'hueB'         => 25,
                'order'        => 6,
                'publications' => ['The Guardian', 'BBC Türkçe'],
            ],
            [
                'slug'         => 'ajansin-yazmadiklari',
                'title'        => 'Ajansın yazmadıkları',
                'kind'         => 'deneme',
                'location'     => 'İstanbul',
                'date'         => '2026-03-04 09:00',
                'excerpt'      => 'Wire servislerine düşen iki cümlelik başlık ile saha arasındaki on iki saatin hikâyesi — neyi atladığımıza dikkat.',
                'body'         => <<<'HTML'
<p>Bir haber ajansına düşen alerti okuduktan sonra yerini bulmak isteyenler, genellikle ajans tarafından yazılmayan şeyleri ararlar. Gazetecilik, ajansın yazdığı iki cümleyle başlar, ama onlarla bitmez. Bu yazı, bir ajansın yazmadıklarına dair bir haritayı denemek.</p>

<p>Standart wire kısa olmalı. İki cümle yeter: kim, ne, nerede, ne zaman. Arada kalanlar — neden, nasıl, ne sonuçla — sahaya, takibe, ikinci günün uzun yazısına bırakılır. Ama çoğu zaman ikinci gün gelmez. Haberler, çok hızlı başka haberlerle değiştirilir. Wire'ın iki cümlesi, o olayın bütün kamusal hafızası olur.</p>

<p>Sahada on iki saat geçirdikten sonra yazmak, iki cümleyi nasıl çoğaltacağını öğrenmekle ilgili değildir. Neyi çoğaltmadığını seçmekle ilgilidir. Bir ismi yazdığında, bir tarihi vurguladığında, bir cümleyi aktardığında — her biri bir diğerini dışarıda bırakır. Seçim gazeteciliğin asıl işidir; yazma değil.</p>

<p>Ajansın yazmadıkları genellikle şunlardır: bağlam, gelenek, çelişen tanıklıklar, üçüncü tanığın sessizliği, bir resmi açıklamanın tonu, bir sokağın kokusu. Uzun-form gazetecilik bu sıralanamayanları sıraya koymaya çalışır. Sıraya koymakla onlara adalet ediyor muyuz — emin değilim. Ama denemek, bizim görevimiz.</p>

<p>Bir okur, wire'ın iki cümlesini okuduğunda ne yapıyorsa onu yapmaya devam etsin. Ama bir ara, bir kez, üç sayfalık bir yazı için otursun. Orada olmayan şeyler, orada olmuş şeyler kadar önemli olabilir.</p>
HTML,
                'hueA'         => 10,
                'hueB'         => 190,
                'order'        => 5,
                'publications' => ['Foreign Policy', 'BBC Türkçe'],
            ],
        ];
    }
}
