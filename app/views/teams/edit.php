<?php $pageTitle = __('teams.edit') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1><?= e(__('teams.edit')) ?></h1>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/team_edit.php?id=<?= e((string) $team['id']) ?>" novalidate style="max-width:480px;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="update">

        <div class="form-group">
            <label for="name"><?= e(__('teams.name')) ?></label>
            <input type="text" id="name" name="name" value="<?= e($input['name'] ?? $team['name']) ?>" maxlength="100" required>
            <?php if (!empty($errors['name'])): ?>
                <span class="form-error"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="season"><?= e(__('teams.season')) ?></label>
            <input type="text" id="season" name="season" value="<?= e($input['season'] ?? ($team['season'] ?? '')) ?>" maxlength="20">
            <?php if (!empty($errors['season'])): ?>
                <span class="form-error"><?= e($errors['season']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= e(__('general.save')) ?></button>
            <a href="/teams.php" class="btn btn-secondary"><?= e(__('general.cancel')) ?></a>
        </div>
    </form>

    <p><a href="/players.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('players.title')) ?></a></p>

    <hr style="margin:2rem 0;">

    <form method="POST" action="/team_edit.php?id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="archive">
        <button type="submit" class="btn btn-danger"><?= e(__('general.archive')) ?></button>
    </form>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
