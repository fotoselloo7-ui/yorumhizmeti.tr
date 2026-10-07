<div class="adm-page-top">
    <div>
        <h2><?= icon('layout', 24) ?> Ana Sayfa Bölümleri</h2>
        <p class="text-sm text-secondary">Ana sayfadaki her bölümü buradan düzenleyebilirsiniz.</p>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Sıra</th>
                        <th>Bölüm</th>
                        <th>Anahtar</th>
                        <th>Alt Başlık</th>
                        <th>Durum</th>
                        <th style="width: 120px; text-align:right;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><span class="adm-cat-badge"><?= (int) $section['sort_order'] ?></span></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:var(--space-2);">
                                <?= icon($section['icon_key'] ?? 'package', 18) ?>
                                <strong><?= e($section['title'] ?? '-') ?></strong>
                            </div>
                        </td>
                        <td><code style="font-size: var(--font-size-xs); background: var(--color-bg); padding:2px 6px; border-radius:4px;"><?= e($section['section_key']) ?></code></td>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= e($section['subtitle'] ?? '') ?></td>
                        <td>
                            <form method="POST" action="/admin/ana-sayfa/<?= $section['id'] ?>/durum" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="status-badge <?= $section['status'] === 'active' ? 'success' : 'danger' ?>" style="border:none; cursor:pointer;">
                                    <?= $section['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right;">
                            <a href="/admin/ana-sayfa/<?= $section['id'] ?>/duzenle" class="btn btn-outline btn-sm">
                                <?= icon('edit', 14) ?> Düzenle
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="adm-mobile-only" style="display: none; padding: 15px;">
            <?php foreach ($sections as $section): ?>
            <div class="adm-order-card-m" style="margin-bottom: var(--space-4); background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px;">
                <div class="adm-order-card-m-top" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span class="font-semibold" style="display:flex; align-items:center; gap:8px;">
                        <?= icon($section['icon_key'] ?? 'package', 16) ?>
                        <?= e($section['title'] ?? '-') ?>
                    </span>
                    <span class="status-badge <?= $section['status'] === 'active' ? 'success' : 'danger' ?>"><?= $section['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                </div>
                <div class="adm-order-card-m-body">
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                        <span class="text-secondary">Anahtar</span>
                        <code><?= e($section['section_key']) ?></code>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:10px;">
                        <span class="text-secondary">Sıra</span>
                        <span class="adm-cat-badge"><?= (int) $section['sort_order'] ?></span>
                    </div>
                    <div style="text-align:right;">
                        <a href="/admin/ana-sayfa/<?= $section['id'] ?>/duzenle" class="btn btn-outline btn-sm">
                            <?= icon('edit', 14) ?> Düzenle
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
