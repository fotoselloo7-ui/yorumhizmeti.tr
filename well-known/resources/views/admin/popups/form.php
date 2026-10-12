<?php
$isEdit=!empty($popup['id']);
$coupons=$popup['coupon_items']??[];
if(!$coupons)$coupons=[['title'=>'','code'=>'','discount'=>'','description'=>'']];
?>
<div class="adm-page-top nv97-popup-top">
  <div>
    <span class="nv97-popup-kicker"><?= icon('image',15) ?> Kampanya Merkezi</span>
    <h2><?= $isEdit?'Popup Düzenle':'Yeni Popup Oluştur' ?></h2>
    <p>Kuponlar, görsel, butonlar, açılış süresi, tekrar gösterim ve sayfa hedeflemesini tek ekrandan yönetin.</p>
  </div>
  <a href="/admin/popuplar" class="btn btn-outline"><?= icon('arrow-left',14) ?> Listeye Dön</a>
</div>

<form method="POST" enctype="multipart/form-data" action="<?= $isEdit?'/admin/popuplar/'.(int)$popup['id'].'/guncelle':'/admin/popuplar/ekle' ?>" class="nv97-popup-form">
<?= csrf_field() ?>
<div class="nv97-admin-two">
  <div class="nv97-admin-main">

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('edit',16) ?> Temel İçerik</h3></div>
      <div class="adm-card-body nv97-form-grid">
        <div class="form-group nv97-span-2"><label>Yönetim Adı *</label><input class="form-control" name="internal_name" maxlength="140" required value="<?= e($popup['internal_name']??'') ?>" placeholder="Örn. Ekim kampanyası / Tüm kuponlar"></div>
        <div class="form-group"><label>Popup Türü</label><select class="form-control" name="type" data-popup-type>
          <option value="campaign" <?= ($popup['type']??'campaign')==='campaign'?'selected':'' ?>>Duyuru + Buton</option>
          <option value="image" <?= ($popup['type']??'')==='image'?'selected':'' ?>>Sadece Görsel</option>
          <option value="coupons" <?= ($popup['type']??'')==='coupons'?'selected':'' ?>>Kupon & İndirim Merkezi</option>
        </select></div>
        <div class="form-group"><label>Yerleşim</label><select class="form-control" name="orientation">
          <option value="horizontal" <?= ($popup['orientation']??'horizontal')==='horizontal'?'selected':'' ?>>Yatay</option>
          <option value="vertical" <?= ($popup['orientation']??'')==='vertical'?'selected':'' ?>>Dikey</option>
        </select></div>
        <div class="form-group"><label>Üst Etiket</label><input class="form-control" name="eyebrow" maxlength="120" value="<?= e($popup['eyebrow']??'') ?>" placeholder="Örn. Sadece bu hafta"></div>
        <div class="form-group"><label>Sıra / Öncelik</label><input class="form-control" type="number" min="0" max="9999" name="sort_order" value="<?= (int)($popup['sort_order']??100) ?>"></div>
        <div class="form-group nv97-span-2"><label>Başlık</label><input class="form-control" name="title" maxlength="220" value="<?= e($popup['title']??'') ?>" placeholder="Kampanyanızı güçlü bir başlıkla anlatın"></div>
        <div class="form-group nv97-span-2"><label>Açıklama</label><textarea class="form-control" name="description" rows="4" maxlength="1200"><?= e($popup['description']??'') ?></textarea></div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('link',16) ?> Butonlar</h3></div>
      <div class="adm-card-body">
        <div class="nv97-form-grid">
          <div class="form-group"><label>Ana Buton Metni</label><input class="form-control" name="primary_button_text" value="<?= e($popup['primary_button_text']??'') ?>" placeholder="Kampanyayı İncele"></div>
          <div class="form-group"><label>Ana Buton Linki</label><input class="form-control" name="primary_button_url" value="<?= e($popup['primary_button_url']??'') ?>" placeholder="/kategoriler"></div>
          <div class="form-group"><label>İkinci Buton Metni</label><input class="form-control" name="secondary_button_text" value="<?= e($popup['secondary_button_text']??'') ?>" placeholder="Tüm Hizmetler"></div>
          <div class="form-group"><label>İkinci Buton Linki</label><input class="form-control" name="secondary_button_url" value="<?= e($popup['secondary_button_url']??'') ?>" placeholder="/kategoriler"></div>
        </div>
      </div>
    </section>

    <section class="adm-card" data-coupon-section>
      <div class="adm-card-header nv97-card-head"><h3><?= icon('grid',16) ?> Kuponlar & İndirimler</h3><button type="button" class="btn btn-outline btn-sm" data-coupon-add><?= icon('plus',13) ?> Kupon Ekle</button></div>
      <div class="adm-card-body">
        <p class="nv97-help">Tek popup içinde en fazla 20 kupon/indirim yayınlayabilirsiniz. Kod alanı boşsa sadece kampanya duyurusu olarak görünür.</p>
        <div data-coupon-list>
        <?php foreach($coupons as $item): ?>
          <div class="nv97-coupon-row">
            <input class="form-control" name="coupon_title[]" value="<?= e($item['title']??'') ?>" placeholder="Başlık / Kampanya">
            <input class="form-control" name="coupon_code[]" value="<?= e($item['code']??'') ?>" placeholder="Kupon kodu">
            <input class="form-control" name="coupon_discount[]" value="<?= e($item['discount']??'') ?>" placeholder="%20 / 500 TL">
            <input class="form-control" name="coupon_description[]" value="<?= e($item['description']??'') ?>" placeholder="Kısa açıklama">
            <button type="button" class="btn btn-outline btn-sm nv97-danger" data-coupon-remove><?= icon('trash',13) ?></button>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('palette',16) ?> Görsel Dil</h3></div>
      <div class="adm-card-body nv97-form-grid">
        <div class="form-group"><label>Gradient Başlangıç</label><input class="form-control" name="background_start" value="<?= e($popup['background_start']??'#151a45') ?>"></div>
        <div class="form-group"><label>Gradient Bitiş</label><input class="form-control" name="background_end" value="<?= e($popup['background_end']??'#6b4de8') ?>"></div>
        <div class="form-group"><label>Metin Rengi</label><input class="form-control" name="text_color" value="<?= e($popup['text_color']??'#ffffff') ?>"></div>
        <div class="form-group"><label>Accent Rengi</label><input class="form-control" name="accent_color" value="<?= e($popup['accent_color']??'#d936a1') ?>"></div>
      </div>
    </section>
  </div>

  <aside class="nv97-admin-side">
    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('image',16) ?> Popup Görseli</h3></div>
      <div class="adm-card-body">
        <div class="form-group"><label>Görsel Yükle</label><div class="nv97-size-note">Yatay için <strong>1200 × 900 px</strong>, dikey için <strong>900 × 1200 px</strong>. WebP önerilir.</div><input class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif">
        <?php if(!empty($popup['image'])): ?><img class="nv97-current-image" src="<?= e(\App\Services\MarketingPopupService::imageUrl($popup['image'])) ?>" alt="Mevcut popup görseli"><label class="nv97-check"><input type="checkbox" name="remove_image" value="1"> Görseli kaldır</label><?php endif; ?></div>
        <div class="form-group"><label>Görsel Alt Metni</label><input class="form-control" name="image_alt" value="<?= e($popup['image_alt']??'') ?>"></div>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('clock',16) ?> Açılış & Tekrar</h3></div>
      <div class="adm-card-body">
        <div class="form-group"><label>Açılış Gecikmesi (ms)</label><input class="form-control" type="number" min="0" max="600000" step="100" name="open_delay_ms" value="<?= (int)($popup['open_delay_ms']??1800) ?>"><small class="nv97-help">1800 = 1,8 saniye sonra açılır.</small></div>
        <div class="form-group"><label>Kapatınca Tekrar Göster (saat)</label><input class="form-control" type="number" min="0" max="8760" name="reopen_hours" value="<?= (int)($popup['reopen_hours']??24) ?>"><small class="nv97-help">0 = sonraki sayfa ziyaretinde tekrar gösterebilir.</small></div>
        <div class="form-group"><label>Otomatik Kapanma (sn)</label><input class="form-control" type="number" min="0" max="3600" name="auto_close_seconds" value="<?= (int)($popup['auto_close_seconds']??0) ?>"><small class="nv97-help">0 = otomatik kapanmaz.</small></div>
        <label class="nv97-switch"><input type="checkbox" name="closable" value="1" <?= !isset($popup['closable'])||!empty($popup['closable'])?'checked':'' ?>><span>Kapatma butonu göster</span></label>
        <label class="nv97-switch"><input type="checkbox" name="close_on_backdrop" value="1" <?= !isset($popup['close_on_backdrop'])||!empty($popup['close_on_backdrop'])?'checked':'' ?>><span>Dış alana tıklayınca kapat</span></label>
        <label class="nv97-switch"><input type="checkbox" name="once_per_session" value="1" <?= !empty($popup['once_per_session'])?'checked':'' ?>><span>Oturumda yalnızca bir kez göster</span></label>
      </div>
    </section>

    <section class="adm-card">
      <div class="adm-card-header"><h3><?= icon('settings',16) ?> Hedefleme & Yayın</h3></div>
      <div class="adm-card-body">
        <div class="form-group"><label>Sayfa Hedefi</label><select class="form-control" name="page_mode">
          <option value="all" <?= ($popup['page_mode']??'all')==='all'?'selected':'' ?>>Tüm site</option>
          <option value="home" <?= ($popup['page_mode']??'')==='home'?'selected':'' ?>>Sadece ana sayfa</option>
          <option value="include" <?= ($popup['page_mode']??'')==='include'?'selected':'' ?>>Sadece belirtilen yollar</option>
          <option value="exclude" <?= ($popup['page_mode']??'')==='exclude'?'selected':'' ?>>Belirtilen yollar hariç</option>
        </select></div>
        <div class="form-group"><label>URL Kuralları</label><textarea class="form-control" name="path_rules" rows="4" placeholder="/kategoriler&#10;/hazir-scriptler/*"><?= e($popup['path_rules']??'') ?></textarea><small class="nv97-help">Her satıra bir yol. * joker karakteri desteklenir.</small></div>
        <div class="form-group"><label>Durum</label><select class="form-control" name="status"><option value="active" <?= ($popup['status']??'inactive')==='active'?'selected':'' ?>>Aktif</option><option value="inactive" <?= ($popup['status']??'inactive')==='inactive'?'selected':'' ?>>Pasif</option></select></div>
        <div class="form-group"><label>Başlangıç</label><input class="form-control" type="datetime-local" name="starts_at" value="<?= !empty($popup['starts_at'])?e(date('Y-m-d\TH:i',strtotime($popup['starts_at']))):'' ?>"></div>
        <div class="form-group"><label>Bitiş</label><input class="form-control" type="datetime-local" name="ends_at" value="<?= !empty($popup['ends_at'])?e(date('Y-m-d\TH:i',strtotime($popup['ends_at']))):'' ?>"></div>
      </div>
    </section>

    <button class="adm-action-btn adm-btn-save nv97-save" type="submit"><?= icon('save',15) ?> <?= $isEdit?'Değişiklikleri Kaydet':'Popup Oluştur' ?></button>
  </aside>
</div>
</form>

<template id="nv97-coupon-template">
  <div class="nv97-coupon-row">
    <input class="form-control" name="coupon_title[]" placeholder="Başlık / Kampanya">
    <input class="form-control" name="coupon_code[]" placeholder="Kupon kodu">
    <input class="form-control" name="coupon_discount[]" placeholder="%20 / 500 TL">
    <input class="form-control" name="coupon_description[]" placeholder="Kısa açıklama">
    <button type="button" class="btn btn-outline btn-sm nv97-danger" data-coupon-remove><?= icon('trash',13) ?></button>
  </div>
</template>
<script>
document.addEventListener('click',function(e){
  const add=e.target.closest('[data-coupon-add]');
  if(add){
    const list=document.querySelector('[data-coupon-list]');
    if(list&&list.children.length<20) list.append(document.getElementById('nv97-coupon-template').content.cloneNode(true));
  }
  const remove=e.target.closest('[data-coupon-remove]');
  if(remove){
    const list=document.querySelector('[data-coupon-list]');
    if(list&&list.children.length>1) remove.closest('.nv97-coupon-row')?.remove();
    else remove.closest('.nv97-coupon-row')?.querySelectorAll('input').forEach(i=>i.value='');
  }
});
</script>