<?php

echo "========================================\n";
echo "       PHP UMS - Deployment Script        \n";
echo "========================================\n\n";

// 1. Git Pull (Optional, uncomment if you use git)
// echo "[-] Pulling latest code from git...\n";
// echo shell_exec('git pull origin main');

// 2. Composer Install
echo "[-] Installing/Updating composer dependencies...\n";
if (file_exists(__DIR__ . '/../composer.json')) {
    echo shell_exec('composer install --no-dev --optimize-autoloader');
} else {
    echo "[!] composer.json not found, skipping.\n";
}

// 3. Apply Migrations (without refresh to keep data)
echo "\n[-] Running database migrations...\n";
echo shell_exec('php ' . __DIR__ . '/migrate.php');

// 4. Clear Cache
echo "\n[-] Clearing cache...\n";
$cacheDir = __DIR__ . '/../storage/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "[-] Cleared $count cache files.\n";
} else {
    echo "[-] Cache directory not found, skipping.\n";
}

echo "\n[✓] Deployment Completed Successfully!\n";
echo "========================================\n";
