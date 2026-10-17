<div class="nv-social-section-heading"><h2>Hizmet türünü seçin</h2><p>Önce hizmet türünü, ardından ihtiyacınıza uygun miktarı seçin. Her paket kendi miktarı ve toplam fiyatıyla satılır.</p></div>
<div class="nv-social-service-grid">
 <?php foreach($socialCatalog['groups'] as $service): ?>
 <a class="nv-social-service-card" href="<?= e($service['url']) ?>">
  <span class="nv-social-service-icon"><?= icon($platformIcon,24) ?></span>
  <h3><?= e($service['name']) ?></h3>
  <p><?= count($service['packages']) ?> farklı miktar seçeneği</p>
  <div class="nv-social-service-price"><strong><?= money($service['price']) ?></strong><span>'den başlayan</span></div>
  <span class="nv-social-service-action">Paketleri Gör <?= icon('arrow-right',18) ?></span>
 </a>
 <?php endforeach; ?>
</div>
