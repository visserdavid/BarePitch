<?php $pageTitle = e(__('players.title')) . ' — ' . e($team['name']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
        <h1><?= e(__('players.title')) ?> — <?= e($team['name']) ?></h1>
        <a href="/player_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="10" y1="4" x2="10" y2="16"/><line x1="4" y1="10" x2="16" y2="10"/></svg>
            <?= e(__('players.add')) ?>
        </a>
    </div>

    <p>
        <a href="/teams.php" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="13 4 7 10 13 16"/></svg>
            <?= e(__('teams.title')) ?>
        </a>
    </p>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <?php if (empty($players)): ?>
        <div class="empty-state">
            <p><?= e(__('players.empty')) ?></p>
            <a href="/player_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary"><?= e(__('players.add')) ?></a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(__('players.shirt_number')) ?></th>
                    <th><?= e(__('players.display_name')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('players.status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                <tr data-href="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>"<?= $player['status'] === 'inactive' ? ' class="row-inactive"' : '' ?>>
                    <td><?= $player['shirt_number'] !== null ? e((string) $player['shirt_number']) : '—' ?></td>
                    <td><?= e($player['display_name']) ?></td>
                    <td class="col-hide-mobile"><?= e($player['status']) ?></td>
                    <td>
                        <div class="row-actions">
                            <a href="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon"
                               aria-label="<?= e(__('general.edit')) ?>"
                               title="<?= e(__('general.edit')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.7 2.3a1 1 0 0 1 1.4 0l2.3 2.3a1 1 0 0 1 0 1.4L5 18.4H1.5V15L13.7 2.3z"/></svg>
                            </a>
                            <?php if ($player['status'] === 'active'): ?>
                            <form method="POST" action="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit"
                                        class="btn-icon btn-icon--warning col-hide-mobile"
                                        aria-label="<?= e(__('players.deactivate')) ?>"
                                        title="<?= e(__('players.deactivate')) ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="8"/><line x1="4.9" y1="4.9" x2="15.1" y2="15.1"/></svg>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
