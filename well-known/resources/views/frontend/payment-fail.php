<section class="section">
    <div class="container" style="max-width: 500px;">
        <div class="card text-center">
            <div style="width:64px; height:64px; border-radius:50%; background:var(--color-soft-red); color:var(--color-red); display:flex; align-items:center; justify-content:center; margin: 0 auto var(--space-4);"><?= icon('alert-circle', 32) ?></div>
            <h1 style="font-size: var(--font-size-2xl); font-weight: 700; margin-bottom: var(--space-2);">Ödeme Başarısız</h1>
            <p class="text-secondary mb-6">Ödeme işlemi tamamlanamadı. Lütfen tekrar deneyin veya farklı bir ödeme yöntemi kullanın.</p>
            <div style="display: flex; gap: var(--space-3); justify-content: center;">
                <a href="/sepet" class="btn btn-primary"><?= icon('shopping-cart', 16) ?> Sepete Dön</a>
                <a href="/iletisim" class="btn btn-light">Destek</a>
            </div>
        </div>
    </div>
</section>
