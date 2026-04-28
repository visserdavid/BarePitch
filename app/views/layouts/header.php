<!DOCTYPE html>
<html lang="<?= e(getenv('APP_LANG') ?: 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'BarePitch') ?></title>
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/assets/css/pages.css">
    <link rel="stylesheet" href="/assets/css/nav.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <span class="site-name">BarePitch</span>
    </div>
</header>
<main class="container">
