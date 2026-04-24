<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/services/TeamService.php';

requireLogin();

$rawId = $_GET['id'] ?? $_POST['id'] ?? '';
$teamId = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($teamId === false) {
    header('Location: /teams.php');
    exit;
}

$model = new TeamModel();
$team  = $model->findForUser((int) $teamId, currentUserId());

if ($team === null) {
    header('Location: /teams.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf()) {
        http_response_code(403);
        exit('Forbidden');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'archive') {
        $model->archive((int) $teamId, currentUserId());
        setFlash('success', __('teams.archived'));
        header('Location: /teams.php');
        exit;
    }

    if ($action === 'update') {
        $service = new TeamService();
        $errors  = $service->validateTeamInput($_POST);

        if (empty($errors)) {
            $name   = trim($_POST['name'] ?? '');
            $season = trim($_POST['season'] ?? '') ?: null;

            $model->update((int) $teamId, currentUserId(), $name, $season);
            setFlash('success', __('teams.updated'));
            header('Location: /team_edit.php?id=' . $teamId);
            exit;
        }

        render('teams/edit', [
            'team'   => $team,
            'errors' => $errors,
            'input'  => [
                'name'   => trim($_POST['name'] ?? ''),
                'season' => trim($_POST['season'] ?? ''),
            ],
            'flash'  => null,
        ]);
        exit;
    }

    header('Location: /teams.php');
    exit;
}

render('teams/edit', [
    'team'   => $team,
    'errors' => [],
    'input'  => [],
    'flash'  => getFlash(),
]);
