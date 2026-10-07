<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/paketler" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <div>
            <h2><?= icon('list', 22) ?> Sipariş Alanları</h2>
            <p class="text-sm text-secondary"><?= e($package['name']) ?></p>
        </div>
    </div>
    <a href="/admin/paket/<?= $package['id'] ?>/duzenle" class="btn btn-outline btn-sm"><?= icon('edit', 14) ?> Paketi Düzenle</a>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h3><?= icon('edit', 18) ?> Alan Tanımları</h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="addField()"><?= icon('plus', 14) ?> Alan Ekle</button>
    </div>
    <div class="adm-card-body">
        <div class="adm-fields-info">
            <?= icon('info', 16) ?>
            <span>Müşterinin sipariş sırasında dolduracağı alanları tanımlayın. Örn: URL, kullanıcı adı, hedef kitle vb.</span>
        </div>

        <form method="POST" action="/admin/paket/<?= $package['id'] ?>/alanlar-kaydet">
            <?= csrfField() ?>
            <div id="fieldsContainer">
                <?php if (empty($fields)): ?>
                <div class="adm-empty-sm" id="noFieldsMsg" style="padding: var(--space-6);">
                    <?= icon('list', 28) ?>
                    <p>Henüz alan tanımlanmamış. Yukarıdaki "Alan Ekle" butonunu kullanın.</p>
                </div>
                <?php endif; ?>
                <?php foreach ($fields as $i => $f): ?>
                <div class="adm-field-card">
                    <div class="adm-field-card-header">
                        <div class="adm-field-card-grip"><?= icon('menu', 14) ?></div>
                        <span class="adm-field-card-num"><?= $i + 1 ?></span>
                        <button type="button" class="adm-action-btn adm-action-danger" onclick="if(confirm('Bu alanı silmek istediğinize emin misiniz?')) this.closest('.adm-field-card').remove()"><?= icon('trash', 14) ?></button>
                    </div>
                    <div class="adm-field-card-body">
                        <div class="adm-field-grid">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="text-xs">Key</label>
                                <input type="text" name="field_key[]" class="form-control" value="<?= e($f['field_key']) ?>" required placeholder="ör: instagram_url">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="text-xs">Label</label>
                                <input type="text" name="field_label[]" class="form-control" value="<?= e($f['field_label']) ?>" required placeholder="ör: Instagram URL">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="text-xs">Tip</label>
                                <select name="field_type[]" class="form-control">
                                    <option value="text" <?= $f['field_type']==='text'?'selected':'' ?>>Text</option>
                                    <option value="url" <?= $f['field_type']==='url'?'selected':'' ?>>URL</option>
                                    <option value="textarea" <?= $f['field_type']==='textarea'?'selected':'' ?>>Textarea</option>
                                    <option value="number" <?= $f['field_type']==='number'?'selected':'' ?>>Number</option>
                                    <option value="select" <?= $f['field_type']==='select'?'selected':'' ?>>Select</option>
                                    <option value="checkbox" <?= $f['field_type']==='checkbox'?'selected':'' ?>>Checkbox</option>
                                    <option value="phone" <?= $f['field_type']==='phone'?'selected':'' ?>>Phone</option>
                                    <option value="email" <?= $f['field_type']==='email'?'selected':'' ?>>Email</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="text-xs">Zorunlu</label>
                                <select name="field_required[<?= $i ?>]" class="form-control">
                                    <option value="1" <?= $f['is_required']?'selected':'' ?>>Evet</option>
                                    <option value="0" <?= !$f['is_required']?'selected':'' ?>>Hayır</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="text-xs">Placeholder</label>
                                <input type="text" name="field_placeholder[]" class="form-control" value="<?= e($f['placeholder'] ?? '') ?>" placeholder="ör: https://...">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="adm-field-actions">
                <button type="button" class="btn btn-outline btn-sm" onclick="addField()"><?= icon('plus', 14) ?> Yeni Alan Ekle</button>
                <button type="submit" class="btn btn-primary"><?= icon('save', 16) ?> Alanları Kaydet</button>
            </div>
        </form>
    </div>
</div>

<script>
function addField() {
    var noMsg = document.getElementById('noFieldsMsg');
    if (noMsg) noMsg.remove();
    var c = document.getElementById('fieldsContainer');
    var i = c.querySelectorAll('.adm-field-card').length;
    var html = '<div class="adm-field-card adm-field-new">' +
        '<div class="adm-field-card-header">' +
            '<div class="adm-field-card-grip"><?= icon('menu', 14) ?></div>' +
            '<span class="adm-field-card-num">' + (i + 1) + '</span>' +
            '<button type="button" class="adm-action-btn adm-action-danger" onclick="if(confirm(\'Bu alanı silmek istediğinize emin misiniz?\')) this.closest(\'.adm-field-card\').remove()"><?= icon('trash', 14) ?></button>' +
        '</div>' +
        '<div class="adm-field-card-body"><div class="adm-field-grid">' +
            '<div class="form-group" style="margin-bottom:0;"><label class="text-xs">Key</label><input type="text" name="field_key[]" class="form-control" required placeholder="ör: instagram_url"></div>' +
            '<div class="form-group" style="margin-bottom:0;"><label class="text-xs">Label</label><input type="text" name="field_label[]" class="form-control" required placeholder="ör: Instagram URL"></div>' +
            '<div class="form-group" style="margin-bottom:0;"><label class="text-xs">Tip</label><select name="field_type[]" class="form-control"><option value="text">Text</option><option value="url">URL</option><option value="textarea">Textarea</option><option value="number">Number</option><option value="select">Select</option><option value="checkbox">Checkbox</option><option value="phone">Phone</option><option value="email">Email</option></select></div>' +
            '<div class="form-group" style="margin-bottom:0;"><label class="text-xs">Zorunlu</label><select name="field_required[' + i + ']" class="form-control"><option value="1">Evet</option><option value="0">Hayır</option></select></div>' +
            '<div class="form-group" style="margin-bottom:0;"><label class="text-xs">Placeholder</label><input type="text" name="field_placeholder[]" class="form-control" placeholder="ör: https://..."></div>' +
        '</div></div></div>';
    c.insertAdjacentHTML('beforeend', html);
}
</script>
