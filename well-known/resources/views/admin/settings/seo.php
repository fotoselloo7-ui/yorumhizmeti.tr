<div class="adm-page-top">
    <div>
        <h2><?= icon('search', 24) ?> SEO Merkezi</h2>
    </div>
</div>

<div class="adm-card nv-brand-seo-overview" style="margin-bottom:20px">
    <div class="adm-card-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <h3><?= icon('building-2',18) ?> NetVera Kurumsal SEO / GEO / AIO</h3>
        <a class="btn btn-primary btn-sm" href="/admin/site-ayarlari"><?= icon('settings',15) ?> Kurumsal Ayarları Düzenle</a>
    </div>
    <div class="adm-card-body">
        <div class="nv-brand-seo-grid">
            <div><span>Kurumsal SEO Başlığı</span><strong><?= e(setting('default_seo_title')) ?></strong></div>
            <div><span>Marka Konumlandırması</span><strong><?= e(setting('brand_positioning')) ?></strong></div>
            <div><span>Kuruluş Türü</span><strong><?= e(setting('seo_org_type')) ?></strong></div>
            <div><span>GEO Kurumsal Tanımı</span><p><?= e(setting('seo_geo_summary')) ?></p></div>
            <div><span>Odak Sektörler</span><p><?= e(setting('seo_entity_topics')) ?></p></div>
            <div><span>AIO Ana Soru</span><p><?= e(setting('seo_home_question')) ?></p></div>
        </div>
    </div>
</div>
<style>
.nv-brand-seo-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.nv-brand-seo-grid>div{border:1px solid #e7eaf2;border-radius:12px;padding:15px;min-width:0;background:#fff}
.nv-brand-seo-grid span{display:block;font-size:11px;color:#7b859e;font-weight:700;margin-bottom:7px}
.nv-brand-seo-grid strong{font-size:13px;line-height:1.6;color:#1a2d50;overflow-wrap:anywhere}
.nv-brand-seo-grid p{font-size:12px;line-height:1.65;color:#53627e;margin:0;overflow-wrap:anywhere}
@media(max-width:650px){.nv-brand-seo-grid{grid-template-columns:1fr}}
</style>

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
            
        </div>
    </div>

    <div class="adm-stat-card" style="cursor: pointer;" onclick="window.open('/robots.txt', '_blank')">
        <div class="adm-stat-icon" style="color: var(--color-amber); background: rgba(245,158,11,0.1);">
            <?= icon('file-text', 24) ?>
        </div>
        <div class="adm-stat-info">
            <h3>robots.txt</h3>
            
        </div>
    </div>
</div>

<div class="adm-form-layout" style="grid-template-columns:minmax(0,1fr)">
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
    
</div>
