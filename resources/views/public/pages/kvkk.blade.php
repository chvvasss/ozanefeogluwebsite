@extends('layouts.public', [
    'title' => 'KVKK Aydınlatma Metni · '.site_setting('identity.name'),
    'description' => 'Kişisel Verilerin Korunması Kanunu kapsamında aydınlatma metni.',
])

@section('content')

<article>
    <section class="scene scene--overture">
        <div class="page-wrap-narrow">
            <p class="eyebrow mb-5">Hukuk</p>
            <h1 class="display-editorial" style="font-size: clamp(var(--text-3xl), 5vw, var(--text-5xl));">
                KVKK Aydınlatma Metni
            </h1>
            <p class="mt-6 text-[var(--text-md)] leading-[1.7] text-[var(--color-ink-muted)] max-w-[62ch]">
                6698 sayılı Kişisel Verilerin Korunması Kanunu (&ldquo;KVKK&rdquo;) kapsamında bu sitenin ziyaretçileri olarak sizleri işlenen veriler, işleme amaçları ve haklarınız konusunda bilgilendirmek amacıyla hazırlanmıştır.
            </p>
        </div>
    </section>

    <section class="scene scene--muted border-t border-[var(--color-rule)]">
        <div class="page-wrap-narrow">
            <div class="prose-article">
                <h2>Veri Sorumlusu</h2>
                <p>Ozan Efeoğlu (gerçek kişi) — İstanbul, Türkiye. İletişim: <a href="mailto:{{ site_setting('contact.email') }}">{{ site_setting('contact.email') }}</a>. Bu site kişisel yayın kanalıdır; Anadolu Ajansı ile doğrudan bir kurumsal veri paylaşımı yapılmamaktadır.</p>

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
                <p>Kanunun 11'inci maddesi uyarınca; kişisel verilerinizin işlenip işlenmediğini öğrenme, işlenmişse bilgi talep etme, işlenme amacını öğrenme, düzeltilmesini isteme, silinmesini isteme, işlemeye itiraz etme haklarına sahipsiniz. Başvurularınız için <a href="mailto:{{ site_setting('contact.email') }}">{{ site_setting('contact.email') }}</a> adresini kullanabilirsiniz. Başvurunuza en geç 30 gün içinde yanıt verilecektir.</p>

                <h2>Güvenlik Tedbirleri (KVKK m.12)</h2>
                <ul>
                    <li>Tüm bağlantı TLS 1.3 üzerinden şifrelenir.</li>
                    <li>Veritabanı disk üzerinde şifrelenir (encryption at rest).</li>
                    <li>Admin paneline erişim tek kullanıcılı, iki-faktör kimlik doğrulama (TOTP) zorunludur.</li>
                    <li>Uygulama sunucusu erişim kayıtları 24 saat içinde rotasyona girer.</li>
                </ul>

                <h2>Kanallar ve Hassas Kaynaklar</h2>
                <p>Bu form <strong>uçtan uca şifrelenmiş değildir</strong>. Hassas konular için lütfen <a href="{{ route('contact') }}">iletişim sayfasında</a> listelenen güvenli kanalları (varsa Signal veya PGP) kullanın. Gazeteci &ndash; kaynak ilişkisi bu sitede Türk basın etiği ve Basın Konseyi ilkeleri çerçevesinde korunur.</p>

                <hr>

                <p><small>Bu metin {{ now()->translatedFormat('d F Y') }} tarihinde yayımlanmıştır. Hukuki gözden geçirme süreci tamamlandıkça güncellenebilir.</small></p>
            </div>
        </div>
    </section>
</article>

@endsection
