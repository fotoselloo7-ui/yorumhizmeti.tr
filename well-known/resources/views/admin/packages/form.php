<?php $isEdit = !empty($package); ?>
<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/paketler" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= $isEdit ? icon('edit', 22) . ' Paket Düzenle' : icon('plus', 22) . ' Paket Ekle' ?></h2>
    </div>
    <?php if ($isEdit): ?>
    <a href="/admin/paket/<?= $package['id'] ?>/alanlar" class="btn btn-outline btn-sm"><?= icon('list', 14) ?> Sipariş Alanları</a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/paket/' . $package['id'] . '/guncelle' : '/admin/paket/kaydet' ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="adm-form-layout">
        <!-- Main Content -->
        <div class="adm-form-main">
            <!-- General -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('package', 18) ?> Temel Bilgiler</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Paket Adı</label>
                        <input type="text" name="name" class="form-control" value="<?= e($package['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($package['slug'] ?? '') ?>" placeholder="Otomatik oluşturulur">
                        <div class="form-hint">Boş bırakırsanız isimden otomatik üretilir.</div>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Kategori Seçin</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($package['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kısa Açıklama</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Paket listesinde gösterilecek kısa açıklama..."><?= e($package['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Detay Açıklama (HTML)</label>
                        <textarea name="description" class="form-control" rows="8" placeholder="Detaylı açıklama, HTML destekler..."><?= e($package['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('dollar-sign', 18) ?> Fiyatlandırma</h3></div>
                <div class="adm-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fiyat (₺)</label>
                            <input type="number" name="price" step="0.01" class="form-control" value="<?= $package['price'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>İndirimli Fiyat (₺)</label>
                            <input type="number" name="discount_price" step="0.01" class="form-control" value="<?= $package['discount_price'] ?? '' ?>" placeholder="Opsiyonel">
                            <div class="form-hint">Boş bırakırsanız indirim uygulanmaz.</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Min Adet</label>
                            <input type="number" name="min_quantity" class="form-control" value="<?= $package['min_quantity'] ?? 1 ?>">
                        </div>
                        <div class="form-group">
                            <label>Max Adet</label>
                            <input type="number" name="max_quantity" class="form-control" value="<?= $package['max_quantity'] ?? 1 ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Teslim Süresi</label>
                            <input type="text" name="delivery_time" class="form-control" value="<?= e($package['delivery_time'] ?? '') ?>" placeholder="ör: 1-3 gün">
                        </div>
                        <div class="form-group">
                            <label>Badge</label>
                            <input type="text" name="badge" class="form-control" value="<?= e($package['badge'] ?? '') ?>" placeholder="ör: Popüler">
                            <div class="form-hint">Paket kartında gösterilecek etiket.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image & Status -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('image', 18) ?> Görsel & Yayın Durumu</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Görsel</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Görsel Alt Metni</label>
                        <input type="text" name="image_alt" class="form-control" value="<?= e($package['image_alt'] ?? '') ?>" placeholder="SEO için alt etiket">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Durum</label>
                            <select name="status" class="form-control">
                                <option value="active" <?= ($package['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= ($package['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sıra</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $package['sort_order'] ?? 0 ?>">
                        </div>
                    </div>
                    <div class="adm-featured-check">
                        <label>
                            <input type="checkbox" name="is_featured" value="1" <?= !empty($package['is_featured']) ? 'checked' : '' ?>>
                            <span><?= icon('star', 16) ?> Öne Çıkan Paket</span>
                        </label>
                        <div class="form-hint">İşaretlerseniz bu paket ana sayfada gösterilir.</div>
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
                        <input type="text" name="seo_title" class="form-control" value="<?= e($package['seo_title'] ?? '') ?>" placeholder="Sayfa başlığı...">
                        <div class="form-hint">35-60 karakter önerilir</div>
                    </div>
                    <div class="form-group">
                        <label>Meta Açıklama</label>
                        <textarea name="seo_description" class="form-control" rows="2" placeholder="Arama motorlarında görünen açıklama..."><?= e($package['seo_description'] ?? '') ?></textarea>
                        <div class="form-hint">120-160 karakter önerilir</div>
                    </div>
                    <div class="form-group">
                        <label>Odak Anahtar Kelime</label>
                        <input type="text" name="seo_focus_keyword" class="form-control" value="<?= e($package['seo_focus_keyword'] ?? '') ?>" placeholder="Ana hedef kelime...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="adm-form-side">
            <div class="adm-card" style="position: sticky; top: 76px;">
                <div class="adm-card-body">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('save', 18) ?> <?= $isEdit ? 'Güncelle' : 'Kaydet' ?></button>
                    <a href="/admin/paketler" class="btn btn-outline btn-block btn-sm" style="margin-top: var(--space-3);"><?= icon('x', 14) ?> Vazgeç</a>
                    <?php if ($isEdit): ?>
                    <a href="/admin/paket/<?= $package['id'] ?>/alanlar" class="btn btn-light btn-block btn-sm" style="margin-top: var(--space-2);"><?= icon('list', 14) ?> Sipariş Alanları</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
