<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/models/TeamModel.php';
require_once __DIR__ . '/../app/services/TeamService.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrf()) {
        http_response_code(403);
        exit('Forbidden');
    }

    $service = new TeamService();
    $errors  = $service->validateTeamInput($_POST);

    if (empty($errors)) {
        $name   = trim($_POST['name'] ?? '');
        $season = trim($_POST['season'] ?? '') ?: null;

        (new TeamModel())->create(currentUserId(), $name, $season);
        setFlash('success', __('teams.created'));
        header('Location: /teams.php');
        exit;
    }

    render('teams/create', [
        'errors' => $errors,
        'input'  => [
            'name'   => trim($_POST['name'] ?? ''),
            'season' => trim($_POST['season'] ?? ''),
        ],
    ]);
    exit;
}

render('teams/create', ['errors' => [], 'input' => []]);
