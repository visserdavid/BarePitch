<?php $pageTitle = e(__('players.title')) . ' — ' . e($team['name']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <h1><?= e(__('players.title')) ?> — <?= e($team['name']) ?></h1>
        <a href="/player_create.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-primary"><?= e(__('players.add')) ?></a>
    </div>

    <p>
        <a href="/teams.php">&larr; <?= e(__('teams.title')) ?></a>
        &nbsp;|&nbsp;
        <a href="/matches.php?team_id=<?= e((string) $team['id']) ?>"><?= e(__('matches.title')) ?></a>
    </p>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
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
                    <th><?= e(__('players.status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                <tr<?= $player['status'] === 'inactive' ? ' style="opacity:0.5;"' : '' ?>>
                    <td><?= $player['shirt_number'] !== null ? e((string) $player['shirt_number']) : '—' ?></td>
                    <td><?= e($player['display_name']) ?></td>
                    <td><?= e($player['status']) ?></td>
                    <td>
                        <a href="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
