<?php

namespace App\Core;

class View
{
    /**
     * Render a view file with extracted variables and optional layout
     */
    public static function render(string $view, array $data = [], ?string $layout = 'app'): void
    {
        $viewFile = __DIR__ . "/../Views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$view}");
        }

        extract($data);
        
        // Start output buffering
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = __DIR__ . "/../Views/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Render a view file with extracted variables and optional layout, returning as string
     */
    public static function renderToString(string $view, array $data = [], ?string $layout = null): string
    {
        $viewFile = __DIR__ . "/../Views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$view}");
        }

        extract($data);
        
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = __DIR__ . "/../Views/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                ob_start();
                require $layoutFile;
                return ob_get_clean();
            }
        }
        
        return $content;
    }

    /**
     * Securely escape output for HTML rendering to prevent XSS
     */
    public static function escape(?string $string): string
    {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
