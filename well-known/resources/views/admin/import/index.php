<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/paketler" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Paketlere Dön</a>
        <h2><?= icon('upload', 24) ?> CSV / Excel İçe Aktar</h2>
    </div>
    <div style="display:flex; gap: 8px;">
        <a href="/admin/import/bos-sablon" class="btn btn-outline btn-sm"><?= icon('download', 14) ?> Boş CSV Şablonu İndir</a>
        <a href="/admin/import/ornek-sablon" class="btn btn-outline btn-sm"><?= icon('download', 14) ?> Örnek Paket CSV İndir</a>
        <a href="/admin/kategoriler/export" class="btn btn-primary btn-sm"><?= icon('download', 14) ?> Kategori ID CSV İndir</a>
    </div>
</div>

<?php if (isset($importResult)): ?>
<div style="margin-bottom: var(--space-6);">
    <h3 style="margin-bottom: var(--space-4);"><?= icon('check-circle', 18) ?> İçe Aktarım Sonuçları</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-4); margin-bottom: var(--space-4);">
        <div class="adm-card" style="border-left: 4px solid var(--color-green);">
            <div class="adm-card-body">
                <div class="text-sm text-secondary">Başarıyla Eklenen/Güncellenen</div>
                <div style="font-size: 24px; font-weight: bold; color: var(--color-green);"><?= $importResult['success'] ?></div>
            </div>
        </div>
        <div class="adm-card" style="border-left: 4px solid var(--color-blue);">
            <div class="adm-card-body">
                <div class="text-sm text-secondary">Atlanan Paket Sayısı</div>
                <div style="font-size: 24px; font-weight: bold; color: var(--color-blue);"><?= $importResult['skipped'] ?></div>
            </div>
        </div>
        <div class="adm-card" style="border-left: 4px solid var(--color-red);">
            <div class="adm-card-body">
                <div class="text-sm text-secondary">Hatalı Satır Sayısı</div>
                <div style="font-size: 24px; font-weight: bold; color: var(--color-red);"><?= count($importResult['errors']) ?></div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($importResult['errors'])): ?>
    <div class="adm-card">
        <div class="adm-card-header" style="background: rgba(220, 38, 38, 0.1); border-bottom: 1px solid rgba(220, 38, 38, 0.2); color: var(--color-red);">
            <h4 style="margin: 0; display:flex; align-items:center; gap: 8px;"><?= icon('alert-triangle', 18) ?> Hata Detayları</h4>
        </div>
        <div class="adm-card-body" style="padding: 0;">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th width="80">Satır</th>
                        <th>Paket Adı</th>
                        <th>Hata Nedeni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($importResult['errors'] as $err): ?>
                    <tr>
                        <td class="font-semibold">Satır <?= $err['row'] ?></td>
                        <td><?= e($err['name'] ?? '-') ?></td>
                        <td style="color: var(--color-red);"><?= e($err['error']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="adm-import-grid" style="grid-template-columns:minmax(0,760px)">
    <!-- Upload Form -->
    <div class="adm-card">
        <div class="adm-card-header"><h3><?= icon('upload', 18) ?> Dosya Yükle</h3></div>
        <div class="adm-card-body">
            <form method="POST" action="/admin/import/islem" enctype="multipart/form-data">
                <?= csrfField() ?>
                <div class="adm-upload-zone" id="uploadZone" onclick="document.getElementById('csvFileInput').click()">
                    <div class="adm-upload-icon"><?= icon('upload', 32) ?></div>
                    <div class="adm-upload-text">
                        <strong>Dosya seçin veya sürükleyin</strong>
                        <span class="text-xs text-secondary">Kabul edilen formatlar: .csv, .txt</span>
                    </div>
                    <div class="adm-upload-filename" id="uploadFileName" style="display:none;"></div>
                    <input type="file" name="csv_file" id="csvFileInput" accept=".csv,.txt" required style="display:none;" onchange="showFileName(this)">
                </div>
                <div class="adm-featured-check" style="margin-top: var(--space-4);">
                    <label>
                        <input type="checkbox" name="update_duplicate" value="1">
                        <span>Aynı slug varsa güncelle</span>
                    </label>
                    
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: var(--space-4);"><?= icon('upload', 16) ?> İçe Aktar</button>
            </form>
        </div>
    </div>


</div>

<script>
function showFileName(input) {
    var zone = document.getElementById('uploadZone');
    var nameEl = document.getElementById('uploadFileName');
    if (input.files && input.files[0]) {
        nameEl.textContent = input.files[0].name;
        nameEl.style.display = 'block';
        zone.classList.add('has-file');
    }
}
</script>
