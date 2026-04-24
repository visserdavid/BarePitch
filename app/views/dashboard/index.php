<?php $pageTitle = __('dashboard.title') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1><?= e(__('dashboard.welcome', ['name' => $displayName])) ?></h1>

    <p>
        <?= e((string) $teamCount) ?> <?= e(__('teams.title')) ?> &mdash;
        <a href="/teams.php"><?= e(__('teams.title')) ?></a>
    </p>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
