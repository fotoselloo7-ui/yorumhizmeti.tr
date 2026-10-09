<?php $isEdit = !empty($category); ?>
<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/kategoriler" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= $isEdit ? icon('edit', 22) . ' Kategori Düzenle' : icon('plus', 22) . ' Kategori Ekle' ?></h2>
    </div>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/kategori/' . $category['id'] . '/guncelle' : '/admin/kategori/kaydet' ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="adm-form-layout">
        <!-- Main Content -->
        <div class="adm-form-main">
            <!-- General Info -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('grid', 18) ?> Genel Bilgiler</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Kategori Adı</label>
                        <input type="text" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($category['slug'] ?? '') ?>" placeholder="Otomatik oluşturulur">
                    </div>
                    <div class="form-group">
                        <label>Üst Kategori</label>
                        <select name="parent_id" class="form-control">
                            <option value="">Ana Kategori</option>
                            <?php foreach ($parents as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($category['parent_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Kategori açıklaması..."><?= e($category['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>İkon</label>
                        <select name="icon_key" class="form-control">
                            <?php foreach ($icons as $ic): ?>
                            <option value="<?= $ic ?>" <?= ($category['icon_key'] ?? 'package') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Image & Status -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('image', 18) ?> Görsel & Durum</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Görsel</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Görsel Alt Metni</label>
                        <input type="text" name="image_alt" class="form-control" value="<?= e($category['image_alt'] ?? '') ?>" placeholder="SEO için alt etiket">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Durum</label>
                            <select name="status" class="form-control">
                                <option value="active" <?= ($category['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sıra</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $category['sort_order'] ?? 0 ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('search', 18) ?> SEO Ayarları</h3></div>
                <div class="adm-card-body">
                    <?php if (!empty($seoResult)): ?>
                    <div class="adm-seo-score-box">
                        <div class="seo-score-circle <?= $seoResult['color'] ?>"><?= $seoResult['score'] ?></div>
                        <div>
                            <div class="font-semibold"><?= $seoResult['label'] ?></div>
                            <div class="text-xs text-secondary">SEO Puanı</div>
                        </div>
                    </div>
                    <?php if (!empty($seoResult['issues'])): ?>
                    <div class="adm-seo-issues">
                        <?php foreach ($seoResult['issues'] as $issue): ?>
                        <div class="adm-seo-issue"><?= icon('alert-circle', 14) ?> <?= e($issue) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>SEO Başlığı</label>
                        <input type="text" name="seo_title" class="form-control" value="<?= e($category['seo_title'] ?? '') ?>" placeholder="Sayfa başlığı...">
                    </div>
                    <div class="form-group">
                        <label>Meta Açıklama</label>
                        <textarea name="seo_description" class="form-control" rows="2" placeholder="Arama motorlarında görünen açıklama..."><?= e($category['seo_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Odak Anahtar Kelime</label>
                        <input type="text" name="seo_focus_keyword" class="form-control" value="<?= e($category['seo_focus_keyword'] ?? '') ?>" placeholder="Ana hedef kelime...">
                    </div>
                </div>
            </div>
            <?php $nvSeoIsCategory = !preg_match('/(yaz[iı]l[iı]m|haz[iı]r.script|software|cms|web.site|tema)/iu', (string)($category['slug']??'').' '.(string)($category['name']??'')); require BASE_PATH.'/resources/views/admin/partials/netvera-seo.php'; ?>
        </div>

        <!-- Sidebar -->
        <div class="adm-form-side">
            <div class="adm-card" style="position: sticky; top: 76px;">
                <div class="adm-card-body">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('save', 18) ?> <?= $isEdit ? 'Güncelle' : 'Kaydet' ?></button>
                    <a href="/admin/kategoriler" class="btn btn-outline btn-block btn-sm" style="margin-top: var(--space-3);"><?= icon('x', 14) ?> Vazgeç</a>
                </div>
            </div>
        </div>
    </div>
</form>
