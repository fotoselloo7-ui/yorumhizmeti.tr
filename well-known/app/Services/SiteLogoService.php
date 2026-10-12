<?php
namespace App\Services;

/**
 * Public brand logo: use the shipped NetVera mark until an admin explicitly
 * uploads a replacement. Old imported site_logo paths cannot hide the mark.
 * This class does not alter the DB or any product/category routes.
 */
final class SiteLogoService
{
    public const DEFAULT_ASSET = 'img/netvera-logo-current.svg';
    public const FOOTER_DEFAULT_ASSET = 'img/netvera-logo-footer-white.svg';

    public static function defaultUrl(): string
    {
        return \asset(self::DEFAULT_ASSET);
    }

    public static function footerDefaultUrl(): string
    {
        return \asset(self::FOOTER_DEFAULT_ASSET);
    }

    public static function customUrl(): ?string
    {
        // This flag is set only by the administrator's actual logo upload.
        // Older imported site_logo values are deliberately not treated as active.
        if (\setting('site_logo_mode', 'default') !== 'custom') {
            return null;
        }

        $stored = trim((string) \setting('site_logo', ''));
        if ($stored === '') {
            return null;
        }
        $url = \upload_url($stored);
        $path = parse_url($url, PHP_URL_PATH);

        // The existing uploader writes to public/uploads/branding.
        // A missing file must never produce a broken logo in the header.
        if (is_string($path) && str_starts_with($path, '/uploads/')) {
            $full = PUBLIC_PATH . $path;
            return is_file($full) ? $url : null;
        }

        // Allow valid externally hosted HTTPS logos only when explicitly
        // configured as custom. Client-side image fallback handles outages.
        if (filter_var($url, FILTER_VALIDATE_URL)
            && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https') {
            return $url;
        }
        return null;
    }

    public static function url(): string
    {
        return self::customUrl() ?? self::defaultUrl();
    }

    public static function footerUrl(): string
    {
        return self::customUrl() ?? self::footerDefaultUrl();
    }

    public static function isCustom(): bool
    {
        return self::customUrl() !== null;
    }
}
