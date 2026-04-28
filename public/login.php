<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/services/AuthService.php';

$auth = new AuthService();

if ($auth->isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf()) {
        http_response_code(403);
        exit('Forbidden');
    }
    $ip       = $_SERVER['REMOTE_ADDR'];
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $error    = null;

    if ($auth->isRateLimited($ip)) {
        $error = 'Invalid email or password.';
        render('auth/login', ['error' => $error, 'email' => $email]);
        exit;
    }

    rotateCsrfToken();

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $auth->recordFailedAttempt($ip);
        $error = 'Invalid email or password.';
    } elseif (strlen($password) < 10) {
        $auth->recordFailedAttempt($ip);
        $error = 'Invalid email or password.';
    } else {
        if ($auth->login($email, $password)) {
            $auth->clearAttempts($ip);
            header('Location: /dashboard.php');
            exit;
        }
        $auth->recordFailedAttempt($ip);
        $error = 'Invalid email or password.';
    }

    render('auth/login', ['error' => $error, 'email' => $email]);
    exit;
}

render('auth/login', []);
