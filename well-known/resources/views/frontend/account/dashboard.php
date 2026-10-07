<section class="section panel-section">
    <div class="container">
        <div class="panel-layout">
            <?php include __DIR__ . '/../partials/user-sidebar.php'; ?>
            <div class="panel-content">

                <!-- Welcome Banner -->
                <div class="panel-welcome">
                    <div class="panel-welcome-text">
                        <h1>Hoş Geldiniz, <?= e($user['name']) ?></h1>
                        <p>Hesap özetinizi, siparişlerinizi ve destek taleplerinizi buradan yönetebilirsiniz.</p>
                    </div>
                    <div class="panel-welcome-actions">
                        <a href="/kategoriler" class="btn btn-primary btn-sm"><?= icon('shopping-cart', 16) ?> Yeni Sipariş</a>
                        <a href="/destek/yeni" class="btn btn-outline btn-sm"><?= icon('headphones', 16) ?> Destek Talebi</a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="panel-stats">
                    <div class="panel-stat-card">
                        <div class="panel-stat-icon stat-blue"><?= icon('package', 22) ?></div>
                        <div class="panel-stat-info">
                            <span class="panel-stat-value"><?= $totalOrders ?></span>
                            <span class="panel-stat-label">Toplam Sipariş</span>
                        </div>
                    </div>
                    <div class="panel-stat-card">
                        <div class="panel-stat-icon stat-amber"><?= icon('clock', 22) ?></div>
                        <div class="panel-stat-info">
                            <span class="panel-stat-value"><?= $pendingOrders ?></span>
                            <span class="panel-stat-label">Bekleyen Sipariş</span>
                        </div>
                    </div>
                    <div class="panel-stat-card">
                        <div class="panel-stat-icon stat-green"><?= icon('check-circle', 22) ?></div>
                        <div class="panel-stat-info">
                            <span class="panel-stat-value"><?= $completedOrders ?></span>
                            <span class="panel-stat-label">Tamamlanan</span>
                        </div>
                    </div>
                    <div class="panel-stat-card">
                        <div class="panel-stat-icon stat-red"><?= icon('headphones', 22) ?></div>
                        <div class="panel-stat-info">
                            <span class="panel-stat-value"><?= $openTickets ?></span>
                            <span class="panel-stat-label">Açık Destek</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="panel-quick-actions">
                    <a href="/kategoriler" class="panel-action-card">
                        <div class="panel-action-icon" style="background: var(--color-soft-blue); color: var(--color-blue);"><?= icon('shopping-cart', 22) ?></div>
                        <span>Yeni Sipariş Ver</span>
                    </a>
                    <a href="/siparislerim" class="panel-action-card">
                        <div class="panel-action-icon" style="background: var(--color-soft-green); color: var(--color-green);"><?= icon('package', 22) ?></div>
                        <span>Siparişlerimi Gör</span>
                    </a>
                    <a href="/destek/yeni" class="panel-action-card">
                        <div class="panel-action-icon" style="background: var(--color-soft-amber); color: var(--color-amber);"><?= icon('headphones', 22) ?></div>
                        <span>Destek Talebi Aç</span>
                    </a>
                    <a href="/hesabim#profil" class="panel-action-card">
                        <div class="panel-action-icon" style="background: var(--color-soft-red); color: var(--color-red);"><?= icon('user', 22) ?></div>
                        <span>Profilimi Düzenle</span>
                    </a>
                </div>

                <!-- Two column: Recent Orders & Recent Tickets -->
                <div class="panel-grid-2">
                    <!-- Recent Orders -->
                    <div class="panel-card">
                        <div class="panel-card-header">
                            <h3><?= icon('package', 18) ?> Son Siparişler</h3>
                            <a href="/siparislerim" class="panel-card-link">Tümünü Gör <?= icon('arrow-right', 14) ?></a>
                        </div>
                        <div class="panel-card-body">
                            <?php if (empty($recentOrders)): ?>
                            <div class="panel-empty-sm">
                                <?= icon('package', 32) ?>
                                <p>Henüz siparişiniz yok</p>
                                <a href="/kategoriler" class="btn btn-primary btn-sm"><?= icon('shopping-cart', 14) ?> Hizmetleri İncele</a>
                            </div>
                            <?php else: ?>
                            <div class="panel-list">
                                <?php foreach (array_slice($recentOrders, 0, 5) as $o): ?>
                                <a href="/siparis/<?= $o['id'] ?>" class="panel-list-item">
                                    <div class="panel-list-left">
                                        <span class="panel-list-title">#<?= e($o['order_number']) ?></span>
                                        <span class="panel-list-meta"><?= formatDate($o['created_at'], 'd M Y') ?></span>
                                    </div>
                                    <div class="panel-list-right">
                                        <span class="panel-list-amount"><?= money($o['total_amount']) ?></span>
                                        <span class="badge badge-<?= orderStatusColor($o['order_status']) ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Tickets -->
                    <div class="panel-card">
                        <div class="panel-card-header">
                            <h3><?= icon('headphones', 18) ?> Son Destek Talepleri</h3>
                            <a href="/destek" class="panel-card-link">Tümünü Gör <?= icon('arrow-right', 14) ?></a>
                        </div>
                        <div class="panel-card-body">
                            <?php if (empty($recentTickets)): ?>
                            <div class="panel-empty-sm">
                                <?= icon('headphones', 32) ?>
                                <p>Henüz destek talebiniz yok</p>
                                <a href="/destek/yeni" class="btn btn-outline btn-sm"><?= icon('plus', 14) ?> Talep Oluştur</a>
                            </div>
                            <?php else: ?>
                            <div class="panel-list">
                                <?php foreach ($recentTickets as $t): ?>
                                <a href="/destek/<?= $t['id'] ?>" class="panel-list-item">
                                    <div class="panel-list-left">
                                        <span class="panel-list-title"><?= e($t['subject']) ?></span>
                                        <span class="panel-list-meta">#<?= e($t['ticket_number']) ?> · <?= formatDate($t['updated_at'], 'd M') ?></span>
                                    </div>
                                    <div class="panel-list-right">
                                        <span class="badge badge-<?= $t['status'] === 'closed' ? 'default' : ($t['status'] === 'admin_reply' ? 'success' : ($t['status'] === 'open' ? 'info' : 'warning')) ?>"><?= e(supportStatusLabel($t['status'])) ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div id="profil" class="panel-card">
                    <div class="panel-card-header">
                        <h3><?= icon('user', 18) ?> Profil Bilgileri</h3>
                    </div>
                    <div class="panel-card-body">
                        <form method="POST" action="/hesabim/guncelle" class="panel-form">
                            <?= csrfField() ?>
                            <div class="panel-form-row">
                                <div class="form-group">
                                    <label for="profileName">Ad Soyad</label>
                                    <input type="text" name="name" id="profileName" class="form-control" value="<?= e($user['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="profilePhone">Telefon</label>
                                    <input type="tel" name="phone" id="profilePhone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="05XX XXX XX XX">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="profileEmail">E-posta</label>
                                <input type="email" id="profileEmail" class="form-control" value="<?= e($user['email']) ?>" disabled>
                                <span class="form-hint"><?= icon('lock', 12) ?> E-posta adresi değiştirilemez</span>
                            </div>
                            <div class="panel-form-actions">
                                <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Bilgilerimi Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Section -->
                <div id="sifre" class="panel-card">
                    <div class="panel-card-header">
                        <h3><?= icon('lock', 18) ?> Şifre Değiştir</h3>
                    </div>
                    <div class="panel-card-body">
                        <form method="POST" action="/hesabim/sifre-degistir" class="panel-form">
                            <?= csrfField() ?>
                            <div class="form-group">
                                <label for="currentPass">Mevcut Şifre</label>
                                <input type="password" name="current_password" id="currentPass" class="form-control" required placeholder="Mevcut şifrenizi girin">
                            </div>
                            <div class="panel-form-row">
                                <div class="form-group">
                                    <label for="newPass">Yeni Şifre</label>
                                    <input type="password" name="new_password" id="newPass" class="form-control" required placeholder="En az 6 karakter">
                                </div>
                                <div class="form-group">
                                    <label for="newPassConfirm">Yeni Şifre Tekrar</label>
                                    <input type="password" name="new_password_confirmation" id="newPassConfirm" class="form-control" required placeholder="Tekrar girin">
                                </div>
                            </div>
                            <div class="panel-form-actions">
                                <button type="submit" class="btn btn-primary"><?= icon('lock', 16) ?> Şifreyi Güncelle</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- WhatsApp Support Box -->
                <?php if (setting('site_whatsapp')): ?>
                <div class="panel-whatsapp-box">
                    <div class="panel-whatsapp-content">
                        <?= icon('whatsapp', 28) ?>
                        <div>
                            <strong>Yardıma mı ihtiyacınız var?</strong>
                            <p>WhatsApp üzerinden bize ulaşabilirsiniz. Destek ekibimiz en kısa sürede size dönüş yapacaktır.</p>
                        </div>
                    </div>
                    <a href="https://wa.me/<?= e(setting('site_whatsapp')) ?>" target="_blank" rel="noopener" class="btn btn-success btn-sm"><?= icon('send', 14) ?> WhatsApp Destek</a>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
