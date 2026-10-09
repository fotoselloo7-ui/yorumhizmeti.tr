<div class="adm-page-top"><h2><?= icon('credit-card',22) ?> Ödeme Modülleri</h2></div>
<div class="nv68-payment-list">
 <?php foreach($gateways as $gw): ?>
 <article class="nv68-payment-row">
  <div class="nv68-payment-meta">
   <span class="nv68-payment-icon"><?= icon($gw['type']==='online'?'credit-card':'wallet',21) ?></span>
   <div><strong><?= e($gw['name']) ?></strong><small><?= $gw['type']==='online'?'Online POS':'Havale / EFT' ?></small></div>
  </div>
  <div class="nv68-payment-actions">
   <?php if($gw['is_default']): ?><span class="badge badge-success">Varsayılan</span><?php endif; ?>
   <span class="status-badge <?= $gw['is_active']?'success':'default' ?>"><?= $gw['is_active']?'Aktif':'Pasif' ?></span>
   <form method="post" action="/admin/odeme-modulleri/<?= (int)$gw['id'] ?>/toggle"><?= csrfField() ?><button class="btn btn-light btn-sm" type="submit" aria-label="<?= $gw['is_active']?'Pasifleştir':'Etkinleştir' ?>" title="<?= $gw['is_active']?'Pasifleştir':'Etkinleştir' ?>"><?= icon($gw['is_active']?'eye-off':'eye',15) ?></button></form>
   <?php if($gw['type']==='online' && !$gw['is_default']): ?>
   <form method="post" action="/admin/odeme-modulleri/<?= (int)$gw['id'] ?>/varsayilan"><?= csrfField() ?><button class="btn btn-light btn-sm" type="submit" title="Varsayılan Yap" aria-label="Varsayılan Yap"><?= icon('star',15) ?></button></form>
   <?php endif; ?>
   <?php if($gw['gateway_key']==='paytr'): ?><a href="/admin/paytr-ayarlari" class="btn btn-light btn-sm" aria-label="PayTR Ayarları"><?= icon('settings',15) ?></a><?php endif; ?>
   <?php if($gw['gateway_key']==='iyzico'): ?><a href="/admin/iyzico-ayarlari" class="btn btn-light btn-sm" aria-label="iyzico Ayarları"><?= icon('settings',15) ?></a><?php endif; ?>
  </div>
 </article>
 <?php endforeach; ?>
</div>
<style>
.nv68-payment-list{display:grid;gap:12px;max-width:1200px}
.nv68-payment-row{min-width:0;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:17px 22px;border:1px solid #e2e7f0;background:#fff;border-radius:15px;box-shadow:0 3px 15px rgba(30,45,80,.035)}
.nv68-payment-meta{display:flex;align-items:center;gap:13px}
.nv68-payment-icon{display:grid;place-items:center;width:45px;height:45px;flex-shrink:0;border-radius:12px;background:#eef3ff;color:#4169e5}
.nv68-payment-meta strong{display:block;font-size:14px;color:#1a2b4c}.nv68-payment-meta small{display:block;font-size:11px;color:#7d89a2;margin-top:3px}
.nv68-payment-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.nv68-payment-actions form{margin:0}
@media(max-width:600px){.nv68-payment-row{padding:14px}.nv68-payment-actions{width:100%;justify-content:flex-end}}
</style>