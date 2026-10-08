<?php $edit=!empty($product);$d=$data??[]; ?>
<div class="adm-page-top"><h2>Netvera Yazılım Editörü</h2><a class="btn btn-outline" href="/admin/netvera-yazilimlar">Geri</a></div>
<form method="post" enctype="multipart/form-data" action="/admin/netvera-yazilimlar/kaydet">
<?= csrfField() ?><input type="hidden" name="id" value="<?= (int)($product['legacy_id']??0) ?>">
<div class="adm-card"><div class="adm-card-body">
<label>Ürün Adı</label><input class="form-control" required name="name" value="<?= e($product['name']??'') ?>">
<label>Slug</label><input class="form-control" name="slug" value="<?= e($product['slug']??'') ?>">
<?php if($edit): ?><label><input type="checkbox" name="allow_slug_change" value="1"> Eski URL'yi değiştirmeye izin ver</label><?php endif; ?>
<label>Yazılım Kategorisi</label><select class="form-control" name="category_legacy_id">
<?php foreach($categories as $c): ?><option value="<?= (int)$c['legacy_id'] ?>" <?= (int)($product['category_legacy_id']??0)===(int)$c['legacy_id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<label>Kısa Açıklama</label><textarea class="form-control" name="short_desc"><?= e($product['short_desc']??'') ?></textarea>
<label>Detay Açıklama</label><textarea class="form-control" name="description" rows="6"><?= e($product['description']??'') ?></textarea>
<?php foreach(['price','old_price','badge','sort_order','meta_title','meta_description','focus_keyword'] as $k): ?>
<label><?= e($k) ?></label><input class="form-control" name="<?= $k ?>" value="<?= e($product[$k]??'') ?>">
<?php endforeach; ?>
<label>Kapak</label><input class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp">
<?php foreach(['is_active','is_featured','is_popular','is_new','demo_is_active','demo_is_public'] as $k): ?>
<label><input type="checkbox" name="<?= $k ?>" value="1" <?= !empty($k==='is_active'?($product['active']??1):($d[$k]??0))?'checked':'' ?>> <?= e($k) ?></label>
<?php endforeach; ?>
<div class="adm-card"><div class="adm-card-header"><h3>Demo ve Sürüm Bilgileri</h3></div><div class="adm-card-body">
<?php foreach(['demo_url'=>'Demo URL','demo_video_url'=>'Demo Video','current_version'=>'Sürüm','last_updated_on'=>'Güncelleme Tarihi','install_type'=>'Kurulum Tipi','install_info'=>'Kurulum Bilgileri'] as $key=>$label): ?>
<div class="form-group"><label><?= e($label) ?></label><input class="form-control" name="<?= $key ?>" value="<?= e($d[$key]??'') ?>"></div>
<?php endforeach; ?></div></div>
<div class="adm-card"><div class="adm-card-header"><h3>Lisans, Destek & SEO</h3></div><div class="adm-card-body">
<?php foreach(['support_duration_type'=>'Destek Tipi','support_duration_months'=>'Destek Süresi','update_duration_type'=>'Güncelleme Tipi','update_duration_months'=>'Güncelleme Süresi','buy_url'=>'Satış URL','secondary_keywords'=>'Yardımcı Kelimeler','tags'=>'Etiketler','og_title'=>'OG Başlık','og_description'=>'OG Açıklama','og_image'=>'OG Görseli'] as $key=>$label): ?>
<div class="form-group"><label><?= e($label) ?></label><input class="form-control" name="<?= $key ?>" value="<?= e($d[$key]??'') ?>"></div><?php endforeach; ?>
</div></div>
<button class="btn btn-primary" type="submit">Kaydet</button>
</div></div></form>