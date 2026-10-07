<div class="adm-page-top">
    <div>
        <h2><?= icon('grid', 24) ?> Kategoriler</h2>
        <p class="text-sm text-secondary">Hizmet kategorilerinizi yönetin.</p>
    </div>
    <div style="display:flex; gap: 8px;">
        <form method="POST" action="/admin/kategoriler/varsayilan-kur" style="margin:0;" onsubmit="return confirm('Varsayılan kategorileri kurmak istediğinize emin misiniz? (Var olanlar silinmez)')">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-secondary btn-sm"><?= icon('layers', 16) ?> Varsayılanları Kur</button>
        </form>
        <a href="/admin/kategoriler/export" class="btn btn-outline btn-sm"><?= icon('download', 16) ?> Kategori ID CSV İndir</a>
        <a href="/admin/kategori/ekle" class="btn btn-primary btn-sm"><?= icon('plus', 16) ?> Kategori Ekle</a>
    </div>
</div>

<?php if (empty($categories)): ?>
<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('grid', 32) ?><p>Henüz kategori eklenmemiş.</p></div>
    </div>
</div>
<?php else: ?>

<form method="POST" action="/admin/kategoriler/toplu-islem" id="bulkCatForm">
<?= csrfField() ?>
<div class="adm-bulk-actions" id="bulkCatActions" style="display:none; padding: 12px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 6px; margin-bottom: 16px; align-items: center; gap: 12px;">
    <span class="text-sm font-semibold"><span id="selectedCatCount">0</span> kategori seçildi</span>
    <select name="action" class="form-control form-control-sm" style="width: auto;" required>
        <option value="">Toplu İşlem Seç...</option>
        <option value="active">Aktif Et</option>
        <option value="inactive">Pasife Al</option>
        <option value="delete">Sil (Pasife Alır/Siler)</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Seçili kategoriler için bu işlemi yapmak istediğinize emin misiniz?')">Uygula</button>
</div>

<!-- Desktop Table -->
<div class="adm-card adm-cat-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="selectAllCats" onchange="toggleAllCats(this)"></th>
                    <th>ID</th>
                    <th>İkon</th>
                    <th>Kategori Adı</th>
                    <th>Slug</th>
                    <th>Paket</th>
                    <th>SEO</th>
                    <th>Durum</th>
                    <th>Sıra</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?= $cat['id'] ?>" class="cat-checkbox" onchange="updateCatBulkActions()"></td>
                    <td class="text-sm text-secondary">#<?= $cat['id'] ?></td>
                    <td>
                        <div class="adm-icon-cell"><?= icon($cat['icon_key'] ?? 'package', 18) ?></div>
                    </td>
                    <td class="font-semibold">
                        <?php if (!empty($cat['parent_id'])): ?>
                            <span class="text-secondary" style="margin-right:4px;">└</span>
                        <?php endif; ?>
                        <?= e($cat['name']) ?>
                        <?php if (!empty($cat['parent_id'])): ?>
                            <small class="text-xs text-secondary">(Alt Kategori)</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-xs text-secondary"><?= e($cat['slug']) ?></td>
                    <td>
                        <span class="adm-count-badge"><?= $cat['package_count'] ?></span>
                    </td>
                    <td>
                        <?php $seoScore = $cat['seo_score'] ?? 0; ?>
                        <span class="adm-seo-pill <?= $seoScore >= 80 ? 'seo-good' : ($seoScore >= 50 ? 'seo-ok' : 'seo-bad') ?>"><?= $seoScore ?></span>
                    </td>
                    <td>
                        <span class="status-badge <?= $cat['status'] === 'active' ? 'success' : 'default' ?>"><?= $cat['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                    </td>
                    <td class="text-sm text-secondary"><?= $cat['sort_order'] ?></td>
                    <td>
                        <div class="adm-actions">
                            <a href="/admin/kategori/<?= $cat['id'] ?>/duzenle" class="adm-action-btn" title="Düzenle"><?= icon('edit', 14) ?></a>
                            <form method="POST" action="/admin/kategori/<?= $cat['id'] ?>/sil" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"><?= csrfField() ?><button class="adm-action-btn adm-action-danger" title="Sil"><?= icon('trash', 14) ?></button></form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-cat-mobile">
    <div style="padding: 12px; display:flex; align-items:center; gap: 8px; border-bottom: 1px solid var(--color-border);">
        <input type="checkbox" id="selectAllCatsMobile" onchange="toggleAllCats(this)"> 
        <label for="selectAllCatsMobile" class="text-sm font-semibold">Tümünü Seç</label>
    </div>
    <?php foreach ($categories as $cat): ?>
    <div class="adm-order-card-m">
        <div class="adm-order-card-m-top">
            <div class="flex-center gap-2">
                <input type="checkbox" name="ids[]" value="<?= $cat['id'] ?>" class="cat-checkbox" onchange="updateCatBulkActions()">
                <div class="adm-icon-cell"><?= icon($cat['icon_key'] ?? 'package', 16) ?></div>
                <span class="font-semibold"><?= e($cat['name']) ?></span>
            </div>
            <span class="status-badge <?= $cat['status'] === 'active' ? 'success' : 'default' ?>"><?= $cat['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">ID</span>
                <span class="text-sm font-semibold">#<?= $cat['id'] ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Tip</span>
                <span class="text-xs"><?= empty($cat['parent_id']) ? 'Ana Kategori' : 'Alt Kategori' ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Slug</span>
                <span class="text-xs text-secondary"><?= e($cat['slug']) ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Paket Sayısı</span>
                <span class="adm-count-badge"><?= $cat['package_count'] ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">SEO</span>
                <?php $seoScore = $cat['seo_score'] ?? 0; ?>
                <span class="adm-seo-pill <?= $seoScore >= 80 ? 'seo-good' : ($seoScore >= 50 ? 'seo-ok' : 'seo-bad') ?>"><?= $seoScore ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Sıra</span>
                <span class="text-sm"><?= $cat['sort_order'] ?></span>
            </div>
        </div>
        <div class="adm-card-m-actions">
            <a href="/admin/kategori/<?= $cat['id'] ?>/duzenle" class="adm-order-card-m-action" style="border-right: 1px solid var(--color-border);"><?= icon('edit', 14) ?> Düzenle</a>
            <form method="POST" action="/admin/kategori/<?= $cat['id'] ?>/sil" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')" style="flex:1;margin:0;">
                <?= csrfField() ?>
                <button class="adm-order-card-m-action" style="width:100%;color:var(--color-red);border:none;background:none;cursor:pointer;font-family:var(--font-family);"><?= icon('trash', 14) ?> Sil</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</form>

<script>
function toggleAllCats(source) {
    const checkboxes = document.querySelectorAll('.cat-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    // sync both selectAll checkboxes
    document.getElementById('selectAllCats').checked = source.checked;
    if (document.getElementById('selectAllCatsMobile')) {
        document.getElementById('selectAllCatsMobile').checked = source.checked;
    }
    updateCatBulkActions();
}

function updateCatBulkActions() {
    const checkedCount = document.querySelectorAll('.cat-checkbox:checked').length;
    const bulkBar = document.getElementById('bulkCatActions');
    const countSpan = document.getElementById('selectedCatCount');
    
    if (checkedCount > 0) {
        bulkBar.style.display = 'flex';
        countSpan.textContent = checkedCount;
    } else {
        bulkBar.style.display = 'none';
    }
}
</script>
<?php endif; ?>
