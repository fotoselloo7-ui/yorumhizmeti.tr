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
              <?php foreach(['demo_is_active'=>'Demo bağlantısı aktif','demo_is_public'=>'Demo herkese açık gösterilsin','demo_credentials_public'=>'Herkese açık DEMO hesap bilgilerini ürün detayında göster (Kopyala / Yapıştır)'] as $key=>$label): ?>
                <label><input type="checkbox" id="nvpa-<?= e($key) ?>" name="<?= e($key) ?>" value="1" data-nvpa-demo-gate <?= !empty($d[$key])?'checked':'' ?>> <?= e($label) ?></label>
              <?php endforeach; ?>
            </div>
            <?php
              $nvpaHasSavedDemo=trim((string)($d['demo_admin_username']??$d['demo_username']??''))!=='' ||
                  trim((string)($d['demo_username']??''))!=='' ||
                  !empty(json_decode((string)($d['demo_accounts_json']??'[]'),true));
              $nvpaDemoReady=!empty($d['demo_is_active'])&&!empty($d['demo_is_public'])&&
                  !empty($d['demo_credentials_public'])&&!empty($d['demo_url'])&&$nvpaHasSavedDemo;
            ?>
            <div id="nvpa-demo-publication-status" role="status" aria-live="polite" style="margin:9px 0 12px;padding:12px 14px;border-radius:10px;border:1px solid <?= $nvpaDemoReady?'#c6e8d2':'#f0dbac' ?>;background:<?= $nvpaDemoReady?'#f0fff5':'#fff9ec' ?>;font-size:13px;line-height:1.6">
              <strong id="nvpa-demo-publication-title"><?= $nvpaDemoReady?'DEMO girişleri yayında':'DEMO girişleri ziyaretçilere kapalı' ?></strong>
              <span id="nvpa-demo-publication-detail" style="display:block;margin-top:4px">
                <?= $nvpaDemoReady
                    ? 'Ayrıntı sayfasının Canlı Demo sekmesinde kullanıcı adı ve şifreyi Kopyala / Yapıştır şeklinde görebilirsiniz.'
                    : 'Hesap alanlarını doldurmak tek başına yayınlamaz. Üç kutuyu da işaretleyip kaydetmelisiniz. Gerçek yönetici şifresini burada kullanmayın.' ?>
              </span>
              <?php if($nvLiveUrl): ?><a href="<?= e($nvLiveUrl.'#demo') ?>" target="_blank" rel="noopener noreferrer" style="display:inline-block;margin-top:7px;font-weight:700">Ürün detayını aç / Canlı Demo</a><?php endif; ?>
            </div>
            <p class="nvpa-demo-disclosure-help" style="padding:12px 14px;margin:10px 0 16px;border:1px solid #d8dff2;border-radius:11px;background:#f7f9ff;color:#41526e;font-size:12px;line-height:1.7">
              <strong>Demo hesaplarının görünmesi için:</strong> Yukarıdaki üç kutuyu işaretleyin, yalnızca tanıtım amacıyla oluşturduğunuz test kullanıcı adını ve test şifresini aşağıya girin, ardından kaydedin. Gerçek müşteri veya site yönetici hesabını burada paylaşmayın. Yayınlanan bilgiler herkes tarafından görüntülenebilir ve kopyalanabilir.
            </p>
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
              
            </div>
            
          </div>
        </section>
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('shield-check',18) ?> Lisans, Destek ve Güncellemeler</h3><span>03 / Teslimat</span></div>
          <div class="adm-card-body">
            <div class="nvpa-two">
              <?php foreach(['support_duration_type'=>'Destek Süresi Türü','support_duration_months'=>'Destek Süresi (ay)','update_duration_type'=>'Güncelleme Süresi Türü','update_duration_months'=>'Güncelleme Süresi (ay)','buy_url'=>'Satın Alma Linki','tags'=>'Ürün Etiketleri'] as $key=>$label): ?>
              <div class="nvpa-field"><label for="nvpa-<?= e($key) ?>"><?= e($label) ?></label><input id="nvpa-<?= e($key) ?>" class="form-control" name="<?= e($key) ?>" value="<?= e($d[$key]??'') ?>"></div>
              <?php endforeach; ?>
            </div>
            <div class="nvpa-checks nvpa-checks-stack">
              <label><input type="checkbox" name="purchase_enabled" value="1" <?= (array_key_exists('purchase_enabled',$d)?!empty($d['purchase_enabled']):!empty($d['buy_url']))?'checked':'' ?>> Satışa Aç / ürün detayında “Satın Al” butonunu göster</label>
              <small>Satışa açmak için fiyat ve geçerli bir satın alma linki girin. Kapalıyken ürün yayında ve demo aktif kalabilir.</small>
            </div>
            
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
            <?php if($edit): ?>
            <div class="nvpa-field">
              <label>Ürüne Özel Kupon</label>
              <a class="btn btn-outline btn-sm" href="/admin/kuponlar/yeni?target=script:<?= (int)$product['legacy_id'] ?>&amp;name=<?= rawurlencode((string)$product['name']) ?>">
                <?= icon('tag',14) ?> Bu Ürüne Kupon Tanımla
              </a>
              <small>Kupon ekranında bu yazılım otomatik seçili gelir.</small>
            </div>
            <?php endif; ?>
          </div>
        </section>
        <section class="adm-card nvpa-card">
          <div class="adm-card-header"><h3><?= icon('image',17) ?> Ürün Kapak Görseli</h3></div>
          <div class="adm-card-body">
            <?php if(!empty($product['cover_image'])): ?>
              <div class="nvpa-cover"><img src="<?= e(upload_url($product['cover_image'])) ?>" alt="Mevcut ürün kapağı"></div>
            <?php endif; ?>
            <div class="nvpa-field"><label for="nvpa-cover-input">Kapak Yükle</label><input id="nvpa-cover-input" class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp"></div>
            
          </div>
        </section>
        <div class="nvpa-sticky-save">
          <button class="btn btn-primary" type="submit"><?= icon('save',16) ?> <?= $edit?'Değişiklikleri Kaydet':'Yazılımı Kaydet' ?></button>
          
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
        
      </div>
    </section>
    <section class="adm-card nvpa-card">
      <div class="adm-card-header"><h3><?= icon('layers',18) ?> Ürün Modülleri ve Teknik Detaylar</h3><span>05 / Yapılandırılmış Veriler</span></div>
      <div class="adm-card-body">
        
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
  var status=document.getElementById('nvpa-demo-publication-status');
  var title=document.getElementById('nvpa-demo-publication-title');
  var detail=document.getElementById('nvpa-demo-publication-detail');
  var gateActive=form.querySelector('[name="demo_is_active"]');
  var gatePublic=form.querySelector('[name="demo_is_public"]');
  var gateCredentials=form.querySelector('[name="demo_credentials_public"]');
  function refreshDemoStatus(){
    if(!status || !title || !detail)return;
    var hasAccount=['demo_admin_username','demo_username'].some(function(name){
      var field=form.querySelector('[name="'+name+'"]');
      return field && field.value.trim()!=='';
    });
    var extras=form.querySelector('[name="demo_accounts_json"]');
    if(!hasAccount && extras)try{
      var list=JSON.parse(extras.value||'[]');
      hasAccount=Array.isArray(list)&&list.some(function(x){return x&&typeof x.username==='string'&&x.username.trim();});
    }catch(e){}
    var demoUrl=form.querySelector('[name="demo_url"]');
    var ready=gateActive&&gateActive.checked&&gatePublic&&gatePublic.checked&&
      gateCredentials&&gateCredentials.checked&&demoUrl&&demoUrl.value.trim()!==''&&hasAccount;
    var selected=gateCredentials&&gateCredentials.checked;
    title.textContent=ready?'DEMO girişleri yayına hazır':selected?'DEMO yayın koşulları eksik':'DEMO girişleri ziyaretçilere kapalı';
    detail.textContent=ready
      ?'Kaydettiğinizde yalnızca bu yazılımın herkese açık DEMO test hesabı Canlı Demo sekmesinde gösterilir.'
      :selected
        ?'Yayın için demo bağlantısı, diğer iki kutu ve en az bir TEST kullanıcı adı gerekir. Gerçek yönetici hesabını paylaşmayın.'
        :'Giriş bilgileri saklanır ancak gösterilmez. Üçüncü kutuyu özellikle işaretleyip kaydetmeden ziyaretçiler şifreyi göremez.';
    status.style.background=ready?'#f0fff5':'#fff9ec';
    status.style.borderColor=ready?'#c6e8d2':'#f0dbac';
  }
  form.querySelectorAll('[data-nvpa-demo-gate], [name="demo_url"], [name="demo_admin_username"], [name="demo_username"], [name="demo_accounts_json"]')
    .forEach(function(el){el.addEventListener('change',refreshDemoStatus);el.addEventListener('input',refreshDemoStatus);});
  refreshDemoStatus();
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
