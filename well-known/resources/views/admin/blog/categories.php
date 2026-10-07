<div class="adm-page-top">
    <div>
        <h2><?= icon('folder', 24) ?> Blog Kategorileri</h2>
        <p class="text-sm text-secondary">Blog yazılarınızı gruplandırın.</p>
    </div>
</div>

<div class="adm-import-grid">
    <!-- Form Side -->
    <div>
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('plus', 16) ?> Yeni Kategori Ekle</h3>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="/admin/blog-kategorisi/kaydet">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Kategori Adı <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Örn: Rehberler">
                    </div>
                    <div class="form-group">
                        <label>Slug (URL)</label>
                        <input type="text" name="slug" class="form-control" placeholder="Otomatik oluşturulur">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Durum</label>
                            <select name="status" class="form-control">
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sıralama</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <button type="submit" class="adm-action-btn adm-btn-save" style="width: 100%; justify-content: center; margin-top: 1rem;">
                        <?= icon('plus', 16) ?> Kategori Ekle
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Table Side -->
    <div>
        <div class="adm-card">
            <div class="adm-card-body" style="padding: 0;">
                <?php if (empty($categories)): ?>
                    <div class="adm-empty-sm" style="padding: var(--space-6);"><?= icon('folder', 32) ?><p>Henüz kategori eklenmemiş.</p></div>
                <?php else: ?>
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Yazı Sayısı</th>
                                <th>Durum</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $c): ?>
                            <tr>
                                <td>
                                    <div class="font-semibold"><?= e($c['name']) ?></div>
                                    <div class="text-xs text-secondary">/blog/<?= e($c['slug']) ?></div>
                                </td>
                                <td><span class="adm-cat-badge"><?= $c['post_count'] ?> Yazı</span></td>
                                <td><span class="status-badge <?= $c['status'] === 'active' ? 'success' : 'default' ?>"><?= $c['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span></td>
                                <td>
                                    <form method="POST" action="/admin/blog-kategorisi/<?= $c['id'] ?>/sil" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                        <?= csrfField() ?>
                                        <button class="adm-action-btn adm-action-danger" title="Sil"><?= icon('trash', 14) ?></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
