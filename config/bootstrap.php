<?php
// Autoload PSR-4 básico (App\ => app/)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) require $file;
});

// .env simple (opcional)
$env = BASE_PATH . '/.env';
if (file_exists($env)) {
    foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$k,$v] = array_map('trim', explode('=', $line, 2));
        $_ENV[$k] = $v; putenv("$k=$v");
    }
}

// Debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Sesión
if (session_status() === PHP_SESSION_NONE) session_start();

// Helpers
require BASE_PATH . '/config/helpers.php';
require_once BASE_PATH . '/config/DB.php'; // <-- temporal, para asegurar carga
