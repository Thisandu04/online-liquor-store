
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[name="search"]');
    const categorySelect = document.querySelector('select[name="category"]');
    const form = document.getElementById('filterForm');

    if (!form) return;

    // Auto-submit when category changes, so filtering feels instant
    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            form.submit();
        });
    }

    // Debounced live search — waits until user pauses typing before submitting
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                form.submit();
            }, 600);
        });
    }
});