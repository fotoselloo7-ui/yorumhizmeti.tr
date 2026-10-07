<div class="adm-page-top">
    <div>
        <h2><?= icon('search', 24) ?> SEO Merkezi</h2>
        <p class="text-sm text-secondary">Arama motoru optimizasyonu, sitemap ve skor analizleri.</p>
    </div>
</div>

<div class="adm-stats-grid mb-6">
    <div class="adm-stat-card">
        <div class="adm-stat-icon" style="color: var(--color-blue); background: var(--color-soft-blue);">
            <?= icon('bar-chart-2', 24) ?>
        </div>
        <div class="adm-stat-info">
            <?php $scoreClass = $avgScore >= 80 ? 'var(--color-green)' : ($avgScore >= 50 ? 'var(--color-amber)' : 'var(--color-red)'); ?>
            <h3 style="color: <?= $scoreClass ?>;"><?= $avgScore ?>/100</h3>
            <p>Ortalama SEO Puanı</p>
        </div>
    </div>
    
    <div class="adm-stat-card" style="cursor: pointer;" onclick="window.open('/sitemap.xml', '_blank')">
        <div class="adm-stat-icon" style="color: var(--color-green); background: var(--color-soft-green);">
            <?= icon('map', 24) ?>
        </div>
        <div class="adm-stat-info">
            <h3>Sitemap XML</h3>
            <p>Görüntülemek için tıklayın</p>
        </div>
    </div>

    <div class="adm-stat-card" style="cursor: pointer;" onclick="window.open('/robots.txt', '_blank')">
        <div class="adm-stat-icon" style="color: var(--color-amber); background: rgba(245,158,11,0.1);">
            <?= icon('file-text', 24) ?>
        </div>
        <div class="adm-stat-info">
            <h3>robots.txt</h3>
            <p>Görüntülemek için tıklayın</p>
        </div>
    </div>
</div>

<div class="adm-form-layout">
    <!-- Left Column: Tables -->
    <div class="adm-form-main" style="display:flex; flex-direction:column; gap:var(--space-6);">
        <!-- Categories -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('folder', 16) ?> Kategoriler</h3>
            </div>
            <div class="adm-card-body" style="padding: 0;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Puan</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                        <tr>
                            <td>
                                <div class="font-semibold"><?= e($c['name']) ?></div>
                                <div class="text-xs text-secondary" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($c['seo_title'] ?: 'SEO Başlığı Yok') ?></div>
                            </td>
                            <td>
                                <?php $sc = ($c['seo_score'] ?? 0); $cl = $sc >= 80 ? 'seo-good' : ($sc >= 50 ? 'seo-ok' : 'seo-bad'); ?>
                                <span class="adm-seo-pill <?= $cl ?>"><?= $sc ?></span>
                            </td>
                            <td><a href="/admin/kategori/<?= $c['id'] ?>/duzenle" class="adm-action-btn"><?= icon('edit', 14) ?></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Packages -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('package', 16) ?> Paketler</h3>
            </div>
            <div class="adm-card-body" style="padding: 0;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Puan</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($packages)): ?>
                            <tr><td colspan="3" class="text-center text-secondary py-4">Aktif paket yok</td></tr>
                        <?php else: ?>
                            <?php foreach ($packages as $p): ?>
                            <tr>
                                <td>
                                    <div class="font-semibold"><?= e($p['name']) ?></div>
                                    <div class="text-xs text-secondary" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($p['seo_title'] ?: 'SEO Başlığı Yok') ?></div>
                                </td>
                                <td>
                                    <?php $sc = ($p['seo_score'] ?? 0); $cl = $sc >= 80 ? 'seo-good' : ($sc >= 50 ? 'seo-ok' : 'seo-bad'); ?>
                                    <span class="adm-seo-pill <?= $cl ?>"><?= $sc ?></span>
                                </td>
                                <td><a href="/admin/paket/<?= $p['id'] ?>/duzenle" class="adm-action-btn"><?= icon('edit', 14) ?></a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Blog Posts -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('file-text', 16) ?> Blog Yazıları</h3>
            </div>
            <div class="adm-card-body" style="padding: 0;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Puan</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr><td colspan="3" class="text-center text-secondary py-4">Aktif yazı yok</td></tr>
                        <?php else: ?>
                            <?php foreach ($posts as $p): ?>
                            <tr>
                                <td>
                                    <div class="font-semibold"><?= e($p['title']) ?></div>
                                    <div class="text-xs text-secondary" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($p['seo_title'] ?: 'SEO Başlığı Yok') ?></div>
                                </td>
                                <td>
                                    <?php $sc = ($p['seo_score'] ?? 0); $cl = $sc >= 80 ? 'seo-good' : ($sc >= 50 ? 'seo-ok' : 'seo-bad'); ?>
                                    <span class="adm-seo-pill <?= $cl ?>"><?= $sc ?></span>
                                </td>
                                <td><a href="/admin/blog/<?= $p['id'] ?>/duzenle" class="adm-action-btn"><?= icon('edit', 14) ?></a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Info & Default Settings -->
    <div class="adm-form-sidebar">
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('info', 16) ?> SEO İpuçları</h3>
            </div>
            <div class="adm-card-body">
                <ul class="text-sm text-secondary" style="list-style: disc; padding-left: 1rem; display: flex; flex-direction: column; gap: 8px;">
                    <li>SEO başlıklarınızın <strong>35-60 karakter</strong> arasında olduğundan emin olun.</li>
                    <li>Meta açıklamaları <strong>120-160 karakter</strong> uzunluğunda tutun.</li>
                    <li>Her sayfa için spesifik <strong>odak anahtar kelime</strong> belirleyin.</li>
                    <li>Sitemap her yeni içerik eklendiğinde otomatik olarak güncellenir.</li>
                </ul>
                <a href="/admin/ayarlar/genel" class="btn btn-outline btn-sm btn-block mt-4"><?= icon('settings', 14) ?> Varsayılan SEO Ayarları</a>
            </div>
        </div>
    </div>
</div>
