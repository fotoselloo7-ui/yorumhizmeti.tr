<section class="section panel-section nv62-dealer">
  <div class="container"><div class="panel-layout">
    <?php include __DIR__.'/../partials/user-sidebar.php'; ?>
    <main class="panel-content">
      <div class="panel-welcome">
        <div class="panel-welcome-text"><h1>Bayilik & İş Ortaklığı</h1>
          <p>İş ortağı başvurunuzu takip edin, referans bağlantınızı yönetin ve geçmiş NetVera haklarınızı görüntüleyin.</p>
        </div>
        <div class="panel-welcome-actions"><a href="/hazir-scriptler" class="btn btn-outline btn-sm"><?= icon('monitor',15) ?> Yazılımlar</a></div>
      </div>
      <?php if(!$dealerReady): ?>
        <div class="nv62-dealer-card">
          <h2><?= icon('database',18) ?> Bayilik modülü kurulumu bekliyor</h2>
          <p>Eski müşteri ve komisyon geçmişiniz korunur; yeni başvuru ve referans bağlantısı modülü yöneticinin veritabanı kurulumundan sonra açılır.</p>
        </div>
      <?php elseif(!$dealer): ?>
        <div class="nv62-dealer-card nv62-dealer-apply">
          <span class="nv62-dealer-eyebrow"><?= icon('handshake',15) ?> YAZILIM İŞ ORTAKLIĞI</span>
          <h2>NetVera Bayilik Programı</h2>
          <p>Yazılım çözümlerini müşterilerinize tanıtın. Başvurunuz onaylandıktan sonra size özel referans bağlantısı ve belirlenen komisyon oranı açılır.</p>
          <form method="post" action="/bayilik/basvur">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-primary"><?= icon('send',15) ?> Bayilik Başvurusu Gönder</button>
          </form>
          <small>Başvuru ücretsizdir; onay ve oranlar yönetici değerlendirmesine bağlıdır. Otomatik ödeme taahhüdü içermez.</small>
        </div>
      <?php else: ?>
        <?php
          $dealerState=(string)$dealer['status'];
          $stateNames=['pending'=>'Değerlendirmede','approved'=>'Aktif Bayi','rejected'=>'Başvuru Reddedildi','suspended'=>'Askıya Alındı'];
          $tierNames=['starter'=>'Başlangıç','pro'=>'Pro','agency'=>'Ajans'];
        ?>
        <div class="nv62-dealer-stats">
          <div class="nv62-dealer-card"><span>Bayilik Durumu</span><strong><?= e($stateNames[$dealerState]??$dealerState) ?></strong></div>
          <div class="nv62-dealer-card"><span>Bayi Seviyesi</span><strong><?= e($tierNames[$dealer['tier']]??'Başlangıç') ?></strong></div>
          <div class="nv62-dealer-card"><span>Onaylanan Komisyon Oranı</span><strong><?= $dealerState==='approved' ? e(number_format((float)$dealer['commission_rate'],2,',','.')).'%' : '—' ?></strong></div>
          <div class="nv62-dealer-card"><span>Referans Ziyaretleri</span><strong><?= $dealerState==='approved'?(int)$visits:'—' ?></strong></div>
        </div>
        <?php if($dealerState==='approved'): ?>
        <?php $refUrl='/bayi/'.rawurlencode((string)$dealer['referral_code']); ?>
        <div class="nv62-dealer-card nv62-dealer-linkbox">
          <h2><?= icon('link',18) ?> Size Özel Referans Bağlantısı</h2>
          <p>Müşterilerinizi yazılım kataloğumuza yönlendirmek için bu bağlantıyı kullanın.</p>
          <div class="nv62-copybar"><input id="nv62-dealer-link" readonly value="<?= e($refUrl) ?>" aria-label="Referans bağlantınız">
            <button type="button" data-nv62-copy><?= icon('copy',15) ?> Kopyala</button></div>
          <small>Ziyaret sayısı satış veya komisyon değildir. Komisyonlar yalnızca doğrulanmış sipariş ve sözleşme şartlarına göre hesaplanır.</small>
        </div>
        <?php else: ?>
        <div class="nv62-dealer-card"><p>Referans kodu ve komisyon koşulları hesabınız onaylandıktan sonra etkinleşir. Sorularınız için <a href="/destek/yeni">destek talebi</a> oluşturabilirsiniz.</p></div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(!empty($legacy['affiliate'])): ?>
      <div class="nv62-dealer-card">
        <h2><?= icon('history',18) ?> Eski NetVera Bayilik Geçmişiniz</h2>
        <div class="nv62-history-grid">
          <div><span>Önceki bayi durumu</span><strong><?= e($legacy['affiliate']['status']) ?></strong></div>
          <div><span>Toplam kaydedilmiş komisyon</span><strong><?= money((float)$legacy['commission_total']) ?></strong></div>
          <div><span>Ödenmiş geçmiş komisyon</span><strong><?= money((float)$legacy['paid_total']) ?></strong></div>
          <div><span>Bekleyen geçmiş komisyon</span><strong><?= money((float)$legacy['pending_total']) ?></strong></div>
        </div>
        <p class="nv62-dealer-note">Bu tutarlar eski NetVera kayıtlarının geçmiş görünümüdür. Bayilik başvurusu ya da onayı otomatik bakiye aktarımı, yeni hak ediş veya ödeme oluşturmaz.</p>
      </div>
      <?php endif; ?>
    </main>
  </div></div>
</section>
<script>
document.querySelectorAll('[data-nv62-copy]').forEach(button=>{
 button.addEventListener('click',()=>{
  const input=document.getElementById('nv62-dealer-link');
  if(!input)return;
  const path=input.value;
  const full=new URL(path,location.origin).href;
  if(navigator.clipboard?.writeText){
    navigator.clipboard.writeText(full).then(()=>{button.textContent='Kopyalandı'}).catch(()=>{input.select()});
  }else{input.select();}
 });
});
</script>
