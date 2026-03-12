(function () {
    var STORAGE_KEY = 'auth-theme';
    var MODE_KEY = 'auth-theme-mode';
    var root = document.documentElement;
    var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    function resolveTheme() {
        try {
            var mode = localStorage.getItem(MODE_KEY);
            var stored = localStorage.getItem(STORAGE_KEY);
            if (mode === 'manual' && (stored === 'light' || stored === 'dark')) {
                return stored;
            }
        } catch (e) {}

        var prefersDark = media ? media.matches : false;
        return prefersDark ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
    }

    function initToggle() {
        var toggle = document.querySelector('[data-auth-theme-toggle]');
        if (!toggle) {
            return;
        }

        var label = toggle.querySelector('[data-auth-theme-label]');
        var current = root.getAttribute('data-theme') || resolveTheme();
        applyTheme(current);
        toggle.setAttribute('aria-pressed', current === 'dark' ? 'true' : 'false');
        if (label) {
            label.textContent = current === 'dark' ? 'Dark' : 'Light';
        }

        toggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            toggle.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
            if (label) {
                label.textContent = next === 'dark' ? 'Dark' : 'Light';
            }

            try {
                localStorage.setItem(MODE_KEY, 'manual');
                localStorage.setItem(STORAGE_KEY, next);
            } catch (e) {}
        });
    }

    applyTheme(resolveTheme());

    if (media) {
        media.addEventListener('change', function (event) {
            try {
                if (localStorage.getItem(MODE_KEY) === 'manual') {
                    return;
                }
            } catch (e) {}

            applyTheme(event.matches ? 'dark' : 'light');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToggle);
    } else {
        initToggle();
    }
})();
