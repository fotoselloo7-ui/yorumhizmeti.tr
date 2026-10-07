<?php
namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;

class SanitizerService
{
    private static array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'a', 'img', 'ul', 'ol', 'li', 
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'table', 'thead', 'tbody', 
        'tr', 'th', 'td', 'figure', 'figcaption', 'div', 'span'
    ];

    /**
     * Güvenli HTML temizliği
     */
    public static function cleanHtml(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        // Temel strip_tags ile tamamen engellenmiş tagleri baştan atalım
        $allowedTagsString = '<' . implode('><', self::$allowedTags) . '>';
        $html = strip_tags($html, $allowedTagsString);

        // DOM işlemleri ile attribute temizliği
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        
        // UTF-8 sorunlarını önlemek için HTML'i sarıyoruz
        $wrappedHtml = '<?xml encoding="UTF-8"><body>' . $html . '</body>';
        $dom->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $xpath = new DOMXPath($dom);
        
        // Tüm elementleri dön
        $nodes = $xpath->query('//*');
        foreach ($nodes as $node) {
            if ($node instanceof DOMElement) {
                // Event attribute'larını ve style'ı temizle
                for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
                    $attr = $node->attributes->item($i);
                    $attrName = strtolower($attr->name);
                    
                    if (str_starts_with($attrName, 'on') || $attrName === 'style' || $attrName === 'srcdoc') {
                        $node->removeAttributeNode($attr);
                    }
                    
                    // javascript: linkleri engelle
                    if ($attrName === 'href' || $attrName === 'src') {
                        $val = strtolower(trim($attr->value));
                        if (str_starts_with($val, 'javascript:') || str_starts_with($val, 'vbscript:') || str_starts_with($val, 'data:text/html')) {
                            $node->removeAttributeNode($attr);
                        }
                    }
                    
                    // a etiketine target blank varsa rel noopener noreferrer ekle
                    if ($node->tagName === 'a' && $attrName === 'target' && $attr->value === '_blank') {
                        $node->setAttribute('rel', 'noopener noreferrer');
                    }
                }
            }
        }

        // Script, iframe kalıntıları varsa temizle
        $badTags = ['script', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'style', 'link', 'meta'];
        foreach ($badTags as $tag) {
            $badNodes = $dom->getElementsByTagName($tag);
            while ($badNodes->length > 0) {
                $badNode = $badNodes->item(0);
                $badNode->parentNode->removeChild($badNode);
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        
        if (!$body) {
            return '';
        }

        $cleanHtml = '';
        foreach ($body->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        libxml_clear_errors();
        
        // Temizlerken kalan boş etiketleri temizleyelim
        $cleanHtml = preg_replace('/<p>\s*<\/p>/i', '', $cleanHtml);
        
        return trim($cleanHtml);
    }
}
