<?php
namespace App\Services;

/** Transparent, query-specific service/offer guidance, not fake testimonials. */
final class ServiceEditorialBlocks
{
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
                'NetVera Teknoloji Yazılım üzerinden iletişim ve teklif bilgilerine ulaşabilirsiniz. Hizmet açıklamaları güncellenebilir; kesin kapsam ilgili ürünün detayında ve onaylanan teklif veya sipariş koşullarında açıklanır.'
            ],
            'faq'=>[
                ['question'=>$name.' hizmeti neleri kapsar?','answer'=>$focus],
                ['question'=>$name.' için fiyat nasıl belirlenir?','answer'=>$quote
                    ? 'İş kapsamı, adet veya entegrasyon gereksinimleri ve teslim takvimi belirlendikten sonra size özel fiyat teklifi hazırlanır.'
                    : 'Satış fiyatı seçilen paket ve miktar üzerinden belirlenir. İlgili tutar ve satın alma şartları paket detay sayfasında gösterilir.'],
                ['question'=>'Çalışma sonuçları garanti edilir mi?','answer'=>'Belirli bir Google sıralaması, satış miktarı ya da üçüncü taraf platform metriklerinin kalıcılığı garanti edilemez. Teslim ve destek koşulları, hizmetin gerçek kapsamına göre belirlenir.'],
                ['question'=>'Başlamadan önce hangi bilgiler gerekir?','answer'=>'Hizmet veya hesap bağlantısı, hedefler ve gerekli teknik erişimler seçilen hizmete göre değişebilir. Şifre veya hassas erişim gerekiyorsa yalnızca yetkili ve güvenli kanallarla ilerlenmelidir.']
            ]
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
                'Bu hizmetin içeriği ve teknik koşulları ürün detayındaki bilgiler ile varsa yazılı teklife göre belirlenir. NetVera dışında çalışan platformların algoritmaları ve inceleme süreçleri üzerinde kesin sonuç taahhüt edilmez.'
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
