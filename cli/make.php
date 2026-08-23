<?php

/**
 * UMS CLI Code Generator
 * Usage: php cli/make.php [type] [name]
 * Types: controller, model
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

$type = $argv[1] ?? null;
$name = $argv[2] ?? null;

if (!$type || !$name) {
    echo "Usage: php cli/make.php [type] [name]\n";
    echo "Types: controller, model\n";
    exit(1);
}

$baseDir = dirname(__DIR__);

function createController($name, $baseDir) {
    $className = ucfirst($name) . 'Controller';
    $viewFolder = strtolower($name);
    
    $stubPath = $baseDir . '/cli/stubs/controller.stub';
    $targetPath = $baseDir . '/app/Controllers/' . $className . '.php';
    
    if (file_exists($targetPath)) {
        echo "Error: Controller {$className} already exists.\n";
        exit(1);
    }
    
    $content = file_get_contents($stubPath);
    $content = str_replace('{{CLASS_NAME}}', $className, $content);
    $content = str_replace('{{VIEW_FOLDER}}', $viewFolder, $content);
    
    file_put_contents($targetPath, $content);
    echo "Created Controller: {$className} ({$targetPath})\n";
    
    // Create View Folder
    $viewDirPath = $baseDir . '/app/Views/' . $viewFolder;
    if (!is_dir($viewDirPath)) {
        mkdir($viewDirPath, 0777, true);
        echo "Created View Directory: {$viewDirPath}\n";
    }
}

function createModel($name, $baseDir) {
    $className = ucfirst($name) . 'Model';
    // Basic pluralization for table name
    $tableName = strtolower($name) . 's';
    
    $stubPath = $baseDir . '/cli/stubs/model.stub';
    $targetPath = $baseDir . '/app/Models/' . $className . '.php';
    
    if (file_exists($targetPath)) {
        echo "Error: Model {$className} already exists.\n";
        exit(1);
    }
    
    if (!is_dir(dirname($targetPath))) {
        mkdir(dirname($targetPath), 0777, true);
    }
    
    $content = file_get_contents($stubPath);
    $content = str_replace('{{CLASS_NAME}}', $className, $content);
    $content = str_replace('{{TABLE_NAME}}', $tableName, $content);
    
    file_put_contents($targetPath, $content);
    echo "Created Model: {$className} ({$targetPath})\n";
}

switch (strtolower($type)) {
    case 'controller':
        createController($name, $baseDir);
        break;
    case 'model':
        createModel($name, $baseDir);
        break;
    default:
        echo "Error: Invalid type '{$type}'. Allowed: controller, model\n";
        exit(1);
}

echo "Success!\n";
