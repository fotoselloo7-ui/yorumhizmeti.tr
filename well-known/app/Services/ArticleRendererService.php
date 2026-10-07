<?php
namespace App\Services;

class ArticleRendererService
{
    /**
     * Replaces shortcodes with actual HTML components
     *
     * @param string $html
     * @return string
     */
    public static function render(string $html): string
    {
        // [info]...[/info]
        $html = preg_replace_callback('/\[info\](.*?)\[\/info\]/is', function($matches) {
            return '<div class="article-callout callout-info"><i class="ri-information-line"></i> <div>' . trim($matches[1]) . '</div></div>';
        }, $html);

        // [warning]...[/warning]
        $html = preg_replace_callback('/\[warning\](.*?)\[\/warning\]/is', function($matches) {
            return '<div class="article-callout callout-warning"><i class="ri-alert-line"></i> <div>' . trim($matches[1]) . '</div></div>';
        }, $html);

        // [image src="..." alt="..." caption="..."]
        $html = preg_replace_callback('/\[image([^\]]+)\]/is', function($matches) {
            $attrs = self::parseAttributes($matches[1]);
            $src = $attrs['src'] ?? '';
            $alt = $attrs['alt'] ?? '';
            $caption = $attrs['caption'] ?? '';
            
            if (!$src) return '';

            $out = '<figure class="article-image">';
            $out .= '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '" loading="lazy">';
            if ($caption) {
                $out .= '<figcaption>' . htmlspecialchars($caption) . '</figcaption>';
            }
            $out .= '</figure>';
            
            return $out;
        }, $html);

        // [cta title="..." text="..." button="..." url="..."]
        $html = preg_replace_callback('/\[cta([^\]]+)\]/is', function($matches) {
            $attrs = self::parseAttributes($matches[1]);
            $title = $attrs['title'] ?? 'Destek Alın';
            $text = $attrs['text'] ?? '';
            $button = $attrs['button'] ?? 'Tıklayın';
            $url = $attrs['url'] ?? '#';

            return '
            <div class="article-cta">
                <div class="article-cta-content">
                    <h4>' . htmlspecialchars($title) . '</h4>
                    <p>' . htmlspecialchars($text) . '</p>
                </div>
                <a href="' . htmlspecialchars($url) . '" target="_blank" class="article-cta-btn">' . htmlspecialchars($button) . '</a>
            </div>';
        }, $html);

        // [package id="12"] or [service id="5"] fallback gracefully
        $html = preg_replace_callback('/\[(package|service)\s+id=["\']?(\d+)["\']?\]/is', function($matches) {
            $type = $matches[1];
            $id = (int)$matches[2];
            
            // In a real scenario we'd query DB for package info.
            // Since we can't easily inject Database here gracefully without increasing coupling,
            // we will just return a placeholder or an empty string if we can't render it.
            // Or we could use App\Core\Database::getInstance() directly.
            try {
                $db = \App\Core\Database::getInstance();
                if ($type === 'package') {
                    $pkg = $db->fetch("SELECT name, slug FROM packages WHERE id = ? AND status='active'", [$id]);
                    if ($pkg) {
                        return '<div class="article-package-card"><i class="ri-box-3-line"></i> <div><h5>' . htmlspecialchars($pkg['name']) . '</h5><a href="/paket/' . htmlspecialchars($pkg['slug']) . '">Paketi İncele</a></div></div>';
                    }
                } else {
                    $cat = $db->fetch("SELECT name, slug FROM categories WHERE id = ? AND status='active'", [$id]);
                    if ($cat) {
                        return '<div class="article-package-card"><i class="ri-folder-add-line"></i> <div><h5>' . htmlspecialchars($cat['name']) . '</h5><a href="/kategori/' . htmlspecialchars($cat['slug']) . '">Hizmeti İncele</a></div></div>';
                    }
                }
            } catch (\Exception $e) {
                // Ignore DB errors
            }
            
            return '';
        }, $html);

        return $html;
    }

    private static function parseAttributes(string $attrString): array
    {
        $attributes = [];
        // Matches key="value" or key='value'
        if (preg_match_all('/(\w+)\s*=\s*(["\'])(.*?)\2/is', $attrString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes[strtolower($match[1])] = $match[3];
            }
        }
        return $attributes;
    }
}
