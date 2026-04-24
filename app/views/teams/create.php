<?php $pageTitle = __('teams.create') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1><?= e(__('teams.create')) ?></h1>

    <form method="POST" action="/team_create.php" novalidate style="max-width:480px;">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="name"><?= e(__('teams.name')) ?></label>
            <input type="text" id="name" name="name" value="<?= e($input['name'] ?? '') ?>" maxlength="100" required>
            <?php if (!empty($errors['name'])): ?>
                <span class="form-error"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="season"><?= e(__('teams.season')) ?></label>
            <input type="text" id="season" name="season" value="<?= e($input['season'] ?? '') ?>" maxlength="20">
            <?php if (!empty($errors['season'])): ?>
                <span class="form-error"><?= e($errors['season']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= e(__('general.save')) ?></button>
            <a href="/teams.php" class="btn btn-secondary"><?= e(__('general.cancel')) ?></a>
        </div>
    </form>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
