<div class="adm-page-top">
    <div>
        <h2><?= icon('layout', 24) ?> Sayfalar</h2>
        <p class="text-sm text-secondary">Kurumsal sayfalarınızı (Hakkımızda, KVKK vb.) yönetin.</p>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body" style="padding: 0;">
        <?php if (empty($pages)): ?>
            <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('layout', 32) ?><p>Henüz sayfa oluşturulmamış.</p></div>
        <?php else: ?>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>URL</th>
                        <th>SEO</th>
                        <th>Durum</th>
                        <th>Son Güncelleme</th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td><div class="font-semibold"><?= e($p['title']) ?></div></td>
                        <td class="text-xs text-secondary">/sayfa/<?= e($p['slug']) ?></td>
                        <td>
                            <?php $seoScore = $p['seo_score'] ?? 0; ?>
                            <span class="adm-seo-pill <?= $seoScore >= 80 ? 'seo-good' : ($seoScore >= 50 ? 'seo-ok' : 'seo-bad') ?>"><?= $seoScore ?></span>
                        </td>
                        <td><span class="status-badge <?= $p['status'] === 'active' ? 'success' : 'default' ?>"><?= $p['status'] === 'active' ? 'Yayında' : 'Pasif' ?></span></td>
                        <td class="text-xs text-secondary"><?= formatDate($p['updated_at'] ?? $p['created_at']) ?></td>
                        <td>
                            <div class="adm-actions">
                                <a href="/admin/sayfa/<?= $p['id'] ?>/duzenle" class="adm-action-btn" title="Düzenle"><?= icon('edit', 14) ?></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Mobile Cards -->
            <div class="adm-mobile-only" style="display: none;">
                <?php foreach ($pages as $p): ?>
                <div class="adm-order-card-m" style="margin-bottom: var(--space-4);">
                    <div class="adm-order-card-m-top">
                        <span class="font-semibold"><?= e($p['title']) ?></span>
                        <span class="status-badge <?= $p['status'] === 'active' ? 'success' : 'default' ?>"><?= $p['status'] === 'active' ? 'Yayında' : 'Pasif' ?></span>
                    </div>
                    <div class="adm-order-card-m-body">
                        <div class="adm-order-card-m-row">
                            <span class="adm-order-card-m-label">URL</span>
                            <span class="text-xs text-secondary">/sayfa/<?= e($p['slug']) ?></span>
                        </div>
                        <div class="adm-order-card-m-row">
                            <span class="adm-order-card-m-label">Tarih</span>
                            <span class="text-xs text-secondary"><?= formatDate($p['updated_at'] ?? $p['created_at']) ?></span>
                        </div>
                    </div>
                    <div class="adm-card-m-actions">
                        <a href="/admin/sayfa/<?= $p['id'] ?>/duzenle" class="adm-order-card-m-action" style="width:100%; justify-content:center;"><?= icon('edit', 14) ?> Düzenle</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <style>
            @media (max-width: 768px) {
                .adm-table { display: none; }
                .adm-mobile-only { display: block !important; }
            }
            </style>
        <?php endif; ?>
    </div>
</div>
