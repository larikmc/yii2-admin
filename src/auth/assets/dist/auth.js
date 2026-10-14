/**
 * yii2-admin auth scripts
 * Theme switcher, password visibility toggle, CapsLock detection and interactive polish.
 */
(function () {
    'use strict';

    var STORAGE_KEY_ADMIN = 'admin-theme';
    var STORAGE_KEY_AUTH = 'auth-theme';
    var MODE_KEY = 'auth-theme-mode';
    var root = document.documentElement;
    var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    function resolveTheme() {
        try {
            var adminTheme = localStorage.getItem(STORAGE_KEY_ADMIN);
            if (adminTheme === 'light' || adminTheme === 'dark') {
                return adminTheme;
            }
            var authTheme = localStorage.getItem(STORAGE_KEY_AUTH);
            if (authTheme === 'light' || authTheme === 'dark') {
                return authTheme;
            }
        } catch (e) {}

        var prefersDark = media ? media.matches : false;
        return prefersDark ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        root.classList.toggle('dark', theme === 'dark');
        if (document.body) {
            document.body.classList.toggle('dark', theme === 'dark');
        }

        // Update toggles in DOM
        document.querySelectorAll('[data-auth-theme-toggle], [data-sz-theme-toggle]').forEach(function (toggle) {
            toggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            var icon = toggle.querySelector('.material-symbols-rounded, .icon');
            if (icon) {
                icon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
            }
            var label = toggle.querySelector('[data-auth-theme-label]');
            if (label) {
                label.textContent = theme === 'dark' ? 'Темная' : 'Светлая';
            }
            toggle.setAttribute('title', theme === 'dark' ? 'Переключить на светлую тему' : 'Переключить на темную тему');
        });
    }

    function setTheme(theme) {
        applyTheme(theme);
        try {
            localStorage.setItem(MODE_KEY, 'manual');
            localStorage.setItem(STORAGE_KEY_ADMIN, theme);
            localStorage.setItem(STORAGE_KEY_AUTH, theme);
        } catch (e) {}
    }

    // Delegated click handler for theme toggles and password toggles
    document.addEventListener('click', function (e) {
        // Theme toggle
        var themeBtn = e.target.closest('[data-auth-theme-toggle], [data-sz-theme-toggle]');
        if (themeBtn) {
            e.preventDefault();
            var current = root.getAttribute('data-theme') || resolveTheme();
            var next = current === 'dark' ? 'light' : 'dark';
            setTheme(next);
            return;
        }

        // Password visibility toggle
        var passBtn = e.target.closest('[data-password-toggle], .auth-password-toggle');
        if (passBtn) {
            e.preventDefault();
            e.stopPropagation();

            var targetId = passBtn.getAttribute('data-password-toggle');
            var input = null;

            if (targetId && targetId.trim() !== '') {
                input = document.getElementById(targetId);
            }

            if (!input) {
                var container = passBtn.closest('.auth-input-group, .auth-password-wrap') || passBtn.parentElement;
                if (container) {
                    input = container.querySelector('input');
                }
            }

            if (!input) {
                input = passBtn.previousElementSibling;
            }

            if (!input) return;

            var isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';

            var icon = passBtn.querySelector('.material-symbols-rounded, .icon');
            if (icon) {
                icon.textContent = isPass ? 'visibility_off' : 'visibility';
            }
            passBtn.setAttribute('aria-label', isPass ? 'Скрыть пароль' : 'Показать пароль');
            passBtn.setAttribute('title', isPass ? 'Скрыть пароль' : 'Показать пароль');
            input.focus();
        }
    });

    // Caps Lock indicator
    function initCapsLockDetection() {
        document.querySelectorAll('input[type="password"]').forEach(function (input) {
            var wrap = input.closest('.auth-input-group, .auth-password-wrap') || input.parentElement;
            if (!wrap) return;

            var warning = wrap.parentElement.querySelector('.auth-caps-warning');
            if (!warning) {
                warning = document.createElement('div');
                warning.className = 'auth-caps-warning';
                warning.setAttribute('role', 'alert');
                warning.innerHTML = '<span class="material-symbols-rounded">keyboard_capslock</span> Caps Lock включен';
                warning.style.display = 'none';
                wrap.parentElement.appendChild(warning);
            }

            function checkCaps(e) {
                if (e.getModifierState) {
                    var isCaps = e.getModifierState('CapsLock');
                    warning.style.display = isCaps ? 'flex' : 'none';
                }
            }

            input.addEventListener('keydown', checkCaps);
            input.addEventListener('keyup', checkCaps);
            input.addEventListener('blur', function () {
                warning.style.display = 'none';
            });
        });
    }

    // Captcha refresh interaction animation
    function initCaptchaFeedback() {
        var captchaImg = document.querySelector('.auth-captcha-image img');
        if (captchaImg) {
            captchaImg.addEventListener('click', function () {
                captchaImg.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
                captchaImg.style.opacity = '0.5';
                captchaImg.style.transform = 'scale(0.96)';
                setTimeout(function () {
                    captchaImg.style.opacity = '1';
                    captchaImg.style.transform = 'scale(1)';
                }, 300);
            });
        }
    }

    // Initial theme set before render
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

    function init() {
        applyTheme(resolveTheme());
        initCapsLockDetection();
        initCaptchaFeedback();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
