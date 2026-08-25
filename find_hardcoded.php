<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Views'));
$results = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        preg_match_all('/>([^<\{\}]+)</', $content, $matches);
        foreach ($matches[1] as $match) {
            $text = trim($match);
            if (strlen($text) > 2 && preg_match('/^[a-zA-Z]/', $text)) {
                // Ignore standard variables, common JS, etc.
                if (strpos($text, 'function') === 0 || strpos($text, 'var ') === 0 || strpos($text, 'let ') === 0) continue;
                $results[$file->getPathname()][] = $text;
            }
        }
    }
}
echo json_encode(array_filter($results), JSON_PRETTY_PRINT);
