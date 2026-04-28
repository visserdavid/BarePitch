<?php $pageTitle = e(__('players.edit')) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
        <h1><?= e(__('players.edit')) ?></h1>
    </div>

    <p>
        <a href="/players.php?team_id=<?= e((string) $team['id']) ?>" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="13 4 7 10 13 16"/></svg>
            <?= e($team['name']) ?>
        </a>
    </p>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>" novalidate class="form-container">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="update">

        <div class="form-group">
            <label for="display_name"><?= e(__('players.display_name')) ?></label>
            <input type="text" id="display_name" name="display_name" value="<?= e($input['display_name'] ?? $player['display_name']) ?>" maxlength="100" required>
            <?php if (!empty($errors['display_name'])): ?>
                <span class="form-error"><?= e($errors['display_name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="shirt_number"><?= e(__('players.shirt_number')) ?></label>
            <input type="number" id="shirt_number" name="shirt_number" value="<?= e($input['shirt_number'] ?? ($player['shirt_number'] !== null ? (string) $player['shirt_number'] : '')) ?>" min="1" max="99">
            <?php if (!empty($errors['shirt_number'])): ?>
                <span class="form-error"><?= e($errors['shirt_number']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= e(__('general.save')) ?></button>
            <a href="/players.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.cancel')) ?></a>
        </div>
    </form>

    <hr>

    <?php if ($player['status'] === 'active'): ?>
    <form method="POST" action="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')" style="display:inline;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="deactivate">
        <button type="submit" class="btn btn-secondary"><?= e(__('players.deactivate')) ?></button>
    </form>
    <?php endif; ?>

    <?php if (!$hasMatchHistory): ?>
    <form method="POST" action="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('players.delete_confirm')) ?>')" style="display:inline; margin-left:0.5rem;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="delete">
        <button type="submit" class="btn btn-danger"><?= e(__('general.delete')) ?></button>
    </form>
    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
