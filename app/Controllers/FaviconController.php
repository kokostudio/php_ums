<?php

namespace App\Controllers;

use App\Core\BrandIcon;
use App\Core\Settings;

class FaviconController
{
    public function render(): void
    {
        $defaultIcon  = BrandIcon::DEFAULT_ICON;
        $defaultColor = BrandIcon::DEFAULT_COLOR;

        try {
            $iconKey = $_GET['icon'] ?? Settings::get('logo_icon', $defaultIcon);
            $color   = $_GET['color'] ?? Settings::get('logo_icon_color', $defaultColor);
        } catch (\Throwable $e) {
            $iconKey = $_GET['icon'] ?? $defaultIcon;
            $color   = $_GET['color'] ?? $defaultColor;
        }

        $icon = BrandIcon::get($iconKey);

        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: public, max-age=86400');

        $pathsHtml = '';
        foreach ($icon['paths'] as $p) {
            $pathsHtml .= '<path fill="' . htmlspecialchars($color) . '" d="' . htmlspecialchars($p) . '"/>';
        }

        echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="64" height="64">
  {$pathsHtml}
</svg>
SVG;
        exit;
    }
}
