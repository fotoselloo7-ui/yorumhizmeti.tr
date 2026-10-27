<?php
// An indexable guide to the selected public social service. Derive every
// quantity and price statement from published packages, not marketing guesses.
// $serviceLanding is null on normal /kategori pages.
if (!empty($serviceLanding)):
    $selectedName = (string)$serviceLanding['name'];
    $selectedPackages = $socialCatalog['packages'] ?? [];
    $quantities = [];
    foreach ($selectedPackages as $selectedPackage) {
        $detail = \App\Services\SocialPackageService::info($selectedPackage);
        if ($detail && (int)$detail['quantity'] > 0) $quantities[]=(int)$detail['quantity'];
    }
    $quantities = array_values(array_unique($quantities));
    sort($quantities, SORT_NUMERIC);
    $packageCount = count($selectedPackages);
    $quantityMessage = $quantities
        ? number_format($quantities[0],0,',','.').($quantities[count($quantities)-1]!==$quantities[0]?'–'.number_format($quantities[count($quantities)-1],0,',','.'):'' )
        : 'paket detaylarında belirtilen';
?>
<section class="social-knowledge social-service-detail-guide" id="service-selection-guide" aria-labelledby="social-selection-heading">
 <div class="container">
  <div class="social-section-heading">
   <div><span class="social-eyebrow">NETVERA / HİZMET VE FİYAT REHBERİ</span>
    <h2 id="social-selection-heading"><?= e($selectedName) ?>: paket seçimi ve kapsam</h2>
    <p><?= e($selectedName) ?> seçeneklerini karşılaştırmadan önce miktar, fiyat, teslimat ve koşulları inceleyin.</p>
   </div>
  </div>
  <div class="social-editorial-sections">
   <section class="social-editorial-block">
    <div class="social-editorial-copy">
     <h3><?= e($selectedName) ?> paketleri nasıl seçilir?</h3>
     <p>Bu sayfada <?= (int)$packageCount ?> yayımlanmış paket listeleniyor. Paketlerde <?= e($quantityMessage) ?> aralığındaki miktarlar bulunabilir. Her seçeneğin teslim edilecek birimi, tutarı, tahmini süresi ve varsa telafi koşulu kendi ürün detayında belirtilir.</p>
     <p>En düşük fiyatlı seçenek her kullanım senaryosu için aynı sonucu sağlamaz. Hizmet tipi, ülke hedeflemesi, teslimat koşulları ve üçüncü taraf platform kuralları sipariş vermeden önce karşılaştırılmalıdır. Organik etkileşim, sıralama veya hesap büyümesi garanti edilmez.</p>
     <p>Görünen paket ücreti, seçilen miktarın tamamı içindir. Ödeme adımına geçmeden önce ürün sayfasında belirtilen teslimat bilgisini ve toplam tutarı kontrol edin. Bu sayfadaki bilgiler tüm diğer <?= e($category['name']) ?> hizmetlerine otomatik olarak uygulanmaz.</p>
     <p><a href="<?= e(\App\Services\PublicSeoUrls::category($category)) ?>">Diğer <?= e($category['name']) ?> hizmetlerini karşılaştırın</a></p>
    </div>
   </section>
  </div>
  <div class="social-section-heading"><div><h2><?= e($selectedName) ?> hakkında sık sorulan sorular</h2></div></div>
  <div class="social-faq-grid">
   <details><summary>Bu hizmetin paket miktarları nasıl belirleniyor? <?= icon('plus',17) ?></summary><p>Mevcut <?= (int)$packageCount ?> paket arasında belirtilen miktarları ve varsa farklı hizmet kalite türlerini karşılaştırabilirsiniz. Sipariş miktarı, ilgili paket sayfasında kesinleştirilir.</p></details>
   <details><summary>Paket fiyatına neler dahil? <?= icon('plus',17) ?></summary><p>Ürün kartında görünen fiyat, o pakette açıklanan miktarın toplamıdır. Teslimat ve varsa telafi koşulları ürün detayında yer alır; farklı paketler aynı kapsama sahip olmak zorunda değildir.</p></details>
   <details><summary>Siparişi vermeden önce neyi kontrol etmeliyim? <?= icon('plus',17) ?></summary><p>Doğru profil veya içerik bağlantısını, seçtiğiniz paketin miktarını, teslimat açıklamalarını ve platformun kurallarını kontrol edin. Hesap şifresi paylaşmayın; sorularınız için iletişim sayfamızdan destek alın.</p></details>
  </div>
 </div>
</section>
<?php endif; ?>
