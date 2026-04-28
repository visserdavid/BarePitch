<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/services/AuthService.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrf()) {
    http_response_code(403);
    exit('Forbidden');
}
rotateCsrfToken();

(new AuthService())->logout();

header('Location: /login.php');
exit;
