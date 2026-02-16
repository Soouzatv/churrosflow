document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('menu-toggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('menu-overlay');

    function closeSidebar() {
        if (sidebar) sidebar.classList.add('hidden');
        if (overlay) overlay.classList.add('hidden');
    }

    function openSidebar() {
        if (sidebar) sidebar.classList.remove('hidden');
        if (overlay) overlay.classList.remove('hidden');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (!sidebar) return;
            if (sidebar.classList.contains('hidden')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    document.querySelectorAll('[data-dismiss]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-dismiss');
            var target = document.getElementById(id);
            if (target) target.remove();
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var text = form.getAttribute('data-confirm') || 'Deseja continuar?';
            if (!confirm(text)) {
                e.preventDefault();
            }
        });
    });

    var applyThemeBtn = document.getElementById('apply_theme_preview');
    var p = document.getElementById('theme_primary');
    var p2 = document.getElementById('theme_primary_2');
    var s1 = document.getElementById('theme_sidebar_a');
    var s2 = document.getElementById('theme_sidebar_b');

    function syncColorLabel(inputEl) {
        if (!inputEl) return;
        var valueId = inputEl.getAttribute('data-value-id');
        if (!valueId) return;
        var valueEl = document.getElementById(valueId);
        if (valueEl) valueEl.textContent = (inputEl.value || '').toUpperCase();
    }

    function applyThemeVars() {
        if (p) document.documentElement.style.setProperty('--primary', p.value);
        if (p2) document.documentElement.style.setProperty('--primary-2', p2.value);
        if (s1) document.documentElement.style.setProperty('--sidebar-a', s1.value);
        if (s2) document.documentElement.style.setProperty('--sidebar-b', s2.value);
        syncColorLabel(p);
        syncColorLabel(p2);
        syncColorLabel(s1);
        syncColorLabel(s2);
    }

    [p, p2, s1, s2].forEach(function (el) {
        if (!el) return;
        el.addEventListener('input', applyThemeVars);
        el.addEventListener('change', applyThemeVars);
    });

    if (applyThemeBtn) {
        applyThemeBtn.addEventListener('click', applyThemeVars);
    }

    if (p || p2 || s1 || s2) {
        applyThemeVars();
    }
});
