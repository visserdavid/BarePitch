<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/models/PlayerModel.php';

requireLogin();

$rawTeamId = $_GET['team_id'] ?? '';
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

$players = (new PlayerModel())->findAllForTeam((int) $teamId, currentUserId());

render('players/index', [
    'team'         => $team,
    'players'      => $players,
    'flash'        => getFlash(),
    'bottomNavNew' => ['url' => '/player_create.php?team_id=' . $teamId, 'label' => __('players.add')],
]);
