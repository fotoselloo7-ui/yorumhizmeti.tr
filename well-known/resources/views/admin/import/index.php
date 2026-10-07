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

<div class="adm-import-grid">
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
                    <div class="form-hint">İşaretlerseniz mevcut paketler güncellenir, yeni eklenmez.</div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: var(--space-4);"><?= icon('upload', 16) ?> İçe Aktar</button>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="adm-card">
        <div class="adm-card-header"><h3><?= icon('info', 18) ?> Kullanım Kılavuzu</h3></div>
        <div class="adm-card-body">
            <div class="adm-import-info">
                <h4>Zorunlu Sütunlar</h4>
                <div class="adm-import-cols">
                    <div class="adm-import-col adm-col-required"><code>name</code> <span>Paket adı</span></div>
                    <div class="adm-import-col adm-col-required"><code>category_id</code> <span>Kategori ID</span></div>
                    <div class="adm-import-col adm-col-required"><code>price</code> <span>Fiyat</span></div>
                </div>

                <h4 style="margin-top: var(--space-5);">Opsiyonel Sütunlar</h4>
                <div class="adm-import-cols">
                    <div class="adm-import-col"><code>slug</code> <span>Paket slug (opsiyonel)</span></div>
                    <div class="adm-import-col"><code>discount_price</code> <span>İndirimli fiyat</span></div>
                    <div class="adm-import-col"><code>short_description</code> <span>Kısa açıklama</span></div>
                    <div class="adm-import-col"><code>description</code> <span>Detay açıklama</span></div>
                    <div class="adm-import-col"><code>delivery_time</code> <span>Teslim süresi</span></div>
                    <div class="adm-import-col"><code>min_quantity</code> <span>Min adet</span></div>
                    <div class="adm-import-col"><code>max_quantity</code> <span>Max adet</span></div>
                    <div class="adm-import-col"><code>status</code> <span>active / inactive</span></div>
                    <div class="adm-import-col"><code>is_featured</code> <span>Öne çıkan (1/0)</span></div>
                    <div class="adm-import-col"><code>sort_order</code> <span>Sıra (sayı)</span></div>
                    <div class="adm-import-col"><code>seo_title</code> <span>SEO Başlık</span></div>
                    <div class="adm-import-col"><code>seo_description</code> <span>SEO Açıklama</span></div>
                    <div class="adm-import-col"><code>focus_keyword</code> <span>SEO Odak Kelime</span></div>
                </div>

                <div class="adm-import-note" style="margin-top: var(--space-5);">
                    <?= icon('alert-circle', 16) ?>
                    <div>
                        <strong>Önemli</strong>
                        <ul style="margin-top: 4px; padding-left: var(--space-4); list-style: disc;">
                            <li>CSV dosyası UTF-8 formatında olmalıdır.</li>
                            <li>İlk satır sütun başlıkları olmalıdır (name, category_id, vs).</li>
                            <li>Virgül veya noktalı virgül ile ayırabilirsiniz.</li>
                            <li>Slug belirtilmezse otomatik üretilir.</li>
                            <li>category_id geçersizse satır atlanır (hata olarak listelenir).</li>
                        </ul>
                    </div>
                </div>

                <div class="adm-import-note" style="margin-top: var(--space-5); background: var(--color-surface); border-color: var(--color-border);">
                    <div style="width: 100%; overflow-x: auto; white-space: nowrap;">
                        <strong style="display:block; margin-bottom: 8px;">Örnek CSV Satırı:</strong>
                        <code style="display:block; padding: 10px; background: #1a1a1a; color: #fff; border-radius: 4px; font-size: 12px;">name,category_id,price,discount_price,short_description,description,delivery_time,min_quantity,max_quantity,status,slug,seo_title,seo_description,focus_keyword<br>Instagram Yorum Paketi - 50,12,149,,50 adet Instagram yorum paketi.,Instagram gönderileriniz için SEO uyumlu açıklamalı yorum hizmeti.,1-3 iş günü,1,10,active,instagram-yorum-paketi-50,Instagram Yorum Paketi,Instagram yorum paketi ile gönderi etkileşiminizi artırın.,instagram yorum paketi</code>
                    </div>
                </div>
            </div>
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
