<?php $pageTitle = 'Log in — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section class="auth-form">
    <h1>Log in</h1>

    <?php if (!empty($error)): ?>
        <p class="form-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="/login.php" novalidate>
        <?= csrfField() ?>

        <div class="field">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required
                autocomplete="email"
            >
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="field">
            <button type="submit">Log in</button>
        </div>
    </form>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
