</main>

<nav class="bottom-nav">
    <a href="/dashboard.php" class="bottom-nav__item"><?= e(__('dashboard.title')) ?></a>
    <a href="/teams.php" class="bottom-nav__item"><?= e(__('teams.title')) ?></a>
    <?php if (isset($bottomNavNew)): ?>
    <a href="<?= e($bottomNavNew['url']) ?>" class="bottom-nav__item">+ <?= e($bottomNavNew['label']) ?></a>
    <?php endif; ?>
</nav>

<script>
document.querySelectorAll('tr[data-href]').forEach(function(row) {
    row.addEventListener('click', function(e) {
        if (!e.target.closest('a, button, form')) {
            window.location = row.dataset.href;
        }
    });
});
</script>

</body>
</html>
