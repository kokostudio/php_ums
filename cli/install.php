<?php

echo "========================================\n";
echo "       PHP UMS - Installation Script      \n";
echo "========================================\n\n";

// 1. Copy .env
$envFile = __DIR__ . '/../.env';
$envExample = __DIR__ . '/../.env.example';

if (!file_exists($envFile)) {
    if (file_exists($envExample)) {
        copy($envExample, $envFile);
        echo "[-] Copied .env.example to .env\n";

        // สุ่ม APP_KEY ให้อัตโนมัติถ้ายังว่างอยู่ (ใช้ทั้ง Hashids salt และ AES-256-GCM)
        $envContent = file_get_contents($envFile);
        $generatedKey = 'base64:' . base64_encode(random_bytes(32));
        if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $m) && trim($m[1]) === '') {
            $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $generatedKey, $envContent, 1);
            file_put_contents($envFile, $envContent);
            echo "[-] Generated random APP_KEY\n";
        }
    } else {
        echo "[!] .env.example not found. Please create .env manually.\n";
    }
} else {
    echo "[-] .env already exists.\n";
}

// 2. Create required directories
$dirs = [
    '/../storage',
    '/../storage/logs',
    '/../storage/cache',
    '/../public/uploads',
    '/../public/uploads/settings',
    '/../public/uploads/profiles'
];

foreach ($dirs as $dir) {
    $path = __DIR__ . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
        echo "[-] Created directory: " . ltrim($dir, '/..') . "\n";
    } else {
        // Ensure writable
        @chmod($path, 0775);
    }
}
echo "[-] Storage and upload directories are set up.\n";

// 3. Database configuration reminder
echo "\n[!] Please ensure you have configured your database in the .env file.\n";
echo "Press 'y' to run migrations and seed the database, or any other key to exit: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'y') {
    echo "\n[-] Running Database Migrations (Fresh)...\n";
    $output = shell_exec('php ' . __DIR__ . '/migrate.php --refresh');
    echo $output;
    echo "\n[✓] Installation Completed Successfully!\n";
} else {
    echo "\n[-] Exited. When ready, you can run: php cli/migrate.php --refresh\n";
}

echo "========================================\n";
