<?php
$subtotal = 0;
$totalDiscount = 0;
foreach ($cartItems as $item) {
    $qty = $item['quantity'];
    $subtotal += $item['price'] * $qty;
    if ($item['discount_price'] && $item['discount_price'] < $item['price']) {
        $totalDiscount += ($item['price'] - $item['discount_price']) * $qty;
    }
}
$finalTotal = $total; // Calculated in CartController
?>
<section class="section" style="padding-top: var(--space-8); padding-bottom: var(--space-16);">
    <div class="container">
        <h1 style="font-size: var(--font-size-3xl); font-weight: 800; color: var(--color-dark); margin-bottom: var(--space-8); letter-spacing: -0.02em;">
            Sepetim <span style="color: var(--color-text-secondary); font-size: var(--font-size-lg); font-weight: 500; margin-left: var(--space-2);">(<?= count($cartItems) ?> Ürün)</span>
        </h1>

        <?php if (empty($cartItems)): ?>
        <div class="empty-state card" style="text-align: center; padding: var(--space-12) var(--space-6); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 400px; border: 1px dashed var(--color-border); background: var(--color-bg);">
            <div style="width: 96px; height: 96px; border-radius: 50%; background: var(--color-soft-blue); color: var(--color-blue); display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-6);">
                <?= icon('shopping-bag', 48) ?>
            </div>
            <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--color-dark); margin-bottom: var(--space-3);">Sepetiniz Boş</h3>
            <p style="color: var(--color-text-secondary); font-size: var(--font-size-lg); max-width: 400px; margin-bottom: var(--space-8);">
                Sepetinizde henüz hiçbir hizmet paketi bulunmuyor. Popüler hizmetlerimizi inceleyerek hemen sipariş oluşturabilirsiniz.
            </p>
            <a href="/kategoriler" class="btn btn-primary btn-lg" style="min-width: 200px; justify-content: center;">
                <?= icon('grid', 18) ?> Hizmetleri İncele
            </a>
        </div>
        <?php else: ?>
        <div class="cart-layout" style="display: grid; grid-template-columns: 1fr 380px; gap: var(--space-8); align-items: start;">
            
            <!-- Sol: Sepet İçeriği -->
            <div class="cart-items-container">
                <div class="card" style="padding: 0; overflow: hidden;">
                    <div class="cart-header" style="display: grid; grid-template-columns: 1fr 100px 140px 40px; gap: var(--space-4); padding: var(--space-4) var(--space-6); background: var(--color-bg); border-bottom: 1px solid var(--color-border); font-size: var(--font-size-xs); font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                        <div>Hizmet Detayı</div>
                        <div style="text-align: center;">Adet</div>
                        <div style="text-align: right;">Toplam Fiyat</div>
                        <div></div>
                    </div>
                    
                    <?php foreach ($cartItems as $item): ?>
                    <?php
                        $itemLower = strtolower($item['name']);
                        $icon = 'box'; $color = 'var(--color-blue)';
                        if (strpos($itemLower, 'instagram') !== false) { $icon = 'instagram'; $color = '#E1306C'; }
                        elseif (strpos($itemLower, 'tiktok') !== false) { $icon = 'tiktok'; $color = '#000000'; }
                        elseif (strpos($itemLower, 'youtube') !== false) { $icon = 'youtube'; $color = '#FF0000'; }
                        elseif (strpos($itemLower, 'twitter') !== false || strpos($itemLower, 'x') !== false) { $icon = 'twitter'; $color = '#1DA1F2'; }
                        elseif (strpos($itemLower, 'facebook') !== false) { $icon = 'facebook'; $color = '#1877F2'; }
                        elseif (strpos($itemLower, 'google') !== false || strpos($itemLower, 'seo') !== false) { $icon = 'google'; $color = '#4285F4'; }
                        elseif (strpos($itemLower, 'spotify') !== false) { $icon = 'music'; $color = '#1DB954'; }
                        elseif (strpos($itemLower, 'twitch') !== false) { $icon = 'video'; $color = '#9146FF'; }
                        elseif (strpos($itemLower, 'telegram') !== false) { $icon = 'send'; $color = '#0088cc'; }
                    ?>
                    <div class="cart-item-row" style="display: grid; grid-template-columns: 1fr 100px 140px 40px; gap: var(--space-4); align-items: center; padding: var(--space-5) var(--space-6); border-bottom: 1px solid var(--color-border); transition: var(--transition);">
                        
                        <!-- Hizmet Bilgisi -->
                        <div style="display: flex; gap: var(--space-4); align-items: flex-start; min-width: 0;">
                            <div class="cart-item-icon" style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: <?= $color ?>15; color: <?= $color ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <?= icon($icon, 24) ?>
                            </div>
                            <div style="min-width: 0;">
                                <h3 style="font-size: var(--font-size-base); font-weight: 600; color: var(--color-dark); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <a href="/paket/<?= e($item['slug'] ?? '') ?>" style="color: inherit; text-decoration: none;"><?= e($item['name']) ?></a>
                                </h3>
                                <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($item['short_description'] ?? 'Sosyal medya hizmeti') ?>
                                </p>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                                    Birim: 
                                    <?php if ($item['discount_price'] && $item['discount_price'] < $item['price']): ?>
                                        <span style="text-decoration: line-through; margin-right: 4px;"><?= money($item['price']) ?></span>
                                        <span style="color: var(--color-text); font-weight: 500;"><?= money($item['discount_price']) ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--color-text); font-weight: 500;"><?= money($item['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Adet -->
                        <div style="text-align: center;">
                            <div style="display: inline-block; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 4px 12px; font-weight: 600; font-size: var(--font-size-sm);">
                                <?= $item['quantity'] ?>
                            </div>
                        </div>

                        <!-- Toplam Fiyat -->
                        <div style="text-align: right;">
                            <div style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-dark);">
                                <?= money($item['line_total']) ?>
                            </div>
                        </div>

                        <!-- Sil Butonu -->
                        <div style="text-align: right;">
                            <form method="POST" action="/sepet/sil" style="margin: 0;">
                                <?= csrfField() ?>
                                <input type="hidden" name="key" value="<?= $item['cart_key'] ?>">
                                <button type="submit" class="btn btn-icon cart-remove-btn" style="background: transparent; border: none; color: var(--color-red); opacity: 0.7; transition: var(--transition);" title="Kaldır" aria-label="Sepetten Çıkar">
                                    <?= icon('x-circle', 20) ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top: var(--space-6);">
                    <a href="/kategoriler" class="btn btn-light" style="font-weight: 500;">
                        <?= icon('arrow-left', 16) ?> Alışverişe Devam Et
                    </a>
                </div>
            </div>

            <!-- Sağ: Sepet Özeti -->
            <div class="cart-sidebar">
                <div class="card" style="position: sticky; top: 90px; padding: var(--space-6);">
                    <h3 style="font-size: var(--font-size-xl); font-weight: 700; margin-bottom: var(--space-6); color: var(--color-dark); display: flex; align-items: center; gap: var(--space-2);">
                        <?= icon('file-text', 20) ?> Sepet Özeti
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: var(--space-3); margin-bottom: var(--space-5);">
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--color-text-secondary); font-size: var(--font-size-base);">
                            <span>Ara Toplam</span>
                            <span style="font-weight: 500; color: var(--color-text);"><?= money($subtotal) ?></span>
                        </div>
                        <?php if ($totalDiscount > 0): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--color-red); font-size: var(--font-size-base);">
                            <span>İndirim</span>
                            <span style="font-weight: 500;">-<?= money($totalDiscount) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="border-top: 1px dashed var(--color-border); margin: var(--space-5) 0; padding-top: var(--space-5);">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-size: var(--font-size-lg); font-weight: 600; color: var(--color-dark);">Genel Toplam</span>
                            <span style="font-size: 32px; font-weight: 800; color: var(--color-blue); line-height: 1;"><?= money($finalTotal) ?></span>
                        </div>
                    </div>

                    <a href="/odeme" class="btn btn-success btn-lg" style="width: 100%; justify-content: center; font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-5);">
                        Ödemeye Geç <?= icon('arrow-right', 20) ?>
                    </a>

                    <div class="trust-badges" style="background: var(--color-bg); border-radius: var(--radius-lg); padding: var(--space-4);">
                        <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-3);">
                            <div style="color: var(--color-green); flex-shrink: 0;"><?= icon('shield', 18) ?></div>
                            <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">256-Bit SSL ile <strong style="color: var(--color-dark);">Güvenli Ödeme</strong></div>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-3);">
                            <div style="color: var(--color-blue); flex-shrink: 0;"><?= icon('headphones', 18) ?></div>
                            <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Satış Öncesi ve Sonrası <strong style="color: var(--color-dark);">7/24 Destek</strong></div>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <div style="color: var(--color-amber); flex-shrink: 0;"><?= icon('zap', 18) ?></div>
                            <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Sipariş Sonrası <strong style="color: var(--color-dark);">Hızlı Teslimat</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.cart-item-row:hover { background: var(--color-bg); }
.cart-remove-btn:hover { opacity: 1 !important; color: var(--color-red) !important; transform: scale(1.1); }
</style>
