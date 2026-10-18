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
<section class="adm-card" aria-labelledby="technical-seo-heading">
 <div class="adm-card-header"><h3 id="technical-seo-heading"><?= icon('shield-check',18) ?> Teknik SEO / GEO / AIO Sağlığı</h3></div>
 <div class="adm-card-body">
  <p>Salt okunur teknik kontrol; Google sıralama puanı veya canlı indeks raporu değildir. URL, içerik veya ayarları otomatik değiştirmez.</p>
  <ul class="nv-seo-health"><?php foreach($technicalSeoChecks??[]as $check): ?><li><div><strong><?= e($check['label']) ?></strong><p><?= e($check['detail']) ?></p></div><span class="nv-seo-health-state is-<?= e($check['status']) ?>"><?= $check['status']==='pass'?'Kontrol geçti':'İnceleyin' ?></span></li><?php endforeach; ?></ul>
  <p class="text-xs text-secondary">Canlı indeksleme ve alıntılar: <a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">Google Search Console</a> · <a href="https://www.bing.com/webmasters/" target="_blank" rel="noopener noreferrer">Bing Webmaster Tools / AI Performance</a>. Bu hesaplardan veri alınmaz veya dışarı veri gönderilmez.</p>
 </div>
</section>

<div class="adm-stats-grid mb-6">
    <div class="adm-stat-card">
        <div class="adm-stat-icon" style="color: var(--color-blue); background: var(--color-soft-blue);">
            <?= icon('bar-chart-2', 24) ?>
        </div>
        <div class="adm-stat-info">
            <?php $scoreClass = $avgScore >= 80 ? 'var(--color-green)' : ($avgScore >= 50 ? 'var(--color-amber)' : 'var(--color-red)'); ?>
            <h3 style="color: <?= $scoreClass ?>;"><?= $avgScore ?>/100</h3>
            <p>Ortalama Editoryal Kontrol Puanı</p>
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

<p class="text-xs text-secondary">Aşağıdaki puanlar ve başlıklar veritabanındaki editoryal kayıtları gösterir; güncel sayfa çıktısındaki otomatik başlıkları, indeks durumunu veya Google sıralamasını ölçmez.</p>
<div class="adm-form-layout" style="grid-template-columns:minmax(0,1fr)">
    <!-- Left Column: Tables -->
    <div class="adm-form-main" style="display:flex; flex-direction:column; gap:var(--space-6);">
        <!-- Categories -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('folder', 16) ?> Kategoriler</h3>
            </div>
            <div class="adm-card-body adm-table-scroll" style="padding: 0;">
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
            <div class="adm-card-body adm-table-scroll" style="padding: 0;">
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
            <div class="adm-card-body adm-table-scroll" style="padding: 0;">
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
