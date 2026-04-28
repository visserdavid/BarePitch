<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/PlayerModel.php';
require_once __DIR__ . '/../app/services/PlayerService.php';

requireLogin();

$rawId     = $_GET['id'] ?? $_POST['id'] ?? '';
$rawTeamId = $_GET['team_id'] ?? $_POST['team_id'] ?? '';

$playerId = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$teamId   = filter_var($rawTeamId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($playerId === false || $teamId === false) {
    header('Location: /teams.php');
    exit;
}

$teamModel   = new TeamModel();
$playerModel = new PlayerModel();

$team = $teamModel->findForUser((int) $teamId, currentUserId());

if ($team === null) {
    header('Location: /teams.php');
    exit;
}

$player = $playerModel->findForTeam((int) $playerId, (int) $teamId, currentUserId());

if ($player === null) {
    header('Location: /players.php?team_id=' . $teamId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf()) {
        http_response_code(403);
        exit('Forbidden');
    }
    rotateCsrfToken();

    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $service = new PlayerService();
        $errors  = $service->validatePlayerInput($_POST);

        if (empty($errors)) {
            $displayName = trim($_POST['display_name'] ?? '');
            $shirtRaw    = trim($_POST['shirt_number'] ?? '');
            $shirtNumber = $shirtRaw !== '' ? (int) $shirtRaw : null;

            $playerModel->update((int) $playerId, (int) $teamId, currentUserId(), $displayName, $shirtNumber);
            setFlash('success', __('players.updated'));
            header('Location: /player_edit.php?id=' . $playerId . '&team_id=' . $teamId);
            exit;
        }

        render('players/edit', [
            'team'            => $team,
            'player'          => $player,
            'errors'          => $errors,
            'input'           => [
                'display_name' => trim($_POST['display_name'] ?? ''),
                'shirt_number' => trim($_POST['shirt_number'] ?? ''),
            ],
            'hasMatchHistory' => $playerModel->hasMatchHistory((int) $playerId),
            'flash'           => null,
        ]);
        exit;
    }

    if ($action === 'deactivate') {
        $playerModel->setInactive((int) $playerId, (int) $teamId, currentUserId());
        setFlash('success', __('players.deactivated'));
        header('Location: /players.php?team_id=' . $teamId);
        exit;
    }

    if ($action === 'delete') {
        if ($playerModel->hasMatchHistory((int) $playerId)) {
            setFlash('error', __('players.has_match_history'));
            header('Location: /player_edit.php?id=' . $playerId . '&team_id=' . $teamId);
            exit;
        }

        $playerModel->delete((int) $playerId, (int) $teamId, currentUserId());
        setFlash('success', __('players.deleted'));
        header('Location: /players.php?team_id=' . $teamId);
        exit;
    }

    header('Location: /players.php?team_id=' . $teamId);
    exit;
}

render('players/edit', [
    'team'            => $team,
    'player'          => $player,
    'errors'          => [],
    'input'           => [],
    'hasMatchHistory' => $playerModel->hasMatchHistory((int) $playerId),
    'flash'           => getFlash(),
]);
