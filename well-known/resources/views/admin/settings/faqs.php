<div class="adm-page-top">
    <div>
        <h2><?= icon('help-circle', 24) ?> SSS Yönetimi</h2>
    </div>
</div>

<div class="adm-import-grid">
    <!-- Form Side -->
    <div>
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('plus', 16) ?> Yeni Soru Ekle</h3>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="/admin/sss/kaydet">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Soru <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control" required placeholder="Müşterilerin sık sorduğu soru...">
                    </div>
                    <div class="form-group">
                        <label>Cevap (HTML) <span class="text-danger">*</span></label>
                        <textarea name="answer" class="form-control" rows="5" required placeholder="Sorunun cevabı..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group mb-0">
                            <label>Durum</label>
                            <select name="status" class="form-control">
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label>Sıralama</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <button type="submit" class="adm-action-btn adm-btn-save" style="width: 100%; justify-content: center; margin-top: 1.5rem;">
                        <?= icon('plus', 16) ?> Ekle
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Table Side -->
    <div>
        <div class="adm-card">
            <div class="adm-card-body" style="padding: 0;">
                <?php if (empty($faqs)): ?>
                    <div class="adm-empty-sm" style="padding: var(--space-6);"><?= icon('help-circle', 32) ?><p>Henüz SSS eklenmemiş.</p></div>
                <?php else: ?>
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Soru</th>
                                <th>Sıra</th>
                                <th>Durum</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faqs as $f): ?>
                            <tr>
                                <td>
                                    <div class="font-semibold" style="line-height: 1.4;"><?= e($f['question']) ?></div>
                                    <div class="text-xs text-secondary" style="margin-top: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= strip_tags($f['answer']) ?></div>
                                </td>
                                <td><span class="adm-cat-badge"><?= $f['sort_order'] ?></span></td>
                                <td><span class="status-badge <?= ($f['status'] ?? 'active') === 'active' ? 'success' : 'default' ?>"><?= ($f['status'] ?? 'active') === 'active' ? 'Aktif' : 'Pasif' ?></span></td>
                                <td>
                                    <form method="POST" action="/admin/sss/<?= $f['id'] ?>/sil" onsubmit="return confirm('Bu soruyu silmek istediğinize emin misiniz?')">
                                        <?= csrfField() ?>
                                        <button class="adm-action-btn adm-action-danger" title="Sil"><?= icon('trash', 14) ?></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Mobile Cards -->
                    <div class="adm-mobile-only" style="display: none;">
                        <?php foreach ($faqs as $f): ?>
                        <div class="adm-order-card-m" style="margin-bottom: var(--space-4);">
                            <div class="adm-order-card-m-top">
                                <span class="font-semibold" style="line-height:1.3; margin-right:8px;"><?= e($f['question']) ?></span>
                                <span class="status-badge <?= ($f['status'] ?? 'active') === 'active' ? 'success' : 'default' ?>"><?= ($f['status'] ?? 'active') === 'active' ? 'Aktif' : 'Pasif' ?></span>
                            </div>
                            <div class="adm-order-card-m-body">
                                <div class="adm-order-card-m-row">
                                    <span class="adm-order-card-m-label">Cevap Özeti</span>
                                    <span class="text-xs text-secondary" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"><?= strip_tags($f['answer']) ?></span>
                                </div>
                                <div class="adm-order-card-m-row">
                                    <span class="adm-order-card-m-label">Sıra</span>
                                    <span class="adm-cat-badge"><?= $f['sort_order'] ?></span>
                                </div>
                            </div>
                            <div class="adm-card-m-actions">
                                <form method="POST" action="/admin/sss/<?= $f['id'] ?>/sil" onsubmit="return confirm('Silmek istediğinize emin misiniz?')" style="width:100%; margin:0;">
                                    <?= csrfField() ?>
                                    <button class="adm-order-card-m-action" style="width:100%;color:var(--color-red);border:none;background:none;justify-content:center;"><?= icon('trash', 14) ?> Sil</button>
                                </form>
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
    </div>
</div>
