<?php
$nvHistory = isset($netveraHistory) && is_array($netveraHistory) ? $netveraHistory : [];
$nvOrders = array_slice($nvHistory['orders'] ?? [], 0, 12);
$nvAffiliate = $nvHistory['affiliate'] ?? null;
?>
<?php if ($nvOrders || $nvAffiliate): ?>
<section class="panel-card nv57-legacy-history" aria-label="NetVera eski satın alma ve bayilik geçmişi">
    <div class="panel-card-header">
        <h3><?= icon('layers',18) ?> NetVera Satın Almalarım & Bayilik</h3>
        <span class="nv57-history-pill">Eski hesabınızdan aktarılan kayıtlar</span>
    </div>
    <div class="panel-card-body">
        <?php if ($nvAffiliate): ?>
        <div class="nv57-dealer-summary">
            <div><small>Bayilik / İş Ortağı Durumu</small>
                <strong><?= e($nvAffiliate['status']==='approved'?'Onaylı Bayi':($nvAffiliate['status']==='pending'?'Onay Bekliyor':'Pasif / Askıda')) ?></strong></div>
            <div><small>Referans Kodunuz</small><strong><?= e($nvAffiliate['referral_code'] ?: '—') ?></strong></div>
            <div><small>Eski Komisyon Geçmişi</small><strong><?= money((float)($nvHistory['commission_total']??0)) ?></strong></div>
        </div>
        <p class="nv57-history-note">Burada eski NetVera komisyonları gösterilir. Yeni satışlara otomatik komisyon tahakkuku, eski ödeme/bayilik sözleşmesi doğrulanmadan başlamaz.</p>
        <?php endif; ?>
        <?php if ($nvOrders): ?>
        <div class="nv57-purchases">
            <?php foreach ($nvOrders as $order): ?>
            <article class="nv57-purchase">
                <span class="nv57-purchase-icon"><?= icon('monitor',19) ?></span>
                <div class="nv57-purchase-copy">
                    <strong><?= e($order['product_name'] ?: 'Dijital Hizmet / Yazılım') ?></strong>
                    <small><?= e($order['order_no']) ?> · <?= e($order['created_at'] ? formatDate($order['created_at'],'d.m.Y') : 'Eski sipariş') ?></small>
                    <?php if ((int)$order['has_entitlements']===1): ?>
                    <span class="nv57-entitlement"><?= icon('shield-check',12) ?> Kaydedilmiş lisans / satın alma hakları mevcut</span>
                    <?php endif; ?>
                </div>
                <div class="nv57-purchase-status">
                    <strong><?= money((float)$order['amount']) ?></strong>
                    <span><?= e($order['payment_status']) ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <p class="nv57-history-note">Eski yazılım lisansı, kurulum veya yetki sorunlarınız için <a href="/destek/yeni">destek talebi oluşturabilirsiniz</a>.</p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
