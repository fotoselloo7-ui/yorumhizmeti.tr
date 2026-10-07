<section class="section">
    <div class="container">
        <div class="user-dashboard">
            <?php include __DIR__ . '/../partials/user-sidebar.php'; ?>
            <div>
                <!-- Üst Başlık (Header) -->
                <div class="order-detail-header fade-in-up">
                    <div style="display:flex; align-items:center; gap:var(--space-4);">
                        <a href="/siparislerim" class="btn btn-outline btn-sm" aria-label="Geri Dön" style="padding: 6px 12px; border-radius: var(--radius-lg);"><?= icon('arrow-left', 16) ?> Geri</a>
                        <div>
                            <h1 style="font-size: var(--font-size-lg); font-weight: 800; margin:0; color: var(--color-dark);">Sipariş #<?= e($order['order_number']) ?></h1>
                            <span style="font-size: var(--font-size-xs); color: var(--color-text-secondary);"><?= formatDate($order['created_at'], 'd M Y H:i') ?></span>
                        </div>
                    </div>
                    <span class="status-badge <?= orderStatusColor($order['order_status']) ?>"><?= e(orderStatusLabel($order['order_status'])) ?></span>
                </div>

                <div class="order-detail-layout">
                    <!-- Sol Kolon: Ana İçerik -->
                    <div class="order-detail-main">
                        
                        <?php if ($order['admin_note'] && empty($order['customer_visible_note'])): ?>
                            <!-- Admin Notu Müşteriye Gösterilmeyecek (Sadece comment olarak bırakıldı) -->
                        <?php endif; ?>

                        <?php if (!empty($order['customer_visible_note'])): ?>
                        <div class="alert alert-info fade-in-up" style="margin-bottom: var(--space-6); background: rgba(37,99,235,0.05); border: 1px solid rgba(37,99,235,0.1); border-radius: var(--radius-lg); padding: var(--space-4);">
                            <div style="display:flex; align-items:flex-start; gap:var(--space-3);">
                                <div style="color: var(--color-blue); margin-top:2px;"><?= icon('info', 20) ?></div>
                                <div>
                                    <h4 style="margin:0 0 4px 0; font-size: var(--font-size-sm); font-weight: 600; color: var(--color-blue);">Sipariş Notu</h4>
                                    <p style="margin:0; font-size: var(--font-size-sm); color: var(--color-text-secondary);"><?= e($order['customer_visible_note']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Sipariş Kalemleri -->
                        <div class="card mb-6 fade-in-up">
                            <div class="card-header" style="background: var(--color-bg); padding: var(--space-4); border-bottom: 1px solid var(--color-border);"><h3 class="card-title m-0" style="font-size: var(--font-size-base);"><?= icon('package', 18) ?> Sipariş Edilen Paketler</h3></div>
                            <div class="card-body" style="padding: 0;">
                                <?php foreach ($items as $item): ?>
                                <?php 
                                    // Dinamik ikon çözümü (Mevcut paket adından veya veriden)
                                    $pIcon = 'package';
                                    if (stripos($item['package_name'], 'instagram') !== false) $pIcon = 'instagram';
                                    elseif (stripos($item['package_name'], 'tiktok') !== false) $pIcon = 'tiktok';
                                    elseif (stripos($item['package_name'], 'youtube') !== false) $pIcon = 'youtube';
                                    elseif (stripos($item['package_name'], 'twitter') !== false || stripos($item['package_name'], 'x') !== false) $pIcon = 'twitter';
                                    elseif (stripos($item['package_name'], 'facebook') !== false) $pIcon = 'facebook';
                                    elseif (stripos($item['package_name'], 'google') !== false) $pIcon = 'google';
                                ?>
                                <div class="order-item-card">
                                    <div class="order-item-icon">
                                        <?= icon($pIcon, 24, "pill-icon-$pIcon") ?>
                                    </div>
                                    <div class="order-item-info">
                                        <div style="font-weight: 700; font-size: var(--font-size-base); color: var(--color-dark);"><?= e($item['package_name']) ?></div>
                                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-top: 2px;">Adet: <strong><?= $item['quantity'] ?></strong></div>
                                    </div>
                                    <div class="order-item-price"><?= money($item['total']) ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Müşteri Bilgileri -->
                        <?php if (!empty($fields)): ?>
                        <div class="card mb-6 fade-in-up" style="animation-delay: 0.1s;">
                            <div class="card-header" style="background: var(--color-bg); padding: var(--space-4); border-bottom: 1px solid var(--color-border);"><h3 class="card-title m-0" style="font-size: var(--font-size-base);"><?= icon('file-text', 18) ?> Girilen Bilgiler</h3></div>
                            <div class="card-body" style="padding: var(--space-5);">
                                <div class="customer-info-grid">
                                    <?php foreach ($fields as $f): ?>
                                    <div class="info-field">
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-secondary); margin-bottom: 4px; font-weight: 600;"><?= e($f['field_label']) ?></div>
                                        <div style="font-size: var(--font-size-sm); font-weight: 500; color: var(--color-dark); word-break: break-all;"><?= e($f['field_value']) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Havale Bildirimi Formu -->
                        <?php if ($order['payment_method'] === 'bank_transfer' && $order['payment_status'] !== 'paid'): ?>
                        <div class="card mb-6 fade-in-up" style="animation-delay: 0.2s; border-color: rgba(245,158,11,0.3); box-shadow: 0 4px 15px rgba(245,158,11,0.05);">
                            <div class="card-header" style="background: rgba(245,158,11,0.05); padding: var(--space-4); border-bottom: 1px solid rgba(245,158,11,0.1);"><h3 class="card-title m-0" style="font-size: var(--font-size-base); color: #B45309;"><?= icon('inbox', 18) ?> Havale Bildirimi Gönder</h3></div>
                            <div class="card-body" style="padding: var(--space-5);">
                                <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-4);">Siparişinizin işleme alınması için lütfen ödemenizi aşağıdaki hesaplardan birine yapın ve bildirim formunu doldurun.</p>
                                
                                <?php if (!empty($bankAccounts)): ?>
                                <div class="mb-4">
                                    <?php foreach ($bankAccounts as $ba): ?>
                                    <div style="background: #fff; padding: var(--space-3) var(--space-4); border-radius: var(--radius-md); border: 1px solid var(--color-border); margin-bottom: var(--space-2); display:flex; align-items:center; justify-content:space-between;">
                                        <div>
                                            <div style="font-weight: 600; font-size: var(--font-size-sm);"><?= e($ba['bank_name']) ?></div>
                                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);"><?= e($ba['account_holder']) ?></div>
                                        </div>
                                        <div style="font-family: monospace; font-size: var(--font-size-sm); background: var(--color-bg); padding: 4px 8px; border-radius: 4px;"><?= e($ba['iban']) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                                <form method="POST" action="/havale-bildirimi" enctype="multipart/form-data">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <div class="form-row">
                                        <div class="form-group"><label>Gönderen Ad Soyad</label><input type="text" name="sender_name" class="form-control" required placeholder="İsim Soyisim"></div>
                                        <div class="form-group"><label>Havale Yapılan Banka</label><input type="text" name="bank_name" class="form-control" required placeholder="Örn: Ziraat Bankası"></div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group"><label>Tutar (₺)</label><input type="number" name="amount" step="0.01" class="form-control" value="<?= $order['total_amount'] ?>" required></div>
                                        <div class="form-group"><label>Dekont (Opsiyonel)</label><input type="file" name="receipt_file" class="form-control" accept="image/*,.pdf" style="padding: 7px 12px;"></div>
                                    </div>
                                    <div class="form-group"><label>Not (Opsiyonel)</label><textarea name="note" class="form-control" rows="2" placeholder="Belirtmek istediğiniz bir detay varsa buraya yazabilirsiniz."></textarea></div>
                                    <button type="submit" class="btn btn-primary" style="width: 100%;"><?= icon('send', 16) ?> Bildirimi Gönder</button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Durum Geçmişi (Timeline) -->
                        <div class="card fade-in-up" style="animation-delay: 0.3s;">
                            <div class="card-header" style="background: var(--color-bg); padding: var(--space-4); border-bottom: 1px solid var(--color-border);"><h3 class="card-title m-0" style="font-size: var(--font-size-base);"><?= icon('activity', 18) ?> Durum Geçmişi</h3></div>
                            <div class="card-body" style="padding: var(--space-5);">
                                <div class="order-timeline-modern">
                                    <?php 
                                    $logCount = count($logs);
                                    foreach ($logs as $index => $log): 
                                        $isLatest = ($index === 0);
                                        $statusClass = '';
                                        $iconName = 'check';
                                        
                                        if ($isLatest) {
                                            $statusClass = 'active';
                                            if (in_array($log['new_status'], ['cancelled', 'refunded'])) {
                                                $statusClass = 'error';
                                                $iconName = 'x';
                                            } elseif ($log['new_status'] === 'completed') {
                                                $statusClass = 'completed';
                                            }
                                        } else {
                                            $statusClass = 'completed';
                                        }
                                    ?>
                                    <div class="timeline-step <?= $statusClass ?>">
                                        <div class="timeline-marker"><?= icon($iconName, 12) ?></div>
                                        <div class="timeline-content">
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 4px;">
                                                <div style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-dark);"><?= e(orderStatusLabel($log['new_status'])) ?></div>
                                                <div style="font-size: 11px; color: var(--color-text-secondary);"><?= formatDate($log['created_at'], 'd M Y H:i') ?></div>
                                            </div>
                                            <?php if ($log['note']): ?>
                                            <p style="margin:0; font-size: var(--font-size-sm); color: var(--color-text-secondary);"><?= e($log['note']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Kolon: Özet -->
                    <div class="order-detail-sidebar">
                        <div class="summary-card fade-in-up" style="animation-delay: 0.1s;">
                            <div class="summary-card-header">
                                Sipariş Özeti
                            </div>
                            <div class="summary-card-body">
                                <div class="summary-row">
                                    <span class="text-secondary">Tutar</span>
                                    <strong style="color: var(--color-dark); font-size: var(--font-size-base);"><?= money($order['total_amount']) ?></strong>
                                </div>
                                <div class="summary-row">
                                    <span class="text-secondary">Ödeme Yöntemi</span>
                                    <span style="font-weight: 500;"><?= e($order['payment_gateway'] ?? ($order['payment_method'] === 'bank_transfer' ? 'Havale/EFT' : 'Bakiye')) ?></span>
                                </div>
                                <div class="summary-row" style="padding-top: var(--space-3); border-top: 1px dashed var(--color-border); margin-top: var(--space-3);">
                                    <span class="text-secondary">Ödeme Durumu</span>
                                    <?php 
                                        $psClass = 'badge-outline ';
                                        $psLabel = '';
                                        switch($order['payment_status']) {
                                            case 'paid': $psClass .= 'text-green'; $psLabel = 'Ödendi'; break;
                                            case 'pending': $psClass .= 'text-amber'; $psLabel = 'Bekliyor'; break;
                                            case 'failed': $psClass .= 'text-red'; $psLabel = 'Başarısız'; break;
                                            case 'refunded': $psClass .= 'text-secondary'; $psLabel = 'İade Edildi'; break;
                                        }
                                    ?>
                                    <span class="badge <?= $psClass ?>" style="padding: 2px 8px; font-size: 10px; font-weight: 700;"><?= $psLabel ?></span>
                                </div>

                                <div style="margin-top: var(--space-5);">
                                    <a href="/destek/yeni" class="btn btn-outline" style="width: 100%; justify-content: center;">
                                        <?= icon('life-buoy', 16) ?> Destek Talebi Aç
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card fade-in-up" style="margin-top: var(--space-4); background: linear-gradient(135deg, #F0F6FF 0%, #FFFFFF 100%); animation-delay: 0.2s;">
                            <div class="card-body" style="padding: var(--space-4); text-align: center;">
                                <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.1); color: var(--color-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
                                    <?= icon('shield-check', 24) ?>
                                </div>
                                <h4 style="font-size: var(--font-size-sm); font-weight: 600; margin-bottom: 4px; color: var(--color-dark);">Güvenli İşlem</h4>
                                <p style="font-size: 12px; color: var(--color-text-secondary); margin-bottom: var(--space-4);">Siparişiniz sistemimizde %100 güvenle işlenmektedir.</p>
                                
                                <?php if (setting('site_whatsapp')): ?>
                                <a href="https://wa.me/<?= e(setting('site_whatsapp')) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; background: #25D366; border-color: #25D366;">
                                    <?= icon('whatsapp', 16) ?> WhatsApp Destek
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
