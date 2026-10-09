<?php
$nvseo=$nvSeoData??[];
?>
<div class="adm-card">
 <div class="adm-card-header"><h3><?= icon('search',18) ?> Netvera Gelişmiş SEO / GEO</h3></div>
 <div class="adm-card-body">
  
  <div class="form-group"><label>Yardımcı Anahtar Kelimeler</label>
   <textarea name="nvseo_secondary_keywords" rows="2" maxlength="1000" class="form-control" placeholder="Virgülle ayırın"><?= e($nvseo['secondary_keywords']??'') ?></textarea></div>
  <div class="form-group"><label>Robots</label>
   <select class="form-control" name="nvseo_robots">
    <?php foreach([''=>'Mevcut robots davranışını koru','index,follow'=>'index, follow','index,follow,max-image-preview:large'=>'index, follow, max-image-preview:large','noindex,follow'=>'noindex, follow','noindex,nofollow'=>'noindex, nofollow'] as $v=>$label): ?>
    <option value="<?= e($v) ?>" <?= ($nvseo['robots']??'')===$v?'selected':'' ?>><?= e($label) ?></option>
    <?php endforeach; ?>
   </select></div>
  <div class="form-group"><label>GEO: Doğrulanmış İçerik Özeti</label>
   <textarea name="nvseo_geo_summary" rows="3" maxlength="500" class="form-control"><?= e($nvseo['geo_summary']??'') ?></textarea></div>
  <div class="form-group"><label>Entity / İlişkili Konular</label>
   <input name="nvseo_entity_topics" maxlength="500" class="form-control" value="<?= e($nvseo['entity_topics']??'') ?>" placeholder="Virgülle ayırın"></div>
  <div class="form-group"><label>Hizmet Bölgesi</label>
   <input name="nvseo_service_area" maxlength="300" class="form-control" value="<?= e($nvseo['service_area']??'') ?>"></div>
  <div class="form-row">
   <div class="form-group"><label>Yazar / Kurum</label><input class="form-control" name="nvseo_author_name" maxlength="120" value="<?= e($nvseo['author_name']??'') ?>"></div>
   <div class="form-group"><label>Yazar Türü</label>
    <select name="nvseo_author_type" class="form-control">
     <option value="Organization" <?= ($nvseo['author_type']??'')==='Person'?'':'selected' ?>>Organization</option>
     <option value="Person" <?= ($nvseo['author_type']??'')==='Person'?'selected':'' ?>>Person</option>
    </select></div>
  </div>
  <div class="form-group"><label>Yazar URL</label>
   <input name="nvseo_author_url" type="url" class="form-control" value="<?= e($nvseo['author_url']??'') ?>"></div>
  <?php if(!empty($nvSeoIsBlog)||!empty($nvSeoIsCategory)): ?>
  <div class="nv44-editorial-title"><?= icon('sparkles',15) ?> GEO / AIO İçerik Yapısı</div>
  
  <div class="form-group">
    <label for="nvseo_content_intent">Kullanıcı Arama Niyeti</label>
    <select class="form-control" id="nvseo_content_intent" name="nvseo_content_intent">
      <?php foreach([''=>'Seçiniz','bilgi'=>'Bilgi edinme','karsilastirma'=>'Karşılaştırma / değerlendirme','satin-alma'=>'Satın alma öncesi araştırma','yerel'=>'Yerel hizmet araştırması','rehber'=>'Nasıl yapılır / adım adım rehber'] as $value=>$label): ?>
      <option value="<?= e($value) ?>" <?= ($nvseo['content_intent']??'')===$value?'selected':'' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label for="nvseo_main_question">Kullanıcının Ana Sorusu</label>
    <textarea class="form-control" id="nvseo_main_question" name="nvseo_main_question" rows="2" maxlength="300" placeholder="Örneğin: Haber sitesi yazılımı hangi özelliklere sahip olmalı?"><?= e($nvseo['main_question']??'') ?></textarea>
  </div>
  <div class="form-group">
    <label for="nvseo_direct_answer">Doğrudan Cevap / AIO Kısa Özet</label>
    <textarea class="form-control" id="nvseo_direct_answer" name="nvseo_direct_answer" rows="4" maxlength="800" placeholder="Soruyu doğal biçimde cevaplayan, doğrulanabilir 2–4 cümle."><?= e($nvseo['direct_answer']??'') ?></textarea>
  </div>
  <div class="form-group">
    <label for="nvseo_sources">Kaynaklar / Doğrulama Bağlantıları</label>
    <textarea class="form-control" id="nvseo_sources" name="nvseo_sources" rows="3" maxlength="2500" placeholder="Her satıra kaynak URL'si"><?= e($nvseo['sources']??'') ?></textarea>
  </div>
  <div class="form-row">
    <div class="form-group">
      <label for="nvseo_reviewer_name">Editör / Son Kontrol Eden</label>
      <input class="form-control" id="nvseo_reviewer_name" name="nvseo_reviewer_name" maxlength="120" value="<?= e($nvseo['reviewer_name']??'') ?>" placeholder="Gerçek editör adı">
    </div>
    <div class="form-group">
      <label for="nvseo_last_reviewed">Son İçerik Kontrolü</label>
      <input class="form-control" id="nvseo_last_reviewed" type="date" name="nvseo_last_reviewed" value="<?= e($nvseo['last_reviewed']??'') ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="nvseo_image_title">Görsel Title / Başlık</label>
    <input class="form-control" id="nvseo_image_title" name="nvseo_image_title" maxlength="240" value="<?= e($nvseo['image_title']??'') ?>" placeholder="Kapak görselini açıklayan kısa başlık">
  </div>
  <?php endif; ?>
  <div class="form-group"><label>sameAs — Resmî Profiller (Her Satıra Bir URL)</label>
   <textarea name="nvseo_same_as_urls" rows="2" class="form-control"><?= e($nvseo['same_as_urls']??'') ?></textarea></div>
 </div>
</div>