(function () {
    document.querySelectorAll('.attendance-status').forEach(function (select) {
        select.addEventListener('change', function () {
            var row = select.closest('.attendance-row');
            if (!row) return;
            if (select.value === 'selected') {
                row.classList.add('attendance-row--selected');
            } else {
                row.classList.remove('attendance-row--selected');
            }
        });
    });
}());
