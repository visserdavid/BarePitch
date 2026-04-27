<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/PlayerModel.php';
require_once __DIR__ . '/../app/services/PlayerService.php';

requireLogin();

$rawTeamId = $_GET['team_id'] ?? $_POST['team_id'] ?? '';
$teamId = filter_var($rawTeamId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($teamId === false) {
    header('Location: /teams.php');
    exit;
}

$team = (new TeamModel())->findForUser((int) $teamId, currentUserId());

if ($team === null) {
    header('Location: /teams.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf()) {
        http_response_code(403);
        exit('Forbidden');
    }

    $service = new PlayerService();
    $errors  = $service->validatePlayerInput($_POST);

    if (empty($errors)) {
        $displayName = trim($_POST['display_name'] ?? '');
        $shirtRaw    = trim($_POST['shirt_number'] ?? '');
        $shirtNumber = $shirtRaw !== '' ? (int) $shirtRaw : null;

        (new PlayerModel())->create((int) $teamId, $displayName, $shirtNumber);
        setFlash('success', __('players.created'));
        header('Location: /players.php?team_id=' . $teamId);
        exit;
    }

    render('players/create', [
        'team'   => $team,
        'errors' => $errors,
        'input'  => [
            'display_name' => trim($_POST['display_name'] ?? ''),
            'shirt_number' => trim($_POST['shirt_number'] ?? ''),
        ],
    ]);
    exit;
}

render('players/create', [
    'team'   => $team,
    'errors' => [],
    'input'  => [],
]);
