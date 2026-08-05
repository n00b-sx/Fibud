<?php

namespace App\Helpers;

class OpenMojiHelper
{
    /**
     * Convert any emoji or unicode character string into OpenMoji CDN SVG URL.
     */
    public static function getUrl(?string $emoji): string
    {
        if (empty($emoji)) {
            $emoji = '💵';
        }

        $codePoints = [];
        $length = mb_strlen($emoji, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($emoji, $i, 1, 'UTF-8');
            $code = mb_ord($char, 'UTF-8');
            // Ignore Variation Selector-16 (U+FE0F)
            if ($code !== 0xFE0F) {
                $codePoints[] = strtoupper(dechex($code));
            }
        }

        $hex = implode('-', $codePoints);

        return "https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/{$hex}.svg";
    }

    /**
     * Render an <img> tag pointing to OpenMoji SVG.
     */
    public static function render(?string $emoji, string $class = 'size-5 inline-block', string $alt = ''): string
    {
        $url = static::getUrl($emoji);
        $safeAlt = e($alt ?: $emoji);
        return sprintf(
            '<img src="%s" alt="%s" class="%s shrink-0" loading="lazy" onError="this.style.display=\'none\';" />',
            $url,
            $safeAlt,
            e($class)
        );
    }
}
