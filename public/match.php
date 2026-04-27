<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/MatchModel.php';
require_once __DIR__ . '/../app/models/AttendanceModel.php';

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

$matchModel = new MatchModel();
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

    $statuses = $_POST['statuses'] ?? [];
    if (!is_array($statuses)) {
        $statuses = [];
    }

    (new AttendanceModel())->saveStatuses((int) $matchId, (int) $teamId, currentUserId(), $statuses);
    setFlash('success', __('attendance.saved'));
    header('Location: /match.php?id=' . $matchId . '&team_id=' . $teamId);
    exit;
}

$attendanceModel = new AttendanceModel();

render('matches/detail', [
    'team'    => $team,
    'match'   => $match,
    'players' => $attendanceModel->findForMatch((int) $matchId, (int) $teamId, currentUserId()),
    'summary' => $attendanceModel->getStatusSummary((int) $matchId),
    'flash'   => getFlash(),
]);
