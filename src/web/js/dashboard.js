// Toggle the visibility of a dropdown menu
const toggleDropdown = (dropdown, menu, isOpen) => {
    dropdown.classList.toggle("sz-dd--open", isOpen);
    // делаем анимацию высоты
    menu.style.height = isOpen ? `${menu.scrollHeight}px` : "0px";
};

// Close all open dropdowns
const closeAllDropdowns = () => {
    document.querySelectorAll("[data-sz-dd].sz-dd--open").forEach((openDropdown) => {
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

        closeAllDropdowns();
        toggleDropdown(dropdown, menu, !isOpen);
    });
});

// Sidebar collapse
const sidebar = document.querySelector(".sz-sidebar");
const backdrop = document.querySelector(".sz-mobile-backdrop");

const syncSidebarState = () => {
    if (!sidebar) return;

    const collapsed = sidebar.classList.contains("sz-sidebar--collapsed");

    document.body.classList.toggle("sz-sidebar-is-collapsed", collapsed);
    document.body.classList.toggle("sz-sidebar-is-open", !collapsed);

    if (backdrop) {
        backdrop.classList.toggle("sz-mobile-backdrop--visible", window.innerWidth <= 1024 && !collapsed);
    }
};

document.querySelectorAll(".sz-sidebar__toggler, .sz-sidebar-menu-btn").forEach((button) => {
    button.addEventListener("click", () => {
        closeAllDropdowns();
        if (sidebar) {
            sidebar.classList.toggle("sz-sidebar--collapsed");
            syncSidebarState();
        }
    });
});

// Collapse sidebar by default on small screens
if (window.innerWidth <= 1024 && sidebar) sidebar.classList.add("sz-sidebar--collapsed");
syncSidebarState();

if (backdrop) {
    backdrop.addEventListener("click", () => {
        if (!sidebar) return;
        sidebar.classList.add("sz-sidebar--collapsed");
        syncSidebarState();
    });
}

window.addEventListener("resize", () => {
    if (!sidebar) return;

    if (window.innerWidth <= 1024) {
        sidebar.classList.add("sz-sidebar--collapsed");
    }

    syncSidebarState();
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
