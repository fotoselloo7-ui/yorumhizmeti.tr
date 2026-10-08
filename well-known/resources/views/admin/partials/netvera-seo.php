<?php
$nvseo=$nvSeoData??[];
?>
<div class="adm-card">
 <div class="adm-card-header"><h3><?= icon('search',18) ?> Netvera Gelişmiş SEO / GEO</h3></div>
 <div class="adm-card-body">
  <p class="form-hint">Mevcut URL, kategori ve ödeme ayarlarını değiştirmez. Alanlarda sadece doğrulanmış bilgiler kullanın.</p>
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
  <div class="form-group"><label>sameAs — Resmî Profiller (Her Satıra Bir URL)</label>
   <textarea name="nvseo_same_as_urls" rows="2" class="form-control"><?= e($nvseo['same_as_urls']??'') ?></textarea></div>
 </div>
</div>