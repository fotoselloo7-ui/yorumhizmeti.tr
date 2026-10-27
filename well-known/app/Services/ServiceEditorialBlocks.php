<?php
namespace App\Services;

/** Transparent, query-specific service/offer guidance, not fake testimonials. */
final class ServiceEditorialBlocks
{
    /**
     * Useful topical checkpoints, scoped by the real service category name.
     * Neither geographic office locations nor performance results are invented.
     * This copy supplements (never replaces) editor-authored SEO fields.
     */
    private static function checkpoints(string $name):array
    {
        $text=mb_strtolower($name,'UTF-8');
        if(preg_match('/seo|arama motoru|backlink|organik|anahtar kelime/u',$text)){
            return [
                'Teknik incelemede robots ve indekslenebilirlik, canonical adresler, yönlendirme zincirleri, sayfa hızı ve mobil kullanım birlikte ele alınır. Sorunların önceliği, etkilenen URL sayısına ve sayfaların gerçekten hangi sorguları karşılaması gerektiğine göre belirlenir.',
                'İçerik planında ana sayfa, hizmet ve alt kategori sorguları için birbirini tekrar etmeyen sayfalar tanımlanır. Google Search Console verileri sorgu niyeti, tıklama ve görünürlük açısından incelenebilir; yapay zekâ aramalarında görünürlük ölçümü ise kullanılan kaynağa göre değişebilir.',
                'Yerel işletmelerde harita profilinin gerçek konum bilgileri ile web sitesindeki şirket bilgilerinin uyumlu olması önemlidir. Sahte şube konumları, ücretli bağlantı şemaları veya kesin ilk sıra garantisi önerilmez.'
            ];
        }
        if(preg_match('/reklam|google ads|meta ads|kampanya|dijital pazarlama/u',$text)){
            return [
                'Kampanya hazırlığında iş hedefi ile ölçülecek olay birbirinden ayrılır. Telefon görüşmesi, form gönderimi, e-ticaret satın alımı ve mağaza ziyareti aynı dönüşüm değildir. Verinin uygun izinlerle doğru kaydedildiği kontrol edilmeden optimizasyon kararı verilmez.',
                'Reklam hesabında hedefleme, arama terimleri, negatif anahtar kelimeler, kreatif çeşitliliği, teklif stratejisi ve günlük harcama sınırları birlikte değerlendirilir. Reklam bütçesi, hizmet yönetim bedeli ve görsel üretim maliyeti teklif üzerinde ayrı kalemler olabilir.',
                'Performans değerlendirmesi yalnız görüntülenme ve tıklama üzerinden yapılmaz; geçerli taleplerin kalitesi ve mümkünse satışa dönüşümü incelenir. Reklam platformunun açık artırma koşulları ve müşteri talebi sürekli değiştiğinden sabit sonuç garantisi verilmez.'
            ];
        }
        if(preg_match('/web|site|wordpress|ticaret|e-ticaret|uygulama|mobil|yaz[ıi]l[ıi]m|entegrasyon|otomasyon/u',$text)){
            return [
                'Teknik kapsamda sayfa şablonları, mobil erişilebilirlik, yönetici yetkileri, formlar, veri akışı, yedekleme ve ihtiyaç duyulan entegrasyonlar baştan listelenmelidir. Hazır şablon uyarlaması ile özel geliştirme aynı iş kalemi değildir.',
                'Kullanıcıların gerçekten gerçekleştireceği işlemler üzerinden ekran akışları tasarlanır. İçerik girişinin müşteriye mi ekibe mi ait olduğu, eğitim ve yayın sonrası destek gibi konular teslim planında ayrıca belirtilir.',
                'Yayına alma öncesinde performans, güvenlik, form uçları, mobil kullanım, erişim izinleri ve SEO teknik etiketleri kontrol edilir. Arama motorlarında yükselme bir yazılımın satın alınmasıyla otomatik gerçekleşmez.'
            ];
        }
        if(preg_match('/grafik|logo|kurumsal kimlik|tasarım|içerik|icerik|video|reels|prodüksiyon|produksiyon/u',$text)){
            return [
                'İş başlangıcında marka yönergeleri, hedef kitle, yayınlanacak platform, ölçüler ve dosya formatları belirlenir. Görsel çalışmalarda kullanılan stok, müzik ve fontların ticari lisans koşulları ayrıca değerlendirilmelidir.',
                'İçerik üretiminde araştırma, taslak, kontrol, revizyon ve nihai onay adımları ayrı planlanır. SEO odaklı metinler kullanıcı sorularını cevaplamalı; anahtar kelimeler ve şehir isimleri sırf uzunluk artırmak için tekrarlanmamalıdır.',
                'Teslim edilen dosyaların kaynak dosya içerip içermediği, kaç revizyonun dahil olduğu ve yayın yönetimi hizmetinin ayrıca sağlanıp sağlanmadığı teklif aşamasında netleştirilir.'
            ];
        }
        if(preg_match('/instagram|tiktok|youtube|facebook|telegram|threads|spotify|discord|twitter|twitch|sosyal medya|takip[çc]i|beğeni|izlenme|abone/u',$text)){
            return [
                'Platform odaklı paketlerde profil, kanal ve içerik bağlantısı birbirinden farklı hedeflerdir. Paket siparişinde istenen alanın gerçekten doğru olduğundan emin olun; sosyal medya şifresi veya iki aşamalı doğrulama kodu paylaşılmamalıdır.',
                'Takipçi, beğeni, izlenme veya yorum miktarı tek başına gerçek potansiyel müşteri, kalıcı etkileşim veya organik büyüme kanıtı değildir. Hesap durumuna ve platform kurallarına aykırı hizmetler risk taşıyabilir.',
                'Telafi, hedefleme ve teslim süresi koşulları yalnız ilgili pakette açıkça belirtilmişse geçerlidir. Farklı fiyat segmentleri kalite, hız ve hedefleme açısından eşdeğer varsayılmamalıdır.'
            ];
        }
        return [
            'Teklif öncesinde kullanılacak kanallar, işin mevcut durumu ve hangi teslimlerin kapsama dahil olduğu netleştirilir. Böylece ek talepler, bakım veya destek gibi konular yanlış beklenti oluşturmaz.',
            'Sonuçların takibi için başlangıç ölçümleri ve anlaşılabilir raporlar belirlenir. Her proje aynı göstergeyle değerlendirilmez; geçerli ölçümler iş hedefine göre seçilir.'
        ];
    }

    public static function category(array $cat, bool $quote):array
    {
        $name=trim((string)($cat['name']??'Dijital hizmet'));
        $focus=CategoryEditorialCopyService::description($cat);
        $process=$quote
            ? 'Hizmet kapsamı; yapılacak işler, ihtiyaç duyulan içerikler, revizyonlar ve teslimler temelinde belirlenir. Bu nedenle bağlayıcı fiyat teklif aşamasında paylaşılır.'
            : 'Bu kategoride miktar, hizmet türü, teslim süresi ve varsa sağlayıcı eşleştirmesi birbirinden farklı olabilir. Sipariş öncesinde seçilen paketin gerçek koşullarını kontrol edin.';
        $measure=$quote
            ? 'Sonuçlar proje amacına göre tanımlanır; görünürlük, form dönüşümleri, trafik kalitesi veya teslim edilen yazılım işlevleri değerlendirilebilir.'
            : 'Platform metrikleri kısa dönemde değişebilir. Takipçi, görüntülenme veya yorum sayısı satış, gerçek topluluk etkileşimi ya da kalıcı hesap büyümesi garantisi değildir.';
        return [
            'title'=>$name.' hakkında kapsamlı rehber',
            'lead'=>$focus,
            'paragraphs'=>[
                $process,
                $name.' seçeneklerini değerlendirirken hedefinizi ve mevcut durumunuzu belirleyin. Kullanılacak kanalları, proje başlangıç koşullarını ve sunulan iş kapsamını karşılaştırın. Yalnızca fiyat ya da paket adından hareketle karar vermeyin.',
                $measure,
                ...self::checkpoints($name),
                'NetVera Teknoloji Yazılım üzerinden iletişim ve teklif bilgilerine ulaşabilirsiniz. Hizmet açıklamaları güncellenebilir; kesin kapsam ilgili ürünün detayında ve onaylanan teklif veya sipariş koşullarında açıklanır.'
            ],
            'faq'=>array_merge([
                ['question'=>$name.' hizmeti neleri kapsar?','answer'=>$focus],
                ['question'=>$name.' için fiyat nasıl belirlenir?','answer'=>$quote
                    ? 'İş kapsamı, adet veya entegrasyon gereksinimleri ve teslim takvimi belirlendikten sonra size özel fiyat teklifi hazırlanır.'
                    : 'Satış fiyatı seçilen paket ve miktar üzerinden belirlenir. İlgili tutar ve satın alma şartları paket detay sayfasında gösterilir.'],
                ['question'=>'Çalışma sonuçları garanti edilir mi?','answer'=>'Belirli bir Google sıralaması, satış miktarı ya da üçüncü taraf platform metriklerinin kalıcılığı garanti edilemez. Teslim ve destek koşulları, hizmetin gerçek kapsamına göre belirlenir.'],
                ['question'=>'Başlamadan önce hangi bilgiler gerekir?','answer'=>'Hizmet veya hesap bağlantısı, hedefler ve gerekli teknik erişimler seçilen hizmete göre değişebilir. Şifre veya hassas erişim gerekiyorsa yalnızca yetkili ve güvenli kanallarla ilerlenmelidir.']
            ], ServiceIntentFaqService::category($cat))
        ];
    }

    public static function package(array $pkg, bool $quote):array
    {
        $name=trim((string)($pkg['name']??''));
        if($name==='')$name=trim((string)($pkg['seo_title']??'Hizmet paketi'));
        $category=trim((string)($pkg['category_name']??'Dijital hizmet'));
        $description=trim(strip_tags((string)($pkg['short_description']??'')));
        if($description===''||mb_strlen($description,'UTF-8')<30){
            $description=$name.', '.$category.' kapsamında değerlendirilen bir hizmet seçeneğidir. Uygulama adımları ve teslim koşulları seçilen kapsamla netleştirilir.';
        }
        $how=$quote?'Kapsam, bütçe ve proje takvimi karşılıklı değerlendirilerek yazılı teklif verilir.':
            'Seçilen paketin miktarı ve teslim koşulları sipariş öncesinde gösterilir. Tamamlanma süresi üçüncü taraf platform koşullarına bağlı olarak değişebilir.';
        return [
            'title'=>$name.' hizmet kapsamı ve seçim rehberi',
            'lead'=>$description,
            'paragraphs'=>[
                $how,
                'Hangi paketin uygun olduğuna karar verirken hizmetin hedefini, uygulanacağı dijital kanalı, istenen teslimleri ve teknik sınırlamaları karşılaştırın. Gereksinimler değişiyorsa sipariş öncesinde NetVera ile iletişime geçin.',
                'Bu hizmetin içeriği ve teknik koşulları ürün detayındaki bilgiler ile varsa yazılı teklife göre belirlenir. NetVera dışında çalışan platformların algoritmaları ve inceleme süreçleri üzerinde kesin sonuç taahhüt edilmez.',
                ...self::checkpoints($category)
            ],
            'faq'=>[
                ['question'=>$name.' ne için kullanılır?','answer'=>$description],
                ['question'=>'Bu hizmetin fiyatını nasıl öğrenebilirim?','answer'=>$quote?'Projenizi ve beklentilerinizi iletişim formundan ileterek yazılı fiyat ve kapsam teklifi talep edebilirsiniz.':'Güncel paket satış fiyatı ürünün üst bölümünde belirtilir. Sepete eklemeden önce miktarı ve teslim koşullarını inceleyin.'],
                ['question'=>'Teslim süreci nasıl belirlenir?','answer'=>$how],
                ['question'=>'Sonuç ve görünürlük garantisi var mı?','answer'=>'Hizmete ait teslim şartları paket detayında tanımlanır; belirli sıralama, satış veya hesap etkileşimi sonucu garanti edilmez.']
            ]
        ];
    }
}
