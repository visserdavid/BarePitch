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
</head>
<body>
<header class="site-header">
    <div class="container">
        <span class="site-name">BarePitch</span>
        <?php if (currentUserId() !== null): ?>
        <nav class="site-nav">
            <form method="POST" action="/logout.php">
                <?= csrfField() ?>
                <button type="submit"><?= e(__('auth.logout')) ?></button>
            </form>
        </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container">
