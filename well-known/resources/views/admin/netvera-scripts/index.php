<div class="adm-page-top">
 <div><h2><?= icon('monitor',21) ?> Netvera Hazır Yazılımlar</h2>
 <p class="text-sm text-secondary">Orijinal slug, ürün görselleri, lisans, demo ve SEO alanlarını koruyan bağımsız yazılım kataloğu.</p></div>
 <?php if($ready): ?><a href="/admin/netvera-yazilimlar/ekle" class="btn btn-primary btn-sm"><?= icon('plus',15) ?> Yeni Yazılım Ekle</a><?php endif; ?>
</div>
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
