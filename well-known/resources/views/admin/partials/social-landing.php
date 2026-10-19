<?php
use App\Services\SocialStorefrontService as SocialEditor;
$socialEditorType=$socialEditorScope??(array_key_exists('package',get_defined_vars())?'package':'category');
$socialEditorEntity=$socialEditorType==='package'?($package??[]):($category??[]);
if($socialEditorType==='package'&&!empty($socialEditorEntity['category_id'])){
    $socialEditorCategory=\App\Services\SocialPackageService::category((int)$socialEditorEntity['category_id']);
    $socialEditorParent=!empty($socialEditorCategory['parent_id'])?\App\Services\SocialPackageService::category((int)$socialEditorCategory['parent_id']):[];
    $socialEditorEntity+=['category_name'=>$socialEditorCategory['name']??'','category_slug'=>$socialEditorCategory['slug']??'','parent_category_name'=>$socialEditorParent['name']??'','parent_category_slug'=>$socialEditorParent['slug']??'','_sale'=>\App\Services\SocialPackageService::info($socialEditorEntity)];
}
$socialEditorId=(int)($socialEditorEntity['id']??0);
$socialEditorStored=$socialEditorId?SocialEditor::stored($socialEditorType,$socialEditorId):[];
$socialEditorDefault=SocialEditor::defaults($socialEditorEntity,$socialEditorType);
$socialEditorSections=$socialEditorStored['sections']??$socialEditorDefault['sections'];
$socialEditorFaqs=$socialEditorStored['faqs']??$socialEditorDefault['faqs'];
?>
<section class="adm-card">
 <div class="adm-card-header"><h3><?= icon('layers',18) ?> Sosyal medya sayfa tasarımı ve içerikleri</h3></div>
 <div class="adm-card-body">
  <input type="hidden" name="social_content_present" value="1">
  <p>Yalnızca sosyal medya kategorileri ve paketlerinde uygulanır. Sayfanın tek H1 başlığı banner'da, aşağıdaki bölüm başlıkları H2, ara başlıkları H3 olarak üretilir. HTML veya H1 etiketi yazmanız gerekmez. URL, paket miktarı ve fiyat değişmez.</p>
  <div class="form-group"><label for="social-page-title">Sayfa H1 başlığı</label><input class="form-control" id="social-page-title" name="social_page_title" maxlength="180" value="<?= e($socialEditorStored['title']??'') ?>" placeholder="<?= e($socialEditorDefault['title']) ?>"><small><?= $socialEditorType==='directory'?'Ana dizinin meta başlığı bu başlıktan, meta açıklaması banner açıklamasından hazırlanır.':'Boş bırakılırsa kategori/paket adı kullanılır. Meta title mevcut SEO alanından yönetilir.' ?></small></div>
  <div class="form-group"><label for="social-page-summary">Banner açıklaması</label><textarea class="form-control" id="social-page-summary" name="social_page_summary" maxlength="600" rows="3" placeholder="<?= e($socialEditorDefault['summary']) ?>"><?= e($socialEditorStored['summary']??'') ?></textarea></div>
  <?php foreach(['image'=>'Masaüstü banner görseli','mobile_image'=>'Mobil banner görseli (isteğe bağlı)']as $field=>$label): ?>
  <div class="form-group"><label for="social-page-<?= $field ?>"><?= $label ?></label><input id="social-page-<?= $field ?>" class="form-control" type="file" name="social_upload_<?= $field ?>" accept="image/jpeg,image/png,image/webp,image/gif"><input class="form-control" type="text" name="social_page_<?= $field ?>" value="<?= e($socialEditorStored[$field]??'') ?>" aria-label="<?= $label ?> mevcut dosya yolu" placeholder="Otomatik görsel için boş bırakın"><small>JPG/PNG/WebP/GIF, en fazla 5 MB. 3:2 oran önerilir. Mevcut yolu temizlemek otomatik görsele döndürür; dosyayı silmez.</small></div>
  <?php endforeach; ?>
  <div class="form-group"><label for="social-page-alt">Banner alt metni</label><input id="social-page-alt" class="form-control" name="social_page_image_alt" maxlength="220" value="<?= e($socialEditorStored['image_alt']??'') ?>" placeholder="<?= e($socialEditorDefault['image_alt']) ?>"></div>
  <div class="form-group"><label for="social-content-mode">Alt içerik ve SSS yayın modu</label><select id="social-content-mode" class="form-control" name="social_content_mode"><?php foreach(['default'=>'Hizmete göre hazır içerik kullan','custom'=>'Aşağıdaki özel içerikleri yayınla','hidden'=>'Alt içerik ve SSS bölümlerini gizle']as $value=>$label): ?><option value="<?= $value ?>" <?= ($socialEditorStored['mode']??'default')===$value?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select><small>Metinlerinizi yayınlamak için “özel içerikleri yayınla” seçin. Boş bölümler gösterilmez. Anahtar kelime tekrarına veya doğrulanmamış garanti/rakam kullanımına yer vermeyin.</small></div>
  <h4>Sayfa içerik bölümleri</h4><p>En fazla 12 bölüm. Bir bölümü kaldırmak için o bölümün bütün alanlarını temizleyin. Düz metin ve satır sonları desteklenir.</p>
  <?php for($s=0;$s<12;$s++): $block=$socialEditorSections[$s]??[]; ?>
  <details><summary><?= sprintf('%02d',$s+1) ?> · <?= e($block['title']??'Yeni içerik bölümü') ?></summary>
   <div class="form-group"><label for="social-section-title-<?= $s ?>">H2 başlık</label><input id="social-section-title-<?= $s ?>" name="social_sections[<?= $s ?>][title]" maxlength="220" class="form-control" value="<?= e($block['title']??'') ?>"></div>
   <div class="form-group"><label for="social-section-body-<?= $s ?>">Açıklama / doğrudan yanıt</label><textarea id="social-section-body-<?= $s ?>" name="social_sections[<?= $s ?>][body]" maxlength="6000" rows="5" class="form-control"><?= e($block['body']??'') ?></textarea></div>
   <div class="form-group"><label for="social-section-subheading-<?= $s ?>">H3 ara başlık (isteğe bağlı)</label><input id="social-section-subheading-<?= $s ?>" name="social_sections[<?= $s ?>][subheading]" maxlength="220" class="form-control" value="<?= e($block['subheading']??'') ?>"></div>
   <div class="form-group"><label for="social-section-detail-<?= $s ?>">Ara başlığın açıklaması</label><textarea id="social-section-detail-<?= $s ?>" name="social_sections[<?= $s ?>][detail]" maxlength="3000" rows="3" class="form-control"><?= e($block['detail']??'') ?></textarea></div>
   <div class="form-group"><label for="social-section-list-<?= $s ?>">Liste maddeleri (her satıra bir madde)</label><textarea id="social-section-list-<?= $s ?>" name="social_sections[<?= $s ?>][list]" maxlength="2000" rows="4" class="form-control"><?= e($block['list']??'') ?></textarea></div>
   <div class="form-group"><label for="social-section-upload-<?= $s ?>">Bölüm görseli</label><input id="social-section-upload-<?= $s ?>" type="file" name="social_section_upload[<?= $s ?>]" accept="image/jpeg,image/png,image/webp,image/gif" class="form-control"><input type="text" name="social_sections[<?= $s ?>][image]" class="form-control" value="<?= e($block['image']??'') ?>" aria-label="Bölüm <?= $s+1 ?> mevcut görsel yolu"></div>
   <div class="form-group"><label for="social-section-alt-<?= $s ?>">Görsel alt metni</label><input id="social-section-alt-<?= $s ?>" name="social_sections[<?= $s ?>][alt]" maxlength="220" class="form-control" value="<?= e($block['alt']??'') ?>"></div>
  </details>
  <?php endfor; ?>
  <h4>Sıkça sorulan sorular</h4><p>Sorular H1/H2 yerine açılabilir soru başlığı olarak gösterilir; yanıtlar sunucu HTML'inde bulunur. Zengin sonuç veya sıralama garantisi verilmez.</p>
  <?php for($s=0;$s<12;$s++): $faq=$socialEditorFaqs[$s]??[]; ?>
  <details><summary>Soru <?= $s+1 ?> · <?= e($faq['question']??'Yeni soru') ?></summary><div class="form-group"><label for="social-faq-question-<?= $s ?>">Soru</label><input id="social-faq-question-<?= $s ?>" class="form-control" name="social_faqs[<?= $s ?>][question]" maxlength="240" value="<?= e($faq['question']??'') ?>"></div><div class="form-group"><label for="social-faq-answer-<?= $s ?>">Yanıt</label><textarea id="social-faq-answer-<?= $s ?>" class="form-control" name="social_faqs[<?= $s ?>][answer]" maxlength="1800" rows="4"><?= e($faq['answer']??'') ?></textarea></div></details>
  <?php endfor; ?>
 </div>
</section>
