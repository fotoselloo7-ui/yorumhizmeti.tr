<div class="adm-page-top">
    <div>
        <h2><?= icon('briefcase', 24) ?> Banka Hesapları</h2>
        <p class="text-sm text-secondary">Müşterilerin havale/EFT ödemeleri yapabileceği banka hesaplarını yönetin.</p>
    </div>
</div>

<div class="adm-import-grid">
    <!-- Form Side -->
    <div>
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('plus', 16) ?> Yeni Hesap Ekle</h3>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="/admin/banka-hesabi/kaydet">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Banka Adı <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" required placeholder="Ör: Garanti BBVA">
                    </div>
                    <div class="form-group">
                        <label>Hesap Sahibi <span class="text-danger">*</span></label>
                        <input type="text" name="account_holder" class="form-control" required placeholder="Ör: Ad Soyad Ltd. Şti.">
                    </div>
                    <div class="form-group">
                        <label>IBAN <span class="text-danger">*</span></label>
                        <input type="text" name="iban" class="form-control" required placeholder="TR00 0000 0000 0000 0000 0000 00">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Şube Kodu</label>
                            <input type="text" name="branch_code" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Hesap No</label>
                            <input type="text" name="account_number" class="form-control">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group mb-0">
                            <label>Durum</label>
                            <select name="status" class="form-control">
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label>Sıra</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <button type="submit" class="adm-action-btn adm-btn-save" style="width: 100%; justify-content: center; margin-top: 1.5rem;">
                        <?= icon('plus', 16) ?> Banka Hesabı Ekle
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Table Side -->
    <div>
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('list', 16) ?> Kayıtlı Hesaplar</h3>
            </div>
            <div class="adm-card-body" style="padding: 0;">
                <?php if (empty($accounts)): ?>
                    <div class="adm-empty-sm" style="padding: var(--space-6);"><?= icon('briefcase', 32) ?><p>Henüz banka hesabı eklenmemiş.</p></div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>Banka</th>
                                    <th>IBAN</th>
                                    <th>Durum</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accounts as $a): ?>
                                <tr>
                                    <td>
                                        <div class="font-semibold"><?= e($a['bank_name']) ?></div>
                                        <div class="text-xs text-secondary"><?= e($a['account_holder']) ?></div>
                                    </td>
                                    <td class="text-sm"><?= e($a['iban']) ?></td>
                                    <td><span class="status-badge <?= $a['status'] === 'active' ? 'success' : 'default' ?>"><?= $a['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span></td>
                                    <td>
                                        <form method="POST" action="/admin/banka-hesabi/<?= $a['id'] ?>/sil" onsubmit="return confirm('Bu banka hesabını silmek istediğinize emin misiniz?')">
                                            <?= csrfField() ?>
                                            <button class="adm-action-btn adm-action-danger" title="Sil"><?= icon('trash', 14) ?></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="adm-mobile-only" style="display: none; padding: 15px;">
                        <?php foreach ($accounts as $a): ?>
                        <div class="adm-order-card-m" style="margin-bottom: var(--space-4); background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px;">
                            <div class="adm-order-card-m-top" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                <span class="font-semibold"><?= e($a['bank_name']) ?></span>
                                <span class="status-badge <?= $a['status'] === 'active' ? 'success' : 'default' ?>"><?= $a['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                            </div>
                            <div class="adm-order-card-m-body">
                                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                                    <span class="text-secondary">Hesap Sahibi</span>
                                    <span><?= e($a['account_holder']) ?></span>
                                </div>
                                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                                    <span class="text-secondary">IBAN</span>
                                    <span><?= e($a['iban']) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
