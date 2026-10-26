<div class="nv-contact-page">
<section class="nv-contact-hero" aria-labelledby="contact-heading"><div class="container">
    <nav class="nv-contact-breadcrumb" aria-label="Sayfa konumu"><a href="/">Ana Sayfa</a><span aria-hidden="true">/</span><span aria-current="page">İletişim</span></nav>
    <div class="nv-contact-intro"><div><span class="nv-contact-eyebrow"><?= icon('message-circle',16) ?> İLETİŞİM</span><h1 id="contact-heading"><?= e($contact['contact_heading']) ?></h1><p><?= e($contact['contact_intro']) ?></p></div><a class="nv-contact-soft-link" href="#contact-form">Bize mesaj bırakın <?= icon('arrow-down',16) ?></a></div>
</div></section>
<section class="nv-contact-main"><div class="container">
    <div class="nv-contact-grid">
        <aside class="nv-contact-details" aria-labelledby="contact-details-title"><span class="nv-contact-eyebrow">BİRLİKTE BAŞLAYALIM</span><h2 id="contact-details-title">Size bir mesaj kadar yakınız.</h2><p>Size uygun kanalı seçin; sorularınızı ve proje detaylarını bizimle paylaşın.</p>
            <nav class="nv-contact-channels" aria-label="İletişim kanalları"><?php foreach($contact['channels']as $channel): ?><a href="<?= e($channel['url']) ?>" <?= str_starts_with($channel['url'],'https://')?'target="_blank" rel="noopener noreferrer"':'' ?>><span class="nv-contact-channel-icon"><?= icon($channel['icon'],20) ?></span><span><small><?= $channel['icon']==='phone'?'Telefon':($channel['icon']==='mail'?'E-posta':'Doğrudan iletişim') ?></small><strong><?= e($channel['label']) ?></strong></span><?= icon('arrow-up-right',16) ?></a><?php endforeach; ?></nav>
            <?php if($contact['site_address']!==''): ?><div class="nv-contact-address"><?= icon('map-pin',20) ?><div><h3>Adresimiz</h3><address><?= nl2br(e($contact['site_address'])) ?></address></div></div><?php endif; ?>
            <?php if($contact['contact_support_hours']!==''): ?><div class="nv-contact-note"><?= icon('clock',16) ?><p><?= e($contact['contact_support_hours']) ?></p></div><?php endif; ?>
        </aside>
        <div class="nv-contact-form" id="contact-form"><span class="nv-contact-eyebrow">SİZİ DİNLEYELİM</span><h2>Nasıl yardımcı olabiliriz?</h2><p class="nv-contact-form-lead">Kısa bir mesajla başlayın. Tüm alanların doldurulması gereklidir.</p>
            <?php if(!empty($flash['contact_success'])): ?><div class="yv-form-feedback success" role="status"><?= e($flash['contact_success']) ?></div><?php endif; ?><?php if(!empty($flash['contact_error'])): ?><div class="yv-form-feedback error" role="alert"><?= e($flash['contact_error']) ?></div><?php endif; ?>
            <form method="POST" action="/iletisim"><?= csrfField() ?><div class="yv-honeypot" aria-hidden="true"><label>Web sitesi<input name="website_url" type="text" tabindex="-1" autocomplete="off"></label></div>
                <div class="nv-contact-form-row"><div class="nv-contact-field"><label for="contact-name">Ad soyad</label><input id="contact-name" autocomplete="name" name="name" minlength="2" maxlength="120" required placeholder="Adınız soyadınız"></div><div class="nv-contact-field"><label for="contact-email">E-posta adresi</label><input id="contact-email" autocomplete="email" type="email" name="email" maxlength="190" required placeholder="ornek@email.com"></div></div>
                <div class="nv-contact-field"><label for="contact-subject">Konu</label><input id="contact-subject" name="subject" minlength="3" maxlength="190" required placeholder="Projeniz veya sorunuz hakkında"></div>
                <div class="nv-contact-field"><label for="contact-message">Mesajınız</label><textarea id="contact-message" name="message" rows="5" minlength="10" maxlength="10000" required placeholder="İhtiyacınızı ve varsa proje detaylarını paylaşın..."></textarea></div>
                <label class="nv-contact-consent"><input type="checkbox" name="privacy_consent" value="1" required><span><a href="/sayfa/kvkk" target="_blank" rel="noopener">KVKK bilgilendirmesini</a> okudum ve mesajımın yanıtlanması için bilgilerimin işlenmesini kabul ediyorum.</span></label>
                <div class="nv-contact-submit"><button class="nv-contact-button" type="submit">Mesajı gönder <?= icon('arrow-right',18) ?></button><span><?= icon('lock',14) ?> Bilgileriniz güvenli bağlantıyla iletilir.</span></div>
            </form>
        </div>
    </div>
    <?php if($contact['map_url']!==''): ?><section class="nv-contact-map" aria-labelledby="contact-map-title"><div class="nv-contact-map-heading"><div><span class="nv-contact-eyebrow"><?= icon('map-pin',16) ?> KONUM</span><h2 id="contact-map-title"><?= e($contact['contact_map_title']?:'Konum ve ulaşım') ?></h2><p><?= e($contact['contact_map_description']) ?></p></div><?php if($contact['site_address']!==''): ?><address><?= nl2br(e($contact['site_address'])) ?></address><?php endif; ?></div>
        <div class="nv-contact-map-stage" data-contact-map>
            <iframe id="contact-map-frame" src="<?= e($contact['map_url']) ?>" title="<?= e($contact['contact_map_title']?:'NetVera Alanya konum haritası') ?>" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="nv-contact-map-fallback"><span>Harita yüklenmiyorsa ayrı sekmede görüntüleyebilirsiniz.</span><a href="<?= e($contact['map_url']) ?>" target="_blank" rel="noopener noreferrer">Google Haritalar'da aç <?= icon('arrow-up-right',14) ?></a></div>
    </section><?php endif; ?>
</div></section>
</div>
