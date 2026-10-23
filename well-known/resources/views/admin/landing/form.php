<?php
$d=$landing;
$editorField=static function(string $label,string $name,string $value,int $max=180,int $rows=1):void{
 $id='landing-field-'.substr(hash('sha256',$name),0,12);
 echo '<div class="form-group"><label for="'.$id.'">'.e($label).'</label>';
 if($rows>1)echo '<textarea class="form-control" id="'.$id.'" name="'.e($name).'" rows="'.$rows.'" maxlength="'.$max.'">'.e($value).'</textarea>';
 else echo '<input class="form-control" id="'.$id.'" name="'.e($name).'" maxlength="'.$max.'" value="'.e($value).'">';
 echo '</div>';
};
?>
<div class="landing-editor"><header class="landing-editor-intro"><span>KONUMSAL HİZMET / LANDING PAGE</span><h1><?= $landingRow?'Sayfanızı geliştirin.':'Özgün bir hizmet sayfası oluşturun.' ?></h1><p>Tek H1, yapılandırılmış H2/H3 bölümleri ve gerçek hizmet kapsamı. İletişim bilgileri ortak site ayarlarından alınır. Şehir adı değiştirilmiş kopyalar veya var olmayan ofis bilgileri yayınlamayın.</p><a href="/admin/konumsal-sayfalar">← Konumsal sayfalar</a><?php if($landingRow): ?><a href="/<?= rawurlencode($landingRow['slug']) ?>?onizleme=1" target="_blank" rel="noopener">Yetkili önizleme ↗</a><?php endif; ?></header>
<form method="post" action="/admin/konumsal-sayfalar/kaydet" enctype="multipart/form-data" data-landing-editor><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)($landingRow['id']??0) ?>"><input type="hidden" name="revision" value="<?= $landingRow?e(\App\Services\RegionalLandingService::revision($landingRow)):'' ?>">
<nav class="landing-editor-nav" aria-label="Editör bölümleri"><a href="#landing-basic">Kimlik & yayın</a><a href="#landing-design">5 Tema</a><a href="#landing-visual">Banner</a><a href="#landing-body">İçerik</a><a href="#landing-selection">Hizmet & referans</a><a href="#landing-seo">SEO / GEO / AIO</a></nav>
<section class="adm-card" id="landing-basic"><div class="adm-card-header"><h2>Kimlik, konum ve yayın</h2></div><div class="adm-card-body">
<?php $editorField('Sayfa ana başlığı — tek H1','title',$d['title']);$editorField('Kök URL slug (örn. alanya-reklam-ajansi)','slug',$d['slug']); ?>
<p class="landing-editor-help">Slug değişikliği eski adresi 301 ile korur. Sistem rotaları kullanılamaz. Yeni adreslerde küçük harf, sayı ve tire kullanın.</p><div class="landing-editor-grid">
<?php foreach(['city'=>['Şehir / ilçe',100],'province'=>['İl',100],'service'=>['Hizmet adı',160],'area'=>['Ayrıntılı hizmet bölgesi',300]]as $key=>[$label,$max])$editorField($label,$key,$d[$key],$max); ?>
</div><div class="landing-editor-grid"><?php foreach(['status'=>['Yayın durumu',['inactive'=>'Taslak / yayından kaldır','active'=>'Yayında']],'working_mode'=>['Çalışma biçimi',['remote'=>'Uzaktan / çevrim içi','hybrid'=>'Uzaktan ve planlı görüşme','onsite'=>'Planlı yerinde çalışma']],'theme'=>['Renk teması',['violet'=>'NetVera mor','blue'=>'Kurumsal mavi','rose'=>'Sosyal pembe']]]as $key=>[$label,$options]): ?><div class="form-group"><label for="landing-<?= $key ?>"><?= $label ?></label><select id="landing-<?= $key ?>" class="form-control" name="<?= $key ?>"><?php foreach($options as $value=>$text): ?><option value="<?= $value ?>" <?= $d[$key]===$value?'selected':'' ?>><?= $text ?></option><?php endforeach; ?></select></div><?php endforeach; ?></div><p class="landing-editor-help">Yayın ve indeksleme ayrı tercihlerdir. Şehir, Service → areaServed şemasında kullanılır; yerel şube veya harita adresi üretilmez.</p></div></section>

<section class="adm-card landing-design-section" id="landing-design">
  <div class="adm-card-header"><h2>Şehir Bazlı Tasarım Seçimi — 5 Premium Tema</h2></div>
  <div class="adm-card-body">
    <p class="landing-editor-help">Her şehir/hizmet sayfasında bağımsız bir tema kullanabilirsiniz. Tema yalnızca görsel yerleşimi değiştirir; H1, H2/H3, içerik blokları, hizmetler, referanslar, canonical, SEO/GEO/AIO verileri ve mevcut URL aynı kalır.</p>
    <?php $nvDesigns=[
      'spotlight'=>['Spotlight','Premium Ajans','Lacivert hero, güçlü tipografi, bento hizmet kartları.','Koyu premium ajans','star'],
      'atlas'=>['Atlas','Yerel Güven','Adaçayı tonları, konum vurgusu, rehber/timeline yaklaşımı.','Güven veren yerel','map-pin'],
      'signal'=>['Signal','Dönüşüm & Teklif','Dinamik mercan renkler, görünür CTA, güçlü hizmet kartları.','Satış ve dönüşüm','trending-up'],
      'canvas'=>['Canvas','Editoryal Prestij','Ferah dergi düzeni, büyük başlıklar, uzun makaleye uygun.','İçerik ağırlıklı','book-open'],
      'prism'=>['Prism','Teknoloji & AI','Lacivert-mor-teal degradeler, cam hissi, modern vitrin.','Yapay zekâ/teknoloji','sparkles'],
    ]; ?>
    <div class="landing-design-grid" role="radiogroup" aria-label="Şehir sayfası tasarımını seçin">
    <?php foreach($nvDesigns as $nvKey=>$nvDesign): ?>
      <label class="landing-design-option">
        <input type="radio" name="layout" value="<?= e($nvKey) ?>" <?= ($d['layout']??'studio')===$nvKey?'checked':'' ?>>
        <span class="landing-design-option-body">
          <span class="landing-design-thumbnail" data-design="<?= e($nvKey) ?>" aria-hidden="true">
            <span class="landing-design-navline"><i></i><i></i><i></i></span>
            <span class="landing-design-hero-mini"><i></i><b></b><em></em><small></small></span>
            <span class="landing-design-content-mini"><i></i><i></i><i></i></span>
            <span class="landing-design-footer-mini"></span>
          </span>
          <span class="landing-design-meta">
            <strong><?= icon($nvDesign[4],15) ?> <?= e($nvDesign[0]) ?></strong>
            <small><?= e($nvDesign[1]) ?></small>
            <span><?= e($nvDesign[2]) ?></span>
          </span>
          <span class="landing-design-actions">
            <span class="landing-design-chosen"><?= icon('check-circle',15) ?> Seçili Tasarım</span>
            <?php if($landingRow): ?><a href="/<?= rawurlencode($landingRow['slug']) ?>?onizleme=1&amp;tasarim=<?= e($nvKey) ?>" target="_blank" rel="noopener">Sayfada Önizle <?= icon('external-link',12) ?></a><?php endif; ?>
          </span>
        </span>
      </label>
    <?php endforeach; ?>
    </div>
    <details class="landing-design-legacy" <?= in_array(($d['layout']??'studio'),['studio','editorial'],true)?'open':'' ?>>
      <summary>Önceki tasarımları koru (Studio / Editorial)</summary>
      <div class="landing-design-legacy-options">
        <label><input type="radio" name="layout" value="studio" <?= ($d['layout']??'studio')==='studio'?'checked':'' ?>> Studio — mevcut orijinal tasarım</label>
        <label><input type="radio" name="layout" value="editorial" <?= ($d['layout']??'studio')==='editorial'?'checked':'' ?>> Editorial — önceki içerik tasarımı</label>
      </div>
    </details>
    <p class="landing-editor-help">Temaları canlı sayfaya kaydetmeden önce yanlarındaki önizleme bağlantılarıyla deneyebilirsiniz. Mevcut şehir sayfaları kendiliğinden değiştirilmez.</p>
  </div>
</section>
<section class="adm-card" id="landing-visual"><div class="adm-card-header"><h2>Banner ve özgün yerel kapsam</h2></div><div class="adm-card-body">
<?php $editorField('Banner açıklaması','summary',$d['summary'],600,3);$editorField('Bu bölgeye özgü kapsam: bu sayfa neden ayrı hazırlanıyor?','local_context',$d['local_context'],3000,5); ?>
<div class="landing-editor-grid"><?php foreach(['image'=>'Masaüstü görseli','mobile_image'=>'Mobil görsel (opsiyonel)']as $key=>$label): ?><div><?php $editorField($label.' — yerel dosya yolu',$key,$d[$key],500); ?><div class="form-group"><label for="upload-<?= $key ?>">Yeni görsel yükle (en fazla 5 MB)</label><input id="upload-<?= $key ?>" class="form-control" type="file" name="<?= $key ?>_upload" accept="image/jpeg,image/png,image/webp,image/gif"></div></div><?php endforeach; ?></div>
<?php $editorField('Banner alt metni','image_alt',$d['image_alt'],220);$editorField('İletişim butonu metni','cta_label',$d['cta_label'],70); ?><p class="landing-editor-help">Telefon, WhatsApp ve e-posta Site Ayarları’ndan alınır. Görsel yollarını boş bırakarak metin odaklı banner kullanabilirsiniz.</p></div></section>
<section class="adm-card" id="landing-body"><div class="adm-card-header"><h2>İçerik mimarisi — H2 / H3</h2></div><div class="adm-card-body"><p class="landing-editor-help">En fazla 12 bölüm. Boş bölümler yayınlanmaz. HTML eklemeyin; H1 şablondan üretilir. Başlık ve metin birlikte doldurulmalıdır. İç bağlantı biçimi: [anlamlı metin](/kategori/ornek).</p>
<?php for($i=0;$i<12;$i++): $block=$d['sections'][$i]??[]; ?><details class="landing-editor-details" data-content-row><summary>Bölüm <?= $i+1 ?><?= !empty($block['title'])?' — '.e($block['title']):'' ?></summary><div class="landing-editor-fields"><div class="landing-editor-row-actions"><button type="button" class="btn btn-sm btn-outline" data-move-row="up">↑ Yukarı</button><button type="button" class="btn btn-sm btn-outline" data-move-row="down">↓ Aşağı</button></div>
<?php foreach(['title'=>['H2 başlık',220,1],'body'=>['Bilgi metni',6000,6],'subheading'=>['H3 ara başlık (opsiyonel)',220,1],'detail'=>['Ara başlık açıklaması',3000,4],'image'=>['Bölüm görseli: Medya Kütüphanesi’nden yerel yol',500,1],'alt'=>['Görsel alt metni',220,1]]as $key=>[$label,$max,$rows])$editorField($label,'sections['.$i.']['.$key.']',$block[$key]??'',$max,$rows); ?>
</div></details><?php endfor; ?>
<?php $contentHome=false;$contentData=$d;$contentPrefix='';$contentId='landing';require __DIR__.'/content-fields.php'; ?></div></section>
<section class="adm-card" id="landing-selection"><div class="adm-card-header"><h2>Hizmetler ve referanslar</h2></div><div class="adm-card-body"><p class="landing-editor-help">En fazla 24 hizmet ve 24 aktif referans. Seçimler mevcut verilerden okunur; bu kayıtların fiyatı veya içeriği değişmez. Referanslar genel portföy örneğidir, yerel müşteri iddiası eklenmez.</p><div class="landing-editor-grid"><div><h3>Mevcut hizmetler</h3><div class="landing-editor-options"><?php foreach($landingCategories as $category): ?><label><input type="checkbox" name="service_ids[]" value="<?= (int)$category['id'] ?>" <?= in_array((int)$category['id'],$d['service_ids'],true)?'checked':'' ?>><?= e($category['name']) ?></label><?php endforeach; ?></div></div><div><h3>Aktif referanslar</h3><div class="landing-editor-options"><?php foreach($landingReferences as $reference): ?><label><input type="checkbox" name="reference_ids[]" value="<?= e((string)$reference['id']) ?>" <?= in_array((string)$reference['id'],$d['reference_ids'],true)?'checked':'' ?>><?= e($reference['title']) ?></label><?php endforeach; ?><?php if(!$landingReferences): ?><p>Referanslarımız bölümünden aktif çalışma ekleyebilirsiniz.</p><?php endif; ?></div></div></div></div></section>
<section class="adm-card"><div class="adm-card-header"><h2>Bölüm ve iletişim başlıkları</h2></div><div class="adm-card-body"><?php foreach(['services_title'=>'Hizmetler başlığı','references_title'=>'Referanslar başlığı','faq_title'=>'SSS başlığı','cta_title'=>'Alt iletişim başlığı','cta_summary'=>'Alt iletişim açıklaması']as $key=>$label)$editorField($label,$key,$d[$key],$key==='cta_summary'?500:180); ?></div></section>
<section class="adm-card" id="landing-seo"><div class="adm-card-body"><?php $seoForHome=false;$seoPrefix='seo';$seoData=$d['seo'];require __DIR__.'/seo-fields.php'; ?><details class="landing-editor-details" open><summary>İçerik hazırlık notları</summary><ul><?php foreach($landingNotes as $note): ?><li><?= e($note) ?></li><?php endforeach; ?><li>Sitemap yalnızca yayında, indekslemeye açık ve kendi canonical adresini kullanan sayfaları içerir.</li><li>Aynı içeriği yalnızca şehir adını değiştirerek yayınlamak engellenir.</li></ul></details></div></section>
<div class="landing-editor-save"><span>Otomatik kayıt yoktur. Çıkmadan önce değişikliklerinizi kaydedin.</span><button class="btn btn-primary" type="submit">Sayfayı kaydet</button></div></form></div>
