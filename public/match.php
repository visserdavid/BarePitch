<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/MatchModel.php';

requireLogin();

$rawId     = $_GET['id'] ?? '';
$rawTeamId = $_GET['team_id'] ?? '';

$matchId = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$teamId  = filter_var($rawTeamId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($matchId === false || $teamId === false) {
    header('Location: /teams.php');
    exit;
}

$team = (new TeamModel())->findForUser((int) $teamId, currentUserId());

if ($team === null) {
    header('Location: /teams.php');
    exit;
}

$match = (new MatchModel())->findForTeam((int) $matchId, (int) $teamId, currentUserId());

if ($match === null) {
    header('Location: /matches.php?team_id=' . $teamId);
    exit;
}

render('matches/detail', [
    'team'  => $team,
    'match' => $match,
]);
