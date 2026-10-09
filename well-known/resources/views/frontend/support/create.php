<section class="section panel-section">
    <div class="container">
        <div class="panel-layout">
            <?php include __DIR__ . '/../partials/user-sidebar.php'; ?>
            <div class="panel-content">

                <div class="panel-page-header">
                    <div>
                        <h1><?= icon('plus', 24) ?> Yeni Destek Talebi</h1>
                        
                    </div>
                    <a href="/destek" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Taleplerime Dön</a>
                </div>

                <div class="panel-grid-support-create">
                    <div class="panel-card">
                        <div class="panel-card-header">
                            <h3><?= icon('send', 18) ?> Talep Formu</h3>
                        </div>
                        <div class="panel-card-body">
                            <form method="POST" action="/destek/olustur" enctype="multipart/form-data" class="panel-form">
                                <?= csrfField() ?>

                                <?php if (!empty($orders)): ?>
                                <div class="form-group">
                                    <label for="supportOrder"><?= icon('package', 14) ?> İlgili Sipariş <span class="form-hint-inline">(Opsiyonel)</span></label>
                                    <select name="order_id" id="supportOrder" class="form-control">
                                        <option value="">Sipariş seçin (opsiyonel)</option>
                                        <?php foreach ($orders as $o): ?>
                                        <option value="<?= $o['id'] ?>">#<?= e($o['order_number']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="supportSubject"><?= icon('edit', 14) ?> Konu</label>
                                    <input type="text" name="subject" id="supportSubject" class="form-control" placeholder="Talebinizin konusunu yazın" required>
                                </div>

                                <div class="form-group">
                                    <label for="supportPriority"><?= icon('alert-triangle', 14) ?> Öncelik</label>
                                    <select name="priority" id="supportPriority" class="form-control">
                                        <option value="low">Düşük</option>
                                        <option value="medium" selected>Normal</option>
                                        <option value="high">Yüksek</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="supportMessage"><?= icon('message-circle', 14) ?> Mesajınız</label>
                                    <textarea name="message" id="supportMessage" class="form-control" rows="6" placeholder="Sorununuzu veya talebinizi detaylı şekilde anlatın..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="supportAttachment"><?= icon('upload', 14) ?> Dosya Ekle <span class="form-hint-inline">(Opsiyonel)</span></label>
                                    <div class="panel-file-upload">
                                        <input type="file" name="attachment" id="supportAttachment" class="panel-file-input">
                                        <div class="panel-file-placeholder" id="filePlaceholder">
                                            <?= icon('upload', 24) ?>
                                            <span>Dosya seçmek için tıklayın veya sürükleyin</span>
                                            <span class="text-sm text-secondary">PNG, JPG, PDF - Max 5MB</span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg btn-block"><?= icon('send', 18) ?> Talebi Gönder</button>
                            </form>
                        </div>
                    </div>

                    <!-- Info sidebar -->
                    <div class="panel-support-info">
                        <?php if (setting('site_whatsapp')): ?>
                        <div class="panel-card">
                            <div class="panel-card-body">
                                <div class="panel-info-box panel-info-whatsapp">
                                    <?= icon('whatsapp', 20) ?>
                                    <div>
                                        <strong>Acil Destek</strong>
                                        
                                        <a href="https://wa.me/<?= e(setting('site_whatsapp')) ?>" target="_blank" rel="noopener" class="btn btn-success btn-sm" style="margin-top: 8px;"><?= icon('send', 14) ?> WhatsApp</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
