<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/MatchModel.php';
require_once __DIR__ . '/../app/services/MatchService.php';

requireLogin();

$rawId     = $_GET['id'] ?? $_POST['id'] ?? '';
$rawTeamId = $_GET['team_id'] ?? $_POST['team_id'] ?? '';

$matchId = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$teamId  = filter_var($rawTeamId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($matchId === false || $teamId === false) {
    header('Location: /teams.php');
    exit;
}

$teamModel  = new TeamModel();
$matchModel = new MatchModel();

$team = $teamModel->findForUser((int) $teamId, currentUserId());

if ($team === null) {
    header('Location: /teams.php');
    exit;
}

$match = $matchModel->findForTeam((int) $matchId, (int) $teamId, currentUserId());

if ($match === null) {
    header('Location: /matches.php?team_id=' . $teamId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf()) {
        http_response_code(403);
        exit('Forbidden');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $service = new MatchService();
        $errors  = $service->validateMatchInput($_POST);

        if (empty($errors)) {
            $opponentName = trim($_POST['opponent_name'] ?? '');
            $matchDate    = trim($_POST['match_date'] ?? '');
            $kickoffTime  = trim($_POST['kickoff_time'] ?? '') ?: null;
            $location     = trim($_POST['location'] ?? '') ?: null;
            $homeAway     = trim($_POST['home_away'] ?? '') ?: null;

            $matchModel->update((int) $matchId, (int) $teamId, currentUserId(), $opponentName, $matchDate, $kickoffTime, $location, $homeAway);
            setFlash('success', __('matches.updated'));
            header('Location: /match_edit.php?id=' . $matchId . '&team_id=' . $teamId);
            exit;
        }

        render('matches/edit', [
            'team'   => $team,
            'match'  => $match,
            'errors' => $errors,
            'input'  => [
                'opponent_name' => trim($_POST['opponent_name'] ?? ''),
                'match_date'    => trim($_POST['match_date'] ?? ''),
                'kickoff_time'  => trim($_POST['kickoff_time'] ?? ''),
                'location'      => trim($_POST['location'] ?? ''),
                'home_away'     => trim($_POST['home_away'] ?? ''),
            ],
            'flash'  => null,
        ]);
        exit;
    }

    if ($action === 'archive') {
        $matchModel->archive((int) $matchId, (int) $teamId, currentUserId());
        setFlash('success', __('matches.archived'));
        header('Location: /matches.php?team_id=' . $teamId);
        exit;
    }

    header('Location: /matches.php?team_id=' . $teamId);
    exit;
}

render('matches/edit', [
    'team'   => $team,
    'match'  => $match,
    'errors' => [],
    'input'  => [],
    'flash'  => getFlash(),
]);
