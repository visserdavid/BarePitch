<?php $pageTitle = e($match['opponent_name']) . ' — ' . e($match['match_date']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1><?= e($match['opponent_name']) ?> — <?= e($match['match_date']) ?></h1>

    <p><a href="/matches.php?team_id=<?= e((string) $team['id']) ?>">&larr; <?= e($team['name']) ?></a></p>

    <table class="table" style="max-width:480px;">
        <tbody>
            <tr>
                <th><?= e(__('matches.date')) ?></th>
                <td><?= e($match['match_date']) ?></td>
            </tr>
            <tr>
                <th><?= e(__('matches.kickoff')) ?></th>
                <td><?= $match['kickoff_time'] !== null ? e(substr($match['kickoff_time'], 0, 5)) : '—' ?></td>
            </tr>
            <tr>
                <th><?= e(__('matches.location')) ?></th>
                <td><?= $match['location'] !== null ? e($match['location']) : '—' ?></td>
            </tr>
            <tr>
                <th><?= e(__('matches.home_away')) ?></th>
                <td><?= $match['home_away'] !== null ? e(__('matches.' . $match['home_away'])) : '—' ?></td>
            </tr>
            <tr>
                <th><?= e(__('matches.status')) ?></th>
                <td><?= e(__('matches.status.' . $match['status'])) ?></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:1.25rem;">
        <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
    </p>

    <hr style="margin:2rem 0;">

    <h2><?= e(__('attendance.title')) ?></h2>
    <p><?= e(__('matches.attendance_placeholder')) ?></p>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
