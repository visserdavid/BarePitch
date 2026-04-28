<?php

declare(strict_types=1);

// Resolve project root (two levels up from app/config/)
$root = dirname(__DIR__, 2);

// Load .env file
$envFile = $root . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

// Error reporting
if (getenv('APP_ENV') === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', $root . '/storage/logs/error.log');
}

// Session with secure defaults
session_name(getenv('SESSION_NAME') ?: 'barepitch_session');
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure'   => str_starts_with(getenv('APP_URL') ?: '', 'https://'),
    'use_strict_mode' => true,
]);

require_once __DIR__ . '/database.php';
require_once dirname(__DIR__) . '/helpers/lang.php';
require_once dirname(__DIR__) . '/helpers/view.php';
require_once dirname(__DIR__) . '/helpers/auth.php';
sendSecurityHeaders();
require_once dirname(__DIR__) . '/helpers/csrf.php';
require_once dirname(__DIR__) . '/helpers/flash.php';
