<style>
/* Premium Contact Layout */
:root {
    --contact-primary: #3b82f6;
    --contact-primary-light: #eff6ff;
    --contact-dark: #0f172a;
    --contact-text: #334155;
    --contact-muted: #64748b;
    --contact-border: #e2e8f0;
    --contact-bg: #f8fafc;
    --radius-lg: 24px;
    --radius-md: 16px;
    --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
}

body { background-color: var(--contact-bg); }

.contact-hero {
    position: relative;
    background: #fff;
    padding: 60px 0 40px;
    border-bottom: 1px solid var(--contact-border);
    text-align: center;
}

.contact-hero-badge {
    display: inline-block;
    padding: 6px 16px;
    background: var(--contact-primary-light);
    color: var(--contact-primary);
    border-radius: 30px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 20px;
}

.contact-hero-title {
    font-size: 42px;
    font-weight: 900;
    color: var(--contact-dark);
    line-height: 1.25;
    margin-bottom: 15px;
    letter-spacing: -0.5px;
}
@media(max-width: 768px) { .contact-hero-title { font-size: 32px; } }

.contact-hero-subtitle {
    font-size: 18px;
    color: var(--contact-muted);
    max-width: 600px;
    margin: 0 auto 30px;
}

.breadcrumb-center {
    font-size: 14px;
    font-weight: 500;
    color: var(--contact-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.breadcrumb-center a { color: var(--contact-primary); text-decoration: none; transition: 0.2s; }
.breadcrumb-center a:hover { opacity: 0.8; }
.breadcrumb-center .separator { color: #cbd5e1; font-size: 12px; }

.contact-content-wrapper {
    padding: 60px 0 80px;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: 50px;
}
@media(max-width: 991px) {
    .contact-grid { grid-template-columns: 1fr; }
}

/* Info Cards */
.contact-info-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.contact-info-card {
    background: #fff;
    border: 1px solid var(--contact-border);
    border-radius: var(--radius-md);
    padding: 24px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    transition: 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}
.contact-info-card:hover {
    border-color: #cbd5e1;
    box-shadow: var(--shadow-soft);
    transform: translateY(-2px);
}
.contact-info-icon {
    width: 50px;
    height: 50px;
    background: var(--contact-primary-light);
    color: var(--contact-primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.contact-info-text h3 {
    font-size: 17px;
    font-weight: 700;
    color: var(--contact-dark);
    margin: 0 0 6px;
}
.contact-info-text p, .contact-info-text a {
    font-size: 15px;
    color: var(--contact-text);
    margin: 0;
    text-decoration: none;
    line-height: 1.5;
}
.contact-info-text a:hover {
    color: var(--contact-primary);
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}
.social-links a {
    width: 44px;
    height: 44px;
    background: #fff;
    border: 1px solid var(--contact-border);
    color: var(--contact-muted);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: 0.3s;
}
.social-links a:hover {
    background: var(--contact-primary);
    color: #fff;
    border-color: var(--contact-primary);
    transform: translateY(-3px);
}

/* Contact Form Premium */
.contact-form-box {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 40px;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0,0,0,0.03);
}
.contact-form-box h3 {
    font-size: 24px;
    font-weight: 800;
    color: var(--contact-dark);
    margin-bottom: 25px;
}
.form-floating {
    position: relative;
    margin-bottom: 20px;
}
.form-floating .form-control {
    width: 100%;
    padding: 16px 20px;
    background: var(--contact-bg);
    border: 1px solid var(--contact-border);
    border-radius: 12px;
    font-size: 15px;
    color: var(--contact-text);
    outline: none;
    transition: 0.3s;
    font-family: inherit;
}
.form-floating .form-control:focus {
    background: #fff;
    border-color: var(--contact-primary);
    box-shadow: 0 0 0 4px var(--contact-primary-light);
}
.form-floating label {
    font-size: 14px;
    font-weight: 600;
    color: var(--contact-dark);
    margin-bottom: 8px;
    display: block;
}
.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media(max-width: 576px) {
    .form-grid-2 { grid-template-columns: 1fr; gap: 0; }
}

.btn-submit {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--contact-primary), #60a5fa);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}
.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
}
.btn-submit i { font-size: 20px; }
</style>

<div class="contact-hero">
    <div class="container">
        <div class="breadcrumb-center mb-4">
            <a href="/">Ana Sayfa</a>
            <span class="separator"><i class="ri-arrow-right-s-line"></i></span>
            <span style="color: var(--contact-dark); font-weight: 600;">İletişim</span>
        </div>
        
        <div class="contact-hero-badge">7/24 Destek</div>
        <h1 class="contact-hero-title">Bizimle İletişime Geçin</h1>
        <p class="contact-hero-subtitle">Sorularınız, görüşleriniz veya işbirlikleri için bize dilediğiniz zaman ulaşabilirsiniz.</p>
    </div>
</div>

<div class="contact-content-wrapper">
    <div class="container">
        <div class="contact-grid">
            
            <!-- Contact Info -->
            <div>
                <h2 style="font-size: 28px; font-weight: 800; color: var(--contact-dark); margin-bottom: 30px;">İletişim Bilgileri</h2>
                
                <div class="contact-info-list">
                    <?php if (setting('site_email')): ?>
                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="ri-mail-send-fill"></i></div>
                        <div class="contact-info-text">
                            <h3>E-posta Adresimiz</h3>
                            <a href="mailto:<?= e(setting('site_email')) ?>"><?= e(setting('site_email')) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (setting('site_phone')): ?>
                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="ri-phone-fill"></i></div>
                        <div class="contact-info-text">
                            <h3>Telefon & WhatsApp</h3>
                            <a href="tel:<?= e(str_replace(' ', '', setting('site_phone'))) ?>"><?= e(setting('site_phone')) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="ri-map-pin-fill"></i></div>
                        <div class="contact-info-text">
                            <h3>Çalışma Saatleri</h3>
                            <p>Pazartesi - Pazar: 09:00 - 23:59<br>7/24 Canlı Destek</p>
                        </div>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" target="_blank"><i class="ri-instagram-line"></i></a>
                    <a href="#" target="_blank"><i class="ri-twitter-x-line"></i></a>
                    <a href="#" target="_blank"><i class="ri-facebook-circle-fill"></i></a>
                    <a href="https://api.whatsapp.com/send?phone=<?= e(str_replace([' ','+'], '', setting('site_phone'))) ?>" target="_blank"><i class="ri-whatsapp-line"></i></a>
                </div>
            </div>

            <!-- Contact Form -->
            <div>
                <div class="contact-form-box">
                    <h3>Mesaj Gönderin</h3>
                    <form method="POST" action="/iletisim">
                        <?= csrfField() ?>
                        
                        <div class="form-grid-2">
                            <div class="form-floating">
                                <label>Adınız Soyadınız</label>
                                <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required>
                            </div>
                            <div class="form-floating">
                                <label>E-posta Adresiniz</label>
                                <input type="email" name="email" class="form-control" placeholder="ornek@mail.com" required>
                            </div>
                        </div>

                        <div class="form-floating">
                            <label>Konu</label>
                            <input type="text" name="subject" class="form-control" placeholder="Hangi konuda yardıma ihtiyacınız var?" required>
                        </div>

                        <div class="form-floating">
                            <label>Mesajınız</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Lütfen mesajınızı detaylı bir şekilde yazın..." required style="resize: vertical;"></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            Gönder <i class="ri-send-plane-fill"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
