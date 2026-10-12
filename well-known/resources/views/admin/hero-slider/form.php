<?php
$isEdit=!empty($slide['id']);
$quick=$slide['quick_links']??[];
if(!$quick) $quick=[['label'=>'','url'=>'','icon'=>'link','color'=>'#6d5dfc']];
$iconOptions=$icons??[];
?>
<div class="adm-page-top nv94-slider-top">
  <div>
    <span class="nv94-kicker"><?= icon('image',15) ?> Ana Sayfa Vitrini</span>
    <h2><?= $isEdit?'Slider Düzenle':'Yeni Slider Ekle' ?></h2>
    <p>Görsel, metin, CTA butonları, kategori, ikonlar, renkler ve otomatik geçiş süresini tek ekrandan yönetin.</p>
  </div>
  <a href="/admin/sliderlar" class="btn btn-outline"><?= icon('arrow-left',14) ?> Listeye Dön</a>
</div>

<form method="POST" enctype="multipart/form-data" action="<?= $isEdit?'/admin/sliderlar/'.(int)$slide['id'].'/guncelle':'/admin/sliderlar/ekle' ?>" class="nv94-slider-form">
<?= csrf_field() ?>
<div class="nv94-admin-two">
  <div class="nv94-admin-main">
    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('edit',16) ?> İçerik</h3></div>
      <div class="adm-card-body nv94-form-grid">
        <div class="form-group"><label>Üst Etiket</label><input class="form-control" name="eyebrow" maxlength="120" value="<?= e($slide['eyebrow']??'') ?>" placeholder="Örn. Dijitalde tek merkez"></div>
        <div class="form-group"><label>Kategori Etiketi</label><input class="form-control" name="category_label" maxlength="120" value="<?= e($slide['category_label']??'') ?>" placeholder="Örn. Sosyal Medya Hizmetleri"></div>
        <div class="form-group nv94-span-2"><label>Ana Başlık *</label><input class="form-control" name="title" maxlength="220" required value="<?= e($slide['title']??'') ?>" placeholder="Güçlü, kısa ve satış odaklı başlık"></div>
        <div class="form-group nv94-span-2"><label>Açıklama</label><textarea class="form-control" name="description" rows="4" maxlength="700"><?= e($slide['description']??'') ?></textarea></div>
        <div class="form-group"><label>Kategori Linki</label><input class="form-control" name="category_url" value="<?= e($slide['category_url']??'') ?>" placeholder="/kategoriler/grup/social"></div>
        <div class="form-group"><label>Kategori İkonu</label><select class="form-control" name="category_icon"><?php foreach($iconOptions as $ico): ?><option value="<?= e($ico) ?>" <?= ($slide['category_icon']??'sparkles')===$ico?'selected':'' ?>><?= e($ico) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Kısa İstatistik / Alt Metin</label><input class="form-control" name="stat_text" maxlength="120" value="<?= e($slide['stat_text']??'') ?>" placeholder="Örn. 80+ Kategori"></div>
        <div class="form-group"><label>Slider Sırası</label><input class="form-control" type="number" min="0" max="9999" name="sort_order" value="<?= (int)($slide['sort_order']??100) ?>"></div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('link',16) ?> Butonlar</h3></div>
      <div class="adm-card-body">
        <div class="nv94-button-editor">
          <h4>Ana Buton</h4>
          <div class="nv94-form-grid">
            <div class="form-group"><label>Metin</label><input class="form-control" name="primary_button_text" value="<?= e($slide['primary_button_text']??'') ?>"></div>
            <div class="form-group"><label>Link</label><input class="form-control" name="primary_button_url" value="<?= e($slide['primary_button_url']??'') ?>" placeholder="/kategoriler"></div>
            <div class="form-group"><label>İkon</label><select class="form-control" name="primary_button_icon"><?php foreach($iconOptions as $ico): ?><option value="<?= e($ico) ?>" <?= ($slide['primary_button_icon']??'arrow-right')===$ico?'selected':'' ?>><?= e($ico) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Arka Plan</label><input class="form-control nv94-color-input" name="primary_button_bg" value="<?= e($slide['primary_button_bg']??'#ffffff') ?>" data-color-input></div>
            <div class="form-group"><label>Yazı Rengi</label><input class="form-control nv94-color-input" name="primary_button_text_color" value="<?= e($slide['primary_button_text_color']??'#14224a') ?>" data-color-input></div>
          </div>
        </div>
        <div class="nv94-button-editor">
          <h4>İkinci Buton</h4>
          <div class="nv94-form-grid">
            <div class="form-group"><label>Metin</label><input class="form-control" name="secondary_button_text" value="<?= e($slide['secondary_button_text']??'') ?>"></div>
            <div class="form-group"><label>Link</label><input class="form-control" name="secondary_button_url" value="<?= e($slide['secondary_button_url']??'') ?>"></div>
            <div class="form-group"><label>İkon</label><select class="form-control" name="secondary_button_icon"><?php foreach($iconOptions as $ico): ?><option value="<?= e($ico) ?>" <?= ($slide['secondary_button_icon']??'play-circle')===$ico?'selected':'' ?>><?= e($ico) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Arka Plan</label><input class="form-control nv94-color-input" name="secondary_button_bg" value="<?= e($slide['secondary_button_bg']??'rgba(255,255,255,.12)') ?>" data-color-input></div>
            <div class="form-group"><label>Yazı Rengi</label><input class="form-control nv94-color-input" name="secondary_button_text_color" value="<?= e($slide['secondary_button_text_color']??'#ffffff') ?>" data-color-input></div>
          </div>
        </div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header nv94-card-head-row"><h3><?= icon('grid',16) ?> Küçük Hızlı Linkler</h3><button type="button" class="btn btn-outline btn-sm" data-quick-add><?= icon('plus',13) ?> Link Ekle</button></div>
      <div class="adm-card-body">
        <p class="nv94-help">Bayigram örneğindeki yuvarlak servis ikonları gibi görünür. En fazla 8 öğe.</p>
        <div data-quick-list>
        <?php foreach($quick as $item): ?>
          <div class="nv94-quick-row">
            <input class="form-control" name="quick_label[]" value="<?= e($item['label']??'') ?>" placeholder="Etiket">
            <input class="form-control" name="quick_url[]" value="<?= e($item['url']??'') ?>" placeholder="/link">
            <select class="form-control" name="quick_icon[]"><?php foreach($iconOptions as $ico): ?><option value="<?= e($ico) ?>" <?= ($item['icon']??'link')===$ico?'selected':'' ?>><?= e($ico) ?></option><?php endforeach; ?></select>
            <input class="form-control nv94-color-input" name="quick_color[]" value="<?= e($item['color']??'#6d5dfc') ?>" data-color-input>
            <button type="button" class="btn btn-outline btn-sm nv94-danger" data-quick-remove><?= icon('trash',13) ?></button>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('palette',16) ?> Görsel Dil ve Renkler</h3></div>
      <div class="adm-card-body nv94-form-grid">
        <div class="form-group"><label>Gradient Başlangıç</label><input class="form-control nv94-color-input" name="background_start" value="<?= e($slide['background_start']??'#13255b') ?>" data-color-input></div>
        <div class="form-group"><label>Gradient Bitiş</label><input class="form-control nv94-color-input" name="background_end" value="<?= e($slide['background_end']??'#6d28d9') ?>" data-color-input></div>
        <div class="form-group"><label>Accent Rengi</label><input class="form-control nv94-color-input" name="accent_color" value="<?= e($slide['accent_color']??'#7c5cff') ?>" data-color-input></div>
        <div class="form-group"><label>Metin Rengi</label><input class="form-control nv94-color-input" name="text_color" value="<?= e($slide['text_color']??'#ffffff') ?>" data-color-input></div>
        <div class="form-group nv94-span-2"><label>Overlay</label><input class="form-control" name="overlay_color" value="<?= e($slide['overlay_color']??'rgba(7,16,48,.34)') ?>" placeholder="rgba(7,16,48,.34)"></div>
      </div>
    </section>
  </div>

  <aside class="nv94-admin-side">
    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('image',16) ?> Banner Görselleri</h3></div>
      <div class="adm-card-body">
        <div class="form-group"><label>Masaüstü Görsel</label><div class="nv94-size-note">Önerilen: <strong>1920 × 820 px</strong> (21:9). Minimum 1440 × 617 px. WebP tercih edin.</div><input class="form-control" type="file" name="desktop_image" accept="image/png,image/jpeg,image/webp,image/gif">
          <?php if(!empty($slide['desktop_image'])): ?><img class="nv94-current-image" src="<?= e(AppServicesHeroSliderService::imageUrl($slide['desktop_image'])) ?>" alt="Mevcut masaüstü banner"><label class="nv94-check"><input type="checkbox" name="remove_desktop_image" value="1"> Görseli kaldır</label><?php endif; ?>
        </div>
        <div class="form-group"><label>Mobil Görsel</label><div class="nv94-size-note">Önerilen: <strong>1080 × 1350 px</strong> (4:5). Ana konu merkez/sağ bölgede kalsın.</div><input class="form-control" type="file" name="mobile_image" accept="image/png,image/jpeg,image/webp,image/gif">
          <?php if(!empty($slide['mobile_image'])): ?><img class="nv94-current-image" src="<?= e(AppServicesHeroSliderService::imageUrl($slide['mobile_image'])) ?>" alt="Mevcut mobil banner"><label class="nv94-check"><input type="checkbox" name="remove_mobile_image" value="1"> Görseli kaldır</label><?php endif; ?>
        </div>
        <div class="form-group"><label>Görsel Alt Metni</label><input class="form-control" name="image_alt" value="<?= e($slide['image_alt']??'') ?>"></div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('settings',16) ?> Yayın Ayarları</h3></div>
      <div class="adm-card-body">
        <div class="form-group"><label>Durum</label><select class="form-control" name="status"><option value="active" <?= ($slide['status']??'active')==='active'?'selected':'' ?>>Aktif</option><option value="inactive" <?= ($slide['status']??'')==='inactive'?'selected':'' ?>>Pasif</option></select></div>
        <div class="form-group"><label>Otomatik Geçiş (ms)</label><input class="form-control" type="number" min="2500" max="30000" step="100" name="autoplay_delay" value="<?= (int)($slide['autoplay_delay']??6500) ?>"></div>
        <div class="form-group"><label>Başlangıç</label><input class="form-control" type="datetime-local" name="starts_at" value="<?= !empty($slide['starts_at'])?e(date('Y-m-d\TH:i',strtotime($slide['starts_at']))):'' ?>"></div>
        <div class="form-group"><label>Bitiş</label><input class="form-control" type="datetime-local" name="ends_at" value="<?= !empty($slide['ends_at'])?e(date('Y-m-d\TH:i',strtotime($slide['ends_at']))):'' ?>"></div>
      </div>
    </section>

    <button class="adm-action-btn adm-btn-save nv94-save" type="submit"><?= icon('save',15) ?> <?= $isEdit?'Değişiklikleri Kaydet':'Sliderı Oluştur' ?></button>
  </aside>
</div>
</form>

<template id="nv94-quick-template">
  <div class="nv94-quick-row">
    <input class="form-control" name="quick_label[]" placeholder="Etiket">
    <input class="form-control" name="quick_url[]" placeholder="/link">
    <select class="form-control" name="quick_icon[]"><?php foreach($iconOptions as $ico): ?><option value="<?= e($ico) ?>"><?= e($ico) ?></option><?php endforeach; ?></select>
    <input class="form-control nv94-color-input" name="quick_color[]" value="#6d5dfc" data-color-input>
    <button type="button" class="btn btn-outline btn-sm nv94-danger" data-quick-remove><?= icon('trash',13) ?></button>
  </div>
</template>
<script>
document.addEventListener('click',function(e){
  const add=e.target.closest('[data-quick-add]');
  if(add){
    const list=document.querySelector('[data-quick-list]');
    if(list.children.length>=8)return;
    list.append(document.getElementById('nv94-quick-template').content.cloneNode(true));
  }
  const remove=e.target.closest('[data-quick-remove]');
  if(remove) remove.closest('.nv94-quick-row')?.remove();
});
</script>
