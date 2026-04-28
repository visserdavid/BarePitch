<?php

declare(strict_types=1);

const SESSION_TIMEOUT_SECONDS = 7200;

function requireLogin(): void
{
    if (!isset($_SESSION['user_id'], $_SESSION['last_activity'])) {
        header('Location: /login.php');
        exit;
    }

    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: /login.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function sendSecurityHeaders(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");

    if (str_starts_with(getenv('APP_URL') ?: '', 'https://')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
