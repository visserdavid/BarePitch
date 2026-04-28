<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/MatchModel.php';
require_once __DIR__ . '/../app/services/MatchService.php';

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
    rotateCsrfToken();

    $service = new MatchService();
    $errors  = $service->validateMatchInput($_POST);

    if (empty($errors)) {
        $opponentName = trim($_POST['opponent_name'] ?? '');
        $matchDate    = trim($_POST['match_date'] ?? '');
        $kickoffTime  = trim($_POST['kickoff_time'] ?? '') ?: null;
        $location     = trim($_POST['location'] ?? '') ?: null;
        $homeAway     = trim($_POST['home_away'] ?? '') ?: null;

        $matchId = (new MatchModel())->create((int) $teamId, $opponentName, $matchDate, $kickoffTime, $location, $homeAway);
        setFlash('success', __('matches.created'));
        header('Location: /matches.php?team_id=' . $teamId);
        exit;
    }

    render('matches/create', [
        'team'   => $team,
        'errors' => $errors,
        'input'  => [
            'opponent_name' => trim($_POST['opponent_name'] ?? ''),
            'match_date'    => trim($_POST['match_date'] ?? ''),
            'kickoff_time'  => trim($_POST['kickoff_time'] ?? ''),
            'location'      => trim($_POST['location'] ?? ''),
            'home_away'     => trim($_POST['home_away'] ?? ''),
        ],
    ]);
    exit;
}

render('matches/create', [
    'team'   => $team,
    'errors' => [],
    'input'  => [],
]);
