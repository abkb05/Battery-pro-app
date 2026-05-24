<?php
/**
 * Simple PSR-4 Autoloader for Core PHP MVC structure
 */

// Load Composer autoload first (for external libraries like JWT, mPDF, PhpSpreadsheet)
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/../' . $classPath . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }

    // Fallback: look for class in common directories (controllers, models, helpers)
    $paths = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../helpers/',
        __DIR__ . '/../config/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});

$appConfig = [];
// Load application configuration
if (file_exists(__DIR__ . '/app.php')) {
    $appConfig = require __DIR__ . '/app.php';
    // Set timezone from config if defined
    if (!empty($appConfig['timezone'])) {
        date_default_timezone_set($appConfig['timezone']);
    }
}
// Make config globally accessible for api files etc.
$GLOBALS['appConfig'] = $appConfig;
// Load authentication helper functions
require_once __DIR__ . '/../helpers/auth_helper.php';
