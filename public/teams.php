<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';

requireLogin();

$teams = (new TeamModel())->findAllForUser(currentUserId());
$flash = getFlash();

render('teams/index', [
    'teams' => $teams,
    'flash' => $flash,
]);
