<?php $pageTitle = __('auth.login.title') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section class="auth-form">
    <h1><?= e(__('auth.login.title')) ?></h1>

    <?php if (!empty($error)): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/login.php" novalidate>
        <?= csrfField() ?>

        <div class="field">
            <label for="email"><?= e(__('auth.login.email')) ?></label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= e($email ?? '') ?>"
                required
                autocomplete="email"
            >
        </div>

        <div class="field">
            <label for="password"><?= e(__('auth.login.password')) ?></label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="field">
            <button type="submit"><?= e(__('auth.login.submit')) ?></button>
        </div>
    </form>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
