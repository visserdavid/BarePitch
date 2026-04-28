<?php
$currentPage  = $currentPage  ?? '';
$activeTeamId = $activeTeamId ?? null;
$isLoggedIn   = currentUserId() !== null;
if (!$isLoggedIn) return;
?>
<nav class="bottom-nav" aria-label="Main navigation">

    <a href="/dashboard.php"
       class="bottom-nav__item<?= $currentPage === 'dashboard' ? ' bottom-nav__item--active' : '' ?>"
       aria-current="<?= $currentPage === 'dashboard' ? 'page' : 'false' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12L12 3l9 9"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-5h4v5h4a1 1 0 0 0 1-1v-9"/></svg>
        <span><?= e(__('dashboard.title')) ?></span>
    </a>

    <a href="/teams.php"
       class="bottom-nav__item<?= $currentPage === 'teams' ? ' bottom-nav__item--active' : '' ?>"
       aria-current="<?= $currentPage === 'teams' ? 'page' : 'false' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span><?= e(__('teams.title')) ?></span>
    </a>

    <?php if ($activeTeamId !== null): ?>
    <a href="/players.php?team_id=<?= e((string) $activeTeamId) ?>"
       class="bottom-nav__item bottom-nav__item--context is-visible<?= $currentPage === 'players' ? ' bottom-nav__item--active' : '' ?>"
       aria-current="<?= $currentPage === 'players' ? 'page' : 'false' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M19 7a4 4 0 0 1 0 8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
        <span><?= e(__('players.title')) ?></span>
    </a>

    <a href="/matches.php?team_id=<?= e((string) $activeTeamId) ?>"
       class="bottom-nav__item bottom-nav__item--context is-visible<?= $currentPage === 'matches' ? ' bottom-nav__item--active' : '' ?>"
       aria-current="<?= $currentPage === 'matches' ? 'page' : 'false' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span><?= e(__('matches.title')) ?></span>
    </a>
    <?php endif; ?>

    <form method="POST" action="/logout.php">
        <?= csrfField() ?>
        <button type="submit"
                class="bottom-nav__item<?= '' ?>"
                aria-label="<?= e(__('auth.logout')) ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span><?= e(__('auth.logout')) ?></span>
        </button>
    </form>

</nav>
