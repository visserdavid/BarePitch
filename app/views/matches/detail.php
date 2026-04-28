<?php $pageTitle = e($match['opponent_name']) . ' — ' . e($match['match_date']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
        <h1><?= e($match['opponent_name']) ?></h1>
        <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>"
           class="btn-icon"
           aria-label="<?= e(__('general.edit')) ?>"
           title="<?= e(__('general.edit')) ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.7 2.3a1 1 0 0 1 1.4 0l2.3 2.3a1 1 0 0 1 0 1.4L5 18.4H1.5V15L13.7 2.3z"/></svg>
        </a>
    </div>

    <p>
        <a href="/matches.php?team_id=<?= e((string) $team['id']) ?>" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="13 4 7 10 13 16"/></svg>
            <?= e($team['name']) ?>
        </a>
    </p>

    <table class="table match-info">
        <tbody>
            <tr>
                <th><?= e(__('matches.date')) ?></th>
                <td><?= e($match['match_date']) ?></td>
            </tr>
            <?php if ($match['kickoff_time'] !== null): ?>
            <tr>
                <th><?= e(__('matches.kickoff')) ?></th>
                <td><?= e(substr($match['kickoff_time'], 0, 5)) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($match['location'] !== null): ?>
            <tr>
                <th><?= e(__('matches.location')) ?></th>
                <td><?= e($match['location']) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($match['home_away'] !== null): ?>
            <tr>
                <th><?= e(__('matches.home_away')) ?></th>
                <td><?= e(__('matches.' . $match['home_away'])) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><?= e(__('matches.status')) ?></th>
                <td><?= e(__('matches.status.' . $match['status'])) ?></td>
            </tr>
        </tbody>
    </table>

    <hr>

    <h2><?= e(__('attendance.title')) ?></h2>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <?php if (empty($players)): ?>
        <p><?= e(__('attendance.no_players')) ?></p>
    <?php else: ?>

        <p class="attendance-summary">
            <?= e(__('attendance.summary', [
                'selected'    => (string) $summary['selected'],
                'available'   => (string) $summary['available'],
                'unavailable' => (string) $summary['unavailable'],
                'unknown'     => (string) $summary['unknown'],
            ])) ?>
        </p>

        <form method="POST" action="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>">
            <?= csrfField() ?>

            <?php foreach ($players as $player): ?>
            <?php $isSelected = $player['match_status'] === 'selected'; ?>
            <div class="attendance-row<?= $isSelected ? ' attendance-row--selected' : '' ?>"
                 data-player-id="<?= e((string) $player['id']) ?>">
                <span class="attendance-shirt"><?= $player['shirt_number'] !== null ? e((string) $player['shirt_number']) : '—' ?></span>
                <span class="attendance-name"><?= e($player['display_name']) ?></span>
                <select class="attendance-status" name="statuses[<?= e((string) $player['id']) ?>]">
                    <option value="unknown"<?= $player['match_status'] === 'unknown' ? ' selected' : '' ?>><?= e(__('attendance.status.unknown')) ?></option>
                    <option value="available"<?= $player['match_status'] === 'available' ? ' selected' : '' ?>><?= e(__('attendance.status.available')) ?></option>
                    <option value="unavailable"<?= $player['match_status'] === 'unavailable' ? ' selected' : '' ?>><?= e(__('attendance.status.unavailable')) ?></option>
                    <option value="selected"<?= $player['match_status'] === 'selected' ? ' selected' : '' ?>><?= e(__('attendance.status.selected')) ?></option>
                </select>
            </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary attendance-save"><?= e(__('attendance.save')) ?></button>
        </form>

    <?php endif; ?>
</section>

<script src="/assets/js/match_attendance.js" defer></script>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
