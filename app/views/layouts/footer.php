</main>

<?php include __DIR__ . '/bottom_nav.php'; ?>

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
