/* global React */

window.ADMIN_NAV = [
  {
    label: "Yayın",
    items: [
      { key: "dashboard", glyph: "▤", title: "Masa", count: null },
      { key: "writings", glyph: "✎", title: "Yazılar", count: 47 },
      { key: "publications", glyph: "⎈", title: "Yayınlar", count: 8 },
      { key: "pages", glyph: "☰", title: "Sayfalar", count: 6 },
      { key: "photos", glyph: "◨", title: "Fotoğraflar", count: 312 },
    ],
  },
  {
    label: "Gelen kutusu",
    items: [
      { key: "inbox", glyph: "✉", title: "Mesajlar", count: 4 },
    ],
  },
  {
    label: "Site",
    items: [
      { key: "settings", glyph: "⚙", title: "Ayarlar", count: null },
      { key: "audit", glyph: "⌇", title: "Denetim kaydı", count: null },
      { key: "backup", glyph: "⬒", title: "Yedekleme", count: null },
    ],
  },
  {
    label: "Hesap",
    items: [
      { key: "users", glyph: "☺", title: "Kullanıcılar", count: 2 },
      { key: "profile", glyph: "◉", title: "Profil", count: null },
      { key: "sessions", glyph: "⎙", title: "Oturumlar", count: null },
      { key: "twofa", glyph: "⚿", title: "İki faktör", count: null },
    ],
  },
];

window.ADMIN_WRITINGS = [
  { id: 1, title: "Kasanın altındaki şehir", kind: "Saha yazısı", status: "published", date: "2026-04-26", updated: "2026-04-26 09:14", views: 4218, location: "Hatay" },
  { id: 2, title: "Bir foto‑muhabirin not defteri", kind: "Röportaj", status: "published", date: "2026-04-12", updated: "2026-04-13 15:02", views: 2902, location: "İstanbul" },
  { id: 3, title: "Saf siyah dijital bir yalandır", kind: "Deneme", status: "published", date: "2026-04-03", updated: "2026-04-03 22:48", views: 1640, location: "—" },
  { id: 4, title: "Kabin · saha defteri 016", kind: "Not", status: "published", date: "2026-03-21", updated: "2026-03-21 06:30", views: 540, location: "Adana" },
  { id: 5, title: "Zeytin Dalı'ndan iki kare", kind: "Saha yazısı", status: "published", date: "2026-03-08", updated: "2026-03-08 19:11", views: 3104, location: "Hatay" },
  { id: 6, title: "Drone etiği — birinci taslak", kind: "Deneme", status: "draft", date: null, updated: "2026-04-25 23:50", views: 0, location: "—" },
  { id: 7, title: "Yedi muhabirin not defteri", kind: "Röportaj", status: "scheduled", date: "2026-05-04", updated: "2026-04-22 14:08", views: 0, location: "Adana" },
  { id: 8, title: "Uludağ'da bir bilgisayar programcısı", kind: "Deneme", status: "published", date: "2026-02-18", updated: "2026-02-18 08:30", views: 1922, location: "Bursa" },
  { id: 9, title: "Eski sokak, yeni isim", kind: "Saha yazısı", status: "draft", date: null, updated: "2026-04-20 11:40", views: 0, location: "İstanbul" },
  { id: 10, title: "Anadolu'da gazete arşivi — 2024", kind: "Not", status: "archived", date: "2024-11-12", updated: "2025-01-04 09:00", views: 89, location: "—" },
];

window.ADMIN_INBOX = [
  { id: 1, name: "Elif Demir", email: "elif@bireybirey.org", subject: "Saha — kuzey ışığı dosyası", date: "2026-04-25 18:42", short: "Merhaba Ozan, Birey Birey için Karadeniz hattında üç günlük bir...", body: "Merhaba Ozan,\n\nBirey Birey için Karadeniz hattında üç günlük bir saha çalışması düşünüyoruz. 18 Mayıs ile 22 Mayıs arası uygun musunuz? Ödeme ve seyahat detaylarını ayrıca konuşalım.\n\nİyi çalışmalar,\nElif", status: "new", topic: "Saha — yeni dosya" },
  { id: 2, name: "Murat K.", email: "murat@archive.istanbul", subject: "Arşiv — 2018 Hatay seri", date: "2026-04-24 11:08", short: "İyi günler, 2018 Hatay serisinden iki kareyi sergi kataloğu için...", body: "İyi günler,\n\n2018 Hatay serisinden iki kareyi sergi kataloğu için kullanmak istiyoruz. Telif ve kullanım koşulları için bir görüşme yapabilir miyiz?\n\nSaygılarımla,\nMurat", status: "new", topic: "Arşiv kullanımı" },
  { id: 3, name: "Selin Yıldız", email: "selin@yildiz.uni", subject: "Atölye — drone haberciliği", date: "2026-04-22 09:30", short: "Sayın Efeoğlu, Yıldız Üniversitesi İletişim Fakültesi'nde lisans...", body: "Sayın Efeoğlu,\n\nYıldız Üniversitesi İletişim Fakültesi'nde lisans öğrencileri için drone haberciliği üzerine bir atölye konuşması düzenlemek istiyoruz. Mayıs ortasından sonra uygun bir tarih bulabilir miyiz?\n\nSaygılarımla,\nSelin", status: "new", topic: "Atölye / konuşma" },
  { id: 4, name: "Cenk Aksoy", email: "cenk@birgun.net", subject: "Röportaj — saha pratikleri", date: "2026-04-20 16:55", short: "Selam Ozan, BirGün için saha pratikleri üzerine uzun bir röportaj...", body: "Selam Ozan,\n\nBirGün için saha pratikleri üzerine uzun bir röportaj yapmak istiyoruz. Bir buçuk saatlik kayıt yeterli olur. Önümüzdeki iki hafta içinde uygun bir gün?\n\nC.", status: "new", topic: "Röportaj talebi" },
  { id: 5, name: "Aylin Soner", email: "aylin@aperture.org", subject: "Aperture — özet için katkı", date: "2026-04-15 13:20", short: "Hi Ozan, we are putting together an issue on documentary practice...", body: "Hi Ozan,\n\nWe are putting together an issue on documentary practice in the Eastern Mediterranean for Aperture and would like to invite a 1,200‑word contribution from you. Deadline mid‑June.\n\nBest,\nAylin", status: "resolved", topic: "Diğer" },
];

window.ADMIN_AUDIT = [
  { time: "26.04 09:14", actor: "ozan", action: "publish", target: "writings:1 — Kasanın altındaki şehir" },
  { time: "26.04 09:12", actor: "ozan", action: "update", target: "writings:1 — son taslak" },
  { time: "25.04 23:50", actor: "ozan", action: "draft.save", target: "writings:6 — Drone etiği" },
  { time: "25.04 18:42", actor: "system", action: "contact.received", target: "inbox:1 — Elif Demir" },
  { time: "24.04 11:08", actor: "system", action: "contact.received", target: "inbox:2 — Murat K." },
  { time: "23.04 04:00", actor: "system", action: "backup.s3", target: "tar.gz · 412 MB · ok" },
  { time: "22.04 14:08", actor: "ozan", action: "schedule", target: "writings:7 — 04 Mayıs 09:00" },
  { time: "20.04 11:40", actor: "ozan", action: "draft.save", target: "writings:9" },
  { time: "20.04 09:14", actor: "ozan", action: "auth.login", target: "192.168.1.4 · Safari · İstanbul" },
];
