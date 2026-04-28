<?php $pageTitle = e(__('players.title')) . ' — ' . e($team['name']) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
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
                    <th class="col-hide-mobile"><?= e(__('players.status')) ?></th>
                    <th class="col-hide-mobile"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                <tr data-href="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>"<?= $player['status'] === 'inactive' ? ' style="opacity:0.5;"' : '' ?>>
                    <td><?= $player['shirt_number'] !== null ? e((string) $player['shirt_number']) : '—' ?></td>
                    <td><?= e($player['display_name']) ?></td>
                    <td class="col-hide-mobile"><?= e($player['status']) ?></td>
                    <td class="col-hide-mobile">
                        <a href="/player_edit.php?id=<?= e((string) $player['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.edit')) ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
