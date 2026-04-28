<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/models/TeamModel.php';

requireLogin();

$user  = (new UserModel())->findById(currentUserId());
$teams = (new TeamModel())->findAllForUser(currentUserId());

render('dashboard/index', [
    'displayName' => $user['display_name'] ?? 'Coach',
    'teamCount'   => count($teams),
    'currentPage' => 'dashboard',
]);
