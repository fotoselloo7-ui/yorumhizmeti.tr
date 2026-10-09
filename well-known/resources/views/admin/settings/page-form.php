<div class="adm-page-top">
    <div>
        <h2><?= icon('layout', 24) ?> Sayfa Düzenle: <?= e($page['title']) ?></h2>
    </div>
    <a href="/admin/sayfalar" class="btn btn-light btn-sm"><?= icon('arrow-left', 16) ?> Geri Dön</a>
</div>

<form method="POST" action="/admin/sayfa/<?= $page['id'] ?>/guncelle">
    <?= csrfField() ?>
    
    <div class="adm-form-layout">
        <!-- Main Content -->
        <div class="adm-form-main">
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('file-text', 18) ?> Sayfa İçeriği</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Başlık <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= e($page['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug (URL)</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($page['slug']) ?>">
                    </div>
                    <div class="form-group mb-0">
                        <label>İçerik (HTML)</label>
                        <textarea name="content" class="form-control" rows="20"><?= e($page['content']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="adm-form-sidebar">
            <button type="submit" class="adm-action-btn adm-btn-save">
                <?= icon('save', 16) ?> Değişiklikleri Kaydet
            </button>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('settings', 16) ?> Ayarlar</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group mb-0">
                        <label>Durum</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $page['status'] === 'active' ? 'selected' : '' ?>>Yayında</option>
                            <option value="inactive" <?= $page['status'] === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('search', 16) ?> SEO Ayarları</h3>
                </div>
                <div class="adm-card-body">
                    <!-- Google Snippet Önizleme -->
                    <div style="margin-bottom: var(--space-4);">
                        <div style="font-size: 11px; font-weight: 600; color: var(--color-text-secondary); margin-bottom: 4px; text-transform: uppercase;">Snippet Önizleme</div>
                        <div style="background: #fff; border: 1px solid var(--color-border); border-radius: 4px; padding: 12px;">
                            <div style="font-size: 13px; color: #1a73e8; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" id="seoSnippetTitle"><?= e($page['seo_title'] ?? $page['title']) ?></div>
                            <div style="font-size: 11px; color: #006621; margin-bottom: 4px;" id="seoSnippetUrl">site.com/sayfa/<?= e($page['slug']) ?></div>
                            <div style="font-size: 12px; color: #545454; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" id="seoSnippetDesc"><?= e($page['seo_description'] ?? 'Meta açıklama...') ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>SEO Başlığı</label>
                        <input type="text" name="seo_title" id="seo_title" class="form-control" value="<?= e($page['seo_title'] ?? '') ?>" oninput="updateSnippet()">
                    </div>

                    <div class="form-group mb-0">
                        <label>Meta Açıklama</label>
                        <textarea name="seo_description" id="seo_description" class="form-control" rows="3" oninput="updateSnippet()"><?= e($page['seo_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function updateSnippet() {
    var t = document.getElementById('seo_title');
    var d = document.getElementById('seo_description');
    if (document.getElementById('seoSnippetTitle')) {
        document.getElementById('seoSnippetTitle').textContent = t.value || 'Sayfa Başlığı';
    }
    if (document.getElementById('seoSnippetDesc')) {
        document.getElementById('seoSnippetDesc').textContent = d.value || 'Meta açıklama...';
    }
}
</script>
