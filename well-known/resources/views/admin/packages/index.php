<div class="adm-page-top">
    <div>
        <h2><?= icon('package', 24) ?> Paketler</h2>
        <p class="text-sm text-secondary">Tüm hizmet paketlerinizi yönetin.</p>
    </div>
    <div class="adm-page-top-badges">
        <a href="/admin/import" class="btn btn-outline btn-sm"><?= icon('upload', 14) ?> Excel/CSV İçe Aktar</a>
        <a href="/admin/paket/ekle" class="btn btn-primary btn-sm"><?= icon('plus', 16) ?> Paket Ekle</a>
    </div>
</div>

<?php if (empty($packages)): ?>
<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('package', 32) ?><p>Henüz paket eklenmemiş.</p></div>
    </div>
</div>
<?php else: ?>

<form method="POST" action="/admin/paketler/toplu-islem" id="bulkPkgForm">
<?= csrfField() ?>
<div class="adm-bulk-actions" id="bulkPkgActions" style="display:none; padding: 12px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 6px; margin-bottom: 16px; align-items: center; gap: 12px;">
    <span class="text-sm font-semibold"><span id="selectedPkgCount">0</span> paket seçildi</span>
    <select name="action" class="form-control form-control-sm" style="width: auto;" id="bulkPkgActionSelect" onchange="toggleCategorySelect()" required>
        <option value="">Toplu İşlem Seç...</option>
        <option value="active">Aktif Et</option>
        <option value="inactive">Pasife Al</option>
        <option value="featured">Öne Çıkar</option>
        <option value="unfeatured">Öne Çıkanlardan Kaldır</option>
        <option value="change_category">Kategoriyi Değiştir</option>
        <option value="delete">Sil (Pasife Alır/Siler)</option>
    </select>
    
    <select name="new_category_id" id="bulkCategorySelect" class="form-control form-control-sm" style="display:none; width: auto;">
        <option value="">Kategori Seçin...</option>
        <?php foreach($categories ?? [] as $c): ?>
            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Seçili paketler için bu işlemi yapmak istediğinize emin misiniz?')">Uygula</button>
</div>

<!-- Desktop Table -->
<div class="adm-card adm-pkg-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="selectAllPkgs" onchange="toggleAllPkgs(this)"></th>
                    <th>Paket Adı</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>İnd. Fiyat</th>
                    <th>Teslimat</th>
                    <th>Adet</th>
                    <th>SEO</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($packages as $pkg): ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?= $pkg['id'] ?>" class="pkg-checkbox" onchange="updatePkgBulkActions()"></td>
                    <td>
                        <div class="adm-pkg-name">
                            <span class="font-semibold"><?= e($pkg['name']) ?></span>
                            <?php if (!empty($pkg['is_featured'])): ?>
                            <span class="adm-featured-badge"><?= icon('star', 10) ?> Öne Çıkan</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="adm-cat-badge"><?= icon($pkg['icon_key'] ?? 'package', 12) ?> <?= e($pkg['category_name'] ?? '-') ?></span>
                    </td>
                    <td class="font-semibold"><?= money($pkg['price']) ?></td>
                    <td>
                        <?php if ($pkg['discount_price']): ?>
                        <span class="font-semibold" style="color: var(--color-green);"><?= money($pkg['discount_price']) ?></span>
                        <?php else: ?>
                        <span class="text-secondary">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-sm"><?= e($pkg['delivery_time'] ?? '-') ?></td>
                    <td class="text-xs text-secondary"><?= $pkg['min_quantity'] ?>-<?= $pkg['max_quantity'] ?></td>
                    <td>
                        <?php $seoScore = $pkg['seo_score'] ?? 0; ?>
                        <span class="adm-seo-pill <?= $seoScore >= 80 ? 'seo-good' : ($seoScore >= 50 ? 'seo-ok' : 'seo-bad') ?>"><?= $seoScore ?></span>
                    </td>
                    <td>
                        <span class="status-badge <?= $pkg['status'] === 'active' ? 'success' : 'default' ?>"><?= $pkg['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                    </td>
                    <td>
                        <div class="adm-actions">
                            <a href="/admin/paket/<?= $pkg['id'] ?>/alanlar" class="adm-action-btn" title="Alanlar"><?= icon('list', 14) ?></a>
                            <a href="/admin/paket/<?= $pkg['id'] ?>/duzenle" class="adm-action-btn" title="Düzenle"><?= icon('edit', 14) ?></a>
                            <form method="POST" action="/admin/paket/<?= $pkg['id'] ?>/sil" onsubmit="return confirm('Bu paketi silmek istediğinize emin misiniz?')"><?= csrfField() ?><button class="adm-action-btn adm-action-danger" title="Sil"><?= icon('trash', 14) ?></button></form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

        </table>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-pkg-mobile">
    <div style="padding: 12px; display:flex; align-items:center; gap: 8px; border-bottom: 1px solid var(--color-border);">
        <input type="checkbox" id="selectAllPkgsMobile" onchange="toggleAllPkgs(this)"> 
        <label for="selectAllPkgsMobile" class="text-sm font-semibold">Tümünü Seç</label>
    </div>
    <?php foreach ($packages as $pkg): ?>
    <div class="adm-order-card-m">
        <div class="adm-order-card-m-top">
            <div style="display:flex; align-items:center; gap: 8px;">
                <input type="checkbox" name="ids[]" value="<?= $pkg['id'] ?>" class="pkg-checkbox" onchange="updatePkgBulkActions()">
                <span class="font-semibold"><?= e($pkg['name']) ?></span>
                <?php if (!empty($pkg['is_featured'])): ?>
                <span class="adm-featured-badge"><?= icon('star', 10) ?></span>
                <?php endif; ?>
            </div>
            <span class="status-badge <?= $pkg['status'] === 'active' ? 'success' : 'default' ?>"><?= $pkg['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Kategori</span>
                <span class="text-sm"><?= e($pkg['category_name'] ?? '-') ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Fiyat</span>
                <div>
                    <strong><?= money($pkg['price']) ?></strong>
                    <?php if ($pkg['discount_price']): ?>
                    <span class="text-xs" style="color: var(--color-green);"> / <?= money($pkg['discount_price']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Teslimat</span>
                <span class="text-sm"><?= e($pkg['delivery_time'] ?? '-') ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">SEO</span>
                <?php $seoScore = $pkg['seo_score'] ?? 0; ?>
                <span class="adm-seo-pill <?= $seoScore >= 80 ? 'seo-good' : ($seoScore >= 50 ? 'seo-ok' : 'seo-bad') ?>"><?= $seoScore ?></span>
            </div>
        </div>
        <div class="adm-card-m-actions">
            <a href="/admin/paket/<?= $pkg['id'] ?>/alanlar" class="adm-order-card-m-action" style="border-right: 1px solid var(--color-border);"><?= icon('list', 14) ?> Alanlar</a>
            <a href="/admin/paket/<?= $pkg['id'] ?>/duzenle" class="adm-order-card-m-action" style="border-right: 1px solid var(--color-border);"><?= icon('edit', 14) ?> Düzenle</a>
            <form method="POST" action="/admin/paket/<?= $pkg['id'] ?>/sil" onsubmit="return confirm('Bu paketi silmek istediğinize emin misiniz?')" style="flex:1;margin:0;"><?= csrfField() ?><button class="adm-order-card-m-action" style="width:100%;color:var(--color-red);border:none;background:none;cursor:pointer;font-family:var(--font-family);"><?= icon('trash', 14) ?> Sil</button></form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</form>

<script>
function toggleAllPkgs(source) {
    const checkboxes = document.querySelectorAll('.pkg-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    document.getElementById('selectAllPkgs').checked = source.checked;
    if (document.getElementById('selectAllPkgsMobile')) {
        document.getElementById('selectAllPkgsMobile').checked = source.checked;
    }
    updatePkgBulkActions();
}

function updatePkgBulkActions() {
    const checkedCount = document.querySelectorAll('.pkg-checkbox:checked').length;
    const bulkBar = document.getElementById('bulkPkgActions');
    const countSpan = document.getElementById('selectedPkgCount');
    
    if (checkedCount > 0) {
        bulkBar.style.display = 'flex';
        countSpan.textContent = checkedCount;
    } else {
        bulkBar.style.display = 'none';
    }
}

function toggleCategorySelect() {
    const action = document.getElementById('bulkPkgActionSelect').value;
    const catSelect = document.getElementById('bulkCategorySelect');
    if (action === 'change_category') {
        catSelect.style.display = 'inline-block';
        catSelect.required = true;
    } else {
        catSelect.style.display = 'none';
        catSelect.required = false;
    }
}
</script>
<?php endif; ?>
