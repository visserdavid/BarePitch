<?php $pageTitle = __('teams.title') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <h1><?= e(__('teams.title')) ?></h1>
        <a href="/team_create.php" class="btn btn-primary"><?= e(__('teams.create')) ?></a>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($teams)): ?>
        <div class="empty-state">
            <p><?= e(__('teams.empty')) ?></p>
            <a href="/team_create.php" class="btn btn-primary"><?= e(__('teams.create')) ?></a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(__('teams.name')) ?></th>
                    <th><?= e(__('teams.season')) ?></th>
                    <th><?= e(__('players.title')) ?></th>
                    <th><?= e(__('matches.title')) ?></th>
                    <th><?= e(__('teams.status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                <tr>
                    <td><?= e($team['name']) ?></td>
                    <td><?= e($team['season'] ?? '—') ?></td>
                    <td><?= e((string) $team['player_count']) ?></td>
                    <td><?= e((string) $team['match_count']) ?></td>
                    <td><?= e($team['status']) ?></td>
                    <td>
                        <a href="/players.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('players.title')) ?></a>
                        <a href="/matches.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('matches.title')) ?></a>
                        <a href="/team_edit.php?id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
                        <form method="POST" action="/team_edit.php?id=<?= e((string) $team['id']) ?>" style="display:inline;" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="archive">
                            <button type="submit" class="btn btn-danger"><?= e(__('general.archive')) ?></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
