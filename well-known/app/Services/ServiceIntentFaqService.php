<?php
namespace App\Services;

/**
 * Human-readable factual Q&A, tailored to public service intent.
 * FAQ rich-result eligibility is NOT claimed and no FAQPage JSON-LD is emitted.
 * No invented guarantees, addresses, ratings or delivery times.
 */
final class ServiceIntentFaqService
{
    public static function category(array $category):array
    {
        $name=trim((string)($category['name']??'Dijital hizmet'));
        $slug=(string)($category['slug']??'');
        $key=slugify($name.' '.$slug);
        $platform='sosyal medya';
        foreach(['instagram'=>'Instagram','tiktok'=>'TikTok','youtube'=>'YouTube',
                 'facebook'=>'Facebook','telegram'=>'Telegram','spotify'=>'Spotify',
                 'discord'=>'Discord','linkedin'=>'LinkedIn','threads'=>'Threads',
                 'twitter'=>'X','twitch'=>'Twitch','x-'=>'X'] as $token=>$label){
            if(str_contains($key,$token)){$platform=$label;break;}
        }
        $target='profil veya içerik bağlantısı';
        $difference='Paketin hangi hedefe uygulanacağını, adet birimini ve servis türünü karşılaştırın.';
        $risk='Üçüncü taraf platformlar hizmetlerin görünürlüğünü veya hesap durumunu etkileyebilir; sonuç ve kalıcılık garantisi verilmez.';
        $test='Hizmet kapsamındaki miktar, hedef bağlantı ve varsa teslimat şartlarını birlikte kontrol edin.';
        $mode='social';
        if(preg_match('/seo|geo-optimizasyon|aio|arama-motoru|backlink|icerik-seo|yerel-seo/u',$key)){
            $mode='seo';
        }elseif(preg_match('/google-ads|meta-ads|reklam-yonet|reklam-kampanya|performans-pazarlama/u',$key)){
            $mode='ads';
        }elseif(preg_match('/web-tasarim|web-site|e-ticaret|mobil-uygulama|ozel-yazilim|yazilim-gelistir/u',$key)){
            $mode='software';
        }elseif(preg_match('/grafik|logo|banner|video-kurgu|icerik-uretim|blog-makale|reels-uret/u',$key)){
            $mode='creative';
        }elseif(preg_match('/google-isletme|google-harita|yerel-isletme|itibar|yorum-yonet/u',$key)){
            $mode='local';
        }
        if($mode==='seo'){
            return [
                ['question'=>$name.' çalışmasına başlamadan hangi veriler incelenir?',
                 'answer'=>'Arama Konsolu, dizine ekleme durumu, önemli sayfaların sorgu niyeti, teknik hatalar ve varsa ölçülebilen dönüşümler değerlendirilir. Erişim izinleri iş kapsamına göre ayrıca kararlaştırılır.'],
                ['question'=>$name.' sonuçları nasıl ölçülür?',
                 'answer'=>'İlgili sorguların gösterim ve tıklamaları, organik giriş sayfaları, indeks sorunları ve mümkünse form veya satış dönüşümleri izlenir. Sıralama tek başına iş sonucu değildir.'],
                ['question'=>'SEO, GEO ve AIO için ayrı bir yapay zekâ dosyası gerekir mi?',
                 'answer'=>'Google Arama’daki yapay zekâ özellikleri için ayrı bir dosya ya da özel schema türü zorunlu değildir. Taranabilir metin, açıklayıcı başlıklar, doğru canonical, iç bağlantılar ve kullanıcı sorularına açık cevaplar önceliklidir.'],
                ['question'=>$name.' kaç günde sonuç verir?',
                 'answer'=>'Sabit bir süre ve ilk sıra garantisi yoktur. Değişikliklerin taranması, rakiplerin durumu ve sitenin geçmişi sonuçların görülme süresini etkiler; ilerleme düzenli raporla izlenir.'],
            ];
        }
        if($mode==='ads'){
            return [
                ['question'=>$name.' fiyatına reklam bütçesi dahil mi?',
                 'answer'=>'Kampanyaya harcanan platform bütçesi ile danışmanlık/yönetim bedeli ayrı kalemlerdir. Paket detayında açıkça belirtilmeyen medya harcaması fiyata dahil varsayılmamalıdır.'],
                ['question'=>'Hangi dönüşümler takip edilir?',
                 'answer'=>'İş hedefi doğrultusunda form, telefon araması, rezervasyon veya satın alma gibi ölçümler kurulabilir. Ölçüm kurulumu ve reklam hesabı erişimi hizmet kapsamına göre belirlenir.'],
                ['question'=>'Reklamın kesin satış getirmesi garanti mi?',
                 'answer'=>'Hayır. Tıklama maliyeti, açık artırma, açılış sayfası kalitesi ve müşteri talebi değişkendir. Yönetim hizmeti ölçülebilir optimizasyon ve raporlama üzerine kuruludur.'],
                ['question'=>'Kampanyayı başlatmak için ne gerekir?',
                 'answer'=>'Reklam hesabı yetkisi, hedef kitle, kampanya amacı, reklam metin/görselleri ve bütçe sınırı gerekir. Hesap şifresi paylaşmak yerine platformun yetkilendirme araçları kullanılmalıdır.']
            ];
        }
        if($mode==='software'){
            return [
                ['question'=>$name.' için sabit fiyat neden gösterilmiyor?',
                 'answer'=>'Sayfa ve ekran sayısı, API bağlantıları, yönetici özellikleri, güvenlik, veri taşıma ve destek kapsamı projeye göre değişir. Teknik ihtiyaçların yazılı değerlendirilmesinden sonra fiyat belirlenir.'],
                ['question'=>'SEO ve mobil uyumluluk kapsamda mı?',
                 'answer'=>'Temel erişilebilirlik, mobil görünüm ve taranabilirlik gereksinimleri teklif sırasında listelenir. Özgün içerik, teknik SEO optimizasyonu ve yayın sonrası bakım ayrı iş kalemleri olabilir.'],
                ['question'=>'Kaynak kod, hosting ve bakım dahil mi?',
                 'answer'=>'Kaynak dosyaları, alan adı, hosting, lisans, üçüncü taraf servisler ve yayın sonrası destek kapsamı sözleşmede açıkça belirtilmelidir. Her projede aynı paket uygulanmaz.'],
                ['question'=>'Teklif için hangi bilgiler hazırlanmalı?',
                 'answer'=>'Hedef kullanıcılar, işlev listesi, benzer site veya uygulama örnekleri, mevcut altyapı, gerekli entegrasyonlar ve hedeflenen teslim takvimi paylaşılmalıdır.']
            ];
        }
        if($mode==='creative'){
            return [
                ['question'=>$name.' paketinde kaç teslim ve revizyon bulunur?',
                 'answer'=>'Görsel, video veya metin adedi ile revizyon hakkı seçilen ürünün açıklamasına göre değişir. Pakette belirtilmeyen sınırsız revizyon hakkı varsayılmamalıdır.'],
                ['question'=>'Kaynak dosyaları ve kullanım hakları verilir mi?',
                 'answer'=>'Düzenlenebilir dosyalar, stok materyal lisansı, müzik ve font kullanım izinleri sözleşmede veya paket içeriğinde açıkça belirtilmelidir.'],
                ['question'=>'Üretim için hangi materyaller gerekli?',
                 'answer'=>'Marka renkleri, logo, hedef kitle, yayın kanalı, ölçüler, brief ve varsa görseller istenir. Eksik materyaller ek çekim veya tasarım gerektirebilir.'],
                ['question'=>'İçeriklerin Google’da veya keşfette öne çıkması garanti mi?',
                 'answer'=>'Hayır. İçerik hedef kitleye ve yayın kanalına uygun hazırlanabilir ancak arama sıralaması, viral izlenme ve satış sonucu garanti edilemez.']
            ];
        }
        if($mode==='local'){
            return [
                ['question'=>$name.' için işletmenin gerçek adresi gerekli mi?',
                 'answer'=>'Google İşletme Profili ve harita çalışmalarında işletmenin doğrulanabilir, doğru iletişim ve hizmet bölgesi bilgileri kullanılmalıdır; var olmayan şube adresleri oluşturulmamalıdır.'],
                ['question'=>'Müşteri yorumları nasıl yönetilir?',
                 'answer'=>'Gerçek müşteri geri bildirimi tarafsız biçimde istenebilir, yorumlara profesyonel yanıt verilebilir. Sahte veya ödüllendirilmiş değerlendirmeler teklif kapsamı değildir.'],
                ['question'=>'Haritada ilk sıraya çıkma garantisi var mı?',
                 'answer'=>'Hayır. Harita görünürlüğü konum, alaka ve işletmeye ilişkin birçok sinyale bağlıdır; çalışma kapsamı doğrulanabilir işletme bilgilerini ve kullanıcı deneyimini geliştirmeye yöneliktir.'],
                ['question'=>'İşletme hesabına erişim nasıl sağlanır?',
                 'answer'=>'Şifre vermek yerine ilgili platformun rol veya yetki daveti kullanılmalıdır. Gerekli erişim düzeyi çalışma başlamadan netleştirilir.'],
            ];
        }
        if(preg_match('/takipci|abone|uye|member/u',$key)){
            $target='profil, kanal veya geçerli topluluk bağlantısı';
            $difference='Takipçi, abone veya üye miktarı; görüntülenme, beğeni ve aktif topluluk katılımından farklı ölçümlerdir.';
        }elseif(preg_match('/yorum-begeni|yorum-yanit/u',$key)){
            $target='ilgili yorum veya gönderinin ulaşılabilir bağlantısı';
            $difference='Yorum etkileşimi, gönderi beğenisi ve yeni yorum üretimi farklı hizmet türleridir.';
        }elseif(preg_match('/yorum|comment/u',$key)){
            $target='yorum yapılacak gönderi veya içerik bağlantısı';
            $difference='Yorum türü, metin dili, moderasyon ve kullanım koşulları pakette belirtilen kapsamla sınırlıdır.';
        }elseif(preg_match('/begeni|like|reaksiyon/u',$key)){
            $target='beğeni verilecek gönderi veya video bağlantısı';
            $difference='Gönderi beğenisi profil takibinden farklıdır; toplam miktarın hangi içeriğe ait olduğunu kontrol edin.';
        }elseif(preg_match('/izlenme|goruntulenme|dinlenme|canli-yayin|reels|shorts/u',$key)){
            $target='video, canlı yayın, Reels, Shorts veya parça bağlantısı';
            $difference='İzlenme miktarı; ortalama izlenme süresi, sadık izleyici, reklam geliri veya keşfet erişimi ile aynı metrik değildir.';
        }elseif(preg_match('/kaydetme|paylasim|retweet/u',$key)){
            $target='işlem uygulanacak belirli gönderi veya içerik bağlantısı';
            $difference='Kaydetme veya paylaşım etkileşimi, takipçi ve görüntülenme metriğinden farklıdır.';
        }
        return [
            ['question'=>$name.' hizmeti için hangi bağlantı gerekiyor?',
             'answer'=>$platform.' üzerindeki '.$target.' gerekir. Hedefin erişilebilir olması ve ürün sayfasında istenen içerik türüyle eşleşmesi önemlidir. Hesap şifresi ya da doğrulama kodu paylaşmayın.'],
            ['question'=>$name.' ile benzer hizmetlerin farkı nedir?',
             'answer'=>$difference.' Teslim edilecek birim, hedef kitle ve paket koşulları ürün bazında ayrı değerlendirilmelidir.'],
            ['question'=>$name.' fiyatını nasıl karşılaştırmalıyım?',
             'answer'=>'Aynı adet, platform, hedefleme ve teslim koşullarına sahip paketlerin toplam fiyatını karşılaştırın. Tedarikçisi henüz doğrulanmayan yeni alternatiflerde görülen rakam yalnızca ön değerlendirmedir; teklif onayı satın alma yerine geçmez.'],
            ['question'=>$name.' hesabıma zarar verebilir mi?',
             'answer'=>$risk.' '. $test],
            ['question'=>'Bu hizmetten organik büyüme veya satış garantisi alır mıyım?',
             'answer'=>'Hayır. Sosyal medya metrikleri gerçek müşteri ilişkisini ve satış gelirini kanıtlamaz. Platform politikalarını inceleyin; belirtilmeyen bir etkileşim kalitesi veya telafi taahhüdünü varsaymayın.']
        ];
    }
}
