<?php
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
?>
<section class="section" style="padding-top: var(--space-8); padding-bottom: var(--space-16); background: #F8FAFC;">
    <div class="container">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: var(--space-10);">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: var(--color-blue); color: #fff; border-radius: 50%; margin-bottom: var(--space-4);">
                <?= icon('lock', 32) ?>
            </div>
            <h1 style="font-size: var(--font-size-3xl); font-weight: 800; color: var(--color-dark); margin-bottom: var(--space-2); letter-spacing: -0.02em;">Güvenli Ödeme</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--font-size-lg);">256-Bit SSL şifreleme ile ödemeniz tamamen güvende.</p>
        </div>

        <form id="checkoutForm" method="POST" action="/odeme/islem">
            <?= csrfField() ?>
            <div class="checkout-layout" style="display: grid; grid-template-columns: 1fr 400px; gap: var(--space-8); align-items: start;">
                
                <!-- Sol: Formlar -->
                <div>
                    <!-- Üye Bilgileri Kartı -->
                    <div class="card mb-6" style="padding: var(--space-6); border-radius: var(--radius-2xl); border-color: var(--color-border); box-shadow: var(--shadow-sm);">
                        <h2 style="font-size: var(--font-size-xl); font-weight: 700; color: var(--color-dark); margin-bottom: var(--space-5); display: flex; align-items: center; gap: var(--space-3);">
                            <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: var(--color-blue); color: #fff; border-radius: 50%; font-size: 14px;">1</span>
                            Müşteri Bilgileri
                        </h2>
                        
                        <div style="background: var(--color-bg); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-4);">
                            <div style="display: flex; align-items: center; gap: var(--space-4);">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--color-soft-blue); color: var(--color-blue); display: flex; align-items: center; justify-content: center; font-size: var(--font-size-xl); font-weight: 700;">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: var(--font-size-lg); color: var(--color-dark);"><?= e($user['name']) ?></div>
                                    <div style="color: var(--color-text-secondary); font-size: var(--font-size-sm);"><?= e($user['email']) ?></div>
                                </div>
                                <div style="margin-left: auto;">
                                    <span class="badge badge-success"><?= icon('check-circle', 14) ?> Giriş Yapıldı</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sipariş İçeriği ve Dinamik Alanlar -->
                    <div class="card mb-6" style="padding: var(--space-6); border-radius: var(--radius-2xl); border-color: var(--color-border); box-shadow: var(--shadow-sm);">
                        <h2 style="font-size: var(--font-size-xl); font-weight: 700; color: var(--color-dark); margin-bottom: var(--space-5); display: flex; align-items: center; gap: var(--space-3);">
                            <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: var(--color-blue); color: #fff; border-radius: 50%; font-size: 14px;">2</span>
                            Hizmet Detayları
                        </h2>
                        
                        <div style="display: flex; flex-direction: column; gap: var(--space-5);">
                            <?php foreach ($cartItems as $item): ?>
                            <div style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-5); background: #fff;">
                                
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px dashed var(--color-border);">
                                    <div>
                                        <h3 style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-dark); margin-bottom: 4px;"><?= e($item['name']) ?></h3>
                                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Adet: <?= $item['quantity'] ?></div>
                                    </div>
                                    <div style="font-size: var(--font-size-xl); font-weight: 800; color: var(--color-blue);">
                                        <?= money($item['line_total']) ?>
                                    </div>
                                </div>

                                <?php if (!empty($item['fields'])): ?>
                                <div class="dynamic-fields-wrapper" style="background: var(--color-bg); padding: var(--space-4); border-radius: var(--radius-md);">
                                    <div style="font-size: var(--font-size-sm); font-weight: 600; color: var(--color-dark); margin-bottom: var(--space-3); display: flex; align-items: center; gap: 6px;">
                                        <?= icon('link', 14) ?> İşlem Bilgileri
                                    </div>
                                    <div style="display: grid; gap: var(--space-3);">
                                        <?php foreach ($item['fields'] as $field): ?>
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <label style="font-size: 13px; font-weight: 500;"><?= e($field['field_label']) ?> <?php if ($field['is_required']): ?><span style="color: var(--color-red);">*</span><?php endif; ?></label>
                                            <?php if ($field['field_type'] === 'textarea'): ?>
                                                <textarea name="field_<?= $item['id'] ?>_<?= e($field['field_key']) ?>" class="form-control" placeholder="<?= e($field['placeholder'] ?? '') ?>" <?= $field['is_required'] ? 'required' : '' ?> rows="2"></textarea>
                                            <?php elseif ($field['field_type'] === 'select'): ?>
                                                <select name="field_<?= $item['id'] ?>_<?= e($field['field_key']) ?>" class="form-control" <?= $field['is_required'] ? 'required' : '' ?>>
                                                    <option value="">Seçin</option>
                                                    <?php foreach (explode(',', $field['options'] ?? '') as $opt): ?>
                                                    <option value="<?= e(trim($opt)) ?>"><?= e(trim($opt)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <input type="<?= $field['field_type'] === 'url' ? 'url' : 'text' ?>" name="field_<?= $item['id'] ?>_<?= e($field['field_key']) ?>" class="form-control" placeholder="<?= e($field['placeholder'] ?? '') ?>" <?= $field['is_required'] ? 'required' : '' ?>>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Sağ: Ödeme Özeti ve Seçimi -->
                <div class="checkout-sidebar">
                    <div class="card" style="position: sticky; top: 90px; padding: var(--space-6); border-radius: var(--radius-2xl); border-color: var(--color-border); box-shadow: var(--shadow-md);">
                        
                        <h2 style="font-size: var(--font-size-xl); font-weight: 700; color: var(--color-dark); margin-bottom: var(--space-5); display: flex; align-items: center; gap: var(--space-3);">
                            <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: var(--color-blue); color: #fff; border-radius: 50%; font-size: 14px;">3</span>
                            Ödeme Yöntemi
                        </h2>

                        <?php if (empty($paymentOptions)): ?>
                        <div class="alert alert-warning" style="margin-bottom: var(--space-5);"><?= icon('alert-triangle', 16) ?> <span>Aktif ödeme yöntemi bulunamadı.</span></div>
                        <?php else: ?>
                            <div class="payment-methods" style="display: flex; flex-direction: column; gap: var(--space-3); margin-bottom: var(--space-6);">
                                <?php foreach ($paymentOptions as $i => $opt): ?>
                                <label class="payment-option-label" style="display: flex; align-items: flex-start; gap: var(--space-4); padding: var(--space-4); border: 2px solid var(--color-border); border-radius: var(--radius-lg); cursor: pointer; transition: var(--transition); background: #fff;">
                                    <div style="margin-top: 2px;">
                                        <input type="radio" name="payment_gateway" value="<?= e($opt['key']) ?>" <?= ($opt['is_default'] || $i === 0) ? 'checked' : '' ?> required style="width: 18px; height: 18px; accent-color: var(--color-blue);">
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: var(--font-size-base); color: var(--color-dark); margin-bottom: 4px;"><?= e($opt['name']) ?></div>
                                        <div style="font-size: 13px; color: var(--color-text-secondary); line-height: 1.4;">
                                            <?php if ($opt['key'] === 'bank_transfer'): ?>
                                                Havale veya EFT ile komisyonsuz ödeme. Siparişi tamamladıktan sonra banka hesap numaralarımızı görebilirsiniz.
                                            <?php else: ?>
                                                Kredi veya banka kartınız ile 3D Secure güvencesiyle anında online ödeme.
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Sipariş Notu -->
                        <div class="form-group" style="margin-bottom: var(--space-6);">
                            <label style="font-size: var(--font-size-sm); font-weight: 600; color: var(--color-dark);"><?= icon('edit-3', 16) ?> Sipariş Notu (Opsiyonel)</label>
                            <textarea name="customer_note" class="form-control" rows="2" placeholder="Eklemek istediğiniz not..." style="background: var(--color-bg);"></textarea>
                        </div>

                        <!-- Toplam -->
                        <div style="border-top: 1px dashed var(--color-border); padding-top: var(--space-5); margin-bottom: var(--space-6);">
                            <div style="display: flex; justify-content: space-between; align-items: center; color: var(--color-text-secondary); margin-bottom: var(--space-2);">
                                <span>Ara Toplam</span>
                                <span><?= money($subtotal) ?></span>
                            </div>
                            <?php if ($total < $subtotal): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; color: var(--color-red); margin-bottom: var(--space-2);">
                                <span>İndirim</span>
                                <span>-<?= money($subtotal - $total) ?></span>
                            </div>
                            <?php endif; ?>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: var(--space-4);">
                                <span style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-dark);">Ödenecek Tutar</span>
                                <span style="font-size: 32px; font-weight: 800; color: var(--color-blue); line-height: 1;"><?= money($total) ?></span>
                            </div>
                        </div>

                        <!-- Sozlesmeler -->
                        <label style="display: flex; gap: var(--space-3); align-items: flex-start; margin-bottom: var(--space-6); cursor: pointer;">
                            <input type="checkbox" required style="margin-top: 4px; width: 16px; height: 16px; accent-color: var(--color-blue);">
                            <span style="font-size: 13px; color: var(--color-text-secondary); line-height: 1.5;">
                                <a href="#" style="color: var(--color-blue); text-decoration: underline;">Mesafeli Satış Sözleşmesi</a>'ni ve <a href="#" style="color: var(--color-blue); text-decoration: underline;">Gizlilik Politikası</a>'nı okudum, onaylıyorum.
                            </span>
                        </label>

                        <button type="submit" class="btn btn-success btn-lg" style="width: 100%; justify-content: center; font-size: var(--font-size-lg); font-weight: 600;" id="btnCheckoutSubmit">
                            <?= icon('check-circle', 20) ?> Siparişi Onayla ve Öde
                        </button>
                    </div>
                </div>
                
            </div>
        </form>
    </div>
</section>

<style>
.payment-option-label:hover {
    border-color: var(--color-blue);
    background: #F8FAFC;
}
.payment-option-label:has(input:checked) {
    border-color: var(--color-blue);
    background: #EFF6FF;
    box-shadow: 0 0 0 1px var(--color-blue);
}

@media (max-width: 1024px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnCheckoutSubmit');
    if (!this.checkValidity()) return;
    btn.innerHTML = '<span style="width:18px;height:18px;border:2px solid #fff;border-bottom-color:transparent;border-radius:50%;display:inline-block;animation:spin 1s linear infinite;margin-right:8px;"></span> Lütfen Bekleyin...';
    btn.disabled = true;
});
</script>
