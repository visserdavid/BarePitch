<?php $pageTitle = e(__('matches.edit')) . ' — BarePitch'; ?>
<?php include dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div class="page-header">
        <h1><?= e(__('matches.edit')) ?></h1>
    </div>

    <p>
        <a href="/matches.php?team_id=<?= e((string) $team['id']) ?>" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="13 4 7 10 13 16"/></svg>
            <?= e($team['name']) ?>
        </a>
    </p>

    <?php if (!empty($flash)): ?>
        <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" novalidate class="form-container">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="update">

        <div class="form-group">
            <label for="opponent_name"><?= e(__('matches.opponent')) ?></label>
            <input type="text" id="opponent_name" name="opponent_name" value="<?= e($input['opponent_name'] ?? $match['opponent_name']) ?>" maxlength="100" required>
            <?php if (!empty($errors['opponent_name'])): ?>
                <span class="form-error"><?= e($errors['opponent_name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="match_date"><?= e(__('matches.date')) ?></label>
            <input type="date" id="match_date" name="match_date" value="<?= e($input['match_date'] ?? $match['match_date']) ?>" required>
            <?php if (!empty($errors['match_date'])): ?>
                <span class="form-error"><?= e($errors['match_date']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="kickoff_time"><?= e(__('matches.kickoff')) ?></label>
            <?php $kickoffDisplay = $input['kickoff_time'] ?? ($match['kickoff_time'] !== null ? substr($match['kickoff_time'], 0, 5) : ''); ?>
            <input type="time" id="kickoff_time" name="kickoff_time" value="<?= e($kickoffDisplay) ?>">
            <?php if (!empty($errors['kickoff_time'])): ?>
                <span class="form-error"><?= e($errors['kickoff_time']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="location"><?= e(__('matches.location')) ?></label>
            <input type="text" id="location" name="location" value="<?= e($input['location'] ?? ($match['location'] ?? '')) ?>" maxlength="150">
            <?php if (!empty($errors['location'])): ?>
                <span class="form-error"><?= e($errors['location']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="home_away"><?= e(__('matches.home_away')) ?></label>
            <?php $selectedHomeAway = $input['home_away'] ?? ($match['home_away'] ?? ''); ?>
            <select id="home_away" name="home_away">
                <option value=""></option>
                <option value="home"<?= $selectedHomeAway === 'home' ? ' selected' : '' ?>><?= e(__('matches.home')) ?></option>
                <option value="away"<?= $selectedHomeAway === 'away' ? ' selected' : '' ?>><?= e(__('matches.away')) ?></option>
                <option value="neutral"<?= $selectedHomeAway === 'neutral' ? ' selected' : '' ?>><?= e(__('matches.neutral')) ?></option>
            </select>
            <?php if (!empty($errors['home_away'])): ?>
                <span class="form-error"><?= e($errors['home_away']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= e(__('general.save')) ?></button>
            <a href="/matches.php?team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary"><?= e(__('general.cancel')) ?></a>
        </div>
    </form>

    <hr>

    <p>
        <a href="/match.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" class="btn btn-secondary">
            <?= e(__('matches.detail')) ?> →
        </a>
    </p>

    <form method="POST" action="/match_edit.php?id=<?= e((string) $match['id']) ?>&team_id=<?= e((string) $team['id']) ?>" onsubmit="return confirm('<?= e(__('general.confirm')) ?>')">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="archive">
        <button type="submit" class="btn btn-danger"><?= e(__('general.archive')) ?></button>
    </form>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
