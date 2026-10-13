<div class="adm-page-top">
 <div><h2><?= icon('monitor',21) ?> Netvera Hazır Yazılımlar</h2>
 <p class="text-sm text-secondary">Orijinal slug, ürün görselleri, lisans, demo ve SEO alanlarını koruyan bağımsız yazılım kataloğu.</p></div>
 <?php if($ready): ?><a href="/admin/netvera-yazilimlar/ekle" class="btn btn-primary btn-sm"><?= icon('plus',15) ?> Yeni Yazılım Ekle</a><?php endif; ?>
</div>
<?php if($ready): ?>
<div class="adm-card" style="margin-bottom:16px">
 <div class="adm-card-header">
  <div>
   <h3><?= icon('key',17) ?> Eski NetVera Demo Girişlerini Aktar</h3>
   <p class="text-sm text-secondary" style="margin:4px 0 0">Eski NetVera ürünlerindeki herkese açık demo admin kullanıcı/e-posta ve şifre alanlarını yeni ürün kayıtlarıyla eşleştirir.</p>
  </div>
 </div>
 <div class="adm-card-body">
  <form method="post" action="/admin/netvera-yazilimlar/demo-aktar" enctype="multipart/form-data" style="display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:end">
   <?= csrfField() ?>
   <div>
    <label for="nv-demo-source-sql" style="display:block;font-weight:700;margin-bottom:6px">Eski NetVera SQL yedeği</label>
    <input id="nv-demo-source-sql" type="file" name="source_sql" accept=".sql,text/plain,application/sql" class="form-control">
    <small class="text-secondary">Bu projede eski <code>script_products</code> tablosu bulunmuyorsa eski NetVera <strong>.sql</strong> yedeğini burada seçmeniz gerekir. Dosya çalıştırılmaz; yalnızca herkese açık demo alanları okunup mevcut ürünlerle eşleştirilir.</small>
   </div>
   <button type="submit" class="btn btn-primary"><?= icon('download',14) ?> SQL'den Demo Bilgilerini Aktar</button>
  </form>
 </div>
</div>
<?php endif; ?>

<div class="adm-card">
 <div class="adm-card-header"><h3><?= icon('layers',17) ?> Yazılım Ürünleri (<?= count($products) ?>)</h3></div>
 <div class="adm-card-body">
  <div style="overflow-x:auto">
  <table class="table" style="width:100%;border-collapse:collapse">
   <thead><tr><th>Ürün</th><th>SEO Slug</th><th>Fiyat</th><th>Durum</th><th>Düzenle</th></tr></thead>
   <tbody><?php foreach($products as $product): ?>
    <tr>
     <td><?= e($product['name']) ?></td>
     <td><code><?= e($product['slug']) ?></code></td>
     <td><?= money($product['price']) ?></td>
     <td><?= (int)$product['active']?'Yayında':'Pasif' ?></td>
     <td><?php if($ready): ?><a class="btn btn-outline btn-sm" href="/admin/netvera-yazilimlar/<?= (int)$product['legacy_id'] ?>/duzenle"><?= icon('edit',13) ?> Düzenle</a><?php endif; ?>
       <a class="btn btn-light btn-sm" target="_blank" rel="noopener" href="/hazir-scriptler/<?= e($product['slug']) ?>"><?= icon('external-link',12) ?> Gör</a></td>
    </tr><?php endforeach; ?></tbody>
  </table></div>
 </div>
</div>
