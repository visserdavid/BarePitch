<?php $pageTitle = e(__('matches.title')) . ' — ' . e($team['name']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
        <h1><?= e(__('matches.title')) ?> — <?= e($team['name']) ?></h1>
        <a href="/match_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="10" y1="4" x2="10" y2="16"/><line x1="4" y1="10" x2="16" y2="10"/></svg>
            <?= e(__('matches.create')) ?>
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

    <?php if (empty($upcoming) && empty($past)): ?>
        <div class="empty-state">
            <p><?= e(__('matches.empty')) ?></p>
            <a href="/match_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary"><?= e(__('matches.create')) ?></a>
        </div>
    <?php else: ?>

        <?php if (!empty($upcoming)): ?>
        <h2 class="section-heading"><?= e(__('matches.upcoming')) ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(__('matches.date')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.kickoff')) ?></th>
                    <th><?= e(__('matches.opponent')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.home_away')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcoming as $match): ?>
                <tr data-href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>">
                    <td><?= e($match['match_date']) ?></td>
                    <td class="col-hide-mobile"><?= $match['kickoff_time'] !== null ? e(substr($match['kickoff_time'], 0, 5)) : '—' ?></td>
                    <td>
                        <?= e($match['opponent_name']) ?>
                        <?php if ((int) $match['selected_count'] > 0): ?>
                            <span class="badge badge-selected"><?= e((string) $match['selected_count']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="col-hide-mobile"><?= $match['home_away'] !== null ? e(__('matches.' . $match['home_away'])) : '—' ?></td>
                    <td class="col-hide-mobile"><?= e(__('matches.status.' . $match['status'])) ?></td>
                    <td>
                        <div class="row-actions">
                            <a href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon col-hide-mobile"
                               aria-label="<?= e(__('matches.detail')) ?>"
                               title="<?= e(__('matches.detail')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="7 4 13 10 7 16"/></svg>
                            </a>
                            <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon"
                               aria-label="<?= e(__('general.edit')) ?>"
                               title="<?= e(__('general.edit')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.7 2.3a1 1 0 0 1 1.4 0l2.3 2.3a1 1 0 0 1 0 1.4L5 18.4H1.5V15L13.7 2.3z"/></svg>
                            </a>
                            <form method="POST" action="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
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

        <?php if (!empty($past)): ?>
        <h2 class="section-heading"><?= e(__('matches.past')) ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(__('matches.date')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.kickoff')) ?></th>
                    <th><?= e(__('matches.opponent')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.home_away')) ?></th>
                    <th class="col-hide-mobile"><?= e(__('matches.status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($past as $match): ?>
                <tr data-href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>">
                    <td><?= e($match['match_date']) ?></td>
                    <td class="col-hide-mobile"><?= $match['kickoff_time'] !== null ? e(substr($match['kickoff_time'], 0, 5)) : '—' ?></td>
                    <td>
                        <?= e($match['opponent_name']) ?>
                        <?php if ((int) $match['selected_count'] > 0): ?>
                            <span class="badge badge-selected"><?= e((string) $match['selected_count']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="col-hide-mobile"><?= $match['home_away'] !== null ? e(__('matches.' . $match['home_away'])) : '—' ?></td>
                    <td class="col-hide-mobile"><?= e(__('matches.status.' . $match['status'])) ?></td>
                    <td>
                        <div class="row-actions">
                            <a href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon col-hide-mobile"
                               aria-label="<?= e(__('matches.detail')) ?>"
                               title="<?= e(__('matches.detail')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="7 4 13 10 7 16"/></svg>
                            </a>
                            <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"
                               class="btn-icon"
                               aria-label="<?= e(__('general.edit')) ?>"
                               title="<?= e(__('general.edit')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.7 2.3a1 1 0 0 1 1.4 0l2.3 2.3a1 1 0 0 1 0 1.4L5 18.4H1.5V15L13.7 2.3z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
