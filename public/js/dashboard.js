const menuToggle = document.querySelector('.menu-toggle');
const sidebarOverlay = document.querySelector('.sidebar-overlay');
const sidebar = document.querySelector('#student-sidebar');

if (menuToggle && sidebarOverlay && sidebar) {
    const closeMenu = () => {
        document.body.classList.remove('dashboard-menu-open');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Open navigation');
    };

    menuToggle.addEventListener('click', () => {
        const isOpen = document.body.classList.toggle('dashboard-menu-open');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
    });

    sidebarOverlay.addEventListener('click', closeMenu);
    sidebar.addEventListener('click', (event) => {
        if (event.target.closest('a, form button')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
}
