<?php

class SeedExistingTranslations
{
    public function up(): void
    {
        $db = \App\Core\Database::getInstance();
        $langDir = __DIR__ . '/../../app/Lang';
        
        if (!is_dir($langDir)) {
            return;
        }

        $locales = ['th', 'en'];

        $stmt = $db->prepare("
            INSERT INTO translations (locale, namespace, key_name, text_value) 
            VALUES (:locale, :namespace, :key_name, :text_value)
            ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)
        ");

        foreach ($locales as $locale) {
            $localeDir = $langDir . '/' . $locale;
            if (!is_dir($localeDir)) continue;

            $files = glob($localeDir . '/*.php');
            foreach ($files as $file) {
                $namespace = basename($file, '.php');
                $data = require $file;
                
                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        $stmt->execute([
                            ':locale' => $locale,
                            ':namespace' => $namespace,
                            ':key_name' => $key,
                            ':text_value' => $value
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $db = \App\Core\Database::getInstance();
        // For down, we could clear the table, but typically we just leave it or TRUNCATE.
        // We'll leave it as is or delete everything.
        $db->exec("TRUNCATE TABLE translations");
    }
}
