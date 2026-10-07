<section class="section">
    <div class="container" style="max-width: 600px;">
        <div class="card text-center">
            <div style="width:64px; height:64px; border-radius:50%; background:var(--color-soft-green); color:var(--color-green); display:flex; align-items:center; justify-content:center; margin: 0 auto var(--space-4);">
                <?= icon('check-circle', 32) ?>
            </div>
            <h1 style="font-size: var(--font-size-2xl); font-weight: 700; margin-bottom: var(--space-2);">
                <?= $method === 'bank_transfer' ? 'Siparişiniz Oluşturuldu' : 'Ödeme Başarılı' ?>
            </h1>
            <?php if ($orderNumber): ?>
            <p class="text-secondary mb-6">Sipariş No: <strong><?= e($orderNumber) ?></strong></p>
            <?php endif; ?>

            <?php if ($method === 'bank_transfer' && !empty($bankAccounts)): ?>
            <div style="text-align: left; margin-top: var(--space-6);">
                <h3 style="font-weight: 600; margin-bottom: var(--space-4);"><?= icon('dollar-sign', 18) ?> Havale/EFT Bilgileri</h3>
                <p class="text-sm text-secondary mb-4">Lütfen aşağıdaki banka hesaplarından birine havale/EFT yapın ve bildirimi gönderin.</p>
                <?php foreach ($bankAccounts as $acc): ?>
                <div style="background: var(--color-bg); border-radius: var(--radius-md); padding: var(--space-4); margin-bottom: var(--space-3);">
                    <div class="font-semibold mb-2"><?= e($acc['bank_name']) ?></div>
                    <div class="text-sm"><strong>Hesap Sahibi:</strong> <?= e($acc['account_holder']) ?></div>
                    <div class="text-sm"><strong>IBAN:</strong> <?= e($acc['iban']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div style="margin-top: var(--space-6); display: flex; gap: var(--space-3); justify-content: center;">
                <a href="/siparislerim" class="btn btn-primary"><?= icon('package', 16) ?> Siparişlerim</a>
                <a href="/" class="btn btn-light">Ana Sayfa</a>
            </div>
        </div>
    </div>
</section>
