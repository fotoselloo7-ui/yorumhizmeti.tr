<div class="adm-page-top">
    <div>
        <h2><?= icon('credit-card', 24) ?> Ödeme Modülleri</h2>
    </div>
</div>

<div class="adm-card" style="max-width: 800px; margin: 0 auto;">
    <div class="adm-card-header">
        <h3><?= icon('settings', 16) ?> Sistem Ödeme Yolları</h3>
    </div>
    <div class="adm-card-body" style="display:flex; flex-direction:column; gap: 15px;">
        <?php foreach ($gateways as $gw): ?>
        <div class="gateway-card" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border: 1px solid var(--color-border); border-radius: 12px; background: #fff; transition: 0.2s; flex-wrap: wrap; gap: 15px;">
            <div class="gateway-info" style="display: flex; align-items: center; gap: 15px;">
                <div class="gateway-icon" style="width:48px; height:48px; border-radius:10px; background: var(--color-soft-blue); color: var(--color-blue); display:flex; align-items:center; justify-content:center;">
                    <?= icon($gw['type'] === 'online' ? 'credit-card' : 'money-dollar-circle-fill', 22) ?>
                </div>
                <div>
                    <div class="font-semibold" style="font-size:15px; color:var(--color-dark);"><?= e($gw['name']) ?></div>
                    <div class="text-xs text-secondary" style="margin-top:2px;"><?= $gw['type'] === 'online' ? 'Online POS' : 'Manuel Ödeme' ?> · <?= e($gw['gateway_key']) ?></div>
                </div>
            </div>
            <div class="gateway-actions" style="display: flex; align-items: center; gap: 12px;">
                <?php if ($gw['is_default']): ?><span class="badge badge-success">Varsayılan</span><?php endif; ?>
                <span class="status-badge <?= $gw['is_active'] ? 'success' : 'default' ?>"><?= $gw['is_active'] ? 'Aktif' : 'Pasif' ?></span>
                <form method="POST" action="/admin/odeme-modulleri/<?= $gw['id'] ?>/toggle" style="margin:0; display:inline-block;"><?= csrfField() ?><button class="btn btn-light btn-sm" title="Durum Değiştir"><?= $gw['is_active'] ? icon('eye-off', 14) : icon('eye', 14) ?></button></form>
                <?php if ($gw['type'] === 'online' && !$gw['is_default']): ?>
                <form method="POST" action="/admin/odeme-modulleri/<?= $gw['id'] ?>/varsayilan" style="margin:0; display:inline-block;"><?= csrfField() ?><button class="btn btn-light btn-sm" title="Varsayılan Yap"><?= icon('star', 14) ?></button></form>
                <?php endif; ?>
                <?php if ($gw['gateway_key'] === 'paytr'): ?><a href="/admin/paytr-ayarlari" class="btn btn-light btn-sm" title="Ayarlar"><?= icon('settings', 14) ?></a><?php endif; ?>
                <?php if ($gw['gateway_key'] === 'iyzico'): ?><a href="/admin/iyzico-ayarlari" class="btn btn-light btn-sm" title="Ayarlar"><?= icon('settings', 14) ?></a><?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
