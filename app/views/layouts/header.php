<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'BarePitch', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <span class="site-name">BarePitch</span>
        <?php if (currentUserId() !== null): ?>
        <nav class="site-nav">
            <form method="POST" action="/logout.php">
                <?= csrfField() ?>
                <button type="submit">Log out</button>
            </form>
        </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container">
