// Toggle the visibility of a dropdown menu smoothly without jumps or leftover artifacts
const toggleDropdown = (dropdown, menu, isOpen) => {
    const toggle = dropdown.querySelector("[data-sz-dd-toggle]");

    // Cancel any active animation listener / timer
    if (menu._szEndHandler) {
        menu.removeEventListener("transitionend", menu._szEndHandler);
        menu._szEndHandler = null;
    }
    if (menu._szTimer) {
        clearTimeout(menu._szTimer);
        menu._szTimer = null;
    }

    if (toggle) {
        toggle.setAttribute("aria-expanded", String(isOpen));
        toggle.classList.toggle("expanded", isOpen);
    }

    if (isOpen) {
        dropdown.classList.add("sz-dd--open");

        // Ensure clean starting point from 0
        menu.style.transition = "none";
        menu.style.height = "0px";
        void menu.offsetHeight; // force reflow
        menu.style.transition = "";

        const targetHeight = menu.scrollHeight;
        menu.style.height = `${targetHeight}px`;

        const onEnd = (e) => {
            if (e && (e.target !== menu || e.propertyName !== "height")) return;
            menu.removeEventListener("transitionend", onEnd);
            menu._szEndHandler = null;
            if (menu._szTimer) {
                clearTimeout(menu._szTimer);
                menu._szTimer = null;
            }
            if (dropdown.classList.contains("sz-dd--open")) {
                menu.style.transition = "none";
                menu.style.height = "";
                void menu.offsetHeight;
                menu.style.transition = "";
            }
        };

        menu._szEndHandler = onEnd;
        menu.addEventListener("transitionend", onEnd);
        menu._szTimer = setTimeout(onEnd, 280);
    } else {
        const currentHeight = menu.getBoundingClientRect().height;
        menu.style.transition = "none";
        menu.style.height = `${currentHeight}px`;
        void menu.offsetHeight; // force reflow
        menu.style.transition = "";

        dropdown.classList.remove("sz-dd--open");

        requestAnimationFrame(() => {
            menu.style.height = "0px";
        });

        const onEnd = (e) => {
            if (e && (e.target !== menu || e.propertyName !== "height")) return;
            menu.removeEventListener("transitionend", onEnd);
            menu._szEndHandler = null;
            if (menu._szTimer) {
                clearTimeout(menu._szTimer);
                menu._szTimer = null;
            }
            if (!dropdown.classList.contains("sz-dd--open")) {
                menu.style.height = "";
            }
        };

        menu._szEndHandler = onEnd;
        menu.addEventListener("transitionend", onEnd);
        menu._szTimer = setTimeout(onEnd, 280);
    }
};

// Close all open dropdowns (optionally except the specified one)
const closeAllDropdowns = (exceptDropdown = null) => {
    document.querySelectorAll("[data-sz-dd].sz-dd--open").forEach((openDropdown) => {
        if (openDropdown === exceptDropdown) return;
        const menu = openDropdown.querySelector("[data-sz-dd-menu]");
        if (menu) toggleDropdown(openDropdown, menu, false);
    });
};

// Attach click event to all dropdown toggles
document.querySelectorAll("[data-sz-dd-toggle]").forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
        e.preventDefault();

        const dropdown = toggle.closest("[data-sz-dd]");
        if (!dropdown) return;

        const menu = dropdown.querySelector("[data-sz-dd-menu]");
        if (!menu) return;

        const isOpen = dropdown.classList.contains("sz-dd--open");

        closeAllDropdowns(dropdown);
        toggleDropdown(dropdown, menu, !isOpen);
    });
});

// Sidebar collapse & compact state
const sidebar = document.getElementById("sidebar") || document.querySelector(".sz-sidebar");
const app = document.getElementById("app") || document.querySelector(".app");
const collapseBtn = document.getElementById("collapse") || document.querySelector(".sz-sidebar__toggler");
const backdrop = document.querySelector(".sz-mobile-backdrop");

const setSidebarCompact = (isCompact, isInitial = false) => {
    if (!sidebar) return;
    if (isInitial) {
        sidebar.style.transition = "none";
        if (app) app.style.transition = "none";
    }
    sidebar.classList.toggle("compact", isCompact);
    sidebar.classList.toggle("sz-sidebar--collapsed", isCompact);
    if (app) app.classList.toggle("sidebar-small", isCompact);
    document.body.classList.toggle("sz-sidebar-is-collapsed", isCompact);
    document.body.classList.toggle("sz-sidebar-is-open", !isCompact);
    if (collapseBtn) collapseBtn.setAttribute("aria-expanded", String(!isCompact));
    if (backdrop) {
        backdrop.classList.toggle("sz-mobile-backdrop--visible", window.innerWidth <= 830 && !isCompact);
    }
    if (isInitial) {
        requestAnimationFrame(() => {
            setTimeout(() => {
                if (sidebar) sidebar.style.transition = "";
                if (app) app.style.transition = "";
            }, 60);
        });
    }
    try {
        localStorage.setItem("admin-sidebar-compact", isCompact ? "1" : "0");
        document.cookie = "admin-sidebar-compact=" + (isCompact ? "1" : "0") + "; path=/; max-age=31536000; SameSite=Lax";
    } catch (e) {}
};

// Restore saved collapse state seamlessly
try {
    const cookieMatch = document.cookie.match(/(?:^|; )admin-sidebar-compact=([^;]*)/);
    const savedCompact = localStorage.getItem("admin-sidebar-compact");
    let isCompact = false;
    if (cookieMatch) {
        isCompact = cookieMatch[1] === "1";
    } else if (savedCompact !== null) {
        isCompact = savedCompact === "1";
    } else {
        isCompact = window.innerWidth <= 830;
    }
    setSidebarCompact(isCompact, true);
} catch (e) {}

// Release preload blocker after initial paint so user-triggered toggles animate smoothly
requestAnimationFrame(() => {
    setTimeout(() => {
        document.documentElement.classList.remove("sz-preload-compact");
    }, 80);
});

document.querySelectorAll("#collapse, .collapse, .sz-sidebar__toggler, .sz-sidebar-menu-btn").forEach((button) => {
    button.addEventListener("click", () => {
        closeAllDropdowns();
        const currentlyCompact = sidebar?.classList.contains("compact") || sidebar?.classList.contains("sz-sidebar--collapsed");
        setSidebarCompact(!currentlyCompact);
    });
});

if (backdrop) {
    backdrop.addEventListener("click", () => {
        setSidebarCompact(true);
    });
}

window.addEventListener("resize", () => {
    if (window.innerWidth <= 830) {
        const currentlyCompact = sidebar?.classList.contains("compact") || sidebar?.classList.contains("sz-sidebar--collapsed");
        if (!currentlyCompact) {
            setSidebarCompact(true);
        }
    }
});

const accountMenu = document.querySelector("[data-sz-account]");
const accountToggle = document.querySelector("[data-sz-account-toggle]");

if (accountMenu && accountToggle) {
    accountToggle.addEventListener("click", () => {
        const isOpen = accountMenu.classList.contains("sz-account-menu--open");
        accountMenu.classList.toggle("sz-account-menu--open", !isOpen);
        accountToggle.setAttribute("aria-expanded", String(!isOpen));
    });

    document.addEventListener("click", (event) => {
        if (!accountMenu.contains(event.target)) {
            accountMenu.classList.remove("sz-account-menu--open");
            accountToggle.setAttribute("aria-expanded", "false");
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            accountMenu.classList.remove("sz-account-menu--open");
            accountToggle.setAttribute("aria-expanded", "false");
        }
    });
}

document.querySelectorAll("[data-sz-toast]").forEach((toast) => {
    const closeButton = toast.querySelector("[data-sz-toast-close]");
    const duration = Number(toast.dataset.duration || 5000);
    const progress = toast.querySelector(".sz-toast__progress-bar");
    let timeoutId = null;

    const closeToast = () => {
        toast.classList.add("sz-toast--closing");
        window.clearTimeout(timeoutId);
        window.setTimeout(() => {
            toast.remove();
        }, 220);
    };

    if (progress) {
        progress.style.animationDuration = `${duration}ms`;
    }

    timeoutId = window.setTimeout(closeToast, duration);

    if (closeButton) {
        closeButton.addEventListener("click", closeToast);
    }
});

// Theme switcher (Light / Dark)
const initTheme = () => {
    try {
        const cookieMatch = document.cookie.match(/(?:^|; )admin-theme=([^;]*)/);
        const savedTheme = cookieMatch ? cookieMatch[1] : localStorage.getItem("admin-theme");
        if (savedTheme === "dark") {
            document.documentElement.classList.add("dark");
            document.body.classList.add("dark");
        } else if (savedTheme === "light") {
            document.documentElement.classList.remove("dark");
            document.body.classList.remove("dark");
        }
    } catch (e) {}

    const themeButton = document.querySelector("[data-sz-theme-toggle]");
    const updateThemeIcon = () => {
        if (!themeButton) return;
        const icon = themeButton.querySelector(".material-symbols-rounded, .icon");
        const isDark = document.body.classList.contains("dark");
        if (icon) {
            if (icon.tagName && icon.tagName.toLowerCase() === "svg") {
                const use = icon.querySelector("use");
                if (use) use.setAttribute("href", isDark ? "#sun" : "#moon");
            } else {
                icon.textContent = isDark ? "light_mode" : "dark_mode";
            }
        }
        themeButton.setAttribute("title", isDark ? "Светлая тема" : "Темная тема");
        themeButton.setAttribute("aria-label", isDark ? "Светлая тема" : "Темная тема");
    };

    updateThemeIcon();

    if (themeButton) {
        themeButton.addEventListener("click", () => {
            const isDark = document.body.classList.toggle("dark");
            document.documentElement.classList.toggle("dark", isDark);
            updateThemeIcon();
            try {
                localStorage.setItem("admin-theme", isDark ? "dark" : "light");
                document.cookie = "admin-theme=" + (isDark ? "dark" : "light") + "; path=/; max-age=31536000; SameSite=Lax";
            } catch (e) {}
        });
    }
};

// App width switcher (Standard / Full width)
const initAppWidth = () => {
    const updateWidthState = (isFluid) => {
        const appEl = document.getElementById("app") || document.querySelector(".app");
        document.body.classList.toggle("sz-app-is-fluid", isFluid);
        document.body.classList.toggle("app-fluid", isFluid);
        if (appEl) {
            appEl.classList.toggle("app-fluid", isFluid);
            appEl.classList.toggle("is-fluid", isFluid);
        }

        const widthButtons = document.querySelectorAll("[data-sz-width-toggle], #widthButton");
        widthButtons.forEach((btn) => {
            const icon = btn.querySelector(".material-symbols-rounded, .icon") || btn;
            if (icon && icon !== btn) {
                icon.textContent = isFluid ? "width_normal" : "width_full";
            }
            const title = isFluid ? "Стандартная ширина" : "По всей ширине";
            btn.setAttribute("title", title);
            btn.setAttribute("aria-label", title);
            btn.classList.toggle("active", isFluid);
        });
    };

    let isFluid = false;
    try {
        isFluid = localStorage.getItem("admin-app-fluid") === "1";
    } catch (e) {}

    updateWidthState(isFluid);

    document.addEventListener("click", (e) => {
        const toggleBtn = e.target.closest("[data-sz-width-toggle], #widthButton");
        if (!toggleBtn) return;
        e.preventDefault();

        const currentFluid = document.body.classList.contains("sz-app-is-fluid") || document.body.classList.contains("app-fluid");
        const nextFluid = !currentFluid;
        updateWidthState(nextFluid);
        try {
            localStorage.setItem("admin-app-fluid", nextFluid ? "1" : "0");
        } catch (err) {}
    });
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        initTheme();
        initAppWidth();
    });
} else {
    initTheme();
    initAppWidth();
}

// Popover toggles (tools popovers: language, bell, profile, etc.)
const closeAllPopovers = () => {
    document.querySelectorAll(".pop").forEach((p) => (p.hidden = true));
    document.querySelectorAll("[data-sz-pop-toggle], [data-sz-popover-toggle]").forEach((t) => t.setAttribute("aria-expanded", "false"));
};

document.querySelectorAll("[data-sz-pop-toggle], [data-sz-popover-toggle]").forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        const targetId = toggle.getAttribute("data-sz-pop-toggle") || toggle.getAttribute("data-sz-popover-target")?.replace("#", "");
        const popover = document.getElementById(targetId);
        if (!popover) return;

        const isCurrentlyHidden = popover.hidden;
        closeAllPopovers();

        popover.hidden = !isCurrentlyHidden;
        toggle.setAttribute("aria-expanded", String(isCurrentlyHidden));
    });
});

document.addEventListener("click", (e) => {
    if (!e.target.closest(".pop") && !e.target.closest("[data-sz-pop-toggle], [data-sz-popover-toggle]")) {
        closeAllPopovers();
    }
});

let toastTimer;
function showToast(msg) {
    let t = document.getElementById('toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'toast';
        t.className = 'toast';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
}
window.showToast = showToast;

document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-toast]');
    if (t) {
        showToast(t.dataset.toast);
    }
    if (e.target.dataset.lang) {
        closeAllPopovers();
        showToast(e.target.dataset.lang === 'ru' ? 'Выбран русский язык' : 'English locale selected');
    }
    if (e.target.dataset.notice) {
        closeAllPopovers();
        showToast(e.target.dataset.notice);
    }
});


