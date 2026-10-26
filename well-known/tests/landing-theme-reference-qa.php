<?php
declare(strict_types=1);

/** Reference-design improvements must not alter the shared editorial data. */
if (PHP_SAPI !== 'cli') exit(2);
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'App\\')) require BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
});
require BASE_PATH . '/app/Core/Helpers.php';

use App\Services\LandingThemeService as Theme;
use App\Services\RegionalLandingService as Page;

$checks = 0;
$ok = static function (bool $condition, string $why) use (&$checks): void {
    if (!$condition) throw new RuntimeException($why);
    $checks++;
};
$rejects = static function (array $data, string $why) use ($ok): void {
    try { Page::validate($data); } catch (InvalidArgumentException $error) { $ok(true, $why); return; }
    $ok(false, $why);
};
$normal = static fn(string $text): string => trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
$parse = static function (string $html): DOMXPath {
    $document = new DOMDocument();
    @$document->loadHTML('<?xml encoding="UTF-8">' . $html);
    return new DOMXPath($document);
};
$render = static function (array $data, string $themeKey, array $sources): string {
    $d = $data;
    $seo = $d['seo'];
    $profile = Theme::profile($themeKey);
    $blocks = Theme::blocks($d, $sources);
    $mode = 'Uzaktan / çevrim içi';
    $landingPreview = false;
    $landingContacts = [['url' => '/iletisim', 'label' => 'İletişim', 'icon' => 'mail']];
    $landingServices = $sources['services'];
    $landingScripts = $sources['scripts'];
    $landingBlogs = $sources['blog'];
    $landingReferences = $sources['references'];
    ob_start();
    require BASE_PATH . '/resources/views/frontend/landing/themes/page.php';
    return (string)ob_get_clean();
};

$legacy = Page::starter('reklam');
$ok(($legacy['location_image'] ?? null) === '' && ($legacy['location_image_alt'] ?? null) === '', 'Legacy payload has optional empty location fields');
$input = $legacy;
$input['location_image'] = '/assets/img/category-heroes/web.webp';
$input['location_image_alt'] = 'Sentetik kent tanıtım fotoğrafı';
$clean = Page::validate($input);
$ok($clean['location_image'] === $input['location_image'] && $clean['location_image_alt'] === $input['location_image_alt'], 'Independent location image survives validation');
$ok($clean['image'] === $legacy['image'] && $clean['mobile_image'] === $legacy['mobile_image'], 'Location image does not replace foreground or mobile art');
foreach (['https://example.test/city.jpg', '//example.test/city.jpg', '/uploads/../city.png', '/assets/img/city.svg', '/assets/img/city.webp?x=1', 'javascript:alert(1)', ['not-a-path']] as $unsafe) {
    $bad = $input;
    $bad['location_image'] = $unsafe;
    $rejects($bad, 'Unsafe location image rejected');
}
$bad = $input;
$bad['location_image_alt'] = '';
$rejects($bad, 'Location image requires an editorial alt description');
$bad['location_image_alt'] = str_repeat('x', 221);
$rejects($bad, 'Location alt is bounded');
$recovered = Page::editorInput(['location_image' => '/uploads/location.webp', 'location_image_alt' => 'Düzeltilmek üzere saklanan metin'], $legacy);
$ok($recovered['location_image'] === '/uploads/location.webp' && $recovered['location_image_alt'] === 'Düzeltilmek üzere saklanan metin', 'Failed form keeps independently entered location fields');

// A non-reference city/service catches accidentally pasted Alanya/İstanbul mockup copy.
$d = $clean;
$d['title'] = 'Bursa İçin Yazılım Hizmeti';
$d['city'] = 'Bursa';
$d['province'] = 'Bursa';
$d['service'] = 'Yazılım Hizmeti';
$d['area'] = 'Bursa hizmet bölgesi';
$d['slug'] = 'bursa-sentetik-yazilim';
$d['summary'] = 'Çalışma kapsamını ve gerçek hizmetleri karşılaştırın.';
$d['local_context'] = 'Bölgenizdeki kapsamı [hizmetler](/kategoriler) üzerinden inceleyin.';
$d['sections'] = [
    ['title' => 'Keşif ve analiz', 'body' => 'Hedefleri ve kapsamı birlikte belirleriz.', 'subheading' => 'İlk görüşme', 'detail' => 'Teslimler ve sınırlar yazılı olarak belirlenir.', 'image' => '', 'alt' => ''],
    ['title' => 'Planlama', 'body' => 'Öncelikler onayınıza sunulur.', 'subheading' => '', 'detail' => '', 'image' => '', 'alt' => ''],
    ['title' => 'Uygulama', 'body' => 'Onaylanan kapsam uygulanır.', 'subheading' => '', 'detail' => '', 'image' => '/assets/img/category-heroes/web.webp', 'alt' => 'Sentetik uygulama görünümü'],
    ['title' => 'Değerlendirme', 'body' => 'Gerçek veriler değerlendirilir; sıra garantisi verilmez.', 'subheading' => '', 'detail' => '', 'image' => '', 'alt' => ''],
];
$d['faqs'] = [['question' => 'Kapsam nasıl belirlenir?', 'answer' => 'Teklif öncesinde [iletişime geçin](/iletisim).']];
$d['seo']['main_question'] = 'Yazılım hizmeti nasıl planlanır?';
$d['seo']['direct_answer'] = 'İhtiyaçlar ve teslimler birlikte değerlendirilir.';
$d['seo']['geo_summary'] = 'Çevrim içi çalışma kapsamı teklif sırasında netleştirilir.';
$d['seo']['focus_keyword'] = 'Bursa Yazılım Hizmeti';
$d['seo']['title'] = 'Bursa Yazılım Hizmeti | NetVera';
$d['seo']['description'] = $d['summary'];
$d['seo']['service_area'] = 'Bursa';
$d['links'] = [['label' => 'Gerçek hizmetler', 'path' => '/kategoriler']];
$sources = [
    'services' => [['name' => 'Sentetik hizmet', 'slug' => 'sentetik-hizmet', 'description' => 'Veri kaynağındaki hizmet açıklaması.']],
    'scripts' => [['title' => 'Sentetik yazılım', 'url' => '/hazir-scriptler/sentetik-yazilim', 'demo_url' => 'https://demo.example.test/', 'description' => 'Veri kaynağındaki yazılım açıklaması.', 'image' => '/assets/img/category-heroes/web.webp', 'category' => 'Yazılım']],
    'blog' => [['title' => 'Sentetik rehber', 'url' => '/blog/sentetik-rehber', 'description' => 'Veri kaynağındaki rehber açıklaması.', 'image' => '', 'image_alt' => '', 'category' => 'Rehber']],
    'references' => [['title' => 'Sentetik portföy', 'description' => 'Veri kaynağındaki portföy açıklaması.', 'image' => '/assets/img/category-heroes/web.webp', 'url' => 'https://example.test/portfolio']],
];
$headingBaseline = null;
$linkBaseline = null;
$blockBaseline = null;
foreach (Theme::all() as $key => $profile) {
    $html = $render($d, $key, $sources);
    $xp = $parse($html);
    $ok(!preg_match('/class="nvl-(?:scene|board|hero-art|place-card|search-card|dimension-target)/', $html), 'No foreground advertising art or floating panels: ' . $key);
    $ok($xp->query('//*[contains(@class,"nvl-city-backdrop")]/img')->length === 1, 'One managed background image: ' . $key);
    $ok($xp->query('//h1')->length === 1 && $normal($xp->query('//h1')->item(0)->textContent) === $d['title'], 'Exact single H1: ' . $key);
    $accent = $xp->query('//h1//*[contains(concat(" ",normalize-space(@class)," ")," nvl-hero-title-accent ")]');
    $ok($accent->length === 1 && $normal($accent->item(0)->textContent) === $d['service'], 'Service accent preserves title copy: ' . $key);
    $ok($xp->query('//*[contains(concat(" ",normalize-space(@class)," ")," nvl-hero-benefits ")]/*')->length === 3, 'Three neutral hero benefits: ' . $key);
    $ok(str_contains($html, $d['location_image']) && str_contains($html, $d['location_image_alt']), 'Configured city image and description rendered: ' . $key);
    $ok(!preg_match('/Google Partner|\+?%\s*(?:98|128|230|246|280|300|320)|500\+|250\+|Müşteri Memnuniyeti|4\.9\s*[★⭐]|Alanya|İstanbul/u', strip_tags($html)), 'No fabricated mockup claims or fixed reference city: ' . $key);
    $headings = [];
    foreach ($xp->query('//h1|//h2|//h3') as $heading) $headings[] = $heading->nodeName . ':' . $normal($heading->textContent);
    $links = [];
    foreach ($xp->query('//a[@href]') as $link) $links[] = $link->getAttribute('href');
    $types = [];
    foreach ($xp->query('//*[@data-component]') as $block) $types[] = $block->getAttribute('data-component');
    $headingBaseline ??= $headings;
    $linkBaseline ??= $links;
    $blockBaseline ??= $types;
    $ok($headings === $headingBaseline, 'All editorial heading levels/copy match across themes: ' . $key);
    $ok($links === $linkBaseline, 'All real links and order match across themes: ' . $key);
    $ok($types === $blockBaseline && count($types) === 9, 'Eight data blocks plus CTA retained in order: ' . $key);
    $ok($xp->query('//*[@data-component="services"]//*[contains(concat(" ",normalize-space(@class)," ")," nvl-service-card ")]')->length === 1, 'One service remains one card: ' . $key);
    foreach (['scripts', 'blog', 'references'] as $kind) {
        $ok($xp->query('//*[@data-component="' . $kind . '"]//*[contains(concat(" ",normalize-space(@class)," ")," nvl-card ")]')->length === 1, 'Sparse catalog does not fabricate cards: ' . $key . ' ' . $kind);
    }
    $ok($xp->query('//*[@data-component="scripts"]//a[contains(concat(" ",normalize-space(@class)," ")," nvl-demo-link ")][@href="https://demo.example.test/"]')->length === 1,
        'An eligible public demo gets a direct second action: ' . $key);
    $ok(!str_contains($html, 'Veri kaynağındaki yazılım açıklaması.') &&
        !str_contains($html, 'Veri kaynağındaki rehber açıklaması.') &&
        str_contains($html, 'Veri kaynağındaki portföy açıklaması.'),
        'Software and blog excerpts omitted without altering reference descriptions: ' . $key);
    $ok($xp->query('//details/summary')->length === 1, 'Native FAQ count retained: ' . $key);
    foreach ($d['sections'] as $section) $ok(str_contains($html, e($section['body'])), 'Full step body retained: ' . $key);
    $ok(str_contains($html, e($d['sections'][0]['detail'])) && str_contains($html, e($d['sections'][0]['subheading'])), 'Step subheading and detail not truncated: ' . $key);
    $ok($xp->query('//script|//style')->length === 0, 'No inline script/stylesheet in themed renderer: ' . $key);
    // The existing, site-wide IconService emits this one bounded size attribute.
    // Do not require a global icon-system rewrite for a body-only theme change.
    $safeInline = true;
    foreach ($xp->query('//*[@style]') as $styled) {
        if ($styled->nodeName !== 'i'
            || !in_array('icon', explode(' ', $styled->getAttribute('class')), true)
            || !preg_match('/^font-size:[0-9]{1,3}px$/D', $styled->getAttribute('style'))) $safeInline = false;
    }
    $ok($safeInline, 'No new inline layout declarations beyond existing IconService size: ' . $key);
    $ok(!str_contains($html, '<header class="site-header') && !str_contains($html, '<footer class="site-footer'), 'Shared site chrome not duplicated: ' . $key);

    $empty = $d;
    $empty['location_image'] = '';
    $empty['location_image_alt'] = '';
    $empty['image'] = '';
    $empty['mobile_image'] = '';
    $empty['image_alt'] = '';
    $empty['sections'] = [];
    $empty['faqs'] = [];
    $empty['local_context'] = '';
    $empty['seo']['direct_answer'] = '';
    $empty['seo']['geo_summary'] = '';
    $emptySources = array_fill_keys(array_keys($sources), []);
    $emptyHtml = $render($empty, $key, $emptySources);
    $emptyXp = $parse($emptyHtml);
    $ok($emptyXp->query('//h1')->length === 1 && $emptyXp->query('//*[@data-component]')->length === 1, 'Missing image/data stays safe and omits empty blocks: ' . $key);
    $ok(!str_contains($emptyHtml, 'src=""') && !str_contains($emptyHtml, 'srcset=""') && !str_contains($emptyHtml, $d['location_image_alt']), 'No broken empty image or stale city visual: ' . $key);
}

// Software-product category definitions must never leak into the services showcase.
foreach (['nakliye-lojistik-scripti', 'mobil-uygulama-sablonlari',
    'teknik-servis-scripti', 'wordpress-temalari'] as $productSlug) {
    $ok(\App\Services\LandingCatalogService::isSoftwareOnlyCategory(['slug' => $productSlug]),
        'Ready software category excluded from service picker: ' . $productSlug);
}
foreach (['seo-hizmetleri', 'web-tasarim', 'mobil-uygulama-hizmetleri'] as $serviceSlug) {
    $ok(!\App\Services\LandingCatalogService::isSoftwareOnlyCategory(['slug' => $serviceSlug]),
        'Agency service preserved in service picker: ' . $serviceSlug);
}
$ok(\App\Services\LandingCatalogService::isSoftwareOnlyCategory([
    'slug' => 'child-entry', 'parent_category_slug' => 'hazir-yazilim-scriptleri'
]), 'Children of software catalog cannot appear as agency services');

$form = file_get_contents(BASE_PATH . '/resources/views/admin/landing/form.php');
$admin = file_get_contents(BASE_PATH . '/app/Controllers/Admin/LandingController.php');
$ok(str_contains($form, 'location_image') && str_contains($form, 'location_image_alt'), 'Admin exposes editable city photo and alt');
$ok(str_contains($admin, "'location_image'") && str_contains($admin, 'Content::upload'), 'City photo uses existing validated upload pipeline');

/* Regression: missing closing comments previously swallowed entire theme layouts.
   Keep real per-theme compositions and a legible user-uploaded mobile hero. */
$themeCss = file_get_contents(PUBLIC_PATH . '/assets/css/landing-themes.css');
$ok($themeCss !== false && substr_count($themeCss, '/*') === substr_count($themeCss, '*/'),
    'Theme stylesheet comments are balanced');
foreach (array_keys(Theme::all()) as $key) {
    $ok(str_contains($themeCss, 'data-theme=' . $key),
        'Theme has independent body-scoped composition: ' . $key);
}
$ok(str_contains($themeCss, '@media(min-width:801px)') && str_contains($themeCss, '@media(max-width:800px)'),
    'Reference layouts have desktop and tablet/mobile variants');
$ok(str_contains($themeCss, '.nvl-hero.nvl-has-location .nvl-stage{padding-bottom:'),
    'Uploaded hero photo has reserved space below mobile text and CTA');
$ok(str_contains($themeCss, '[data-component=services][data-variant=bento]') &&
    str_contains($themeCss, '[data-component=services][data-variant=rows]') &&
    str_contains($themeCss, '[data-component=scripts][data-variant=wide-covers]'),
    'Services and software use layout variations rather than only color changes');
// Regression: the regional builder manages SSS per page in its own tab.
$ok(str_contains($form, "'faq'=>['help-circle','SSS']") &&
    str_contains($form, 'data-builder-panel="faq"') &&
    str_contains($form, 'name="faqs[<?= $i ?>][question]"') &&
    str_contains($form, 'name="faqs[<?= $i ?>][answer]"'),
    'Each landing editor exposes an independent FAQ tab with persisted question and answer controls');
$sharedFields = file_get_contents(BASE_PATH . '/resources/views/admin/landing/content-fields.php');
$ok(str_contains($sharedFields, 'if($contentHome):') &&
    str_contains($sharedFields, 'Soru / cevap'),
    'Home FAQ editing remains available without duplicate regional FAQ inputs');
$ok(str_contains($themeCss, 'Regional landing refinement: layouts only') &&
    str_contains($themeCss, 'grid-template-columns:repeat(2,minmax(0,1fr))!important') &&
    str_contains($themeCss, 'object-fit:contain!important') &&
    str_contains($themeCss, '.nvl-section-overview .nvl-overview-illustrated') &&
    str_contains($themeCss, '[data-component="content"] .nvl-articles'),
    'All theme variants get responsive full-cover software grid and readable overview/process');
$ok(str_contains(file_get_contents(BASE_PATH . '/resources/views/layouts/app.php'), 'nvFooterSocialVisible >= 4') &&
    str_contains(file_get_contents(PUBLIC_PATH . '/assets/css/home-footer-motion-v49.css'), 'footer-social') &&
    str_contains(file_get_contents(BASE_PATH . '/resources/views/layouts/app.php'), 'NetVera; web tasarım'),
    'Shared footer description shortened and existing social controls limited to four');
$ok(str_contains($themeCss, 'Final all-theme blog media containment') &&
    str_contains($themeCss, '[data-component="blog"] .nvl-card-grid .nvl-card-media img') &&
    str_contains($themeCss, 'object-fit:contain!important') &&
    str_contains($themeCss, 'aspect-ratio:16/10!important'),
    'All ten themes keep real blog cover images bounded without cropping or overflow');
$ok(str_contains($themeCss, 'Mobile two-column landing card collections') &&
    str_contains($themeCss, '[data-component="services"] .nvl-service-grid') &&
    str_contains($themeCss, '[data-component="scripts"],[data-component="blog"],[data-component="references"]') &&
    str_contains($themeCss, 'repeat(2, minmax(0,1fr))'),
    'All regional service, script, reference and blog grids use the two-column mobile layout');
$layout = file_get_contents(BASE_PATH . '/resources/views/layouts/app.php');
$ok(str_contains($layout, 'class="nv-footer-about"') &&
    str_contains($layout, 'NetVera; web tasarım'),
    'Shared NetVera footer contains the visible brand and services description');
$ok(str_contains($themeCss, 'Final CTA parity: every regional landing theme') &&
    str_contains($themeCss, '[data-component="scripts"] .nvl-card-footer .nvl-card-link:first-child') &&
    str_contains($themeCss, '[data-component="scripts"] .nvl-card-footer .nvl-demo-link') &&
    str_contains($themeCss, 'min-height:36px') &&
    str_contains($themeCss, '@media(max-width:360px)'),
    'Across ten themes the Inspect Software and Demo buttons have equal height with narrow-screen fallback');
echo "PASS $checks location-image/reference-semantic/sparse-data/no-fabricated-proof checks\n";
