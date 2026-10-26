<?php
namespace App\Controllers;

/**
 * 301 compatibility for URLs already embedded in the imported NetVera blog.
 * No rewriting of archived article bodies, original product slugs or taxonomy.
 * Only this exact audited seven-path allowlist may redirect.
 */
final class LegacyEditorialLinkController
{
    public const REDIRECTS = [
        '/yapay-zeka-haber-yazilimi' => '/hazir-scriptler/haber-sitesi-scripti',
        '/dijital-hizmetler/sosyal-medya-yonetimi' => '/kategoriler/grup/social',
        '/dijital-hizmetler/modern-web-tasarim' => '/kategori/web-tasarim',
        '/dijital-hizmetler/seo-hizmetleri' => '/kategori/seo-hizmetleri',
        '/dijital-hizmetler/google-meta-ads' => '/kategori/dijital-reklam',
        '/dijital-hizmetler' => '/kategoriler',
        '/teklif-al' => '/iletisim',
    ];

    public function go(): void
    {
        $path = rtrim((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'), '/') ?: '/';
        if (!isset(self::REDIRECTS[$path])) {
            \App\Services\PublicSeoUrls::notFound();
            return;
        }
        header('Location: '.self::REDIRECTS[$path], true, 301);
    }
}
