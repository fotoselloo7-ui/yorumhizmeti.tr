<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme - <?= e(setting('site_name')) ?></title>
    <style>body{margin:0;padding:0;background:#f8fafc;display:flex;align-items:center;justify-content:center;min-height:100vh;}iframe{border:none;width:100%;max-width:460px;height:600px;}</style>
</head>
<body>
    <?php if ($gateway === 'paytr'): ?>
        <iframe src="https://www.paytr.com/odeme/guvenli/<?= e($iframeToken) ?>" frameborder="0" scrolling="no"></iframe>
    <?php endif; ?>
</body>
</html>
