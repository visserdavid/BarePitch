<?php $pageTitle = __('dashboard.title') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1><?= e(__('dashboard.welcome', ['name' => $displayName])) ?></h1>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
