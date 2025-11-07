/*
 * Sidebar toggle script
 * - Provides a simple, focused behavior: the #sidebarToggle button toggles
 *   the CSS class `sidebar-collapsed` on <body>, which controls the sidebar
 *   width and related layout via CSS. This file intentionally avoids doing
 *   any DOM layout work (all visuals are driven by CSS) to keep the toggle
 *   instant and prevent layout flicker.
 * - Persists the user's preference in localStorage under `sidebarCollapsed`.
 * - Important: the initial collapsed state is applied server-side by adding
 *   `sidebar-collapsed` to the <body> element in the Blade layout so the
 *   page renders consistently before JavaScript runs.
 */

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const body = document.body;

    if (!toggleBtn) return;

    // Toggle button click handler: toggle class and persist state
    toggleBtn.addEventListener('click', function (e) {
        e.preventDefault();
        body.classList.toggle('sidebar-collapsed');

        const isCollapsed = body.classList.contains('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    });

    // Restore user preference: if user explicitly expanded previously,
    // remove the default collapsed class. We only remove when saved === 'false'
    // because the default markup sets collapsed for instant rendering.
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'false') {
        body.classList.remove('sidebar-collapsed');
    }
});
