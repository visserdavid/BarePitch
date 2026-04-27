<?php $pageTitle = e(__('matches.title')) . ' — ' . e($team['name']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <h1><?= e(__('matches.title')) ?> — <?= e($team['name']) ?></h1>
        <a href="/match_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary"><?= e(__('matches.create')) ?></a>
    </div>

    <p><a href="/teams.php">&larr; <?= e(__('teams.title')) ?></a></p>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($upcoming) && empty($past)): ?>
        <div class="empty-state">
            <p><?= e(__('matches.empty')) ?></p>
            <a href="/match_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary"><?= e(__('matches.create')) ?></a>
        </div>
    <?php else: ?>

        <?php if (!empty($upcoming)): ?>
        <h2 style="margin-top:1.5rem;margin-bottom:0.75rem;">Upcoming</h2>
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(__('matches.date')) ?></th>
                    <th><?= e(__('matches.kickoff')) ?></th>
                    <th><?= e(__('matches.opponent')) ?></th>
                    <th><?= e(__('matches.home_away')) ?></th>
                    <th><?= e(__('matches.location')) ?></th>
                    <th><?= e(__('matches.status')) ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcoming as $match): ?>
                <tr>
                    <td><?= e($match['match_date']) ?></td>
                    <td><?= $match['kickoff_time'] !== null ? e(substr($match['kickoff_time'], 0, 5)) : '—' ?></td>
                    <td><a href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"><?= e($match['opponent_name']) ?></a></td>
                    <td><?= $match['home_away'] !== null ? e(__('matches.' . $match['home_away'])) : '—' ?></td>
                    <td><?= $match['location'] !== null ? e($match['location']) : '—' ?></td>
                    <td><?= e(__('matches.status.' . $match['status'])) ?></td>
                    <td>
                        <?php if ((int) $match['selected_count'] > 0): ?>
                            <span class="badge-selected"><?= e(__('attendance.selected_count', ['count' => (string) $match['selected_count']])) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
                        <form method="POST" action="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" style="display:inline;" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
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

        <?php if (!empty($past)): ?>
        <h2 style="margin-top:2rem;margin-bottom:0.75rem;">Past</h2>
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(__('matches.date')) ?></th>
                    <th><?= e(__('matches.kickoff')) ?></th>
                    <th><?= e(__('matches.opponent')) ?></th>
                    <th><?= e(__('matches.home_away')) ?></th>
                    <th><?= e(__('matches.location')) ?></th>
                    <th><?= e(__('matches.status')) ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($past as $match): ?>
                <tr>
                    <td><?= e($match['match_date']) ?></td>
                    <td><?= $match['kickoff_time'] !== null ? e(substr($match['kickoff_time'], 0, 5)) : '—' ?></td>
                    <td><a href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"><?= e($match['opponent_name']) ?></a></td>
                    <td><?= $match['home_away'] !== null ? e(__('matches.' . $match['home_away'])) : '—' ?></td>
                    <td><?= $match['location'] !== null ? e($match['location']) : '—' ?></td>
                    <td><?= e(__('matches.status.' . $match['status'])) ?></td>
                    <td>
                        <?php if ((int) $match['selected_count'] > 0): ?>
                            <span class="badge-selected"><?= e(__('attendance.selected_count', ['count' => (string) $match['selected_count']])) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
                        <form method="POST" action="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" style="display:inline;" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
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

    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
