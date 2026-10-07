<?php $isEdit = !empty($post); ?>
<div class="adm-page-top">
    <div>
        <h2><?= icon('edit-3', 24) ?> <?= $isEdit ? 'Yazı Düzenle' : 'Yazı Ekle' ?></h2>
        <p class="text-sm text-secondary">Blog yazılarınızı buradan oluşturabilir veya düzenleyebilirsiniz.</p>
    </div>
    <a href="/admin/blog" class="btn btn-light btn-sm"><?= icon('arrow-left', 16) ?> Geri Dön</a>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/blog/' . $post['id'] . '/guncelle' : '/admin/blog/kaydet' ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    
    <div class="adm-form-layout">
        <!-- Main Content (Left) -->
        <div class="adm-form-main">
            
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('file-text', 18) ?> Temel Bilgiler</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="title">Başlık <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" value="<?= e($post['title'] ?? '') ?>" required placeholder="Yazı başlığını girin...">
                    </div>

                    <div class="form-group">
                        <label for="slug">URL Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" value="<?= e($post['slug'] ?? '') ?>" placeholder="otomatik-olusturulur">
                        <div class="form-hint">Boş bırakırsanız başlıktan otomatik üretilir. Aynı isimde varsa sonuna sayı eklenir.</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="blog_category_id">Kategori</label>
                            <select name="blog_category_id" id="blog_category_id" class="form-control">
                                <option value="">Kategorisiz</option>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($post['blog_category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tags">Etiketler</label>
                            <select name="tags[]" id="tags" class="form-control select2-tags" multiple="multiple">
                                <?php 
                                $selectedTags = $postTags ?? [];
                                foreach ($tags as $t): 
                                    $selected = in_array($t['id'], $selectedTags) ? 'selected' : '';
                                ?>
                                <option value="<?= $t['id'] ?>" <?= $selected ?>><?= e($t['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="excerpt">Kısa Özet</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="3" placeholder="Yazının kısa özeti (ana sayfada ve listelerde görünür)..."><?= e($post['excerpt'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3><?= icon('align-left', 18) ?> Akıllı Makale Editörü</h3>
                    <div style="display:flex; gap:10px;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('inline_image_upload').click()">
                            <?= icon('image', 16) ?> Makale İçi Görsel Ekle
                        </button>
                        <input type="file" id="inline_image_upload" accept="image/*" style="display:none;" onchange="uploadInlineImage(this)">
                    </div>
                </div>
                <!-- Image Upload Result Panel (hidden by default) -->
                <div id="image_upload_panel" style="display:none; padding:15px; background:#f0f9ff; border-top:1px solid var(--color-border);">
                    <div style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
                        <img id="uploaded_thumb" src="" alt="" style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid var(--color-border);">
                        <div style="flex:1; min-width:200px;">
                            <div class="form-group mb-2">
                                <label style="font-size:12px; font-weight:600;">Alt Metin</label>
                                <input type="text" id="img_alt_input" class="form-control" placeholder="Görsel açıklaması..." style="font-size:13px;">
                            </div>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="copyImgAs('url')">URL Kopyala</button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="copyImgAs('md')">Markdown Kopyala</button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="copyImgAs('shortcode')">Shortcode Kopyala</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="insertImgTo('cursor')">İmleç Konumuna Ekle</button>
                                <button type="button" class="btn btn-light btn-sm" onclick="insertImgTo('top')">En Üste Ekle</button>
                                <button type="button" class="btn btn-light btn-sm" onclick="insertImgTo('bottom')">En Alta Ekle</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Markdown Toolbar -->
                <div class="md-toolbar" style="display:flex; gap:4px; padding:8px 15px; background:#f8f9fa; border-top:1px solid var(--color-border); flex-wrap:wrap;">
                    <button type="button" class="md-btn" onclick="mdInsert('## ', '')" title="H2 Başlık"><b>H2</b></button>
                    <button type="button" class="md-btn" onclick="mdInsert('### ', '')" title="H3 Başlık"><b>H3</b></button>
                    <span style="width:1px; background:var(--color-border); margin:0 4px;"></span>
                    <button type="button" class="md-btn" onclick="mdWrap('**', '**')" title="Kalın"><i class="ri-bold"></i></button>
                    <button type="button" class="md-btn" onclick="mdWrap('*', '*')" title="İtalik"><i class="ri-italic"></i></button>
                    <span style="width:1px; background:var(--color-border); margin:0 4px;"></span>
                    <button type="button" class="md-btn" onclick="mdInsert('- ', '')" title="Liste Öğesi"><i class="ri-list-unordered"></i></button>
                    <button type="button" class="md-btn" onclick="mdInsert('1. ', '')" title="Sıralı Liste"><i class="ri-list-ordered"></i></button>
                    <button type="button" class="md-btn" onclick="mdInsert('> ', '')" title="Alıntı"><i class="ri-double-quotes-l"></i></button>
                    <span style="width:1px; background:var(--color-border); margin:0 4px;"></span>
                    <button type="button" class="md-btn" onclick="mdWrap('[', '](url)')" title="Link"><i class="ri-link"></i></button>
                    <button type="button" class="md-btn" onclick="document.getElementById('inline_image_upload').click()" title="Görsel"><i class="ri-image-add-line"></i></button>
                    <span style="width:1px; background:var(--color-border); margin:0 4px;"></span>
                    <button type="button" class="md-btn" onclick="mdInsert('\n[info]Bilgi metni[/info]\n', '')" title="Info Kutusu">Info</button>
                    <button type="button" class="md-btn" onclick="mdInsert('\n[cta title=&quot;Başlık&quot; text=&quot;Açıklama&quot; url=&quot;/&quot; button=&quot;İncele&quot;]\n', '')" title="CTA">CTA</button>
                </div>
                <div class="adm-card-body" style="padding: 0;">
                    <div class="editor-container" style="display: flex; flex-direction: row; border-top: 1px solid var(--color-border);">
                        <div class="editor-pane" style="flex: 1; border-right: 1px solid var(--color-border);">
                            <textarea name="raw_content" id="raw_content" class="form-control" style="width:100%; height: 600px; border:none; resize:none; padding:15px; font-family: monospace; font-size: 14px;" placeholder="Makalenizi buraya yazın veya yapıştırın. (Markdown destekler)"><?= e($post['raw_content'] ?? '') ?></textarea>
                        </div>
                        <div class="preview-pane" style="flex: 1; height: 600px; overflow-y: auto; padding: 15px; background: #fdfdfd;">
                            <div id="live_preview" class="article-content" style="max-width: 100%; margin: 0;">
                                <!-- Canlı önizleme buraya gelecek -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="adm-card-footer" style="padding: 10px 15px; background: #f8f9fa; border-top: 1px solid var(--color-border); text-align: right;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updatePreview()">Önizlemeyi Yenile</button>
                </div>
            </div>

            <!-- FAQ Builder -->
            <div class="adm-card">
                <div class="adm-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3><?= icon('help-circle', 18) ?> Sıkça Sorulan Sorular (FAQ)</h3>
                    <button type="button" class="btn btn-light btn-sm" onclick="addFaqRow()"><?= icon('add', 16) ?> Soru Ekle</button>
                </div>
                <div class="adm-card-body">
                    <p class="text-sm text-secondary mb-3">Bu alana eklediğiniz sorular otomatik olarak sayfanın sonuna şık bir akordiyon ve Google FAQ Schema olarak eklenecektir.</p>
                    <div id="faq_container">
                        <?php 
                        $faqs = !empty($post['faqs']) ? json_decode($post['faqs'], true) : [];
                        if (is_array($faqs) && count($faqs) > 0):
                            foreach ($faqs as $i => $faq):
                        ?>
                        <div class="faq-row" style="background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px; margin-bottom: 15px; position: relative;">
                            <button type="button" class="btn btn-danger btn-sm" style="position: absolute; right: 15px; top: 15px; padding: 4px 8px;" onclick="this.parentElement.remove()"><?= icon('delete-bin', 14) ?></button>
                            <div class="form-group">
                                <label>Soru</label>
                                <input type="text" name="faq_questions[]" class="form-control" value="<?= e($faq['question']) ?>" required>
                            </div>
                            <div class="form-group mb-0">
                                <label>Cevap</label>
                                <textarea name="faq_answers[]" class="form-control" rows="2" required><?= e($faq['answer']) ?></textarea>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Sidebar (Right) -->
        <div class="adm-form-sidebar">
            <button type="submit" class="adm-action-btn adm-btn-save">
                <?= icon('save', 16) ?> <?= $isEdit ? 'Değişiklikleri Kaydet' : 'Yazıyı Yayınla' ?>
            </button>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('settings', 16) ?> Yayın Durumu</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active" <?= ($post['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Yayında</option>
                            <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Taslak</option>
                            <option value="scheduled" <?= ($post['status'] ?? '') === 'scheduled' ? 'selected' : '' ?> disabled>Planlandı (Otomatik)</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="published_at">Yayın Tarihi</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="<?= ($post['published_at'] ?? '') ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i') ?>">
                        <div class="form-hint">İleri bir tarih seçerseniz otomatik olarak planlanır.</div>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('image', 18) ?> Görseller</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Kapak Görseli</label>
                        <?php if (!empty($post['image'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= e(upload_url($post['image'])) ?>" alt="Cover" style="max-width:100%; border-radius: 4px;">
                        </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="image_alt">Kapak Görsel Alt Metni</label>
                        <input type="text" name="image_alt" id="image_alt" class="form-control" value="<?= e($post['image_alt'] ?? '') ?>">
                    </div>
                    
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid var(--color-border);">
                    
                    <div class="form-group mb-0">
                        <label>OG (Sosyal Medya) Görseli (Opsiyonel)</label>
                        <?php if (!empty($post['og_image'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= e(upload_url($post['og_image'])) ?>" alt="OG" style="max-width:100%; border-radius: 4px;">
                        </div>
                        <?php endif; ?>
                        <input type="file" name="og_image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3><?= icon('search', 16) ?> Gelişmiş SEO</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="seo_title">SEO Title</label>
                        <input type="text" name="seo_title" id="seo_title" class="form-control" value="<?= e($post['seo_title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="seo_description">Meta Description</label>
                        <textarea name="seo_description" id="seo_description" class="form-control" rows="3"><?= e($post['seo_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="seo_focus_keyword">Focus Keyword</label>
                        <input type="text" name="seo_focus_keyword" id="seo_focus_keyword" class="form-control" value="<?= e($post['seo_focus_keyword'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="canonical_url">Canonical URL (Opsiyonel)</label>
                        <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="<?= e($post['canonical_url'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="schema_type">Schema Tipi</label>
                        <select name="schema_type" id="schema_type" class="form-control">
                            <option value="Article" <?= ($post['schema_type'] ?? '') == 'Article' ? 'selected' : '' ?>>Article (Standart)</option>
                            <option value="NewsArticle" <?= ($post['schema_type'] ?? '') == 'NewsArticle' ? 'selected' : '' ?>>NewsArticle (Haber)</option>
                            <option value="BlogPosting" <?= ($post['schema_type'] ?? '') == 'BlogPosting' ? 'selected' : '' ?>>BlogPosting (Blog)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="d-flex align-items-center" style="gap:10px;">
                            <input type="checkbox" name="noindex" value="1" <?= !empty($post['noindex']) ? 'checked' : '' ?>>
                            Arama Motorlarına Kapat (NoIndex)
                        </label>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="reading_time">Okuma Süresi (Dk)</label>
                        <input type="number" name="reading_time" id="reading_time" class="form-control" value="<?= e($post['reading_time'] ?? '') ?>" placeholder="Otomatik hesaplanır">
                    </div>
                </div>
            </div>

            <!-- Open Graph Ayarları -->
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3><?= icon('share', 16) ?> Open Graph (Sosyal Medya)</h3>
                </div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label for="og_title">OG Title</label>
                        <input type="text" name="og_title" id="og_title" class="form-control" value="<?= e($post['og_title'] ?? '') ?>" placeholder="Boşsa SEO Title kullanılır">
                    </div>
                    <div class="form-group mb-0">
                        <label for="og_description">OG Description</label>
                        <textarea name="og_description" id="og_description" class="form-control" rows="2" placeholder="Boşsa Meta Desc kullanılır"><?= e($post['og_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<!-- JS Kütüphaneleri (Select2 & TinyMCE) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container .select2-selection--multiple { border-color: var(--color-border); border-radius: 8px; min-height: 44px; }
.select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: var(--color-light); border: 1px solid var(--color-border); border-radius: 4px; padding: 4px 8px; margin-top: 6px; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2-tags').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: "Etiket ekleyin..."
    });

    // Otomatik Önizleme (Debounce)
    let timeout = null;
    $('#raw_content').on('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            updatePreview();
        }, 800); // 800ms bekler
    });
    
    // İlk açılışta önizlemeyi yükle
    if ($('#raw_content').val().trim() !== '') {
        updatePreview();
    }

    // Gelişmiş Paste Yakalama (Ctrl+V)
    document.getElementById('raw_content').addEventListener('paste', function(e) {
        let clipboardData = e.clipboardData || window.clipboardData;
        if (!clipboardData) return;
        
        let htmlData = clipboardData.getData('text/html');
        if (htmlData) {
            e.preventDefault();
            
            // Basit bir DOM parse işlemi yapıp HTML'i Markdown'a çevireceğiz
            let parser = new DOMParser();
            let doc = parser.parseFromString(htmlData, 'text/html');
            let body = doc.body;
            
            // Gereksiz etiketleri temizle
            ['script', 'style', 'iframe', 'meta', 'link', 'object', 'embed'].forEach(tag => {
                let els = body.querySelectorAll(tag);
                els.forEach(el => el.remove());
            });
            
            // Recursive dönüştürücü
            function convertNodeToMarkdown(node) {
                if (node.nodeType === 3) { // Text node
                    return node.nodeValue;
                }
                if (node.nodeType !== 1) return ''; // Element değilse yoksay
                
                let text = '';
                
                // Çocukları dönüştür
                node.childNodes.forEach(child => {
                    text += convertNodeToMarkdown(child);
                });
                
                let tag = node.nodeName.toLowerCase();
                
                // Link dönüştürme (javascript: engelle)
                if (tag === 'a') {
                    let href = node.getAttribute('href') || '';
                    if (href.toLowerCase().startsWith('javascript:')) href = '#';
                    return `[${text}](${href})`;
                }
                
                // Kalın yazı
                if (tag === 'strong' || tag === 'b') {
                    return `**${text}**`;
                }
                
                // İtalik
                if (tag === 'em' || tag === 'i') {
                    return `*${text}*`;
                }
                
                // Başlıklar
                if (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(tag)) {
                    let level = parseInt(tag.charAt(1));
                    return '\n\n' + '#'.repeat(level) + ' ' + text.trim() + '\n\n';
                }
                
                // Liste elemanı
                if (tag === 'li') {
                    // Parent ol ise sayı, ul ise tire
                    let parentTag = node.parentNode ? node.parentNode.nodeName.toLowerCase() : '';
                    if (parentTag === 'ol') {
                        return `1. ${text.trim()}\n`;
                    } else {
                        return `- ${text.trim()}\n`;
                    }
                }
                
                // Liste konteyneri
                if (tag === 'ul' || tag === 'ol') {
                    return '\n\n' + text + '\n';
                }
                
                // Paragraf veya blok
                if (tag === 'p' || tag === 'div') {
                    return '\n\n' + text.trim() + '\n\n';
                }
                
                // Alıntı
                if (tag === 'blockquote') {
                    return '\n\n> ' + text.trim() + '\n\n';
                }
                
                return text;
            }
            
            let markdown = convertNodeToMarkdown(body);
            
            // Çoklu boşlukları temizle
            markdown = markdown.replace(/\n{3,}/g, '\n\n').trim();
            
            // Textarea'ya ekle
            insertAtCursor(this, markdown);
            updatePreview();
        }
    });
});

function updatePreview() {
    const content = $('#raw_content').val();
    $.post('/admin/blog/preview', { raw_content: content }, function(response) {
        let html = response.html;
        
        // SSS (FAQ) Önizlemesi
        if (response.faqs && response.faqs.length > 0) {
            html += '<div class="article-faq" style="margin-top:40px;"><h3>Sıkça Sorulan Sorular</h3><div class="faq-accordion">';
            response.faqs.forEach(function(f, i) {
                html += '<div class="faq-item" style="border:1px solid #ddd; margin-bottom:10px; border-radius:8px; padding:15px;">';
                html += '<div style="font-weight:bold; color:var(--primary-color); margin-bottom:5px;">' + f.question + '</div>';
                html += '<div style="font-size:14px; color:#555;">' + f.answer + '</div>';
                html += '</div>';
            });
            html += '</div></div>';
        }
        
        $('#live_preview').html(html);
    }, 'json');
}

var _lastUploadedUrl = '';

function uploadInlineImage(input) {
    if (input.files && input.files[0]) {
        let formData = new FormData();
        formData.append('file', input.files[0]);
        let fileName = input.files[0].name.replace(/\.[^.]+$/, '').replace(/[_-]/g, ' ');
        
        $.ajax({
            url: '/admin/blog/upload-image',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.location) {
                    _lastUploadedUrl = response.location;
                    document.getElementById('uploaded_thumb').src = response.location;
                    document.getElementById('img_alt_input').value = fileName;
                    document.getElementById('image_upload_panel').style.display = 'block';
                }
            },
            error: function(xhr) {
                alert('Görsel yüklenirken hata oluştu: ' + xhr.responseText);
            }
        });
        input.value = '';
    }
}

function copyImgAs(type) {
    const alt = document.getElementById('img_alt_input').value || '';
    let text = '';
    if (type === 'url') text = _lastUploadedUrl;
    else if (type === 'md') text = '![' + alt + '](' + _lastUploadedUrl + ')';
    else if (type === 'shortcode') text = '[image src="' + _lastUploadedUrl + '" alt="' + alt + '"]';
    navigator.clipboard.writeText(text).then(() => { alert('Kopyalandı!'); });
}

function insertImgTo(pos) {
    const alt = document.getElementById('img_alt_input').value || '';
    const shortcode = '\n[image src="' + _lastUploadedUrl + '" alt="' + alt + '" caption=""]\n';
    const ta = document.getElementById('raw_content');
    if (pos === 'top') {
        ta.value = shortcode + ta.value;
    } else if (pos === 'bottom') {
        ta.value = ta.value + shortcode;
    } else {
        insertAtCursor(ta, shortcode);
    }
    updatePreview();
}

function mdInsert(prefix, suffix) {
    const ta = document.getElementById('raw_content');
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const sel = ta.value.substring(start, end) || 'metin';
    const before = ta.value.substring(0, start);
    const after = ta.value.substring(end);
    ta.value = before + prefix + sel + suffix + after;
    ta.selectionStart = start + prefix.length;
    ta.selectionEnd = start + prefix.length + sel.length;
    ta.focus();
    clearTimeout(window._mdTimer);
    window._mdTimer = setTimeout(function(){ updatePreview(); }, 500);
}

function mdWrap(before, after) {
    const ta = document.getElementById('raw_content');
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const sel = ta.value.substring(start, end) || 'metin';
    const pre = ta.value.substring(0, start);
    const post = ta.value.substring(end);
    ta.value = pre + before + sel + after + post;
    ta.selectionStart = start + before.length;
    ta.selectionEnd = start + before.length + sel.length;
    ta.focus();
    clearTimeout(window._mdTimer);
    window._mdTimer = setTimeout(function(){ updatePreview(); }, 500);
}

function insertAtCursor(myField, myValue) {
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    } else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
        myField.selectionStart = startPos + myValue.length;
        myField.selectionEnd = startPos + myValue.length;
    } else {
        myField.value += myValue;
    }
    myField.focus();
}

function addFaqRow() {
    const container = document.getElementById('faq_container');
    const row = document.createElement('div');
    row.className = 'faq-row';
    row.style.cssText = 'background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px; margin-bottom: 15px; position: relative;';
    row.innerHTML = `
        <button type="button" class="btn btn-danger btn-sm" style="position: absolute; right: 15px; top: 15px; padding: 4px 8px;" onclick="this.parentElement.remove()">Sil</button>
        <div class="form-group">
            <label>Soru</label>
            <input type="text" name="faq_questions[]" class="form-control" required>
        </div>
        <div class="form-group mb-0">
            <label>Cevap</label>
            <textarea name="faq_answers[]" class="form-control" rows="2" required></textarea>
        </div>
    `;
    container.appendChild(row);
}
</script>

<style>
.md-btn { padding: 6px 10px; border: 1px solid var(--color-border); border-radius: 6px; background: #fff; cursor: pointer; font-size: 13px; font-weight: 600; color: #334155; transition: 0.2s; display: flex; align-items: center; justify-content: center; min-width: 32px; }
.md-btn:hover { background: #2563EB; color: #fff; border-color: #2563EB; }
.md-btn i { font-size: 16px; }
</style>
