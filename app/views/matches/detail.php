<?php $pageTitle = e($match['opponent_name']) . ' — ' . e($match['match_date']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <h1><?= e($match['opponent_name']) ?> — <?= e($match['match_date']) ?></h1>

    <p><a href="/matches.php?team_id=<?= e((string) $team['id']) ?>">&larr; <?= e($team['name']) ?></a></p>

    <table class="table" style="max-width:480px;margin-bottom:1rem;">
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

    <p>
        <a href="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
    </p>

    <hr style="margin:2rem 0;">

    <h2><?= e(__('attendance.title')) ?></h2>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($players)): ?>
        <p><?= e(__('attendance.no_players')) ?></p>
    <?php else: ?>

        <p class="attendance-summary">
            <?= e(__('attendance.summary', [
                'selected'   => (string) $summary['selected'],
                'available'  => (string) $summary['available'],
                'unavailable'=> (string) $summary['unavailable'],
                'unknown'    => (string) $summary['unknown'],
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
