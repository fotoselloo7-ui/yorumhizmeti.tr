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
    <form class="smm-form" method="post" action="/admin/smm/tedarikci/kaydet">
      <?= csrfField() ?>
      <div class="smm-form-grid">
        <div class="form-group"><label>Tedarikçi ID (düzenleme için)</label><input class="form-control" type="number" min="0" name="id" value="0"><small>Yeni kayıt için 0</small></div>
        <div class="form-group"><label>Görünen yönetim adı</label><input class="form-control" name="name" required maxlength="120" placeholder="Tedarikçi 1"></div>
        <div class="form-group"><label>API uç noktası (HTTPS)</label><input class="form-control" name="endpoint" type="url" required placeholder="https://tedarikci.com/api/v2"></div>
        <div class="form-group"><label>API Anahtarı</label><input class="form-control" name="api_key" type="password" autocomplete="new-password" placeholder="Yeni anahtar / değiştirmek için doldur"></div>
        <div class="form-group"><label>API Para Birimi (maliyet raporu)</label><select class="form-control" name="currency"><option>USD</option><option>TRY</option><option>EUR</option><option>GBP</option></select></div>
        <div class="form-group"><label>Bağlantı durumu</label><select class="form-control" name="is_active"><option value="1">Aktif</option><option value="0">Pasif</option></select></div>
      </div>
      <button class="btn btn-primary" type="submit"><?= icon('save',16) ?> Tedarikçiyi Kaydet</button>
    </form>
   </div>
  </div>
  <?php endif; ?>

  <div class="adm-card"><div class="adm-card-header"><h3><?= icon('layers',18) ?> Bağlantılar &amp; Katalog Senkronizasyonu</h3></div>
    <div class="adm-card-body"><div class="smm-provider-list">
    <?php foreach ($providers as $p): ?>
     <div class="smm-provider"><div><strong><?= $esc($p['name']) ?></strong>
        <small>#<?= (int)$p['id'] ?> · <?= $esc($p['currency']) ?> · <?= $p['is_active']?'Aktif':'Pasif' ?> · Son eşitleme: <?= $esc($p['last_synced_at']??'Henüz yok') ?></small>
        <small><?= $esc($p['endpoint']) ?></small></div>
       <div class="smm-actions">
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
      <div class="smm-table-wrap"><table class="smm-table"><thead><tr><th>Kaynak</th><th>Servis / Kategori</th><th>Tür</th><th>Adet aralığı</th><th>Alış / 1000</th><th>Durum</th><th></th></tr></thead><tbody>
      <?php foreach ($services as $s): ?>
       <tr>
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
      <?php if (!$services): ?><tr><td colspan="7">Servis bulunamadı. Tedarikçiyi ekleyip “Servisleri Çek” işlemini kullanın.</td></tr><?php endif; ?>
      </tbody></table></div>
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
