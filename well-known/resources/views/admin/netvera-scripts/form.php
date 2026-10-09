<?php
$edit=!empty($product);
$d=$data??[];
$nvLiveUrl=$edit?'/hazir-scriptler/'.rawurlencode((string)$product['slug']):null;
$nvOriginalSlug=$edit?(string)$product['slug']:'';
?>
<div class="nvpa">
  <div class="adm-page-top nvpa-top">
    <div>
      <span class="nvpa-eyebrow">NETVERA · ÜRÜN YÖNETİMİ</span>
      <h2><?= $edit?'Yazılımı Düzenle':'Yeni Yazılım Ekle' ?></h2>
      <p>Hazır yazılım bilgileri, sürümler, modüller, demo, lisans ve SEO alanları tek panelde.</p>
    </div>
    <div class="nvpa-top-actions">
      <a class="btn btn-outline btn-sm" href="/admin/netvera-yazilimlar"><?= icon('arrow-left',15) ?> Yazılımlar</a>
      <a class="btn btn-outline btn-sm" href="/admin/netvera-kategoriler"><?= icon('folder',15) ?> Kategoriler</a>
      <?php if($nvLiveUrl): ?><a class="btn btn-outline btn-sm" href="<?= e($nvLiveUrl) ?>" target="_blank" rel="noopener noreferrer"><?= icon('external-link',15) ?> Önizle</a><?php endif; ?>
    </div>
  </div>

  <form id="nvpa-product-form" method="post" enctype="multipart/form-data" action="/admin/netvera-yazilimlar/kaydet">
    <?= csrfField() ?><input type="hidden" name="id" value="<?= (int)($product['legacy_id']??0) ?>">
    <div class="nvpa-layout">
      <div class="nvpa-main">
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('file-text',18) ?> Temel Ürün Bilgileri</h3><span>01 / İçerik</span></div>
          <div class="adm-card-body">
            <div class="nvpa-field"><label for="nvpa-name">Yazılım Adı <b>*</b></label><input id="nvpa-name" class="form-control" name="name" required maxlength="250" value="<?= e($product['name']??'') ?>" placeholder="Örn: NetVera Haber Sitesi Yazılımı Pro"></div>
            <div class="nvpa-two">
              <div class="nvpa-field"><label for="nvpa-slug">Ürün SEO Adresi <b>*</b></label>
                <input id="nvpa-slug" class="form-control" <?= $edit?'readonly':'' ?> name="slug" maxlength="250" value="<?= e($nvOriginalSlug) ?>" placeholder="haber-sitesi-scripti">
                <small><?= $edit?'Google tarafından indekslenen orijinal adres kilitlidir.':'Boş bırakılırsa ürün adından oluşturulur.' ?></small>
              </div>
              <div class="nvpa-field"><label for="nvpa-category">Yazılım Kategorisi <b>*</b></label>
                <select id="nvpa-category" class="form-control" name="category_legacy_id" required>
                  <?php foreach($categories as $c): ?>
                  <option value="<?= (int)$c['legacy_id'] ?>" <?= (int)($product['category_legacy_id']??0)===(int)$c['legacy_id']?'selected':'' ?>><?= e($c['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <small><a href="/admin/netvera-kategoriler">Yeni kategori ekle</a></small>
              </div>
            </div>
            <div class="nvpa-field"><label for="nvpa-summary">Kısa Ürün Açıklaması</label><textarea id="nvpa-summary" class="form-control" rows="3" name="short_desc" placeholder="Müşterinin ürün kartında gördüğü açıklama."><?= e($product['short_desc']??'') ?></textarea></div>
            <div class="nvpa-field"><label for="nvpa-description">Detaylı Ürün Açıklaması</label>
              <textarea id="nvpa-description" class="form-control nvpa-long" rows="12" name="description" placeholder="Yazılımın ne yaptığı, kullanım alanları, avantajları ve teknik detaylar..."><?= e($product['description']??'') ?></textarea>
              <small>HTML içerik girilebilir; herkese açık sayfada güvenli HTML temizleyicisi uygulanır.</small>
            </div>
          </div>
        </section>
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('monitor',18) ?> Demo, Kurulum ve Sürüm</h3><span>02 / Ürün Deneyimi</span></div>
          <div class="adm-card-body">
            <div class="nvpa-two">
              <?php foreach(['demo_url'=>'Canlı Demo URL','demo_admin_url'=>'Demo Yönetim Paneli URL','demo_user_url'=>'Demo Kullanıcı Sayfası URL','demo_video_url'=>'Tanıtım Videosu URL','current_version'=>'Yazılım Sürümü','last_updated_on'=>'Son Güncelleme Tarihi','install_type'=>'Kurulum Türü','install_info'=>'Kurulum Bilgisi'] as $key=>$label): ?>
                <div class="nvpa-field"><label for="nvpa-<?= e($key) ?>"><?= e($label) ?></label><input id="nvpa-<?= e($key) ?>" class="form-control" name="<?= e($key) ?>" value="<?= e($d[$key]??'') ?>"></div>
              <?php endforeach; ?>
            </div>
            <div class="nvpa-checks">
              <?php foreach(['demo_is_active'=>'Demo bağlantısı aktif','demo_is_public'=>'Demo herkese açık gösterilsin','demo_credentials_public'=>'Demo kullanıcı adı ve şifreleri ürün detayında yayınlansın'] as $key=>$label): ?>
                <label><input type="checkbox" name="<?= e($key) ?>" value="1" <?= !empty($d[$key])?'checked':'' ?>> <?= e($label) ?></label>
              <?php endforeach; ?>
            </div>
            <div class="nvpa-two">
              <?php foreach([
                'demo_username'=>'Kullanıcı Demo Kullanıcı Adı',
                'demo_password'=>'Kullanıcı Demo Şifresi',
                'demo_admin_username'=>'Admin Demo Kullanıcı Adı',
                'demo_admin_password'=>'Admin Demo Şifresi'
              ] as $key=>$label): ?>
              <div class="nvpa-field">
                <label for="nvpa-<?= e($key) ?>"><?= e($label) ?></label>
                <input id="nvpa-<?= e($key) ?>" class="form-control" name="<?= e($key) ?>" type="<?= str_contains($key,'password')?'password':'text' ?>"
                       value="<?= e($d[$key]??'') ?>" maxlength="190" autocomplete="off" spellcheck="false">
              </div>
              <?php endforeach; ?>
            </div>
            <div class="nvpa-field">
              <label for="nvpa-demo-note">Demo Kullanım Notu</label>
              <textarea id="nvpa-demo-note" class="form-control" name="demo_note" rows="3" maxlength="2000" placeholder="Demo hesabını test ederken nelere dikkat edilmeli?"><?= e($d['demo_note']??'') ?></textarea>
            </div>
            <div class="nvpa-field">
              <label for="nvpa-demo-accounts">Ek Demo Hesapları (JSON)</label>
              <textarea id="nvpa-demo-accounts" class="form-control" name="demo_accounts_json" rows="4" spellcheck="false"
               placeholder='[{"label":"Editör","username":"test-editör","password":"yalnizca-demo"}]'><?= e($d['demo_accounts_json']??'[]') ?></textarea>
              <small>Ek deneme rolleri için JSON listesi. Yalnızca özel olarak oluşturduğunuz demo hesaplarını kullanın.</small>
            </div>
            <p class="nvpa-note"><?= icon('shield-check',15) ?>
              Demo giriş bilgilerini herkese açık göstermek için son kutucuğu da işaretleyin.
              Gerçek yönetici, müşteri veya ödeme paneli şifrelerini bu alanlara yazmayın.
              Yayınlanan demo hesap bilgileri ziyaretçilere görünür.
            </p>
          </div>
        </section>
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('shield-check',18) ?> Lisans, Destek ve Güncellemeler</h3><span>03 / Teslimat</span></div>
          <div class="adm-card-body">
            <div class="nvpa-two">
              <?php foreach(['support_duration_type'=>'Destek Süresi Türü','support_duration_months'=>'Destek Süresi (ay)','update_duration_type'=>'Güncelleme Süresi Türü','update_duration_months'=>'Güncelleme Süresi (ay)','buy_url'=>'Mevcut Satış Linki','tags'=>'Ürün Etiketleri'] as $key=>$label): ?>
              <div class="nvpa-field"><label for="nvpa-<?= e($key) ?>"><?= e($label) ?></label><input id="nvpa-<?= e($key) ?>" class="form-control" name="<?= e($key) ?>" value="<?= e($d[$key]??'') ?>"></div>
              <?php endforeach; ?>
            </div>
            <p class="nvpa-note"><?= icon('info',15) ?> Mevcut PayTR ve diğer ödeme sağlayıcılarının anahtarları ile callback mekanizmaları bu editörden değiştirilmez.</p>
          </div>
        </section>
      </div>
      <div class="nvpa-side">
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('eye',17) ?> Yayın Durumu</h3></div>
          <div class="adm-card-body">
            <div class="nvpa-checks nvpa-checks-stack">
              <?php foreach([
                'is_active'=>'Ürün yayında',
                'is_featured'=>'Öne çıkan yazılım',
                'is_popular'=>'Popüler ürün',
                'is_new'=>'Yeni ürün'
              ] as $key=>$label):
              $checked=$key==='is_active'?($product['active']??1):($d[$key]??0); ?>
                <label><input type="checkbox" name="<?= e($key) ?>" value="1" <?= $checked?'checked':'' ?>> <?= e($label) ?></label>
              <?php endforeach; ?>
            </div>
            <div class="nvpa-field"><label for="nvpa-order">Ürün Sıralaması</label><input type="number" id="nvpa-order" class="form-control" name="sort_order" value="<?= (int)($product['sort_order']??0) ?>"></div>
          </div>
        </section>
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('wallet',17) ?> Fiyatlandırma</h3></div>
          <div class="adm-card-body">
            <div class="nvpa-field"><label for="nvpa-price">Satış Fiyatı (TL)</label><input id="nvpa-price" type="number" step="0.01" min="0" class="form-control" name="price" value="<?= e($product['price']??'0') ?>" required></div>
            <div class="nvpa-field"><label for="nvpa-old-price">İndirimsiz Eski Fiyat (TL)</label><input id="nvpa-old-price" type="number" step="0.01" min="0" class="form-control" name="old_price" value="<?= e($product['old_price']??'') ?>"></div>
            <div class="nvpa-field"><label for="nvpa-badge">Ürün Rozeti</label><input id="nvpa-badge" class="form-control" name="badge" value="<?= e($product['badge']??'') ?>" placeholder="Örn: Çok Satan"></div>
          </div>
        </section>
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('image',17) ?> Ürün Kapak Görseli</h3></div>
          <div class="adm-card-body">
            <?php if(!empty($product['cover_image'])): ?>
              <div class="nvpa-cover"><img src="<?= e(upload_url($product['cover_image'])) ?>" alt="Mevcut ürün kapağı"></div>
            <?php endif; ?>
            <div class="nvpa-field"><label for="nvpa-cover-input">Kapak Yükle</label><input id="nvpa-cover-input" class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp"></div>
            <small>Önerilen oran: 16:9. Yeni görsel yüklemek, önceki dosyayı fiziksel olarak silmez.</small>
          </div>
        </section>
        <div class="nvpa-sticky-save">
          <button class="btn btn-primary" type="submit"><?= icon('save',16) ?> <?= $edit?'Değişiklikleri Kaydet':'Yazılımı Kaydet' ?></button>
          <small>URL koruması ve güvenli veri doğrulama aktif.</small>
        </div>
      </div>
    </div>

    <section class="adm-card nvpa-card">
      <div class="adm-card-header"><h3><?= icon('search',18) ?> SEO · GEO · AIO</h3><span>04 / Arama Motorları</span></div>
      <div class="adm-card-body">
        <div class="nvpa-two">
          <div class="nvpa-field"><label>SEO Sayfa Başlığı</label><input class="form-control" name="meta_title" maxlength="255" value="<?= e($product['meta_title']??'') ?>" placeholder="Arama sonuçlarındaki başlık"></div>
          <div class="nvpa-field"><label>Odak Anahtar Kelime</label><input class="form-control" name="focus_keyword" value="<?= e($product['focus_keyword']??($d['focus_keyword']??'')) ?>"></div>
          <div class="nvpa-field"><label>Meta Açıklama</label><textarea class="form-control" rows="3" name="meta_description"><?= e($product['meta_description']??'') ?></textarea></div>
          <div class="nvpa-field"><label>Yardımcı Anahtar Kelimeler</label><textarea class="form-control" rows="3" name="secondary_keywords"><?= e($d['secondary_keywords']??'') ?></textarea></div>
          <div class="nvpa-field"><label>Open Graph Başlığı</label><input class="form-control" name="og_title" value="<?= e($d['og_title']??'') ?>"></div>
          <div class="nvpa-field"><label>Open Graph Açıklaması</label><input class="form-control" name="og_description" value="<?= e($d['og_description']??'') ?>"></div>
          <div class="nvpa-field"><label>OG Görsel URL</label><input class="form-control" name="og_image" value="<?= e($d['og_image']??'') ?>" placeholder="https://..."></div>
        </div>
        <p class="nvpa-note"><?= icon('link',15) ?> Eski ürünün <strong>/hazir-scriptler/<?= e($nvOriginalSlug?:'{slug}') ?></strong> adresi korunur; indeksli bağlantı değişiklikleri ayrıca 301 planı gerektirir.</p>
      </div>
    </section>
    <section class="adm-card nvpa-card">
      <div class="adm-card-header"><h3><?= icon('layers',18) ?> Ürün Modülleri ve Teknik Detaylar</h3><span>05 / Yapılandırılmış Veriler</span></div>
      <div class="adm-card-body">
        <p>İçe aktarılan ürünün modül, özellik, lisans ve SSS yapıları korunur. JSON dizi veya nesneleri doğrulanır; geçersiz veri kaydedilmez.</p>
        <div class="nvpa-two">
          <?php foreach([
            'modules_json'=>'Yazılım Modülleri',
            'specs_json'=>'Teknik Özellikler',
            'license_json'=>'Lisans Bilgileri',
            'faq_json'=>'Sık Sorulan Sorular'
          ] as $key=>$label): ?>
          <div class="nvpa-field">
            <label for="nvpa-<?= e($key) ?>"><?= e($label) ?></label>
            <textarea id="nvpa-<?= e($key) ?>" class="form-control nvpa-json" name="<?= e($key) ?>" rows="6" spellcheck="false" data-nvpa-json><?= e($d[$key]??'[]') ?></textarea>
            <small><?= e($label) ?> için mevcut JSON dizisini koruyarak düzenleyin.</small>
          </div>
          <?php endforeach; ?>
        </div>
        <div id="nvpa-json-error" class="nvpa-error" role="alert" hidden></div>
      </div>
    </section>
    <div class="nvpa-bottom-save"><button class="btn btn-primary" type="submit"><?= icon('save',16) ?> Yazılımı Kaydet</button></div>
  </form>

  <?php if($edit && !empty($product['legacy_id'])): ?>
  <section class="adm-card nvpa-card nvpa-gallery">
    <div class="adm-card-header"><h3><?= icon('image',17) ?> Ürün Ekran Görüntüleri</h3><span><?= count($gallery??[]) ?> görsel</span></div>
    <div class="adm-card-body">
      <div class="nvpa-gallery-grid">
        <?php foreach(($gallery??[]) as $img): ?>
        <div class="nvpa-gallery-item">
          <img src="<?= e(upload_url($img['image_path'])) ?>" alt="<?= e($img['alt_text']?:'Ürün ekran görüntüsü') ?>">
          <p><?= e($img['alt_text']??'') ?></p>
          <form method="post" action="/admin/netvera-yazilimlar/galeri/gizle">
            <?= csrfField() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['legacy_id'] ?>">
            <input type="hidden" name="image_id" value="<?= (int)$img['legacy_id'] ?>">
            <button class="btn btn-outline btn-sm" type="submit">Gizle (Dosyayı Koru)</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <form method="post" enctype="multipart/form-data" action="/admin/netvera-yazilimlar/galeri/ekle" class="nvpa-gallery-form">
        <?= csrfField() ?><input type="hidden" name="product_id" value="<?= (int)$product['legacy_id'] ?>">
        <div class="nvpa-two">
          <div class="nvpa-field"><label>Yeni Ekran Görüntüsü</label><input type="file" required name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div>
          <div class="nvpa-field"><label>Görsel Alt Metni (SEO)</label><input name="alt_text" class="form-control" maxlength="290"></div>
        </div>
        <div class="nvpa-field"><label>Görsel Açıklaması</label><input name="caption" class="form-control" maxlength="1500"></div>
        <button class="btn btn-primary" type="submit"><?= icon('plus',15) ?> Galeriye Ekle</button>
      </form>
    </div>
  </section>
  <?php endif; ?>
</div>
<script>
(function(){
  var form=document.getElementById('nvpa-product-form');
  if(!form)return;
  form.addEventListener('submit',function(event){
    var fields=form.querySelectorAll('[data-nvpa-json]');
    var error=document.getElementById('nvpa-json-error');
    for(var i=0;i<fields.length;i++){
      try{
        var parsed=JSON.parse(fields[i].value||'[]');
        if(!parsed || typeof parsed !== 'object')throw new Error('Array or object required');
      }catch(e){
        event.preventDefault();
        error.hidden=false;
        error.textContent='Lütfen "'+fields[i].previousElementSibling.textContent.trim()+'" alanına geçerli bir JSON dizi veya nesnesi girin.';
        fields[i].focus();
        error.scrollIntoView({behavior:'smooth',block:'center'});
        return;
      }
    }
    error.hidden=true;
  });
}());
</script>
