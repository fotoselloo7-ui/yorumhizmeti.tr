<?php
$ready = !empty($installed);
$esc = static fn($value) => e((string)$value);
?>
<div class="smm-admin">
  <div class="adm-page-top">
    <div class="adm-page-top-left"><div><h2>Servis &amp; API Merkezi</h2>
      <p class="text-secondary">Tedarikçi kataloglarını içeride yönetin; müşteriye yalnızca kendi markanız, paket içeriğiniz ve satış fiyatınız gösterilir.</p></div></div>
    <?php if ($ready): ?>
    <form method="post" action="/admin/smm/calistir"><?= csrfField() ?>
      <button class="btn btn-primary" type="submit"><?= icon('refresh-cw',16) ?> Sipariş Kuyruğunu Çalıştır</button>
    </form>
    <?php endif; ?>
  </div>

  <?php if (!$ready): ?>
  <div class="adm-card"><div class="adm-card-body">
    <h3>İlk kurulum</h3><p>Yalnızca yeni entegrasyon tabloları eklenir. Mevcut ürünler, kullanıcılar ve SEO adresleri korunur.</p>
    <form method="post" action="/admin/smm/kur"><?= csrfField() ?><button class="btn btn-primary" type="submit">Veritabanı Modülünü Kur</button></form>
  </div></div>
  <?php else: ?>
  <div class="smm-stats">
   <div class="adm-card"><div class="adm-card-body"><strong><?= count($providers) ?></strong><small>Bağlı tedarikçi</small></div></div>
   <div class="adm-card"><div class="adm-card-body"><strong><?= count($services) ?>+</strong><small>Listelenen servis (en çok 250)</small></div></div>
   <div class="adm-card"><div class="adm-card-body"><strong><?= count($links) ?></strong><small>Entegre paket</small></div></div>
   <div class="adm-card"><div class="adm-card-body"><strong><?= count(array_filter($jobs,static fn($j)=>$j['state']==='manual_review')) ?></strong><small>Manuel kontrol</small></div></div>
  </div>

  <?php if ($canEditProviders): ?>
  <div class="adm-card" id="smm-providers"><div class="adm-card-header"><h3><?= icon('plug',18) ?> Tedarikçi API Bağlantısı</h3></div>
   <div class="adm-card-body">
    <p class="text-secondary">Standart API v2 (POST + form-encoded) desteklenir. Anahtar sunucu tarafında AES-256-GCM ile şifrelenir; ekranda geri gösterilmez.</p>
    <form class="smm-form" id="smm-provider-form" method="post" action="/admin/smm/tedarikci/kaydet">
      <?= csrfField() ?>
      <div class="smm-form-grid">
        <div class="form-group"><label>Tedarikçi ID (düzenleme için)</label><input class="form-control" type="number" min="0" name="id" id="smm-provider-id" value="0"><small>Yeni kayıt için 0</small></div>
        <div class="form-group"><label>Görünen yönetim adı</label><input class="form-control" name="name" id="smm-provider-name" required maxlength="120" placeholder="Tedarikçi 1"></div>
        <div class="form-group"><label>API uç noktası (HTTPS)</label><input class="form-control" name="endpoint" id="smm-provider-endpoint" type="url" required placeholder="https://tedarikci.com/api/v2"></div>
        <div class="form-group"><label>API Anahtarı</label><input class="form-control" name="api_key" type="password" autocomplete="new-password" placeholder="Yeni anahtar / değiştirmek için doldur"></div>
        <div class="form-group"><label>API Para Birimi (maliyet raporu)</label><select class="form-control" name="currency" id="smm-provider-currency"><option>USD</option><option>TRY</option><option>EUR</option><option>GBP</option></select></div>
        <div class="form-group"><label>Bağlantı durumu</label><select class="form-control" name="is_active" id="smm-provider-active"><option value="1">Aktif</option><option value="0">Pasif</option></select></div>
      </div>
      <div class="smm-actions"><button class="btn btn-primary" type="submit"><?= icon('save',16) ?> Tedarikçiyi Kaydet</button><button class="btn btn-outline" type="reset" id="smm-provider-new">Yeni Bağlantı</button></div>
    </form>
   </div>
  </div>
  <?php endif; ?>

  <div class="adm-card"><div class="adm-card-header"><h3><?= icon('layers',18) ?> Bağlantılar &amp; Katalog Senkronizasyonu</h3><?php if($providers && $canEditProviders): ?><form method="post" action="/admin/smm/tedarikciler/esitle"><?= csrfField() ?><button class="btn btn-outline btn-sm" type="submit">Tüm Panellerin Servislerini Çek</button></form><?php endif; ?></div>
    <div class="adm-card-body"><div class="smm-provider-list">
    <?php foreach ($providers as $p): ?>
     <div class="smm-provider"><div><strong><?= $esc($p['name']) ?></strong>
        <small>#<?= (int)$p['id'] ?> · <?= $esc($p['currency']) ?> · <?= $p['is_active']?'Aktif':'Pasif' ?> · Son eşitleme: <?= $esc($p['last_synced_at']??'Henüz yok') ?></small>
        <small><?= $esc($p['endpoint']) ?></small></div>
       <div class="smm-actions">
         <?php if($canEditProviders): ?><button type="button" class="btn btn-outline btn-sm smm-edit-provider" data-id="<?= (int)$p['id'] ?>" data-name="<?= $esc($p['name']) ?>" data-endpoint="<?= $esc($p['endpoint']) ?>" data-currency="<?= $esc($p['currency']) ?>" data-active="<?= (int)$p['is_active'] ?>">Düzenle</button><?php endif; ?>
         <form method="post" action="/admin/smm/tedarikci/<?= (int)$p['id'] ?>/test"><?= csrfField() ?><button type="submit" class="btn btn-outline btn-sm">API Testi / Bakiye</button></form>
         <form method="post" action="/admin/smm/tedarikci/<?= (int)$p['id'] ?>/esitle"><?= csrfField() ?><button type="submit" class="btn btn-primary btn-sm">Servisleri Çek</button></form>
       </div>
     </div>
    <?php endforeach; ?>
    <?php if (!$providers): ?><p>Henüz tedarikçi eklenmemiş. Sınırsız sayıda API hesabı bağlayabilirsiniz.</p><?php endif; ?>
    </div></div>
  </div>

  <div class="adm-card"><div class="adm-card-header"><h3><?= icon('folder',18) ?> Sosyal Medya Kategorileri</h3></div>
   <div class="adm-card-body"><p class="text-secondary">İsterseniz Instagram, TikTok, YouTube vb. mevcut kategoriyi seçin; yeni kategori otomatik olarak “Sosyal Medya Hizmetleri” altında açılır.</p>
    <form method="post" action="/admin/smm/kategori/ekle" class="smm-inline"><?= csrfField() ?>
      <input class="form-control" name="name" maxlength="120" required placeholder="Örnek: Instagram Takipçi"><button class="btn btn-outline" type="submit">Alt Kategori Ekle</button>
    </form>
    <div class="smm-tags"><?php foreach ($categories as $c): ?><span><?= $esc($c['name']) ?></span><?php endforeach; ?></div>
   </div>
  </div>

  <div class="adm-card"><div class="adm-card-header"><h3><?= icon('list',18) ?> Tedarikçi Servislerini İncele</h3></div>
    <div class="adm-card-body">
      <form class="smm-inline" method="get" action="/admin/smm">
        <select class="form-control" name="provider"><option value="0">Tüm tedarikçiler</option>
         <?php foreach ($providers as $p): ?><option value="<?= (int)$p['id'] ?>" <?= $currentProvider===(int)$p['id']?'selected':'' ?>><?= $esc($p['name']) ?></option><?php endforeach; ?>
        </select>
        <input class="form-control" type="search" name="q" placeholder="Servis veya kategori ara" value="<?= $esc($search) ?>">
        <button class="btn btn-outline" type="submit"><?= icon('search',14) ?> Ara</button>
      </form>
      <div class="smm-table-wrap"><table class="smm-table"><thead><tr><th><label class="smm-checkbox"><input type="checkbox" id="smm-check-all" aria-label="Listelenen uygun servisleri seç"> Seç</label></th><th>Kaynak</th><th>Servis / Kategori</th><th>Tür</th><th>Adet aralığı</th><th>Alış / 1000</th><th>Durum</th><th></th></tr></thead><tbody>
      <?php foreach ($services as $s): ?>
       <tr>
         <td><?php if($s['is_available'] && $s['provider_active'] && strcasecmp($s['service_type'],'Default')===0): ?><input type="checkbox" class="smm-service-check" form="smm-bulk-form" name="service_ids[]" value="<?= (int)$s['id'] ?>" aria-label="<?= $esc($s['name']) ?> seç"><?php endif; ?></td>
         <td><?= $esc($s['provider_name']) ?><small>#<?= $esc($s['external_service_id']) ?></small></td>
         <td><strong><?= $esc($s['name']) ?></strong><small><?= $esc($s['category']) ?></small></td>
         <td><?= $esc($s['service_type']) ?></td>
         <td><?= number_format((int)$s['min_quantity'],0,',','.') ?> – <?= number_format((int)$s['max_quantity'],0,',','.') ?></td>
         <td><?= $esc($s['rate_per_1000']) ?> <?= $esc($s['currency']) ?></td>
         <td><?= $s['is_available'] && $s['provider_active']?'Açık':'Kapalı' ?></td>
         <td><?php if($s['is_available'] && $s['provider_active'] && strcasecmp($s['service_type'],'Default')===0): ?>
           <button type="button" class="btn btn-primary btn-sm smm-choose" data-id="<?= (int)$s['id'] ?>" data-name="<?= $esc($s['name']) ?>" data-min="<?= (int)$s['min_quantity'] ?>" data-max="<?= (int)$s['max_quantity'] ?>">Paket Oluştur</button>
           <?php else: ?><small>Manuel/uyumsuz</small><?php endif; ?></td>
       </tr>
      <?php endforeach; ?>
      <?php if (!$services): ?><tr><td colspan="8">Servis bulunamadı. Tedarikçiyi ekleyip “Servisleri Çek” işlemini kullanın.</td></tr><?php endif; ?>
      </tbody></table></div>
    </div>
  </div>


  <div class="adm-card" id="smm-bulk">
    <div class="adm-card-header"><h3><?= icon('layers',18) ?> Seçili Servisleri Toplu Paket Olarak Ekle</h3><span class="text-sm text-secondary" id="smm-selected-count">0 servis seçildi</span></div>
    <div class="adm-card-body">
      <p class="text-secondary">Yukarıdaki listeden 1–80 servisi seç. <strong>API servis adları müşteriye kopyalanmaz.</strong> Paketler platform / hizmet türü / adet başlığıyla taslak oluşturulur; kısa açıklama, detay ve kart özellikleri tüm seçilen paketlerde sizin yazdığınız metin olur. Fiyat ve SEO'yu sonradan paket editöründe ayrı değiştirebilirsin.</p>
      <form id="smm-bulk-form" method="post" action="/admin/smm/paket/toplu-ekle" class="smm-form">
        <?= csrfField() ?>
        <input type="hidden" name="provider_filter" value="<?= (int)$currentProvider ?>">
        <div class="smm-form-grid">
          <div class="form-group"><label>Site Sosyal Medya Kategorisi</label><select name="category_id" class="form-control" required>
            <option value="">Hedef kategori seç</option>
            <?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= $esc($c['name']) ?></option><?php endforeach; ?>
          </select></div>
          <div class="form-group"><label>Ortak Teslimat Adedi</label><input class="form-control" type="number" name="fulfillment_quantity" min="0" value="0" required><small>0 = her servisin minimum adedini kullan. Sınır dışındaki servis atlanır.</small></div>
          <div class="form-group"><label>Ortak Satış Fiyatı (TL)</label><input class="form-control" type="number" name="price" min="1" step="0.01" required placeholder="249.90"></div>
          <div class="form-group"><label>İsteğe Bağlı Marka Öneki</label><input class="form-control" name="label_prefix" maxlength="60" placeholder="Ör. Premium"></div>
        </div>
        <div class="form-group"><label>Ortak Kısa Açıklama — Sadece Sizin Metniniz</label><textarea class="form-control" name="short_description" maxlength="500" rows="2" minlength="10" required placeholder="Seçili paketlerin kart özetinde gösterilecek açıklama"></textarea></div>
        <div class="form-group"><label>Ortak Detaylı Paket Açıklaması (HTML)</label><textarea class="form-control" name="description" rows="5" required placeholder="<h2>Paket Hakkında</h2><p>Kendi hizmet açıklamanız...</p>"></textarea></div>
        <div class="form-group"><label>Ortak Kart Özellikleri (Her Satır Bir Özellik)</label><textarea class="form-control" name="highlight_lines" rows="7" maxlength="1600" placeholder="Şifre paylaşmadan sipariş&#10;Müşteri panelinden takip&#10;Tahmini teslimat bilgisi&#10;Destek ekibimizle iletişim"></textarea><small>8 satır eklerseniz 4+4 kaydırıcıda gösterilir. En fazla 12 satır.</small></div>
        <div class="form-group"><label>Ortak Teslim Süresi</label><input class="form-control" name="delivery_time" maxlength="100" placeholder="Ör. 1–3 gün (gerçek süreye uygun yazın)"></div>
        <button type="submit" class="btn btn-primary"><?= icon('plus',16) ?> Seçili Servislerden Paketleri Oluştur</button>
      </form>
    </div>
  </div>

  <div class="adm-card" id="smm-new-package">
   <div class="adm-card-header"><h3><?= icon('package',18) ?> Özgün Satış Paketi Oluştur</h3></div>
   <div class="adm-card-body"><p class="text-secondary">API servisi yalnızca arka planda teslimat için kullanılır. Başlık, SEO, açıklama ve TL fiyatı size aittir. Kaydettiğiniz paketi mevcut Paketler editöründe dilediğiniz gibi geliştirebilirsiniz.</p>
    <form method="post" action="/admin/smm/paket/olustur" class="smm-form">
      <?= csrfField() ?><input type="hidden" name="provider_filter" value="<?= (int)$currentProvider ?>">
      <div class="smm-form-grid">
       <div class="form-group"><label>Bağlanacak Servis</label>
         <select name="service_id" id="smm-service-select" class="form-control" required><option value="">Servis seçin</option>
         <?php foreach ($services as $s): if(!$s['is_available'] || !$s['provider_active'] || strcasecmp($s['service_type'],'Default')!==0)continue; ?>
           <option value="<?= (int)$s['id'] ?>"><?= $esc($s['provider_name']) ?> #<?= $esc($s['external_service_id']) ?> — <?= $esc($s['name']) ?></option>
         <?php endforeach; ?></select>
       </div>
       <div class="form-group"><label>Hedef Kategori</label><select name="category_id" class="form-control" required><option value="">Kategori seçin</option>
        <?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= $esc($c['name']) ?></option><?php endforeach; ?></select></div>
       <div class="form-group"><label>Tedarikçinin Teslim Edeceği Adet</label><input type="number" name="fulfillment_quantity" id="smm-quantity" min="1" class="form-control" required placeholder="1000"><small id="smm-limits">Seçilen servisin min/max değerlerine uygun olmalı.</small></div>
       <div class="form-group"><label>Kendi Satış Fiyatımız (TL)</label><input type="number" min="1" step="0.01" name="price" class="form-control" required placeholder="249.90"></div>
       <div class="form-group"><label>Özgün Paket Başlığı</label><input name="name" class="form-control" maxlength="300" required placeholder="Instagram 1000 Takipçi Paketi"></div>
       <div class="form-group"><label>Öngörülen Teslim Süresi</label><input name="delivery_time" class="form-control" maxlength="100" placeholder="Ör. 1-3 gün"></div>
      </div>
      <div class="form-group"><label>Özgün Kısa Açıklama</label><textarea name="short_description" required class="form-control" rows="2" maxlength="500" placeholder="Müşterinin göreceği kısa paket tanıtımı"></textarea></div>
      <div class="form-group"><label>Özgün Detaylı Açıklama (HTML)</label><textarea name="description" required class="form-control" rows="5" placeholder="<h2>Paketin içeriği</h2><p>...</p>"></textarea></div>
      <div class="smm-form-grid"><div class="form-group"><label>SEO Başlığı</label><input name="seo_title" class="form-control" maxlength="200"></div><div class="form-group"><label>Meta Açıklaması</label><input name="seo_description" class="form-control" maxlength="500"></div></div>
      <div class="form-group"><label>Odak Anahtar Kelime</label><input name="seo_focus_keyword" class="form-control" maxlength="100"></div>
      <label class="smm-checkbox"><input type="checkbox" name="publish_now" value="1"> Paket oluşturulunca hemen yayına al (varsayılan: taslak/pasif)</label>
      <button type="submit" class="btn btn-primary"><?= icon('save',16) ?> Kendi Paketimizi Oluştur</button>
    </form>
   </div>
  </div>

  <div class="adm-card"><div class="adm-card-header"><h3><?= icon('settings',18) ?> API'ye Bağlı Yayın Paketleri</h3></div>
   <div class="adm-card-body"><div class="smm-table-wrap"><table class="smm-table"><thead><tr><th>Paket</th><th>Tedarikçi</th><th>İşlem ve eşleme</th></tr></thead><tbody>
    <?php foreach ($links as $m): ?>
    <tr><td><strong><?= $esc($m['package_name']) ?></strong><small><?= $m['package_status']==='active'?'Yayında':'Pasif' ?> · <?= number_format((float)$m['price'],2,',','.') ?> TL</small>
      <a href="/admin/paket/<?= (int)$m['package_id'] ?>/duzenle" class="btn btn-outline btn-sm">Açıklama, SEO ve Fiyatı Düzenle</a></td>
     <td><?= $esc($m['provider_name']) ?><small>#<?= $esc($m['external_service_id']) ?> — <?= $esc($m['source_service']) ?></small></td>
     <td><form class="smm-mapping-form" method="post" action="/admin/smm/paket/<?= (int)$m['package_id'] ?>/esle"><?= csrfField() ?>
       <label>Servis İç ID <input class="form-control" type="number" name="service_id" min="1" value="<?= (int)$m['service_id'] ?>" required></label>
       <label>Gönderilecek adet <input class="form-control" type="number" name="fulfillment_quantity" min="1" value="<?= (int)$m['fulfillment_quantity'] ?>" required></label>
       <label class="smm-checkbox"><input type="checkbox" name="enabled" value="1" <?= $m['enabled']?'checked':'' ?>> Aktif</label>
       <button class="btn btn-outline btn-sm" type="submit">Eşlemeyi Kaydet</button></form></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$links): ?><tr><td colspan="3">Henüz satış paketine bağlanmış API servisi yok.</td></tr><?php endif; ?>
    </tbody></table></div></div>
  </div>

  <div class="adm-card"><div class="adm-card-header"><h3><?= icon('shopping-cart',18) ?> API Siparişleri &amp; Manuel Kontrol</h3></div>
    <div class="adm-card-body"><p class="text-secondary">Sadece <strong>ödemesi onaylanmış</strong> siparişler gönderilir. Belirsiz bağlantı hatalarında ikinci kez otomatik satın alma yapılmaz. Müşteri iadeleri ayrıca yönetilir.</p>
    <div class="smm-table-wrap"><table class="smm-table"><thead><tr><th>İç Sipariş</th><th>Servis / Tedarikçi</th><th>Durum</th><th>Son işlem</th><th>İşlemler</th></tr></thead><tbody>
    <?php foreach ($jobs as $j): ?><tr>
      <td><a href="/admin/siparis/<?= (int)$j['order_id'] ?>"><?= $esc($j['order_number']) ?></a><small><?= $esc($j['payment_status']) ?></small></td>
      <td><?= $esc($j['provider_name']) ?><small>Servis #<?= $esc($j['external_service_id']) ?> / <?= (int)$j['quantity'] ?> adet</small></td>
      <td><strong><?= $esc($j['state']) ?></strong><small><?= $esc($j['provider_status']??'') ?></small></td>
      <td><small><?= $esc($j['last_error']??$j['last_checked_at']??'-') ?></small></td>
      <td><div class="smm-actions">
        <?php if(in_array($j['state'],['completed','partial'],true)): ?><form method="post" action="/admin/smm/is/<?= (int)$j['id'] ?>/yenile"><?= csrfField() ?><button class="btn btn-outline btn-sm" type="submit">Yenileme İste</button></form><?php endif; ?>
        <?php if(in_array($j['state'],['submitted','in_progress'],true)): ?><form method="post" action="/admin/smm/is/<?= (int)$j['id'] ?>/iptal"><?= csrfField() ?><button class="btn btn-outline btn-sm" type="submit">İptal İste</button></form><?php endif; ?>
        <?php if($canEditProviders && $j['state']==='manual_review'): ?>
        <form method="post" action="/admin/smm/is/<?= (int)$j['id'] ?>/uzlastir" class="smm-reconcile">
            <?= csrfField() ?>
            <strong>Gönderim sonucu doğrulaması</strong>
            <select name="mode" class="form-control" required>
                <option value="matched">Tedarikçide var — numarasıyla eşleştir</option>
                <option value="retry">Tedarikçide yok — kontrollü tekrar kuyruğa al</option>
            </select>
            <input type="text" name="upstream_order_id" class="form-control"
                   placeholder="Tedarikçi sipariş numarası (eşleştirmede)" maxlength="120">
            <label class="smm-checkbox"><input type="checkbox" name="verify_absent" value="1">
              Siparişin tedarikçide OLUŞMADIĞINI kontrol ettim (tekrar deneme için)</label>
            <button type="submit" class="btn btn-outline btn-sm">Kontrol Sonucunu Kaydet</button>
        </form>
        <?php endif; ?>
        </div></td>
    </tr><?php endforeach; ?>
    <?php if (!$jobs): ?><tr><td colspan="5">Henüz API siparişi oluşmadı.</td></tr><?php endif; ?>
    </tbody></table></div></div>
  </div>

  <?php endif; ?>
</div>
<script>
var bulkBoxes=Array.from(document.querySelectorAll('.smm-service-check'));
var bulkSelectAll=document.getElementById('smm-check-all');
var bulkCounter=document.getElementById('smm-selected-count');
function updateBulkCount(){if(bulkCounter)bulkCounter.textContent=bulkBoxes.filter(function(el){return el.checked}).length+' servis seçildi';}
if(bulkSelectAll)bulkSelectAll.addEventListener('change',function(){
  bulkBoxes.forEach(function(el){el.checked=bulkSelectAll.checked});updateBulkCount();
});
bulkBoxes.forEach(function(el){el.addEventListener('change',updateBulkCount)});
document.querySelectorAll('.smm-edit-provider').forEach(function(btn){
  btn.addEventListener('click',function(){
    var form=document.getElementById('smm-provider-form');
    if(!form)return;
    document.getElementById('smm-provider-id').value=this.dataset.id;
    document.getElementById('smm-provider-name').value=this.dataset.name;
    document.getElementById('smm-provider-endpoint').value=this.dataset.endpoint;
    document.getElementById('smm-provider-currency').value=this.dataset.currency;
    document.getElementById('smm-provider-active').value=this.dataset.active;
    form.scrollIntoView({behavior:'smooth',block:'start'});
  });
});
var newProvider=document.getElementById('smm-provider-new');
if(newProvider)newProvider.addEventListener('click',function(){
  document.getElementById('smm-provider-id').value='0';
});
document.querySelectorAll('.smm-choose').forEach(function(btn){
  btn.addEventListener('click',function(){
    var sel=document.getElementById('smm-service-select');
    var qty=document.getElementById('smm-quantity');
    sel.value=this.dataset.id;qty.min=this.dataset.min;qty.max=this.dataset.max;
    qty.value=Math.max(Number(this.dataset.min)||1,Math.min(1000,Number(this.dataset.max)||1000));
    document.getElementById('smm-limits').textContent='Minimum '+this.dataset.min+' · maksimum '+this.dataset.max;
    document.getElementById('smm-new-package').scrollIntoView({behavior:'smooth',block:'start'});
  });
});
</script>
