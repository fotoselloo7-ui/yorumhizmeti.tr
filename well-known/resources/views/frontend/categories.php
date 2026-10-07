<?php
// Sayfa arka plan rengini daha premium bir his için hafif gri yapalım (app layout buna izin veriyor mu? Evet bg-light var)
?>
<div class="smm-category-page bg-light pb-10">
    <!-- Modern Header & Search -->
    <section class="smm-page-header">
        <div class="container">
            <div class="header-inner-flex">
                <div class="header-text">
                    <div class="badge-pill mb-3">
                        <span class="pulse-dot"></span> Sosyal Medya Hizmetleri
                    </div>
                    <h1 class="page-title">Tüm <span>Hizmet Kategorileri</span></h1>
                    <p class="page-desc">İhtiyacınız olan platformu seçin, organik ve hızlı büyümenin keyfini çıkarın. %100 güvenli işlemler.</p>
                </div>
                <div class="header-search">
                    <div class="search-box">
                        <?= icon('search', 20) ?>
                        <input type="text" id="categorySearch" placeholder="Hangi platformu arıyorsunuz? (Örn: Instagram, TikTok)">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Grid -->
    <section class="smm-platforms-section section pt-0">
        <div class="container">
            <div class="platform-grid" id="platformGrid">
                <?php 
                $colors = ['blue', 'purple', 'green', 'orange', 'red', 'teal'];
                $cIndex = 0;
                foreach ($categories as $index => $cat): 
                    $color = $colors[$cIndex % count($colors)];
                    $cIndex++;
                    $isPopular = ($index < 3); // İlk 3'üne popüler diyelim
                ?>
                <a href="/kategori/<?= e($cat['slug']) ?>" class="platform-card color-<?= $color ?>" data-name="<?= mb_strtolower($cat['name']) ?>">
                    
                    <?php if ($isPopular): ?>
                        <div class="card-badge"><i class="ri-fire-fill"></i> Popüler</div>
                    <?php endif; ?>

                    <div class="card-icon">
                        <?php if (!empty($cat['image'])): ?>
                            <img src="<?= e(upload_url($cat['image'])) ?>" alt="<?= e($cat['image_alt'] ?? $cat['name']) ?>" style="width: 48px; height: 48px; object-fit: contain;">
                        <?php else: ?>
                            <?= icon($cat['icon_key'] ?? 'box', 36) ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-content">
                        <h2 class="title"><?= e($cat['name']) ?></h2>
                        <span class="sub-title"><?= e($cat['name']) ?> Hizmetleri ve Paketleri</span>
                    </div>

                    <div class="card-footer">
                        <span>Paketleri İncele</span>
                        <div class="arrow-icon">
                            <?= icon('arrow-right', 18) ?>
                        </div>
                    </div>
                    
                    <div class="card-bg-shape"></div>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div id="noResults" class="no-results-box" style="display: none;">
                <?= icon('search', 48) ?>
                <h3>Sonuç Bulunamadı</h3>
                <p>Aradığınız kritere uygun bir platform bulamadık.</p>
            </div>
        </div>
    </section>

    <!-- Trust Features -->
    <section class="smm-trust-section">
        <div class="container">
            <div class="trust-features-grid">
                <div class="trust-feature-box">
                    <div class="tf-icon"><?= icon('shield', 24) ?></div>
                    <div class="tf-text">
                        <h4>Şifresiz İşlem</h4>
                        <p>Hesap şifrenizi asla talep etmiyoruz.</p>
                    </div>
                </div>
                <div class="trust-feature-box">
                    <div class="tf-icon"><?= icon('zap', 24) ?></div>
                    <div class="tf-text">
                        <h4>Hızlı Teslimat</h4>
                        <p>Siparişleriniz anında işleme alınır.</p>
                    </div>
                </div>
                <div class="trust-feature-box">
                    <div class="tf-icon"><?= icon('credit-card', 24) ?></div>
                    <div class="tf-text">
                        <h4>Güvenli Ödeme</h4>
                        <p>3D Secure ile 100% güvenli alışveriş.</p>
                    </div>
                </div>
                <div class="trust-feature-box">
                    <div class="tf-icon"><?= icon('headphones', 24) ?></div>
                    <div class="tf-text">
                        <h4>7/24 Destek</h4>
                        <p>Uzman ekibimiz her zaman yanınızda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('categorySearch');
    const cards = document.querySelectorAll('.platform-card');
    const noResults = document.getElementById('noResults');

    if(searchInput) {
        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if(name.includes(term)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if(visibleCount === 0) {
                noResults.style.display = 'flex';
            } else {
                noResults.style.display = 'none';
            }
        });
    }
});
</script>
