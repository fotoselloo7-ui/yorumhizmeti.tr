<div class="adm-page-top">
    <div>
        <h2><?= icon('settings', 24) ?> Site Ayarları</h2>
        <p class="text-sm text-secondary">Genel site bilgileri, iletişim, sosyal medya ve SEO varsayılanları.</p>
    </div>
</div>

<form method="POST" action="/admin/site-ayarlari/kaydet">
    <?= csrfField() ?>
    
    <div class="adm-form-layout">
        <!-- Main Form Grid -->
        <div class="adm-form-main" style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-6);">
            
            <div class="adm-card" style="grid-column: 1 / -1;">
                <div class="adm-card-header">
                    <h3><?= icon('globe', 16) ?> Genel Bilgiler</h3>
                </div>
                <div class="adm-card-body" style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4);">
                    <div class="form-group mb-0">
                        <label>Site Adı</label>
                        <input type="text" name="site_name" class="form-control" value="<?= e(setting('site_name')) ?>">
                        <input type="hidden" name="_group_site_name" value="general">
                    </div>
                    <div class="form-group mb-0">
                        <label>Site URL</label>
                        <input type="url" name="site_url" class="form-control" value="<?= e(setting('site_url')) ?>">
                        <input type="hidden" name="_group_site_url" value="general">
                    </div>
                    <div class="form-group mb-0" style="grid-column: 1 / -1;">
                        <label>Site Slogan</label>
                        <input type="text" name="site_slogan" class="form-control" value="<?= e(setting('site_slogan')) ?>">
                        <input type="hidden" name="_group_site_slogan" value="general">
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('phone', 16) ?> İletişim Bilgileri</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>E-posta Adresi</label>
                        <input type="email" name="site_email" class="form-control" value="<?= e(setting('site_email')) ?>">
                        <input type="hidden" name="_group_site_email" value="contact">
                    </div>
                    <div class="form-group">
                        <label>Telefon Numarası</label>
                        <input type="text" name="site_phone" class="form-control" value="<?= e(setting('site_phone')) ?>" placeholder="+90 555 555 5555">
                        <input type="hidden" name="_group_site_phone" value="contact">
                    </div>
                    <div class="form-group">
                        <label>WhatsApp Destek Hattı</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="<?= e(setting('whatsapp_number')) ?>">
                        <input type="hidden" name="_group_whatsapp_number" value="contact">
                    </div>
                    <div class="form-group mb-0">
                        <label>Fiziksel Adres</label>
                        <textarea name="site_address" class="form-control" rows="2"><?= e(setting('site_address')) ?></textarea>
                        <input type="hidden" name="_group_site_address" value="contact">
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('share-2', 16) ?> Sosyal Medya & Diğer</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Instagram URL</label>
                        <input type="url" name="social_instagram" class="form-control" value="<?= e(setting('social_instagram')) ?>">
                        <input type="hidden" name="_group_social_instagram" value="social">
                    </div>
                    <div class="form-group">
                        <label>Twitter/X URL</label>
                        <input type="url" name="social_twitter" class="form-control" value="<?= e(setting('social_twitter')) ?>">
                        <input type="hidden" name="_group_social_twitter" value="social">
                    </div>
                    <div class="form-group">
                        <label>YouTube URL</label>
                        <input type="url" name="social_youtube" class="form-control" value="<?= e(setting('social_youtube')) ?>">
                        <input type="hidden" name="_group_social_youtube" value="social">
                    </div>
                    <div class="form-group mb-0">
                        <label>Footer Metni (Copyright vs.)</label>
                        <textarea name="footer_text" class="form-control" rows="2"><?= e(setting('footer_text')) ?></textarea>
                        <input type="hidden" name="_group_footer_text" value="footer">
                    </div>
                </div>
            </div>

            <!-- Theme Colors Card -->
            <div class="adm-card" style="grid-column: 1 / -1;">
                <div class="adm-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3><?= icon('palette', 16) ?> Tema Renkleri</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="resetThemeDefaults()">Varsayılana Döndür</button>
                </div>
                <div class="adm-card-body">
                    <p class="text-sm text-secondary mb-4">Hazır temalardan birini seçin veya özel renklerinizi belirleyin. Değişiklikler kaydettikten sonra uygulanır.</p>
                    
                    <!-- Preset Themes -->
                    <div class="theme-presets">
                        <?php
                        $presets = [
                            ['name' => 'Varsayılan Mavi Premium', 'colors' => ['#2563EB','#1E293B','#3B82F6','#2563EB','#1D4ED8','#F8FAFC','#FFFFFF','#111827','#64748B','#E5E7EB']],
                            ['name' => 'Lacivert Kurumsal', 'colors' => ['#1E3A5F','#0F172A','#2980B9','#1E3A5F','#162D4D','#F0F4F8','#FFFFFF','#1A202C','#718096','#E2E8F0']],
                            ['name' => 'Yeşil Güven', 'colors' => ['#059669','#064E3B','#10B981','#059669','#047857','#F0FDF4','#FFFFFF','#1A202C','#6B7280','#D1FAE5']],
                            ['name' => 'Turuncu Enerji', 'colors' => ['#EA580C','#9A3412','#F97316','#EA580C','#C2410C','#FFF7ED','#FFFFFF','#1C1917','#78716C','#FED7AA']],
                            ['name' => 'Mor Sosyal Medya', 'colors' => ['#7C3AED','#4C1D95','#8B5CF6','#7C3AED','#6D28D9','#F5F3FF','#FFFFFF','#1E1B4B','#6B7280','#DDD6FE']],
                            ['name' => 'Siyah Minimal', 'colors' => ['#18181B','#09090B','#3F3F46','#18181B','#27272A','#FAFAFA','#FFFFFF','#09090B','#71717A','#E4E4E7']],
                        ];
                        foreach ($presets as $i => $preset): ?>
                        <button type="button" class="theme-preset-btn" onclick='applyPreset(<?= json_encode($preset["colors"]) ?>)'>
                            <span class="preset-colors">
                                <span style="background:<?= $preset['colors'][0] ?>"></span>
                                <span style="background:<?= $preset['colors'][2] ?>"></span>
                                <span style="background:<?= $preset['colors'][5] ?>"></span>
                            </span>
                            <span class="preset-name"><?= $preset['name'] ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Custom Color Inputs -->
                    <div class="theme-color-grid">
                        <?php
                        $colorFields = [
                            ['key' => 'theme_primary', 'label' => 'Primary Color', 'default' => '#2563EB'],
                            ['key' => 'theme_secondary', 'label' => 'Secondary Color', 'default' => '#1E293B'],
                            ['key' => 'theme_accent', 'label' => 'Accent Color', 'default' => '#3B82F6'],
                            ['key' => 'theme_button', 'label' => 'Button Color', 'default' => '#2563EB'],
                            ['key' => 'theme_button_hover', 'label' => 'Button Hover', 'default' => '#1D4ED8'],
                            ['key' => 'theme_bg', 'label' => 'Background Color', 'default' => '#F8FAFC'],
                            ['key' => 'theme_card', 'label' => 'Card Background', 'default' => '#FFFFFF'],
                            ['key' => 'theme_text', 'label' => 'Text Color', 'default' => '#111827'],
                            ['key' => 'theme_muted', 'label' => 'Muted Text', 'default' => '#64748B'],
                            ['key' => 'theme_border', 'label' => 'Border Color', 'default' => '#E5E7EB'],
                        ];
                        foreach ($colorFields as $idx => $cf): 
                            $val = setting($cf['key'], $cf['default']);
                        ?>
                        <div class="theme-color-item">
                            <label><?= $cf['label'] ?></label>
                            <div class="color-input-group">
                                <input type="color" value="<?= e($val) ?>" onchange="syncColor(this, '<?= $cf['key'] ?>')" class="color-picker-input">
                                <input type="text" name="<?= $cf['key'] ?>" id="<?= $cf['key'] ?>" class="form-control color-hex-input" value="<?= e($val) ?>" maxlength="7" pattern="^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$" oninput="validateHex(this)" data-default="<?= $cf['default'] ?>">
                                <input type="hidden" name="_group_<?= $cf['key'] ?>" value="theme">
                                <span class="color-preview" id="preview_<?= $cf['key'] ?>" style="background:<?= e($val) ?>;"></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar (Right) -->
        <div class="adm-form-sidebar">
            <button type="submit" class="adm-action-btn adm-btn-save">
                <?= icon('save', 16) ?> Ayarları Kaydet
            </button>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('search', 16) ?> Varsayılan SEO</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Varsayılan SEO Başlığı</label>
                        <input type="text" name="default_seo_title" class="form-control" value="<?= e(setting('default_seo_title')) ?>">
                        <input type="hidden" name="_group_default_seo_title" value="seo">
                        <div class="form-hint">Eksik olan sayfalarda otomatik kullanılır.</div>
                    </div>
                    <div class="form-group">
                        <label>Varsayılan Meta Açıklama</label>
                        <textarea name="default_seo_description" class="form-control" rows="3"><?= e(setting('default_seo_description')) ?></textarea>
                        <input type="hidden" name="_group_default_seo_description" value="seo">
                    </div>
                    <div class="form-group mb-0">
                        <label>Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" class="form-control" value="<?= e(setting('google_analytics_id')) ?>" placeholder="G-XXXXXXXXXX">
                        <input type="hidden" name="_group_google_analytics_id" value="seo">
                    </div>
                </div>
            </div>
            
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('settings', 16) ?> Hızlı Bağlantılar</h3>
                </div>
                <div class="adm-card-body">
                    <a href="/admin/ayarlar/smtp" class="btn btn-outline btn-sm btn-block mb-2"><?= icon('mail', 14) ?> SMTP Ayarları</a>
                    <a href="/admin/ayarlar/odeme-yontemleri" class="btn btn-outline btn-sm btn-block mb-2"><?= icon('credit-card', 14) ?> Ödeme Yöntemleri</a>
                    <a href="/admin/ayarlar/banka-hesaplari" class="btn btn-outline btn-sm btn-block"><?= icon('briefcase', 14) ?> Banka Hesapları</a>
                </div>
            </div>

        </div>
    </div>
</form>

<style>
@media (max-width: 992px) {
    .adm-form-main {
        grid-template-columns: 1fr !important;
    }
}
.theme-presets { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 25px; }
.theme-preset-btn { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border: 1px solid var(--color-border); border-radius: 10px; background: #fff; cursor: pointer; transition: 0.2s; font-size: 13px; font-weight: 600; color: var(--color-dark, #1E293B); }
.theme-preset-btn:hover { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
.preset-colors { display: flex; gap: 3px; }
.preset-colors span { width: 18px; height: 18px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1); }
.theme-color-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
.theme-color-item label { font-size: 13px; font-weight: 600; color: var(--color-dark, #1E293B); margin-bottom: 6px; display: block; }
.color-input-group { display: flex; align-items: center; gap: 8px; }
.color-picker-input { width: 36px; height: 36px; border: 1px solid var(--color-border); border-radius: 8px; padding: 2px; cursor: pointer; background: none; }
.color-hex-input { flex: 1; font-family: monospace; font-size: 14px; }
.color-hex-input.invalid { border-color: #EF4444 !important; }
.color-preview { width: 20px; height: 20px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.1); flex-shrink: 0; }
</style>

<script>
function syncColor(picker, key) {
    const hex = picker.value;
    document.getElementById(key).value = hex;
    document.getElementById('preview_' + key).style.background = hex;
}

function validateHex(input) {
    let val = input.value.trim();
    if (!val.startsWith('#')) val = '#' + val;
    val = val.replace(/[^#0-9a-fA-F]/g, '');
    if (val.length > 7) val = val.substring(0, 7);
    input.value = val;
    const valid = /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(val);
    input.classList.toggle('invalid', !valid && val.length > 1);
    if (valid) {
        const fullHex = val.length === 4 ? '#' + val[1]+val[1]+val[2]+val[2]+val[3]+val[3] : val;
        const preview = document.getElementById('preview_' + input.name);
        if (preview) preview.style.background = fullHex;
        const picker = input.previousElementSibling;
        if (picker && picker.type === 'color') picker.value = fullHex;
    }
}

const colorKeys = ['theme_primary','theme_secondary','theme_accent','theme_button','theme_button_hover','theme_bg','theme_card','theme_text','theme_muted','theme_border'];

function applyPreset(colors) {
    colorKeys.forEach((key, i) => {
        const input = document.getElementById(key);
        if (input && colors[i]) {
            input.value = colors[i];
            const preview = document.getElementById('preview_' + key);
            if (preview) preview.style.background = colors[i];
            const picker = input.previousElementSibling;
            if (picker && picker.type === 'color') picker.value = colors[i];
            input.classList.remove('invalid');
        }
    });
}

function resetThemeDefaults() {
    colorKeys.forEach(key => {
        const input = document.getElementById(key);
        if (input) {
            const def = input.dataset.default;
            input.value = def;
            const preview = document.getElementById('preview_' + key);
            if (preview) preview.style.background = def;
            const picker = input.previousElementSibling;
            if (picker && picker.type === 'color') picker.value = def;
            input.classList.remove('invalid');
        }
    });
}
</script>
