/**
 * Student Hub - Universal Theme & Navigation Controller (js/theme.js)
 * Manages universal Light/Dark mode with localStorage persistence,
 * cross-iframe theme synchronization, and responsive navigation across all pages.
 */
(function () {
    const STORAGE_KEY = 'student-hub-theme';

    // Retrieve saved theme or default to light
    function getSavedTheme() {
        try {
            return localStorage.getItem(STORAGE_KEY) ||
                   localStorage.getItem('theme') ||
                   (localStorage.getItem('darkMode') === 'enabled' ? 'dark' : 'light');
        } catch (_) {
            return 'light';
        }
    }

    // Apply theme to document, body, and all toggle buttons
    function applyTheme(theme) {
        const isDark = theme === 'dark';

        if (document.documentElement) {
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.setAttribute('data-theme', theme);
        }

        if (document.body) {
            document.body.classList.toggle('dark', isDark);
        }

        // Update all theme toggle buttons across the page
        const buttons = document.querySelectorAll('#theme-toggle, .theme-btn, .theme-btn-compact');
        buttons.forEach(btn => {
            btn.innerHTML = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
            btn.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} mode`);
            btn.setAttribute('title', `Switch to ${isDark ? 'light' : 'dark'} mode`);
        });

        // Persist theme to localStorage (updating legacy keys for backward compatibility)
        try {
            localStorage.setItem(STORAGE_KEY, theme);
            localStorage.setItem('theme', theme);
            localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        } catch (e) {
            console.warn('localStorage is unavailable:', e);
        }

        // Synchronize with any header or content iframes
        syncIframes(isDark, theme);
    }

    // Push theme state to all iframes
    function syncIframes(isDark, theme) {
        const iframes = document.querySelectorAll('iframe');
        iframes.forEach(iframe => {
            try {
                if (iframe.contentWindow) {
                    iframe.contentWindow.postMessage({ type: 'set-theme', theme }, '*');
                }
                if (iframe.contentDocument && iframe.contentDocument.body) {
                    iframe.contentDocument.body.classList.toggle('dark', isDark);
                }
            } catch (_) {
                // Cross-origin restriction fallback
            }
        });
    }

    // Toggle between light and dark
    function toggleTheme() {
        const currentTheme = (document.body && document.body.classList.contains('dark')) ? 'dark' : 'light';
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
    }

    // Immediately execute on document load to prevent FOUC (flash of unstyled content)
    const initialTheme = getSavedTheme();
    if (document.documentElement) {
        document.documentElement.classList.toggle('dark', initialTheme === 'dark');
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyTheme(getSavedTheme());

        // Attach click listeners to all theme toggle buttons
        document.querySelectorAll('#theme-toggle, .theme-btn, .theme-btn-compact').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleTheme();
            });
        });

        // Setup responsive mobile navigation menu
        const menuBtn = document.getElementById('menu-toggle');
        const navLinks = document.getElementById('nav-links');
        if (menuBtn && navLinks) {
            menuBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const isOpen = navLinks.classList.toggle('nav-open');
                menuBtn.setAttribute('aria-expanded', String(isOpen));
            });

            // Close mobile menu when clicking outside or clicking any nav link
            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        navLinks.classList.remove('nav-open');
                        menuBtn.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        }

        // Sync theme with iframes once they finish loading
        document.querySelectorAll('iframe').forEach(iframe => {
            iframe.addEventListener('load', () => {
                syncIframes(document.body.classList.contains('dark'), getSavedTheme());
            });
        });

        // Listen for storage events across other tabs/windows
        window.addEventListener('storage', (e) => {
            if (e.key === STORAGE_KEY || e.key === 'theme' || e.key === 'darkMode') {
                applyTheme(getSavedTheme());
            }
        });
    });

    // Expose controller globally
    window.StudentHubTheme = {
        apply: applyTheme,
        toggle: toggleTheme,
        get: getSavedTheme
    };
})();
