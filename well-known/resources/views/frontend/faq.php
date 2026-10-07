<style>
/* Premium FAQ Layout */
:root {
    --faq-primary: #3b82f6;
    --faq-primary-light: #eff6ff;
    --faq-dark: #0f172a;
    --faq-text: #334155;
    --faq-muted: #64748b;
    --faq-border: #e2e8f0;
    --faq-bg: #f8fafc;
    --radius-lg: 24px;
    --radius-md: 16px;
    --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
}

body { background-color: var(--faq-bg); }

.faq-hero {
    position: relative;
    background: #fff;
    padding: 60px 0 40px;
    border-bottom: 1px solid var(--faq-border);
    text-align: center;
}

.faq-hero-badge {
    display: inline-block;
    padding: 6px 16px;
    background: var(--faq-primary-light);
    color: var(--faq-primary);
    border-radius: 30px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 20px;
}

.faq-hero-title {
    font-size: 42px;
    font-weight: 900;
    color: var(--faq-dark);
    line-height: 1.25;
    margin-bottom: 15px;
    letter-spacing: -0.5px;
}
@media(max-width: 768px) { .faq-hero-title { font-size: 32px; } }

.faq-hero-subtitle {
    font-size: 18px;
    color: var(--faq-muted);
    max-width: 600px;
    margin: 0 auto 30px;
}

.breadcrumb-center {
    font-size: 14px;
    font-weight: 500;
    color: var(--faq-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.breadcrumb-center a { color: var(--faq-primary); text-decoration: none; transition: 0.2s; }
.breadcrumb-center a:hover { opacity: 0.8; }
.breadcrumb-center .separator { color: #cbd5e1; font-size: 12px; }

.faq-content-wrapper {
    padding: 60px 0 80px;
}

.premium-faq-container {
    max-width: 800px;
    margin: 0 auto;
}

/* FAQ Accordion Premium */
.premium-faq-item {
    background: #fff;
    border: 1px solid var(--faq-border);
    border-radius: var(--radius-md);
    margin-bottom: 16px;
    transition: 0.3s;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}
.premium-faq-item:hover {
    border-color: #cbd5e1;
    box-shadow: var(--shadow-soft);
}
.premium-faq-question {
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    padding: 24px;
    font-size: 17px;
    font-weight: 700;
    color: var(--faq-dark);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.premium-faq-question i {
    width: 36px;
    height: 36px;
    background: var(--faq-primary-light);
    color: var(--faq-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s, background 0.3s;
    font-size: 20px;
    flex-shrink: 0;
    margin-left: 15px;
}
.premium-faq-question.active i { transform: rotate(180deg); background: var(--faq-primary); color: #fff; }
.premium-faq-answer {
    padding: 0 24px 24px;
    display: none;
    color: var(--faq-text);
    line-height: 1.7;
    font-size: 16px;
}

.faq-cta {
    background: linear-gradient(135deg, var(--faq-primary), #60a5fa);
    border-radius: var(--radius-lg);
    padding: 40px;
    text-align: center;
    color: #fff;
    margin-top: 60px;
    box-shadow: var(--shadow-soft);
}
.faq-cta h3 { font-size: 24px; font-weight: 800; margin-bottom: 10px; color: #fff; }
.faq-cta p { font-size: 16px; margin-bottom: 25px; opacity: 0.9; }
.faq-cta .btn-light {
    background: #fff;
    color: var(--faq-primary);
    padding: 12px 30px;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}
.faq-cta .btn-light:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
</style>

<div class="faq-hero">
    <div class="container">
        <div class="breadcrumb-center mb-4">
            <a href="/">Ana Sayfa</a>
            <span class="separator"><i class="ri-arrow-right-s-line"></i></span>
            <span style="color: var(--faq-dark); font-weight: 600;">Sıkça Sorulan Sorular</span>
        </div>
        
        <div class="faq-hero-badge">Yardım Merkezi</div>
        <h1 class="faq-hero-title">Size Nasıl Yardımcı Olabiliriz?</h1>
        <p class="faq-hero-subtitle">Hizmetlerimizle ilgili aklınıza takılan tüm soruların cevaplarını burada bulabilirsiniz.</p>
    </div>
</div>

<div class="faq-content-wrapper">
    <div class="container">
        <div class="premium-faq-container">
            <?php if(!empty($faqs)): ?>
                <?php foreach ($faqs as $faq): ?>
                <div class="premium-faq-item">
                    <button class="premium-faq-question">
                        <span><?= e($faq['question']) ?></span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <div class="premium-faq-answer">
                        <?= nl2br(e($faq['answer'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="ri-question-answer-line" style="font-size: 48px; color: var(--faq-muted); opacity: 0.5;"></i>
                    <p class="mt-3" style="color: var(--faq-muted);">Henüz soru eklenmemiş.</p>
                </div>
            <?php endif; ?>

            <div class="faq-cta">
                <h3>Cevabını bulamadınız mı?</h3>
                <p>Destek ekibimiz tüm sorularınızı yanıtlamak için 7/24 hazır.</p>
                <a href="/iletisim" class="btn-light">
                    <i class="ri-message-3-line"></i> Bize Ulaşın
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.premium-faq-question');
    questions.forEach(q => {
        q.addEventListener('click', function() {
            this.classList.toggle('active');
            const answer = this.nextElementSibling;
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
            } else {
                answer.style.display = 'block';
            }
        });
    });
});
</script>
