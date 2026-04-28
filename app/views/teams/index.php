<?php $pageTitle = __('teams.title') . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
        <h1><?= e(__('teams.title')) ?></h1>
        <a href="/team_create.php" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="10" y1="4" x2="10" y2="16"/><line x1="4" y1="10" x2="16" y2="10"/></svg>
            <?= e(__('teams.create')) ?>
        </a>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
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
                    <th class="col-hide-mobile"><?= e(__('teams.season')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('players.title')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.title')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('teams.status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                <tr data-href="/team_edit.php?id=<?= e((string) $team['id']) ?>">
                    <td><?= e($team['name']) ?></td>
                    <td class="col-hide-mobile"><?= e($team['season'] ?? '—') ?></td>
                    <td class="col-hide-mobile"><?= e((string) $team['player_count']) ?></td>
                    <td class="col-hide-mobile"><?= e((string) $team['match_count']) ?></td>
                    <td class="col-hide-mobile"><?= e($team['status']) ?></td>
                    <td>
                        <div class="row-actions">
                            <a href="/players.php?team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon col-hide-mobile"
                               aria-label="<?= e(__('players.title')) ?>"
                               title="<?= e(__('players.title')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="6" r="3"/><path d="M2 18v-1a5 5 0 0 1 10 0v1"/><path d="M15 10a3 3 0 0 1 0 6"/><path d="M19 18v-1a3 3 0 0 0-3-3"/></svg>
                            </a>
                            <a href="/matches.php?team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon col-hide-mobile"
                               aria-label="<?= e(__('matches.title')) ?>"
                               title="<?= e(__('matches.title')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="16" height="15" rx="2"/><line x1="13" y1="1" x2="13" y2="5"/><line x1="7" y1="1" x2="7" y2="5"/><line x1="2" y1="8" x2="18" y2="8"/></svg>
                            </a>
                            <a href="/team_edit.php?id=<?= e((string) $team['id']) ?>"
                               class="btn-icon"
                               aria-label="<?= e(__('general.edit')) ?>"
                               title="<?= e(__('general.edit')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.7 2.3a1 1 0 0 1 1.4 0l2.3 2.3a1 1 0 0 1 0 1.4L5 18.4H1.5V15L13.7 2.3z"/></svg>
                            </a>
                            <form method="POST" action="/team_edit.php?id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="archive">
                                <button type="submit"
                                        class="btn-icon btn-icon--warning col-hide-mobile"
                                        aria-label="<?= e(__('general.archive')) ?>"
                                        title="<?= e(__('general.archive')) ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="16" height="4" rx="1"/><path d="M3 7v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7"/><polyline points="8 12 10 14 12 12"/><line x1="10" y1="9" x2="10" y2="14"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
