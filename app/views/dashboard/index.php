<?php $pageTitle = 'Dashboard — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1>Welcome, <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></h1>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
