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

document.querySelectorAll(".sz-sidebar__toggler, .sz-sidebar-menu-btn").forEach((button) => {
    button.addEventListener("click", () => {
        closeAllDropdowns();
        if (sidebar) sidebar.classList.toggle("sz-sidebar--collapsed");
    });
});

// Collapse sidebar by default on small screens
if (window.innerWidth <= 1024 && sidebar) sidebar.classList.add("sz-sidebar--collapsed");
