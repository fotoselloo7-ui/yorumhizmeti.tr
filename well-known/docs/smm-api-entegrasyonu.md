# YorumHizmeti.tr — Sosyal Medya API Entegrasyonu (v1)

## Kapsam
- Birden fazla bağımsız SMM tedarikçisi: API v2 POST \`key\` + \`action\` (\`services\`, \`balance\`, \`add\`, \`status\`, \`refill\`, \`cancel\`).
- Sadece \`Default\` tipindeki servisler otomatik fulfillment için paketleştirilebilir. Package/Custom Comments/Drip-feed gibi ek alan isteyen türler listelenir ama yanlış parametreyle sipariş edilmez.
- Tedarikçi kategorileri/kodları **yalnızca admin** ekranındadır. Katalog adları/kategorileri, kaynak fiyatları **hiçbir müşteri şablonuna aktarılmaz**.
- Her müşteriye satılan paket mevcut \`packages\` tablosunda durur; başlık, açıklama, fiyat, indirim, görsel ve SEO alanları diğer paketler gibi yönetilir.
- Aynı tedarikçide tek servis 100, 500, 1.000 vb. ayrı paketlere dönüştürülebilir. Her paket sabit bir fulfillment adedidir; sepetin varsayılan 1 adet paketiyle satılır.
- Satış fiyatı **TL**, tedarikçi API maliyeti ise bağlantı sırasında seçtiğiniz para birimindedir; otomatik kur veya otomatik maliyet kopyalama **yapılmaz**. Kur farklarını elle yönetmelisiniz.

## Kurulum — Mevcut canlı veritabanını korur
1. Repo yolundaki \`well-known/\` uygulama dosyalarını sunucunuzda kullandığınız PHP uygulama yoluna dağıtın; mevcut \`.env\` dosyasını değiştirmeyin.
2. Sunucuda \`php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'\` komutuyla rastgele anahtar üretin.
3. **Yalnızca cPanel sunucusundaki** \`well-known/.env\` dosyasına \`SMM_ENCRYPTION_KEY=base64:...\` ekleyin. Anahtarı GitHub'a yüklemeyin; kaybolursa kayıtlı API anahtarları çözülemez.
4. Admin → **Sosyal Medya API & Servisler** → **Veritabanı Modülünü Kur** butonu. Bu işlem \`smm-multi-provider-v1.sql\` içindeki 5 ayrı \`CREATE TABLE IF NOT EXISTS\` ifadesini çalıştırır; eski tabloları silmez veya değiştirmez.
5. Sırasıyla Tedarikçi 1, 2, 3... adlarını, tam HTTPS \`/api/v2\` URL'lerini, kendi API anahtarlarını, panelin para birimini ekleyin. Yönetici yetkisi: **super_admin**. API anahtarı tekrar ekranda gösterilmez.
6. API Testi / Bakiye → **Servisleri Çek**. Gerekirse yerel sosyal medya alt kategorisi oluşturun. Hizmeti seçip kendi Türkçe ad, açıklama, fiyat, min. tanıtım, SEO alanlarını doldurun; hazır olana kadar **pasif** tutun.
7. Mevcut paket editöründen ayrıntılı HTML, görsel, fiyat, SEO ve yayındaki durumunu düzenleyin. Müşteriler yalnızca \`/paket/slug\` sayfasını görür; var olan SEO linkleri korunur.
8. Ödeme onayından sonra otomatik sipariş için cPanel → Cron Jobs'da **5 dakikada bir** şu komutu çalıştırın:
   \`/usr/local/bin/php /home/HESAP/public_html/well-known/scripts/smm-worker.php >/dev/null 2>&1\`
   Gerçek PHP binary ve dosya yolunu cPanel sunucunuza göre uyarlayın. Cron olmadan admin panelindeki **Sipariş Kuyruğunu Çalıştır** butonu kullanılabilir.
9. Gerçek para harcamadan önce sandbox/test hesabında tek \`Default\` servis siparişiyle uçtan uca prova yapın: kart/havale ödeme onayı, sıra, tedarikçi numarası, durum güncelleme, hizmet teslimi.

## Güvenlik ve çalışma biçimi
- Sadece HTTPS ve public IPv4'e çözümlenen alan adlarına bağlantı; yönlendirme yasak, SSL doğrulaması açık, DNS IP pinleme ve zaman aşımı var.
- Veritabanındaki tedarikçi anahtarları AES-256-GCM ile saklanır ve hiçbir kamu API'sine/şablonuna verilmez. Her müşteri için sağlayıcı sipariş numarası **gizlidir**.
- Sipariş yaratılınca API job kaydı alınır, ancak **ödeme \`paid\` olmadan** yukarı gönderilmez. Aynı sipariş kalemi için veritabanında tekil anahtar kullanılır.
- Gönderimde belirsiz ağ hatası veya worker çökmesi oluşursa iş \`manual_review\` olur. **Otomatik tekrar göndermek yasak** (aynı hizmetin iki kez satın alınmasını önler). Sağlayıcıdan incelenip elle uzlaştırılır.
- Kısmi/iptal/başarısız kaynak durumları müşteri ödemesini kendiliğinden iade etmez. İadeyi ödeme sisteminin admin sürecinde ayrıca ele alın.
- Tamamlanan bütün kalemler SMM kaynaklıysa genel sipariş tamamlandıya alınır; sepet içinde başka manuel hizmet varsa otomatik kapatılmaz.
- API anahtarları veya müşteri linkleri GitHub Actions loglarına veya issue yorumlarına yapıştırılmamalı.

## Sınırlar / ileri adımlar
- Harici servislerin gerçek kalitesi ve sosyal ağ kuralları entegrasyon koduyla garanti edilemez; tedarikçi sözleşmeleri ve ilgili platform politikalarına uygun servisler seçilmelidir. Müşteriye teslim süresi/kalite konusunda doğrulanmamış vaatler verilmemeli.
- Belirli API panelleri \`cancel\` tekil \`order\` bekleyebilir; bu sürüm PerfectPanel uyumlu çoğul \`orders\` işlemini kullanır.
- Bu ilk sürüm otomatik **Default** siparişini destekler; özel yorum, mentions, package, subscription ve drip-feed tiplerinin her biri için ayrı form ve API parametresi gerekir.
- Servis senkronizasyonu admin tarafından başlatılır; otomatik cron yalnızca ödemesi alınan işleri gönderir ve açık sipariş durumlarını kontrol eder.
- Gerçek sağlayıcı erişimi verilmediğinden canlı API, para birimi ve servis tipi uyumluluğu henüz doğrulanmadı. Önce test siparişi zorunludur.

## Ekim 2026: Toplu Servis, Özgün Kart Açıklamaları ve Hedef Bilgisi
- Admin → Sosyal Medya API & Servisler → **Tüm Panellerin Servislerini Çek**: aktif 2/3/çoklu tedarikçiden API kataloglarını tek işlemle eşitler. Her panel ayrı endpoint/API anahtarı ve döviz birimiyle saklanır.
- Servis listesindeki kutucuklardan **en fazla 80** geçerli \`Default\` servisi seçip **Seçili Servisleri Toplu Paket Olarak Ekle** formundan ortak kategori, TL fiyatı, adet, sizin yazdığınız kısa/detay açıklaması ve 0–12 kart özelliğini kaydet. Kaynak SMM servis adı müşteri başlığına aynen kopyalanmaz.
- Toplu paketler **daima pasif** oluşturulur; editör üzerinden açıklama, SEO, fiyat, kategori, görsel ve özel alanlar ayrı ayrı denetlendikten sonra yayımlanır. Aynı servisten farklı teslimat adetlerinde ayrı paketler oluşturulabilir.
- Admin → Paketler → Paket Düzenle → **Satış Kartındaki Özellikler/Açıklamalar**: Her satır paket kartında bir özellik. 4 satır sabit görünür, 5–12 satır dörderli sayfalarda küçük oklarla gezilebilir. Tek tek paketler için de çalışır, SMM API zorunlu değildir.
- Instagram / TikTok / Threads / YouTube vb. sosyal paket detaylarında eğer özel sipariş alanları yoksa **kullanıcı adı** veya **profil/gönderi/video bağlantısı** alanı otomatik oluşturulur. Sepette saklanır, ödeme ekranında önden doldurulur. Sistem ücretli API gönderiminde kullanıcı adını ilgili platformun HTTPS URL biçimine normalize eder.
- Mevcut paketlerin herkese açık SEO adresleri/ürünleri/kategorileri değişmez.

## NetVera özel müşteri kayıtları
- Admin → NetVera Müşteriler artık eski arşiv henüz eklenmemiş olsa bile **gerçek yeni üyeleri, gerçek siparişleri, ödenen sipariş sayılarını ve bayilik başvurularını** gösterir.
- Admin → Bayilik modülünde süper yöneticinin **Bayilik Modülünü Etkinleştir** işlemi, yeni bayi tablolarını mevcut üyeleri silmeden oluşturur.
- **Özel eski müşteri SQL yedeğindeki kullanıcılar, lisans hakları, satın almalar ve komisyonlar otomatik olarak GitHub'a gömülmez.** Bunun tek güvenli yolu: \`docs/netvera-private-customer-migration-v1.md\` ve \`scripts/import-netvera-private-customers.php\` ile özel MySQL staging üzerinde yedekli dry-run → apply → mutabakat. Gerçek eski kayıtlar henüz staging/live veritabanına aktarılmamışsa admin ekranda sayılmaz. Parola/admin girişleri GitHub'a eklenmez.

