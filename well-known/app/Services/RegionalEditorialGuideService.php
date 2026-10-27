<?php
namespace App\Services;

/**
 * Existing published location landings lacked the editor's long guide fields.
 * Render a truthful, topic-specific starting guide without saving generic copy
 * into the page; the editor's own text always wins.
 */
final class RegionalEditorialGuideService
{
    public static function fallback(array $landing):array
    {
        $city=trim((string)($landing['city']??''));
        $service=trim((string)($landing['service']??'dijital hizmet'));
        $scope=trim((string)($landing['local_context']??''));
        $topic=mb_strtolower($service,'UTF-8');
        [$need,$method,$measure]=match(true){
            (bool)preg_match('/web|tasar[ıi]m|site|yaz[ıi]l[ıi]m|uygulama/u',$topic)=>[
                'sayfa mimarisi, mobil erişilebilirlik, yönetim paneli ve gereken entegrasyonlar',
                'içerik envanteri, teknik gereksinimler, geliştirme kilometre taşları ve yayına alma adımları',
                'sayfa yükleme performansı, form dönüşümleri, erişilebilirlik ve bakım talepleri'],
            (bool)preg_match('/reklam|google.ads|meta|kampanya/u',$topic)=>[
                'hedef kitle, bütçe sınırları, teklif stratejileri ve doğru dönüşüm olayları',
                'reklam hesabı denetimi, kreatif geliştirme, ölçüm kurulumu ve kontrollü kampanya testleri',
                'nitelikli başvurular, dönüşüm maliyeti, gösterim payı ve bütçe verimliliği'],
            (bool)preg_match('/seo|arama|geo|aio/u',$topic)=>[
                'tarama ve indekslenebilirlik, arama niyeti, içerik sahipliği ve teknik borç',
                'teknik denetim, öncelik haritası, özgün içerik geliştirme ve düzenli izleme',
                'Search Console sorguları, organik görünürlük, faydalı ziyaretler ve dönüşüm verileri'],
            (bool)preg_match('/sosyal|medya|içerik|icerik|grafik|video/u',$topic)=>[
                'marka dili, hedef topluluk, yayın kanalları ve içerik üretim kapasitesi',
                'içerik takvimi, görsel/video üretimi, yayın onayı ve geri bildirim döngüsü',
                'içerik erişimi, nitelikli etkileşimler ve hedefe uygun başvuru veya trafik'],
            default=>[
                'işletmenin dijital ihtiyaçları, zaman planı, kapsam ve gereksinimler',
                'ihtiyaç analizi, teklif kapsamının yazılması, uygulama ve kalite kontrol',
                'mutabık kalınan teslimler, kullanılabilirlik ve iş hedefleri']
        };
        $location=$city!==''?$city.' bölgesinde hizmet arayan işletmeler':'İşletmeler';
        $paragraphs=[
            $location.' için '.$service.' araştırılırken ilk adım, işin tam olarak hangi sorunu çözeceğini belirlemektir. Her proje için aynı paket ya da tek bir başarı ölçütü doğru değildir. NetVera Teknoloji Yazılım, önce mevcut dijital varlıkları ve istenen çıktıların kapsamını değerlendirir.',
            'Proje görüşmesinde '.$need.' belirlenir. Böylece uygulanacak hizmet ile yalnızca danışmanlık kapsamına giren işler birbirine karıştırılmaz. Gerekiyorsa kullanılan sistemler, yayın kanalları, mevcut içerikler ve yetkili erişimleri ayrıca görüşülür.',
            'Çalışma planı '.$method.' üzerine kurulur. İlgili taraflar teslim takvimini, revizyon sayısını ve dışarıdan sağlanması gereken içerik veya erişimleri teklif sırasında netleştirir. Üçüncü taraf platformların algoritması, onayları ve kampanya performansı doğrudan kontrol edilemeyeceği için kesin sıralama, müşteri veya gelir artışı sözü verilmez.',
            'Sonuç değerlendirmesi için '.$measure.' esas alınabilir. Hangi göstergelerin ölçüleceği başlangıçtaki iş hedefine ve kullanılabilen veri kaynaklarına bağlıdır. Proje tamamlandıktan sonra gerekliyse bakım, optimizasyon ve sonraki adımlar ayrıca planlanır.',
            $city!==''?$city.' odaklı bir açılış sayfası veya hizmet açıklaması, yalnızca şehir adının değiştirilmesiyle oluşturulmaz. Hizmet verilebilen bölgeler, işin uzaktan mı yoksa planlı görüşmeyle mi yürütüleceği ve iletişim yöntemi açıkça anlatılmalıdır. Bu sayfa tek başına '.$city.' içinde fiziksel ofis veya şube bulunduğu anlamına gelmez.':'Fiziksel çalışma adresi ile çevrim içi hizmet alanı birbirinden ayrılmalıdır. Güncel iletişim adresi iletişim sayfasında belirtilir.'
        ];
        return [
            'title'=>$city!==''?$city.' için '.$service.' çalışma rehberi':$service.' çalışma rehberi',
            'lead'=>$service.' projesinde teklif öncesinde hangi konuların değerlendirilmesi, işin nasıl planlanması ve hangi sonuçların izlenmesi gerektiğini öğrenin.',
            'paragraphs'=>$paragraphs,
            'questions'=>[
                ['question'=>$service.' hizmeti için nasıl teklif alabilirim?','answer'=>'İhtiyacınızı, mevcut sistemleri ve beklediğiniz çıktıları iletişim formundan iletin. Kapsam ve teslim planı değerlendirildikten sonra projenize özel teklif hazırlanır.'],
                ['question'=>'Bu hizmet '.$city.' içinde fiziksel ofis gerektirir mi?','answer'=>'Çalışmalar projenin gereksinimine göre uzaktan veya planlı görüşmelerle ilerleyebilir. Güncel şirket adresini iletişim sayfamızdan kontrol edebilirsiniz.'],
                ['question'=>'Çalışmanın sonuçları nasıl takip edilir?','answer'=>'Ölçülecek göstergeler proje başında kararlaştırılır. Sonuçlar kullanılan ölçüm araçlarının sağladığı veriler üzerinden değerlendirilir; belirli Google sırası veya müşteri sayısı garanti edilmez.']
            ]
        ];
    }
}
