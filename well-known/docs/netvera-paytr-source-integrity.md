# Netvera orijinal ödeme modülü değişmezlik denetimi

Kaynak yedekte doğrulanan SHA-256 parmak izleri:
- PayTRProvider.php: a12296e2843849a57144318142f5c18a4b0f45249f64ebb9c69d13ebad6f2aa6
- PaymentGateway.php: 1a51eb071d5a56d39c73f1da2ca2dd3c5aa3002d729c62ad444ba197f28acdc6
- OrderController.php: b8bdea015f65659f7435da0c87d2a032b715c92dc3350390813e9ce3aa55d063

Bu parmak izleri orijinal kaynakla birebir karşılaştırılmadan canlı ödeme entegrasyonu birleştirilmez.

Callback testleri: geçerli test imzası kabul, yanlış hash ve hatalı sipariş kodu ret. Gerçek para hareketi yapılmadı.

**Canlı ödeme modülleri henüz yeni sisteme aktarılmış değildir.** İlgili order/status/idempotency ilişkileri ayrı uyumluluk testleriyle tamamlanmalıdır.
