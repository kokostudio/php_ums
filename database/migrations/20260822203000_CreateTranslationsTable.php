<?php

class CreateTranslationsTable
{
    public function up(): void
    {
        $db = \App\Core\Database::getInstance();
        $sql = "
            CREATE TABLE IF NOT EXISTS translations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                locale VARCHAR(10) NOT NULL,
                namespace VARCHAR(50) NOT NULL,
                key_name VARCHAR(100) NOT NULL,
                text_value TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_translation (locale, namespace, key_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->exec($sql);
    }

    public function down(): void
    {
        $db = \App\Core\Database::getInstance();
        $db->exec("DROP TABLE IF EXISTS translations;");
    }
}
