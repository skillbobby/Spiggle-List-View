<?php

namespace Spiggle\ListView\Support;

class ListAvatar
{
    public static function initials(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '•';
        }

        if (str_contains($name, '@')) {
            $name = strstr($name, '@', true) ?: $name;
            $name = str_replace(['.', '_', '-'], ' ', $name);
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $parts = array_values(array_filter($parts, fn (string $part): bool => $part !== ''));

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1));
        }

        return strtoupper(substr($parts[0] ?? 'U', 0, 2));
    }

    public static function dataUri(string $name): string
    {
        $initials = htmlspecialchars(self::initials($name), ENT_QUOTES | ENT_XML1, 'UTF-8');
        $hue = abs(crc32($name)) % 360;
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="32" fill="hsl({$hue},62%,42%)"/>
  <text x="32" y="38" text-anchor="middle" font-family="Roboto,Arial,sans-serif" font-size="22" font-weight="500" fill="#fff">{$initials}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
