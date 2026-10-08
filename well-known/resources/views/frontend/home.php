<?php
$sec = function(string $key) use ($sections) { return $sections[$key] ?? null; };
$hero = $sec('hero');
$testimonials = $sec('testimonials');

if (!function_exists('yh6Platform')) {
    function yh6Platform(string $slug = '', string $name = ''): array {
        $s = mb_strtolower($slug . ' ' . $name);
        if (str_contains($s,'instagram')) return ['instagram','instagram','Instagram'];
        if (str_contains($s,'tiktok')) return ['tiktok','tiktok','TikTok'];
        if (str_contains($s,'youtube')) return ['youtube','youtube','YouTube'];
        if (str_contains($s,'facebook')) return ['facebook','facebook','Facebook'];
        if (str_contains($s,'twitter') || str_contains($s,' x ')) return ['twitter','twitter','X'];
        if (str_contains($s,'threads')) return ['threads','threads','Threads'];
        if (str_contains($s,'telegram')) return ['telegram','telegram','Telegram'];
        if (str_contains($s,'spotify')) return ['spotify','spotify','Spotify'];
        if (str_contains($s,'discord')) return ['discord','discord','Discord'];
        if (str_contains($s,'linkedin')) return ['linkedin','linkedin','LinkedIn'];
        if (str_contains($s,'twitch')) return ['twitch','twitch','Twitch'];
        if (str_contains($s,'google')) return ['google','google','Google'];
        if (str_contains($s,'e-ticaret') || str_contains($s,'eticaret')) return ['ecommerce','store','E-Ticaret'];
        if (str_contains($s,'mobil')) return ['mobileapp','mobile-app','Mobil'];
        if (str_contains($s,'içerik') || str_contains($s,'icerik')) return ['content','content-create','İçerik'];
        if (str_contains($s,'grafik')) return ['graphic','palette','Tasarım'];
        if (str_contains($s,'yerel')) return ['local','local-business','Yerel'];
        if (str_contains($s,'web') || str_contains($s,'site')) return ['web','globe','Web Site'];
        if (str_contains($s,'seo')) return ['seo','bar-chart','SEO'];
        return ['default','package','Hizmet'];
    }
}

if (!function_exists('yh6PackagePrice')) {
    function yh6PackagePrice(array $pkg): float {
        return (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price'])
            ? (float)$pkg['discount_price']
            : (float)$pkg['price'];
    }
}

if (!function_exists('yh6PackageCard')) {
    function yh6PackageCard(array $pkg): string {
        [$cls,$ico,$label] = yh6Platform($pkg['category_slug'] ?? '', $pkg['name'] ?? '');
        $price = yh6PackagePrice($pkg);
        $old = (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price']) ? (float)$pkg['price'] : null;
        $discount = ($old && $old > 0) ? round((1 - $price / $old) * 100) : null;
        ob_start(); ?>
        <a class="yh6-package-card" href="/paket/<?= e($pkg['slug']) ?>">
            <div class="yh6-package-head">
                <span class="yh6-package-icon <?= e($cls) ?>"><?= icon($ico, 22) ?></span>
                <span class="yh6-package-badge"><?= e($pkg['badge'] ?: (!empty($pkg['is_featured']) ? 'En Popüler' : $label)) ?></span>
            </div>
            <h3><?= e(package_display_name($pkg)) ?></h3>
            <ul>
                <li><?= icon('check-circle', 11) ?> Güvenli ve hızlı işlem</li>
                <li><?= icon('check-circle', 11) ?> Şifresiz sipariş süreci</li>
                <li><?= icon('check-circle', 11) ?> 7/24 destek</li>
            </ul>
            <div class="yh6-rating"><span>★★★★★</span><small>4.9/5 müşteri puanı</small></div>
            <div class="yh6-price">
                <strong><?= money($price) ?></strong>
                <?php if ($old): ?><del><?= money($old) ?></del><?php endif; ?>
                <?php if ($discount): ?><em>%<?= $discount ?></em><?php endif; ?>
            </div>
            <span class="yh6-package-cta">Satın Al <?= icon('arrow-right', 11) ?></span>
        </a>
        <?php return ob_get_clean();
    }
}

if (!function_exists('yh18FeaturedCard')) {
    function yh18FeaturedCard(array $pkg, bool $favorite = false): string {
        [$cls,$ico,$label] = yh6Platform($pkg['category_slug'] ?? '', $pkg['category_name'] ?? $pkg['name'] ?? '');
        $price = yh6PackagePrice($pkg);
        $old = (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price']) ? (float)$pkg['price'] : null;
        $discount = ($old && $old > 0) ? round((1 - $price / $old) * 100) : null;
        $delivery = trim((string)($pkg['delivery_time'] ?? ''));
        $short = trim(strip_tags((string)($pkg['short_description'] ?? '')));
        ob_start(); ?>
        <article class="yh18-featured-card <?= e($cls) ?>">
            <?php if ($favorite): ?><span class="yh18-favorite">Favori Paket</span><?php endif; ?>
            <div class="yh18-card-brand">
                <span class="yh18-card-icon"><?= icon($ico, 22) ?></span>
                <div>
                    <small><?= e($label) ?></small>
                    <h3><?= e(package_display_name($pkg)) ?></h3>
                </div>
            </div>

            <?php if ($short !== ''): ?>
            <p class="yh18-card-summary"><?= e(excerpt($short, 112)) ?></p>
            <?php endif; ?>

            <div class="yh18-card-features">
                <span><?= icon('check-circle', 13) ?><b>Aktif ve güvenli hizmet</b></span>
                <span><?= icon('zap', 13) ?><b><?= e($delivery !== '' ? $delivery : 'Hızlı teslimat') ?></b></span>
                <span><?= icon('shield', 13) ?><b>Şifresiz sipariş</b></span>
                <span><?= icon('headphones', 13) ?><b>7/24 müşteri desteği</b></span>
            </div>

            <div class="yh18-card-bottom">
                <div class="yh18-card-price">
                    <?php if ($old): ?><del><?= money($old) ?></del><?php endif; ?>
                    <strong><?= money($price) ?></strong>
                    <?php if ($discount): ?><em>%<?= $discount ?> indirim</em><?php endif; ?>
                </div>
                <a href="/paket/<?= e($pkg['slug']) ?>" class="yh18-card-cta">Paketi İncele <?= icon('arrow-right', 11) ?></a>
            </div>
        </article>
        <?php return ob_get_clean();
    }
}

$heroCutout = asset('img/hero-woman-cutout.png');

$reviews = ($testimonials && !empty($testimonials['extra'])) ? $testimonials['extra'] : [];
?>

<main class="yh-home-v6">

<section class="yh6-hero">
    <div class="container">
        <div class="yh6-hero-grid">
            <div class="yh6-hero-copy">
                <span class="yh6-eyebrow"><?= icon('award', 12) ?> Sosyal Kanıt, Daha Güçlü Markalar</span>
                <h1>Yorumlarınızla <span>Daha Güçlü Bir İmaj Yaratın!</span></h1>
                <p>Google, Instagram, TikTok, YouTube ve web siteniz için güvenilir yorum, beğeni ve etkileşim hizmetleriyle markanızı büyütün. Gerçek etkileşim, gerçek sonuçlar.</p>
                <div class="yh6-hero-actions">
                    <a href="/kategoriler" class="yh6-btn primary">Hemen İncele <?= icon('arrow-right', 12) ?></a>
                    <a href="#how" class="yh6-btn ghost"><?= icon('play-circle', 14) ?> Nasıl Çalışır?</a>
                </div>
                <div class="yh6-hero-metrics">
                    <div><?= icon('users', 16) ?><span><strong>50.000+</strong><small>Mutlu Müşteri</small></span></div>
                    <div><?= icon('star-fill', 16) ?><span><strong>4.9/5</strong><small>Müşteri Puanı</small></span></div>
                    <div><?= icon('zap', 16) ?><span><strong>Hızlı Teslimat</strong><small>Ortalama 0-6 Saat</small></span></div>
                    <div><?= icon('shield', 16) ?><span><strong>%100 Güvenli</strong><small>SSL ile Koruma</small></span></div>
                </div>
            </div>

            <div class="yh6-hero-art">
                <div class="yh6-hero-blob"></div>
                <img class="yh6-hero-person" src="<?= e($heroCutout) ?>" alt="YorumHizmeti dijital hizmetler" loading="eager" fetchpriority="high">

                <span class="yh6-social-float instagram"><?= icon('instagram', 27) ?></span>
                <span class="yh6-social-float tiktok"><?= icon('tiktok', 24) ?></span>
                <span class="yh6-social-float google"><?= icon('google', 25) ?></span>
                <span class="yh6-social-float youtube"><?= icon('youtube', 24) ?></span>

                <div class="yh6-rating-float"><span>★★★★★</span></div>
                <div class="yh6-review-float">
                    <?= icon('google', 19) ?>
                    <div><strong>Yeni Yorum Geldi!</strong><small>5 yıldızlı değerlendirme</small></div>
                </div>
                <div class="yh6-growth-float">
                    <strong>+285</strong><small>Bu hafta yeni müşteri</small>
                    <i></i><i></i><i></i><i></i><i></i>
                </div>
            </div>
        </div>

        <div class="yh6-platform-bar">
            <?php foreach (array_slice($categories,0,8) as $cat):
                [$cls,$ico,$label] = yh6Platform($cat['slug'] ?? '', $cat['name'] ?? '');
            ?>
            <a href="/kategori/<?= e($cat['slug']) ?>" class="<?= e($cls) ?>">
                <span><?= icon($ico, 23) ?></span>
                <div><strong><?= e($label) ?></strong><small><?= e($cat['name']) ?></small></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="yh6-why">
    <div class="container">
        <div class="yh6-why-grid">
            <div class="yh6-why-copy">
                <span class="yh6-eyebrow">Markanız İçin En İyisi</span>
                <h2>Neden YorumHizmeti?</h2>
                <p>Sosyal kanıt, günümüz dijital dünyasında başarının anahtarıdır. Markanızın güvenilirliğini artırmak için hızlı, güvenli ve etkili çözümler sunuyoruz.</p>
                <ul>
                    <li><?= icon('check-circle', 14) ?> Gerçek ve kaliteli etkileşimler</li>
                    <li><?= icon('check-circle', 14) ?> Hızlı teslimat ve 7/24 destek</li>
                    <li><?= icon('check-circle', 14) ?> %100 gizli ve güvenli hizmet</li>
                    <li><?= icon('check-circle', 14) ?> Uygun fiyatlarla yüksek performans</li>
                    <li><?= icon('check-circle', 14) ?> Tüm platformlar için tek adres</li>
                </ul>
                <a href="/kategoriler" class="yh6-btn primary">Hizmetlerimizi Keşfet <?= icon('arrow-right', 12) ?></a>
            </div>

            <div class="yh6-dashboard">
                <div class="yh6-browser">
                    <div class="yh6-browser-top"><span></span><span></span><span></span><b>Hesap İstatistikleri</b><em>Son 30 Gün</em></div>
                    <div class="yh6-dash-stats"><div><small>Toplam Yorum</small><strong>1.248</strong><b>↗ %46</b></div><div><small>Etkileşim</small><strong>25,6K</strong><b>↗ %42</b></div><div><small>Görüntülenme</small><strong>532K</strong><b>↗ %39</b></div></div>
                    <div class="yh6-chart"><svg viewBox="0 0 600 180" preserveAspectRatio="none"><polyline fill="none" stroke="#7b5cff" stroke-width="7" points="0,145 70,125 135,130 210,90 275,105 340,70 405,92 475,54 540,72 600,38"/><polyline fill="none" stroke="#4ca7ff" stroke-width="4" points="0,155 70,142 135,146 210,111 275,121 340,87 405,104 475,74 540,86 600,59"/></svg></div>
                </div>
                <div class="yh6-dash-services">
                    <div><span class="google"><?= icon('google',15) ?></span><b>Google Yorumları</b><small>5 yeni yorum</small></div>
                    <div><span class="instagram"><?= icon('instagram',15) ?></span><b>Instagram Beğeni</b><small>250 yeni beğeni</small></div>
                    <div><span class="tiktok"><?= icon('tiktok',15) ?></span><b>TikTok İzlenme</b><small>12.4K yeni izlenme</small></div>
                    <div><span class="youtube"><?= icon('youtube',15) ?></span><b>YouTube Yorum</b><small>18 yeni yorum</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($featuredPackageGroups)): ?>
<section class="yh18-featured" id="featured" data-featured-switcher>
    <div class="container">
        <?php
        $firstFeaturedGroup = $featuredPackageGroups[0];
        [$firstFeaturedClass,$firstFeaturedIcon,$firstFeaturedLabel] = yh6Platform(
            $firstFeaturedGroup['category']['slug'] ?? '',
            $firstFeaturedGroup['category']['name'] ?? ''
        );
        ?>
        <div class="yh18-featured-head">
            <div class="yh18-featured-title">
                <span class="yh18-featured-avatar <?= e($firstFeaturedClass) ?>" data-featured-avatar><?= icon($firstFeaturedIcon, 24) ?></span>
                <div>
                    <span class="yh6-eyebrow">Kategoriye Göre Popüler Paketler</span>
                    <h2><span data-featured-title><?= e($firstFeaturedGroup['category']['name']) ?></span> Öne Çıkan Paketler</h2>
                    <p data-featured-subtitle><?= e(($firstFeaturedGroup['category']['name'] ?? 'Hizmet') . ' kategorisindeki öne çıkarılan paketleri karşılaştırın.') ?></p>
                </div>
            </div>

            <div class="yh18-featured-tabs" role="tablist" aria-label="Öne çıkan paket kategorileri">
                <?php foreach($featuredPackageGroups as $gidx=>$group):
                    [$gcls,$gico,$glabel] = yh6Platform($group['category']['slug']??'', $group['category']['name']??'');
                ?>
                <button type="button"
                        class="yh18-featured-tab <?= $gidx===0?'active':'' ?> <?= e($gcls) ?>"
                        data-featured-tab="<?= e((string)$group['category']['id']) ?>"
                        data-title="<?= e($group['category']['name']) ?>"
                        data-url="/kategori/<?= e($group['category']['slug']) ?>"
                        data-class="<?= e($gcls) ?>"
                        aria-selected="<?= $gidx===0?'true':'false' ?>"
                        title="<?= e($group['category']['name']) ?>">
                    <?= icon($gico, 18) ?>
                    <span><?= e($glabel) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="yh18-featured-panes">
            <?php foreach($featuredPackageGroups as $gidx=>$group): ?>
            <div class="yh18-featured-pane <?= $gidx===0?'active':'' ?>"
                 data-featured-pane="<?= e((string)$group['category']['id']) ?>"
                 <?= $gidx===0?'':'hidden' ?>>
                <div class="yh18-featured-grid" data-featured-track role="region" aria-label="<?= e($group['category']['name']) ?> öne çıkan paketler">
                    <?php foreach($group['packages'] as $pidx=>$pkg): ?>
                        <?= yh18FeaturedCard($pkg, $pidx===1) ?>
                    <?php endforeach; ?>
                </div>
                <div class="yh18-carousel-row">
                    <div class="yh18-carousel-controls" aria-label="Paket kaydırma">
                        <button type="button" class="yh18-slide-arrow" data-slide-prev aria-label="Önceki paketler"><?= icon('chevron-left',18) ?></button>
                        <span data-slide-count aria-live="off">1 / <?= count($group['packages']) ?></span>
                        <button type="button" class="yh18-slide-arrow" data-slide-next aria-label="Sonraki paketler"><?= icon('chevron-right',18) ?></button>
                    </div>
                </div>
                <div class="yh18-featured-footer">
                    <a href="/kategori/<?= e($group['category']['slug']) ?>" class="yh18-all-link">
                        <?= e($group['category']['name']) ?> kategorisindeki tüm paketleri gör <?= icon('arrow-right', 11) ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($homeCategoryBlocks)): ?>
<section class="yh6-promos">
    <div class="container">
        <div class="yh6-promo-grid">
        <?php foreach(array_slice($homeCategoryBlocks,0,3) as $idx=>$block):
            $cat=$block['category']; [$cls,$ico,$label]=yh6Platform($cat['slug']??'', $cat['name']??'');
            $promoCutout = $idx === 1 ? asset('img/support-woman-cutout.png') : asset('img/hero-woman-cutout.png');
        ?>
            <a href="/kategori/<?= e($cat['slug']) ?>" class="yh6-promo-card <?= e($cls) ?>">
                <div class="yh6-promo-copy">
                    <span><?= $idx===0?'MARKANI ÖNE ÇIKARIN':($idx===1?'TRENDLERDE YERİNİZİ ALIN':'PROFESYONEL ÇÖZÜMLER') ?></span>
                    <h3><?= e($cat['name']) ?></h3>
                    <p><?= e(excerpt(strip_tags($cat['description'] ?? 'Markanızın görünürlüğünü artıran güçlü dijital hizmetler.'), 100)) ?></p>
                    <em>Paketleri İncele <?= icon('arrow-right', 10) ?></em>
                </div>
                <?php if($idx < 2): ?>
                <div class="yh6-promo-visual"><span class="yh6-promo-orb"><?= icon($ico,24) ?></span><img class="yh6-promo-cutout" src="<?= e($promoCutout) ?>" alt="<?= e($cat['name']) ?>"></div>
                <?php else: ?>
                <div class="yh6-promo-visual device"><div class="yh6-device-stack"><i></i><b></b><span></span></div></div>
                <?php endif; ?>
                <div class="yh6-promo-chips"><small>Hızlı Teslimat</small><small>Güvenli Hizmet</small><small>7/24 Destek</small></div>
            </a>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yh6-stats">
    <div class="container">
        <div><?= icon('users',20) ?><span><strong>50.000+</strong><small>Mutlu Müşteri</small></span></div>
        <div><?= icon('shopping-cart',20) ?><span><strong>250.000+</strong><small>Tamamlanan Sipariş</small></span></div>
        <div><?= icon('star-fill',20) ?><span><strong>4.9/5</strong><small>Müşteri Memnuniyeti</small></span></div>
        <div><?= icon('headphones',20) ?><span><strong>7/24</strong><small>Canlı Destek</small></span></div>
        <div><?= icon('trending-up',20) ?><span><strong>%98</strong><small>Başarılı Teslimat Oranı</small></span></div>
    </div>
</section>

<?php if (!empty($reviews)): ?>
<section class="yh6-reviews">
    <div class="container">
        <div class="yh6-section-head"><div><span class="yh6-eyebrow">Gerçek Kullanıcı Deneyimleri</span><h2>Müşterilerimiz Ne Diyor?</h2><p>Binlerce müşterimizin arasında siz de yerinizi alın. Gerçek yorumlar, gerçek başarı hikayeleri.</p></div><a href="#" class="yh6-link">Tüm Yorumları Gör <?= icon('arrow-right',10) ?></a></div>
        <div class="yh6-review-grid">
            <?php foreach(array_slice($reviews,0,4) as $review): ?>
            <article>
                <div class="yh6-review-top"><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><div><b><?= e($review['name']??'Müşteri') ?></b><em>★★★★★</em></div></div>
                <p><?= e($review['text']??'') ?></p>
                <small><?= e($review['role']??'Doğrulanmış müşteri') ?></small>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yh6-how" id="how">
    <div class="container">
        <div class="yh6-how-intro"><span class="yh6-eyebrow">Sadece 4 Adımda</span><h2>Nasıl Çalışır?</h2><p>Hızlı, güvenli ve kolay bir şekilde hizmete satın alın.</p></div>
        <div class="yh6-how-grid">
            <div><b>1</b><span><?= icon('search',17) ?></span><strong>Paket Seçimi</strong><small>İhtiyacınıza uygun paketi belirleyin.</small></div>
            <div><b>2</b><span><?= icon('credit-card',17) ?></span><strong>Güvenli Ödeme</strong><small>Kredi kartı veya havale ile güvenle ödeme yapın.</small></div>
            <div><b>3</b><span><?= icon('zap',17) ?></span><strong>Hızlı Teslimat</strong><small>Siparişiniz en kısa sürede tamamlanır.</small></div>
            <div><b>4</b><span><?= icon('check-circle',17) ?></span><strong>Sonuçları Görün</strong><small>Hesabınızdan sonuçlarınızı takip edin.</small></div>
        </div>
    </div>
</section>

<?php if (!empty($latestPosts)): ?>
<section class="yh6-blog">
    <div class="container">
        <div class="yh6-section-head"><div><span class="yh6-eyebrow">Bilgi Merkezi</span><h2>Son Blog Yazıları</h2><p>Sosyal medya ve dijital pazarlama hakkında en güncel içerikler.</p></div><a href="/blog" class="yh6-link">Tüm Yazılar <?= icon('arrow-right',10) ?></a></div>
        <div class="yh6-blog-grid">
            <?php foreach(array_slice($latestPosts,0,4) as $post): [$postCls,$postIco,$postLabel]=yh6Platform($post['category_slug']??'', $post['category_name']??$post['title']); ?>
            <a href="/blog/<?= e($post['slug']) ?>" class="yh6-blog-card">
                <div class="yh6-blog-image <?= e($postCls) ?>"><img src="<?= e(!empty($post['image']) ? upload_url($post['image']) : demo_visual_url($post['title'].' '.($post['category_name']??''),'blog')) ?>" alt="<?= e($post['image_alt']??$post['title']) ?>"><span class="yh6-blog-image-badge"><?= icon($postIco,15) ?></span></div>
                <div><small><?= e($post['category_name']??'Rehber') ?></small><h3><?= e($post['title']) ?></h3><p><?= e(excerpt(strip_tags($post['excerpt']??$post['content']??''),95)) ?></p><em><?= !empty($post['published_at'])?formatDate($post['published_at'],'d M Y'):'Güncel' ?> · 5 dk okuma</em></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs)): ?>
<section class="yh6-faq">
    <div class="container">
        <div class="yh6-section-head"><div><span class="yh6-eyebrow">Merak Edilenler</span><h2>Sıkça Sorulan Sorular</h2></div></div>
        <div class="yh6-faq-grid">
            <?php foreach(array_slice($faqs,0,8) as $faq): ?>
            <div class="yh6-faq-item"><button type="button"><span><?= e($faq['question']) ?></span><?= icon('plus',11) ?></button><div><?= nl2br(e($faq['answer'])) ?></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yh6-final">
    <div class="container">
        <div>
            <span class="yh6-eyebrow">Daha Güçlü Bir Marka İçin</span>
            <h2>YorumHizmeti.tr ile Dijital Varlığınızı Güçlendirin</h2>
            <p>Sosyal medya etkileşim hizmetlerinden Google yorumlarına, web site çözümlerinden SEO hizmetlerine kadar ihtiyaç duyduğunuz tüm dijital çözümler tek platformda.</p>
        </div>
        <a href="/kategoriler" class="yh6-btn primary">Hemen Başlayın <?= icon('arrow-right',11) ?></a>
        <div class="yh6-avatar-proof"><span>A</span><span>E</span><span>M</span><div><strong>+50.000</strong><small>Mutlu Müşteri</small></div></div>
    </div>
</section>

</main>

<script>
document.querySelectorAll('.yh6-faq-item button').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const item=btn.parentElement;
        item.classList.toggle('open');
        const body=btn.nextElementSibling;
        body.style.display=body.style.display==='block'?'none':'block';
    });
});
</script>