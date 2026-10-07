<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/ana-sayfa" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= icon('edit', 22) ?> Bölüm Düzenle: <?= e($section['title']) ?></h2>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error mb-4">
    <?= icon('alert-circle', 18) ?>
    <span><?= e(is_array($errors) ? implode(', ', $errors) : $errors) ?></span>
</div>
<?php endif; ?>

<form method="POST" action="/admin/ana-sayfa/<?= $section['id'] ?>/guncelle" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="adm-form-layout">
        <!-- Main Content -->
        <div class="adm-form-main">
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('file-text', 18) ?> İçerik Bilgileri</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="title">Başlık</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?= e($section['title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Alt Başlık</label>
                        <input type="text" name="subtitle" id="subtitle" class="form-control" value="<?= e($section['subtitle'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="content">İçerik</label>
                        <textarea name="content" id="content" class="form-control" rows="6"><?= e($section['content'] ?? '') ?></textarea>
                        <span class="form-hint">HTML desteklenir. SEO metin alanı veya hizmet açıklamaları için kullanabilirsiniz.</span>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                        <div class="form-group">
                            <label for="button_text">Buton Metni</label>
                            <input type="text" name="button_text" id="button_text" class="form-control" value="<?= e($section['button_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="button_url">Buton URL</label>
                            <input type="text" name="button_url" id="button_url" class="form-control" value="<?= e($section['button_url'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Extra Data (JSON) -->
            <?php if (in_array($section['section_key'], ['testimonials', 'info_center', 'how_it_works', 'trust'])): ?>
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('database', 18) ?> Ek Veriler (JSON)</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group mb-0">
                        <label for="extra_data">JSON Veri</label>
                        <textarea name="extra_data" id="extra_data" class="form-control" rows="10" style="font-family: monospace; font-size: var(--font-size-sm);"><?= e($section['extra_data'] ?? '[]') ?></textarea>
                        <span class="form-hint">
                            <?php if ($section['section_key'] === 'testimonials'): ?>
                                Format: [{"name":"Ad", "role":"Unvan", "text":"Yorum", "stars":5}]
                            <?php elseif ($section['section_key'] === 'info_center'): ?>
                                Format: [{"title":"Başlık", "desc":"Açıklama", "link":"/blog"}]
                            <?php else: ?>
                                JSON formatında ek veri girebilirsiniz.
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="adm-form-sidebar">
            <button type="submit" class="adm-action-btn adm-btn-save">
                <?= icon('save', 16) ?> Ayarları Kaydet
            </button>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('settings', 16) ?> Genel Ayarlar</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="icon_key">İkon</label>
                        <select name="icon_key" id="icon_key" class="form-control">
                            <?php foreach ($icons as $iconName): ?>
                            <option value="<?= e($iconName) ?>" <?= ($section['icon_key'] ?? '') === $iconName ? 'selected' : '' ?>>
                                <?= e($iconName) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Sıralama</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="<?= (int)($section['sort_order'] ?? 0) ?>">
                    </div>

                    <div class="form-group mb-0">
                        <label for="status">Durum</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active" <?= ($section['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= ($section['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('image', 16) ?> Görsel</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="image">Görsel Yükle</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <?php if (!empty($section['image'])): ?>
                        <div style="margin-top:var(--space-2);">
                            <img src="<?= e(upload_url($section['image'])) ?>" alt="<?= e($section['image_alt'] ?? '') ?>" style="max-width:100%; max-height:120px; border-radius: var(--radius-md); border:1px solid var(--color-border);">
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group mb-0">
                        <label for="image_alt">Görsel Alt Metni</label>
                        <input type="text" name="image_alt" id="image_alt" class="form-control" value="<?= e($section['image_alt'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('search', 16) ?> SEO Ayarları</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group mb-0">
                        <label for="seo_focus_keyword">Odak Anahtar Kelime</label>
                        <input type="text" name="seo_focus_keyword" id="seo_focus_keyword" class="form-control" value="<?= e($section['seo_focus_keyword'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
