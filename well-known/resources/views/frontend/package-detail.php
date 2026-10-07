<?php
$lowerName = strtolower($package['name'] . ' ' . ($package['category_name'] ?? ''));
$platformIcon = 'box';
$platformColor = 'var(--color-blue)';

if (strpos($lowerName, 'instagram') !== false) { 
    $platformIcon = 'instagram'; 
    $platformColor = '#E1306C'; 
} elseif (strpos($lowerName, 'tiktok') !== false) { 
    $platformIcon = 'tiktok'; 
    $platformColor = '#000000'; 
} elseif (strpos($lowerName, 'youtube') !== false) { 
    $platformIcon = 'youtube'; 
    $platformColor = '#FF0000'; 
} elseif (strpos($lowerName, 'twitter') !== false || strpos($lowerName, 'x') !== false) { 
    $platformIcon = 'twitter'; 
    $platformColor = '#1DA1F2'; 
} elseif (strpos($lowerName, 'facebook') !== false) { 
    $platformIcon = 'facebook'; 
    $platformColor = '#1877F2'; 
} elseif (strpos($lowerName, 'google') !== false || strpos($lowerName, 'seo') !== false) { 
    $platformIcon = 'google'; 
    $platformColor = '#4285F4'; 
} elseif (strpos($lowerName, 'spotify') !== false) { 
    $platformIcon = 'music'; 
    $platformColor = '#1DB954'; 
} elseif (strpos($lowerName, 'twitch') !== false) { 
    $platformIcon = 'video'; 
    $platformColor = '#9146FF'; 
} elseif (strpos($lowerName, 'telegram') !== false) { 
    $platformIcon = 'send'; 
    $platformColor = '#0088cc'; 
}
?>

<div class="pkg-wrapper">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="pkg-breadcrumb">
            <a href="/"><?= icon('home', 14) ?> Ana Sayfa</a> <span class="sep">/</span>
            <a href="/kategori/<?= e($package['category_slug'] ?? '') ?>"><?= e($package['category_name'] ?? 'Hizmetler') ?></a> <span class="sep">/</span>
            <span class="current"><?= e($package['name']) ?></span>
        </div>

        <div class="pkg-layout">
            <!-- Left Side: Content -->
            <div class="pkg-content">
                
                <!-- Header Info -->
                <div class="pkg-header-box">
                    <div class="pkg-header-icon" style="color: <?= $platformColor ?>; background: <?= $platformColor ?>10;">
                        <?= icon($platformIcon, 42) ?>
                    </div>
                    <div class="pkg-header-text">
                        <?php if ($package['badge']): ?>
                            <span class="pkg-badge" style="background: <?= $platformColor ?>15; color: <?= $platformColor ?>;">
                                <?= e($package['badge']) ?>
                            </span>
                        <?php endif; ?>
                        <h1 class="pkg-title"><?= e($package['name']) ?></h1>
                        <?php if ($package['short_description']): ?>
                            <p class="pkg-desc"><?= e($package['short_description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabs (Simulated visual anchor for content) -->
                <div class="pkg-tabs">
                    <div class="pkg-tab active"><?= icon('info', 16) ?> Paket Detayları</div>
                    <div class="pkg-tab"><?= icon('help-circle', 16) ?> S.S.S</div>
                </div>

                <!-- Detaylı Açıklama -->
                <div class="pkg-section">
                    <div class="blog-content pkg-html-content">
                        <?php 
                        $desc = $package['description'];
                        if (empty($desc)) {
                            echo '<p class="text-secondary" style="margin:0;">Bu paket için henüz detaylı bir açıklama girilmemiştir.</p>';
                        } else {
                            // Eğer içerikte hiç HTML etiketi yoksa, metni noktalardan veya satır sonlarından bölerek madde madde yapalım
                            if (strip_tags($desc) === $desc) {
                                // Yeni satırlara veya nokta+boşluk kalıbına göre böl
                                $sentences = preg_split('/(\.\s+|\n+)/', $desc, -1, PREG_SPLIT_NO_EMPTY);
                                if (count($sentences) > 1) {
                                    echo '<ul class="pkg-bullet-list">';
                                    foreach ($sentences as $s) {
                                        $s = trim($s);
                                        if (empty($s)) continue;
                                        // Noktayı geri ekle (eğer regex sildiyse ve cümlenin sonunda yoksa)
                                        if (substr($s, -1) !== '.') $s .= '.';
                                        echo '<li>' . icon('check-circle', 18) . ' <span>' . e($s) . '</span></li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>' . e($desc) . '</p>';
                                }
                            } else {
                                // HTML içerik zaten varsa olduğu gibi bas
                                echo $desc;
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Resim varsa -->
                <?php if (!empty($package['image'])): ?>
                <div class="pkg-section">
                    <img src="<?= e(upload_url($package['image'])) ?>" alt="<?= e($package['image_alt'] ?? $package['name']) ?>" class="pkg-featured-img">
                </div>
                <?php endif; ?>

                <!-- SSS -->
                <div class="pkg-section" style="margin-top: 40px;">
                    <h3 class="pkg-section-title">Sıkça Sorulan Sorular</h3>
                    <div class="pkg-faq">
                        <div class="faq-item">
                            <div class="faq-q"><span>Sipariş verdim, hizmet ne zaman tamamlanır?</span> <?= icon('chevron-down', 16) ?></div>
                            <div class="faq-a"><p>Siparişiniz onaylandıktan sonra, hizmetimiz genellikle paket üzerinde belirtilen "Teslimat Süresi" içinde tamamlanır. Otomatik işlemler anında başlayabilirken, manuel inceleme gerektiren durumlarda bu süre biraz uzayabilir.</p></div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-q"><span>Şifremi vermem gerekiyor mu?</span> <?= icon('chevron-down', 16) ?></div>
                            <div class="faq-a"><p>Kesinlikle hayır! Sistemimizde sağlanan hizmetlerin hiçbirinde şifre veya giriş bilgisi talep edilmez. Sadece ilgili gönderi veya profil bağlantınızı (Link) iletmeniz yeterlidir.</p></div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-q"><span>Ödeme işlemlerim güvende mi?</span> <?= icon('chevron-down', 16) ?></div>
                            <div class="faq-a"><p>Evet, tamamen güvende. Sitemiz 256-bit SSL sertifikası ile korunmaktadır ve 3D Secure onaylı, lisanslı ödeme altyapıları ile hizmet vermektedir.</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Checkout Widget -->
            <div class="pkg-sidebar">
                <div class="pkg-checkout-widget">
                    
                    <!-- Fiyat -->
                    <div class="pkg-price-wrap">
                        <?php if ($package['discount_price'] && $package['discount_price'] < $package['price']): ?>
                            <div class="pkg-old-price"><?= money($package['price']) ?></div>
                            <div class="pkg-new-price"><?= money($package['discount_price']) ?></div>
                        <?php else: ?>
                            <div class="pkg-new-price"><?= money($package['price']) ?></div>
                        <?php endif; ?>
                        <div class="pkg-tax-lbl">KDV Dahildir</div>
                    </div>

                    <hr class="pkg-divider">

                    <!-- Meta -->
                    <div class="pkg-meta-list">
                        <?php if ($package['delivery_time']): ?>
                        <div class="pkg-meta-row">
                            <div class="pkg-meta-icon"><div class="icon-circle text-orange"><?= icon('clock', 14) ?></div></div>
                            <div class="pkg-meta-info">
                                <span class="meta-label">Teslimat Süresi</span>
                                <span class="meta-val"><?= e($package['delivery_time']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($package['min_quantity'] || $package['max_quantity']): ?>
                        <div class="pkg-meta-row">
                            <div class="pkg-meta-icon"><div class="icon-circle text-blue"><?= icon('bar-chart', 14) ?></div></div>
                            <div class="pkg-meta-info">
                                <span class="meta-label">İşlem Limiti</span>
                                <span class="meta-val"><?= number_format($package['min_quantity']) ?> - <?= number_format($package['max_quantity']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Satın Alma Formu -->
                    <form id="packageForm" method="POST" action="/sepet/ekle" class="pkg-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="package_id" value="<?= $package['id'] ?>">

                        <?php if (!empty($fields)): ?>
                            <div class="pkg-dynamic-inputs">
                                <?php foreach ($fields as $field): ?>
                                <div class="pkg-input-group">
                                    <label><?= e($field['field_label']) ?> <?= $field['is_required'] ? '<span class="text-danger">*</span>' : '' ?></label>
                                    <?php if ($field['field_type'] === 'textarea'): ?>
                                        <textarea name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" class="form-control" placeholder="<?= e($field['placeholder'] ?? '') ?>" <?= $field['is_required'] ? 'required' : '' ?> rows="2"></textarea>
                                    <?php elseif ($field['field_type'] === 'select'): ?>
                                        <select name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" class="form-control" <?= $field['is_required'] ? 'required' : '' ?>>
                                            <option value="">Seçiniz</option>
                                            <?php foreach(explode(',', $field['options']??'') as $opt) { $opt=trim($opt); if($opt) echo "<option value='".e($opt)."'>".e($opt)."</option>"; } ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="<?= $field['field_type']==='url'?'url':'text' ?>" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" class="form-control" placeholder="<?= e($field['placeholder'] ?? '') ?>" <?= $field['is_required'] ? 'required' : '' ?>>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($package['max_quantity'] > 1): ?>
                        <div class="pkg-input-group">
                            <label>Sipariş Adedi</label>
                            <input type="number" name="quantity" class="form-control text-center font-bold" value="<?= $package['min_quantity'] ?>" min="<?= $package['min_quantity'] ?>" max="<?= $package['max_quantity'] ?>" style="font-size: 16px;">
                        </div>
                        <?php endif; ?>

                        <div class="pkg-actions">
                            <button type="submit" class="pkg-btn pkg-btn-outline">
                                <?= icon('shopping-cart', 18) ?> Sepete Ekle
                            </button>
                            <button type="button" class="pkg-btn pkg-btn-solid" onclick="buyNow()">
                                <?= icon('zap', 18) ?> Hemen Satın Al
                            </button>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', setting('site_whatsapp') ?? '') ?>" target="_blank" class="pkg-btn pkg-btn-whatsapp">
                                <?= icon('message-circle', 18) ?> WhatsApp'tan Sor
                            </a>
                        </div>
                    </form>

                    <!-- Trust Indicators -->
                    <div class="pkg-trust-footer">
                        <div class="trust-item"><?= icon('shield', 16) ?> SSL Güvencesi</div>
                        <div class="trust-item"><?= icon('headphones', 16) ?> 7/24 Destek</div>
                        <div class="trust-item"><?= icon('check', 16) ?> Garantili</div>
                    </div>

                </div>
            </div>
            
        </div>

        <!-- Related -->
        <?php if (!empty($relatedPackages)): ?>
        <div class="pkg-related">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="m-0" style="font-weight: 700; font-size: 20px;">İlgili Paketler</h3>
                <a href="/kategori/<?= e($package['category_slug'] ?? '') ?>" class="text-secondary" style="font-size: 14px; text-decoration: none; font-weight: 500;">Tümünü Gör &rarr;</a>
            </div>
            <div class="pkg-related-grid">
                <?php foreach ($relatedPackages as $rp): ?>
                    <?php
                    $rpIcon = 'box';
                    $rpColor = 'var(--color-blue)';
                    $rpLower = strtolower($rp['name'] . ' ' . ($package['category_name'] ?? ''));
                    if (strpos($rpLower, 'instagram') !== false) { $rpIcon = 'instagram'; $rpColor = '#E1306C'; }
                    elseif (strpos($rpLower, 'tiktok') !== false) { $rpIcon = 'tiktok'; $rpColor = '#000000'; }
                    elseif (strpos($rpLower, 'youtube') !== false) { $rpIcon = 'youtube'; $rpColor = '#FF0000'; }
                    elseif (strpos($rpLower, 'twitter') !== false || strpos($rpLower, 'x') !== false) { $rpIcon = 'twitter'; $rpColor = '#1DA1F2'; }
                    ?>
                    <a href="/paket/<?= e($rp['slug']) ?>" class="pkg-related-card">
                        <div class="rc-banner" style="background: <?= $rpColor ?>10;">
                            <div class="rc-icon" style="color: <?= $rpColor ?>; background: #fff;">
                                <?= icon($rpIcon, 24) ?>
                            </div>
                        </div>
                        <div class="rc-body">
                            <h4 class="rc-title" title="<?= e($rp['name']) ?>"><?= e($rp['name']) ?></h4>
                            <div class="rc-price">
                                <?php 
                                $rpPrice = ($rp['discount_price'] && $rp['discount_price'] < $rp['price']) ? $rp['discount_price'] : $rp['price']; 
                                echo money($rpPrice); 
                                ?>
                            </div>
                            <div class="rc-btn">İncele &rarr;</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
function extractDynamicFields(form) {
    const dynamicFields = {};
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('field_')) {
            dynamicFields[key] = value;
        }
    }
    return dynamicFields;
}

function buyNow() {
    const form = document.getElementById('packageForm');
    if (!form.reportValidity()) return;
    
    const dynamicFields = extractDynamicFields(form);
    if (Object.keys(dynamicFields).length > 0) {
        sessionStorage.setItem('prefill_fields', JSON.stringify(dynamicFields));
    }

    const btn = form.querySelector('.btn-primary');
    btn.innerHTML = '<span class="pkg-spinner"></span> Yönlendiriliyor...';
    btn.disabled = true;

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => {
        window.location.href = '/odeme';
    }).catch(() => {
        window.location.href = '/odeme';
    });
}

document.getElementById('packageForm').addEventListener('submit', function() {
    const dynamicFields = extractDynamicFields(this);
    if (Object.keys(dynamicFields).length > 0) {
        sessionStorage.setItem('prefill_fields', JSON.stringify(dynamicFields));
    }
});

// FAQ
document.querySelectorAll('.faq-q').forEach(q => {
    q.addEventListener('click', () => {
        const item = q.parentElement;
        item.classList.toggle('active');
    });
});
</script>

<style>
/* Clean & Minimal Package Detail CSS */
.pkg-wrapper {
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
    padding: 30px 0 80px 0;
    font-family: var(--font-family, system-ui, -apple-system, sans-serif);
}

.pkg-breadcrumb {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pkg-breadcrumb a { color: #6c757d; text-decoration: none; display: flex; align-items: center; gap: 4px; transition: color 0.2s; }
.pkg-breadcrumb a:hover { color: var(--color-primary, #0d6efd); }
.pkg-breadcrumb .sep { color: #dee2e6; }
.pkg-breadcrumb .current { color: #212529; font-weight: 500; }

.pkg-layout {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}
@media(max-width: 992px) {
    .pkg-layout { flex-direction: column; }
}

.pkg-content {
    flex: 1;
    min-width: 0;
}

/* Header Box */
.pkg-header-box {
    display: flex;
    gap: 20px;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    margin-bottom: 24px;
}
@media(max-width: 576px) {
    .pkg-header-box { flex-direction: column; padding: 20px; text-align: center; align-items: center; }
}
.pkg-header-icon {
    width: 80px; height: 80px;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pkg-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 12px;
}
.pkg-title {
    font-size: 28px;
    font-weight: 800;
    color: #212529;
    margin: 0 0 10px 0;
    line-height: 1.2;
}
.pkg-desc {
    font-size: 15px;
    color: #6c757d;
    margin: 0;
    line-height: 1.6;
}

/* Tabs */
.pkg-tabs {
    display: flex;
    gap: 24px;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 24px;
}
.pkg-tab {
    padding: 12px 0;
    font-size: 15px;
    font-weight: 600;
    color: #6c757d;
    cursor: default;
    display: flex; align-items: center; gap: 6px;
    position: relative;
}
.pkg-tab.active {
    color: var(--color-primary, #0d6efd);
}
.pkg-tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px; left: 0; right: 0;
    height: 2px;
    background: var(--color-primary, #0d6efd);
    border-radius: 2px 2px 0 0;
}

/* Sections */
.pkg-section {
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    margin-bottom: 24px;
}
.pkg-section-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 20px 0;
    color: #212529;
}
.pkg-html-content {
    font-size: 15px;
    line-height: 1.7;
    color: #495057;
}
.pkg-featured-img {
    width: 100%;
    border-radius: 8px;
    display: block;
}

/* Sidebar Checkout */
.pkg-sidebar {
    width: 380px;
    flex-shrink: 0;
}
@media(max-width: 992px) {
    .pkg-sidebar { width: 100%; }
}
.pkg-checkout-widget {
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    position: sticky;
    top: 90px;
}

.pkg-price-wrap {
    text-align: center;
    margin-bottom: 20px;
}
.pkg-old-price {
    font-size: 16px;
    color: #adb5bd;
    text-decoration: line-through;
    margin-bottom: 4px;
}
.pkg-new-price {
    font-size: 40px;
    font-weight: 800;
    color: #212529;
    line-height: 1;
}
.pkg-tax-lbl {
    font-size: 12px;
    color: #adb5bd;
    margin-top: 6px;
    font-weight: 500;
}

.pkg-divider {
    border: 0;
    border-top: 1px dashed #dee2e6;
    margin: 24px 0;
}

/* Meta list */
.pkg-meta-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}
.pkg-meta-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
.icon-circle {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    background: #f8f9fa;
}
.icon-circle.text-orange { color: #fd7e14; background: #fff3cd; }
.icon-circle.text-blue { color: #0d6efd; background: #cfe2ff; }
.pkg-meta-info {
    display: flex; flex-direction: column;
}
.meta-label { font-size: 12px; color: #6c757d; }
.meta-val { font-size: 14px; font-weight: 600; color: #212529; }

/* Forms */
.pkg-input-group { margin-bottom: 16px; }
.pkg-input-group label {
    display: block;
    font-size: 13px; font-weight: 600; color: #495057; margin-bottom: 6px;
}
.form-control {
    width: 100%; padding: 10px 14px;
    border: 1px solid #ced4da; border-radius: 8px;
    font-size: 14px; color: #212529; background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.form-control:focus {
    border-color: #86b7fe; outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
}

.btn-primary-outline { background: transparent; border: 1px solid var(--color-primary, #0d6efd); color: var(--color-primary, #0d6efd); }
.btn-primary-outline:hover { background: var(--color-primary, #0d6efd); color: #fff; }
.btn-primary { background: var(--color-primary, #0d6efd); border: 1px solid var(--color-primary, #0d6efd); color: #fff; transition: transform 0.1s; }
.btn-primary:hover { transform: translateY(-1px); }

/* Custom Buttons for Widget */
.pkg-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 24px;
}
.pkg-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
}
.pkg-btn-outline {
    background: transparent;
    border: 2px solid var(--color-primary, #0d6efd);
    color: var(--color-primary, #0d6efd);
}
.pkg-btn-outline:hover {
    background: rgba(13, 110, 253, 0.05);
}
.pkg-btn-solid {
    background: var(--color-primary, #0d6efd);
    color: #fff;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}
.pkg-btn-solid:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(13, 110, 253, 0.3);
}
.pkg-btn-whatsapp {
    background: #fff;
    border: 1px solid #e9ecef;
    color: #25D366;
}
.pkg-btn-whatsapp:hover {
    background: #f8f9fa;
    border-color: #25D366;
}

/* Bullet List for Description */
.pkg-bullet-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.pkg-bullet-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #f8f9fa;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    color: #495057;
    font-size: 15px;
    line-height: 1.5;
}
.pkg-bullet-list li svg {
    color: var(--color-primary, #0d6efd);
    flex-shrink: 0;
    margin-top: 2px;
}

/* Trust Footer */
.pkg-trust-footer {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}
.trust-item {
    font-size: 11px; font-weight: 600; color: #adb5bd;
    display: flex; flex-direction: column; align-items: center; gap: 4px;
}

/* FAQ */
.faq-item {
    border-bottom: 1px solid #dee2e6;
}
.faq-item:last-child { border-bottom: none; }
.faq-q {
    padding: 16px 0;
    font-weight: 600; font-size: 15px; color: #212529;
    display: flex; justify-content: space-between; align-items: center;
    cursor: pointer; user-select: none;
}
.faq-q svg { transition: transform 0.2s; color: #6c757d; }
.faq-a {
    max-height: 0; overflow: hidden;
    transition: max-height 0.3s ease;
    font-size: 14px; color: #495057; line-height: 1.6;
}
.faq-item.active .faq-a { max-height: 200px; padding-bottom: 16px; }
.faq-item.active .faq-q svg { transform: rotate(180deg); }

/* Related */
.pkg-related {
    margin-top: 60px;
    padding-top: 40px;
    border-top: 1px solid #dee2e6;
}
.pkg-related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
}
.pkg-related-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.pkg-related-card:hover {
    border-color: #dee2e6;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    transform: translateY(-3px);
}
.rc-banner {
    height: 80px;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.rc-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    margin-bottom: -28px;
    border: 2px solid #fff;
    z-index: 2;
}
.rc-body {
    padding: 40px 20px 24px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.rc-title {
    font-size: 16px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 12px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.rc-price {
    font-size: 20px;
    font-weight: 800;
    color: #212529;
    margin-bottom: 16px;
    flex: 1;
}
.rc-btn {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary, #0d6efd);
    background: rgba(13, 110, 253, 0.05);
    padding: 10px;
    border-radius: 8px;
    transition: background 0.2s;
}
.pkg-related-card:hover .rc-btn {
    background: rgba(13, 110, 253, 0.1);
}

.pkg-spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-bottom-color: #fff;
    border-radius: 50%;
    display: inline-block;
    animation: pkg-spin 1s linear infinite;
    vertical-align: middle;
}
@keyframes pkg-spin { 100% { transform: rotate(360deg); } }
</style>
