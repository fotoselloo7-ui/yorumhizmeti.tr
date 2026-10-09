<div class="adm-page-top">
    <div>
        <h2><?= icon('settings', 24) ?> Site Ayarları</h2>
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

            <div class="adm-card" style="grid-column:1/-1">
                <div class="adm-card-header"><h3><?= icon('building-2',16) ?> NetVera Kurumsal Kimlik</h3></div>
                <div class="adm-card-body nv-brand-grid">
                    <div class="form-group">
                        <label>Marka Kısa Adı</label>
                        <input class="form-control" type="text" name="brand_short_name" maxlength="100" value="<?= e(setting('brand_short_name')) ?>">
                        <input type="hidden" name="_group_brand_short_name" value="branding">
                    </div>
                    <div class="form-group">
                        <label>Ana Konumlandırma</label>
                        <input class="form-control" type="text" name="brand_positioning" maxlength="180" value="<?= e(setting('brand_positioning')) ?>">
                        <input type="hidden" name="_group_brand_positioning" value="branding">
                    </div>
                    <div class="form-group">
                        <label>Yazılım Hizmetleri Başlığı</label>
                        <input class="form-control" type="text" name="brand_sector_software" maxlength="160" value="<?= e(setting('brand_sector_software')) ?>">
                        <input type="hidden" name="_group_brand_sector_software" value="branding">
                    </div>
                    <div class="form-group">
                        <label>Dijital Ajans Başlığı</label>
                        <input class="form-control" type="text" name="brand_sector_agency" maxlength="160" value="<?= e(setting('brand_sector_agency')) ?>">
                        <input type="hidden" name="_group_brand_sector_agency" value="branding">
                    </div>
                    <div class="form-group">
                        <label>Sosyal Medya Hizmetleri Başlığı</label>
                        <input class="form-control" type="text" name="brand_sector_social" maxlength="160" value="<?= e(setting('brand_sector_social')) ?>">
                        <input type="hidden" name="_group_brand_sector_social" value="branding">
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
                        <label>Facebook URL</label>
                        <input type="url" name="social_facebook" class="form-control" value="<?= e(setting('social_facebook')) ?>">
                        <input type="hidden" name="_group_social_facebook" value="social">
                    </div>
                    <div class="form-group">
                        <label>TikTok URL</label>
                        <input type="url" name="social_tiktok" class="form-control" value="<?= e(setting('social_tiktok')) ?>">
                        <input type="hidden" name="_group_social_tiktok" value="social">
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
                <input type="hidden" name="theme_preset_enabled" value="1">
                <input type="hidden" name="_group_theme_preset_enabled" value="theme">
                <div class="adm-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3><?= icon('palette', 16) ?> Tema Renkleri</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="resetThemeDefaults()">Varsayılana Döndür</button>
                </div>
                <div class="adm-card-body">
                    
                    <!-- Preset Themes -->
                    <div class="theme-presets">
                        <?php
                        $presets = [
                            ['name' => 'NetVera Premium (Önerilen)', 'colors' => ['#6B4DE8','#13254B','#D936A1','#6B4DE8','#5136D2','#FFFFFF','#FFFFFF','#17264E','#70809C','#E2E6F3']],
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
                            ['key' => 'theme_primary', 'label' => 'Ana Marka Rengi', 'default' => '#6B4DE8'],
                            ['key' => 'theme_secondary', 'label' => 'Lacivert Metin', 'default' => '#13254B'],
                            ['key' => 'theme_accent', 'label' => 'Vurgu / Geçiş Rengi', 'default' => '#D936A1'],
                            ['key' => 'theme_button', 'label' => 'Buton Rengi', 'default' => '#6B4DE8'],
                            ['key' => 'theme_button_hover', 'label' => 'Buton Hover', 'default' => '#5136D2'],
                            ['key' => 'theme_bg', 'label' => 'Sayfa Zemini', 'default' => '#FFFFFF'],
                            ['key' => 'theme_card', 'label' => 'Kart Zemini', 'default' => '#FFFFFF'],
                            ['key' => 'theme_text', 'label' => 'Metin Rengi', 'default' => '#17264E'],
                            ['key' => 'theme_muted', 'label' => 'İkincil Metin', 'default' => '#70809C'],
                            ['key' => 'theme_border', 'label' => 'Kenarlıklar', 'default' => '#E2E6F3'],
                        ];
                        foreach ($colorFields as $idx => $cf): 
                            $val = setting('theme_preset_enabled','0')==='1' ? setting($cf['key'], $cf['default']) : $cf['default'];
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
                    </div>
                    <div class="form-group">
                        <label>Varsayılan Meta Açıklama</label>
                        <textarea name="default_seo_description" class="form-control" rows="3"><?= e(setting('default_seo_description')) ?></textarea>
                        <input type="hidden" name="_group_default_seo_description" value="seo">
                    </div>
                    <div class="form-group">
                        <label>Varsayılan Open Graph Başlığı</label>
                        <input name="seo_og_title" type="text" class="form-control" maxlength="180" value="<?= e(setting('seo_og_title')) ?>">
                        <input type="hidden" name="_group_seo_og_title" value="seo">
                    </div>
                    <div class="form-group">
                        <label>Varsayılan Open Graph Açıklaması</label>
                        <textarea name="seo_og_description" class="form-control" rows="3" maxlength="500"><?= e(setting('seo_og_description')) ?></textarea>
                        <input type="hidden" name="_group_seo_og_description" value="seo">
                    </div>
                    <div class="form-group mb-0">
                        <label>Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" class="form-control" value="<?= e(setting('google_analytics_id')) ?>" placeholder="G-XXXXXXXXXX">
                        <input type="hidden" name="_group_google_analytics_id" value="seo">
                    </div>
                </div>
            </div>
            
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('globe',16) ?> Kurumsal GEO / AIO</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Kuruluş Schema Türü</label>
                        <select class="form-control" name="seo_org_type">
                            <?php foreach(['Organization'=>'Organization','ProfessionalService'=>'ProfessionalService'] as $k=>$label): ?>
                            <option value="<?= e($k) ?>" <?= setting('seo_org_type')===$k?'selected':'' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="_group_seo_org_type" value="geo">
                    </div>
                    <div class="form-group">
                        <label>Kurumsal Tanım</label>
                        <textarea class="form-control" rows="4" name="seo_org_description" maxlength="750"><?= e(setting('seo_org_description')) ?></textarea>
                        <input type="hidden" name="_group_seo_org_description" value="geo">
                    </div>
                    <div class="form-group">
                        <label>GEO Kurum Özeti</label>
                        <textarea class="form-control" rows="4" name="seo_geo_summary" maxlength="750"><?= e(setting('seo_geo_summary')) ?></textarea>
                        <input type="hidden" name="_group_seo_geo_summary" value="geo">
                    </div>
                    <div class="form-group">
                        <label>İlişkili Sektörler ve Varlıklar</label>
                        <textarea class="form-control" rows="4" name="seo_entity_topics" maxlength="1000"><?= e(setting('seo_entity_topics')) ?></textarea>
                        <input type="hidden" name="_group_seo_entity_topics" value="geo">
                    </div>
                    <div class="form-group">
                        <label>Hizmet Bölgesi</label>
                        <input class="form-control" type="text" name="seo_service_area" maxlength="120" value="<?= e(setting('seo_service_area')) ?>">
                        <input type="hidden" name="_group_seo_service_area" value="geo">
                    </div>
                    <div class="form-group">
                        <label>Ana Sayfa Arama Niyeti</label>
                        <select class="form-control" name="seo_content_intent">
                            <?php foreach(['commercial'=>'Ticari araştırma','transactional'=>'Satın alma / hizmet','informational'=>'Bilgilendirme'] as $intent=>$name): ?>
                            <option value="<?= e($intent) ?>" <?= setting('seo_content_intent')===$intent?'selected':'' ?>><?= e($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="_group_seo_content_intent" value="aio">
                    </div>
                    <div class="form-group">
                        <label>AIO Ana Soru</label>
                        <input class="form-control" name="seo_home_question" type="text" maxlength="250" value="<?= e(setting('seo_home_question')) ?>">
                        <input type="hidden" name="_group_seo_home_question" value="aio">
                    </div>
                    <div class="form-group">
                        <label>AIO Doğrudan Yanıt</label>
                        <textarea class="form-control" name="seo_home_answer" rows="5" maxlength="900"><?= e(setting('seo_home_answer')) ?></textarea>
                        <input type="hidden" name="_group_seo_home_answer" value="aio">
                    </div>
                    <div class="form-group mb-0">
                        <label>Ana Sayfa Robots</label>
                        <select class="form-control" name="seo_default_robots">
                            <?php foreach(['index,follow,max-image-preview:large','index,follow','noindex,follow'] as $rule): ?>
                            <option value="<?= e($rule) ?>" <?= setting('seo_default_robots')===$rule?'selected':'' ?>><?= e($rule) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="_group_seo_default_robots" value="seo">
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
.nv-brand-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.nv-brand-grid .form-group{margin:0}.nv-brand-grid .form-group:nth-child(2){grid-column:span 1}@media(max-width:700px){.nv-brand-grid{grid-template-columns:1fr}}
.theme-presets { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 25px; }
.theme-preset-btn { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border: 1px solid var(--color-border); border-radius: 10px; background: #fff; cursor: pointer; transition: 0.2s; font-size: 13px; font-weight: 600; color: var(--color-dark, #1E293B); }
.theme-preset-btn:hover { border-color: #6B4DE8; box-shadow: 0 0 0 3px rgba(107,77,232,0.1); }
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
