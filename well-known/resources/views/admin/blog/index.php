<div class="adm-page-top">
    <div>
        <h2><?= icon('file-text', 24) ?> Blog Yazıları</h2>
        <p class="text-sm text-secondary">Sitenizdeki blog içeriklerini yönetin.</p>
    </div>
    <a href="/admin/blog/ekle" class="btn btn-primary btn-sm"><?= icon('plus', 16) ?> Yeni Blog Yazısı</a>
</div>

<?php if (empty($posts)): ?>
<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('file-text', 32) ?><p>Henüz blog yazısı eklenmemiş.</p></div>
    </div>
</div>
<!-- Desktop Table -->
<div class="adm-card adm-blog-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <table class="adm-table">
                <thead>
                    <tr>
                        <th>Yazı</th>
                        <th>Kategori / Etiket</th>
                        <th>Hit</th>
                        <th>Yayın Tarihi</th>
                        <th>Durum</th>
                        <th class="text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($posts)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">
                            <?= icon('file-text', 32, 'mb-2') ?><br>
                            Henüz blog yazısı eklenmemiş.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($posts as $post): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center" style="gap:12px;">
                                <?php if($post['image']): ?>
                                <img src="<?= e(upload_url($post['image'])) ?>" alt="Kapak" style="width:48px; height:48px; border-radius:6px; object-fit:cover;">
                                <?php else: ?>
                                <div style="width:48px; height:48px; border-radius:6px; background:var(--color-light); display:flex; align-items:center; justify-content:center; color:var(--color-text-secondary);">
                                    <?= icon('image', 20) ?>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight:600; color:var(--color-dark); margin-bottom:2px;"><?= e($post['title']) ?></div>
                                    <div class="text-xs text-secondary">/blog/<?= e($post['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:500;"><?= e($post['category_name'] ?? 'Kategorisiz') ?></div>
                            <div class="text-xs text-secondary"><?= $post['tag_count'] > 0 ? $post['tag_count'] . ' Etiket' : 'Etiket Yok' ?></div>
                        </td>
                        <td>
                            <div class="badge badge-light"><?= icon('eye', 12) ?> <?= number_format($post['views'] ?? 0) ?></div>
                        </td>
                        <td>
                            <div style="font-size:13px;"><?= date('d.m.Y H:i', strtotime($post['published_at'])) ?></div>
                        </td>
                        <td>
                            <?php 
                            if($post['status'] === 'active') {
                                echo '<span class="status-badge status-paid">'.icon('check-circle', 14).' Yayında</span>';
                            } elseif($post['status'] === 'scheduled') {
                                echo '<span class="status-badge status-pending">'.icon('time', 14).' Planlandı</span>';
                            } else {
                                echo '<span class="status-badge status-failed">'.icon('edit-2', 14).' Taslak</span>';
                            }
                            ?>
                        </td>
                        <td class="text-right">
                            <div class="action-btns">
                                <a href="/blog/<?= e($post['slug']) ?>" target="_blank" class="btn btn-light btn-sm" title="Görüntüle"><?= icon('external-link', 14) ?></a>
                                <a href="/admin/blog/<?= $post['id'] ?>/duzenle" class="btn btn-light btn-sm" title="Düzenle"><?= icon('edit-2', 14) ?></a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('/admin/blog/<?= $post['id'] ?>/sil')" title="Sil"><?= icon('trash', 14) ?></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-blog-mobile">
    <?php foreach ($posts as $p): ?>
    <div class="adm-order-card-m">
        <div class="adm-order-card-m-top">
            <span class="font-semibold" style="line-height: 1.3; margin-right: var(--space-2);"><?= e($p['title']) ?></span>
            <span class="status-badge <?= $p['status'] === 'active' ? 'success' : ($p['status'] === 'draft' ? 'warning' : 'default') ?>"><?= $p['status'] === 'active' ? 'Yayında' : ($p['status'] === 'draft' ? 'Taslak' : 'Pasif') ?></span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Kategori</span>
                <span class="text-sm"><?= e($p['category_name'] ?? 'Kategorisiz') ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Hit</span>
                <span class="text-sm"><?= number_format($p['views'] ?? 0) ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Tarih</span>
                <span class="text-sm text-secondary"><?= date('d.m.Y H:i', strtotime($p['published_at'])) ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Durum</span>
                <?php 
                if($p['status'] === 'active') echo '<span class="status-badge status-paid">Yayında</span>';
                elseif($p['status'] === 'scheduled') echo '<span class="status-badge status-pending">Planlandı</span>';
                else echo '<span class="status-badge status-failed">Taslak</span>';
                ?>
            </div>
        </div>
        <div class="adm-card-m-actions">
            <a href="/admin/blog/<?= $p['id'] ?>/duzenle" class="adm-order-card-m-action" style="border-right: 1px solid var(--color-border);"><?= icon('edit', 14) ?> Düzenle</a>
            <form method="POST" action="/admin/blog/<?= $p['id'] ?>/sil" onsubmit="return confirm('Bu yazıyı silmek istediğinize emin misiniz?')" style="flex:1;margin:0;"><?= csrfField() ?><button class="adm-order-card-m-action" style="width:100%;color:var(--color-red);border:none;background:none;cursor:pointer;font-family:var(--font-family);"><?= icon('trash', 14) ?> Sil</button></form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
