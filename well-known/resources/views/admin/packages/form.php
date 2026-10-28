<?php $isEdit = !empty($package); ?>
<?php $softwareOnly = !empty($softwareOnly); ?>
<?php $socialOptions = $isEdit ? \App\Services\SocialPackageService::info($package) : null; ?>
<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="<?= $softwareOnly ? '/admin/hazir-yazilimlar' : '/admin/paketler' ?>" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= $softwareOnly ? ($isEdit ? icon('edit',22).' Hazır Yazılım Düzenle' : icon('plus',22).' Hazır Yazılım Ekle') : ($isEdit ? icon('edit', 22) . ' Paket Düzenle' : icon('plus', 22) . ' Paket Ekle') ?></h2>
    </div>
    <?php if ($isEdit): ?>
    <a href="/admin/paket/<?= $package['id'] ?>/alanlar" class="btn btn-outline btn-sm"><?= icon('list', 14) ?> Sipariş Alanları</a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $softwareOnly ? ($isEdit ? '/admin/yazilim/' . (int)$package['id'] . '/guncelle' : '/admin/yazilim/kaydet') : ($isEdit ? '/admin/paket/' . $package['id'] . '/guncelle' : '/admin/paket/kaydet') ?>" enctype="multipart/form-data">
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
                    </div>
                    <div class="form-group">
                        <label><?= $softwareOnly ? 'Hazır Yazılım Kategorisi' : 'Kategori' ?></label>
                        <select name="category_id" class="form-control" required>
                            <option value=""><?= $softwareOnly ? 'Yazılım alt kategorisi seçin' : 'Kategori Seçin' ?></option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($package['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($softwareOnly): ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Kısa Açıklama</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Paket listesinde gösterilecek kısa açıklama..."><?= e($package['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Satış Kartındaki Özellikler / Açıklamalar</label>
                        <textarea name="highlight_lines" class="form-control" rows="8" maxlength="1600" placeholder="Şifresiz işlem&#10;Gönderi bağlantısıyla sipariş&#10;Tahmini teslimat süresi&#10;Sipariş takibi"><?=
                            e(!empty($package) ? \App\Services\PackageHighlightsService::text((int)$package['id']) : '')
                        ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Detay Açıklama (HTML)</label>
                        <textarea name="description" class="form-control" rows="8" placeholder="Detaylı açıklama, HTML destekler..."><?= e($package['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <?php if(!$softwareOnly && (!$isEdit || $socialOptions!==null)): require BASE_PATH.'/resources/views/admin/partials/social-landing.php'; endif; ?>
            <!-- Pricing -->
            <?php if(!$softwareOnly): ?>
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('users',18) ?> Sosyal Medya Paket İçeriği</h3></div>
                <div class="adm-card-body">
                    <p>Yalnızca Sosyal Medya Hizmetleri kategorilerinde uygulanır. Bir paket, aşağıdaki miktarın tamamını belirtilen fiyatla satar. Min/Max adet sosyal paketlerde 1'dir.</p>
                    <div class="form-group"><label for="social-service">Hizmet Türü</label><input id="social-service" class="form-control" name="social_service" maxlength="120" value="<?= e($socialOptions['service'] ?? '') ?>" placeholder="Örnek: Instagram Türk Takipçi / Instagram Bot Takipçi"></div>
                    <div class="form-row">
                        <div class="form-group"><label for="social-quantity">Paketin Teslim Miktarı</label><input id="social-quantity" class="form-control" type="number" name="social_quantity" min="1" max="100000000" value="<?= !empty($socialOptions['quantity'])?(int)$socialOptions['quantity']:'' ?>" placeholder="Örnek: 100 / 500 / 1000"></div>
                        <div class="form-group"><label for="social-unit">Miktar Birimi</label><input id="social-unit" class="form-control" name="social_unit" maxlength="40" value="<?= e($socialOptions['unit'] ?? '') ?>" placeholder="takipçi / beğeni / izlenme / abone"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('dollar-sign', 18) ?> Fiyatlandırma</h3></div>
                <div class="adm-card-body">
                    <?php if(!$softwareOnly): ?>
                    <div class="form-group">
                        <label for="nv-price-mode">Satış ve Fiyat Gösterimi</label>
                        <?php $nvPricingMode=$isEdit?\App\Services\ServicePriceMode::forPackage($package):'auto'; ?>
                        <select id="nv-price-mode" name="price_mode" class="form-control" aria-describedby="nv-price-help">
                            <option value="auto" <?= $nvPricingMode==='auto'?'selected':'' ?>>Otomatik (kategori varsayılanı)</option>
                            <option value="fixed" <?= $nvPricingMode==='fixed'?'selected':'' ?>>Sabit fiyat / Sepete ekle</option>
                            <option value="quote" <?= $nvPricingMode==='quote'?'selected':'' ?>>Fiyat Al (özel teklif)</option>
                            <option value="discuss" <?= $nvPricingMode==='discuss'?'selected':'' ?>>Projenizi Konuşalım (özel teklif)</option>
                        </select>
                        <small id="nv-price-help">Teklif seçenekleri fiyatı gizler ve ödeme adımını kapatır. Web tasarım, özel yazılım ve mobil uygulama geliştirme yalnızca teklif usulündedir. Hazır yazılım fiyatlandırması değiştirilmez.</small>
                        <?php $nvMarketOrigin=$isEdit ? \App\Services\MarketProposalGuard::origin((int)$package['id']) : null; ?>
                        <?php if(is_array($nvMarketOrigin)): ?>
                        <div class="adm-alert adm-alert-warning" style="margin-top:12px;padding:12px;border:1px solid #e8d6a8;border-radius:9px;">
                            <strong>Rakip araştırması / Taslak paket</strong>
                            <p style="margin:6px 0;">Paket yayımlanmadan önce hizmet eşdeğerliğini, sağlayıcı maliyetini ve teslim koşullarını kontrol edin.</p>
                            <?php if(!empty($nvMarketOrigin['peer_price'])): ?>
                            <p style="margin:6px 0;">Referans fiyat: <strong><?= money((float)$nvMarketOrigin['peer_price']) ?></strong> · %10 düşük öneri: <strong><?= money(round((float)$nvMarketOrigin['peer_price']*.9,2)) ?></strong></p>
                            <?php else: ?><p style="margin:6px 0;">Kaynak: NetVera miktar tahmini — henüz rakip fiyatı doğrulanmadı.</p><?php endif; ?>
                            <?php if(!empty($nvMarketOrigin['source']) && filter_var($nvMarketOrigin['source'],FILTER_VALIDATE_URL)): ?>
                            <p style="margin:6px 0;">Kaynak: <a href="<?= e($nvMarketOrigin['source']) ?>" target="_blank" rel="noopener noreferrer">Rakip fiyat sayfasını aç</a></p>
                            <?php endif; ?>
                            <small>Araştırma tarihi: <?= e((string)($nvMarketOrigin['reviewed_on']??'')) ?>. Bu kayıt otomatik olarak satışa açılmaz.</small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fiyat (₺)</label>
                            <input type="number" name="price" step="0.01" min="0" class="form-control" value="<?= $package['price'] ?? '0' ?>" required>
                            <small>Fiyat Al / Projenizi Konuşalım seçildiğinde 0 TL bırakılabilir; tutar halka gösterilmez. Sabit fiyatta gerçek tutarı girin.</small>
                        </div>
                        <div class="form-group">
                            <label>İndirimli Fiyat (₺)</label>
                            <input type="number" name="discount_price" step="0.01" class="form-control" value="<?= $package['discount_price'] ?? '' ?>" placeholder="Opsiyonel">
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
                    </div>
                </div>
            </div>

            <?php if(!$softwareOnly): ?>
            <?php $nvRefType='package'; $nvRefId=(int)($package['id']??0); require BASE_PATH.'/resources/views/admin/partials/service-references-picker.php'; ?>
            <?php endif; ?>
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
                    </div>
                    <div class="form-group">
                        <label>Meta Açıklama</label>
                        <textarea name="seo_description" class="form-control" rows="2" placeholder="Arama motorlarında görünen açıklama..."><?= e($package['seo_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Odak Anahtar Kelime</label>
                        <input type="text" name="seo_focus_keyword" class="form-control" value="<?= e($package['seo_focus_keyword'] ?? '') ?>" placeholder="Ana hedef kelime...">
                    </div>
                </div>
            </div>
            <?php require BASE_PATH.'/resources/views/admin/partials/netvera-seo.php'; ?>
        </div>

        <!-- Sidebar -->
        <div class="adm-form-side">
            <div class="adm-card" style="position: sticky; top: 76px;">
                <div class="adm-card-body">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('save', 18) ?> <?= $isEdit ? 'Güncelle' : 'Kaydet' ?></button>
                    <a href="<?= $softwareOnly ? '/admin/hazir-yazilimlar' : '/admin/paketler' ?>" class="btn btn-outline btn-block btn-sm" style="margin-top: var(--space-3);"><?= icon('x', 14) ?> Vazgeç</a>
                    <?php if ($isEdit): ?>
                    <a href="/admin/paket/<?= $package['id'] ?>/alanlar" class="btn btn-light btn-block btn-sm" style="margin-top: var(--space-2);"><?= icon('list', 14) ?> Sipariş Alanları</a>
                    <a href="/admin/kuponlar/yeni?target=package:<?= (int)$package['id'] ?>&amp;name=<?= rawurlencode((string)$package['name']) ?>" class="btn btn-outline btn-block btn-sm" style="margin-top: var(--space-2);"><?= icon('tag', 14) ?> Bu Ürüne Kupon Tanımla</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
