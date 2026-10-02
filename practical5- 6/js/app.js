/**
 * Student Hub - Main Portal Application (js/app.js)
 * Developed by: Student Web Developer
 * 
 * Features on Index Page:
 * 1. Background Image Slider behind the main page (transitions campus photos smoothly)
 * 2. Light / Dark Theme Switcher with localStorage persistence
 * 3. Responsive Navigation Hamburger Menu with accessible ARIA attributes
 * 4. Campus Notification Banner loaded dynamically from data/notices.json via Fetch API
 */

document.addEventListener('DOMContentLoaded', () => {

    // Helper: DOM Element Selector
    const get = (id) => document.getElementById(id);

    // ========================================================
    // 1. SAFE FETCH API HELPER
    // ========================================================
    async function safeFetch(url) {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Failed to load ${url} (HTTP ${response.status})`);
        }
        return await response.json();
    }

    // ========================================================
    // 2. LIGHT / DARK THEME SWITCHER (localStorage)
    // ========================================================
    const themeButton = get('theme-toggle');

    function applyTheme(theme) {
        const isDark = theme === 'dark';
        document.body.classList.toggle('dark', isDark);

        if (themeButton) {
            themeButton.textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
            themeButton.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} theme`);
        }

        try {
            localStorage.setItem('student-hub-theme', theme);
        } catch (err) {
            console.warn('localStorage is unavailable:', err);
        }
    }

    function initTheme() {
        if (window.StudentHubTheme) return; // Delegated to universal theme.js
        let savedTheme = 'light';
        try {
            savedTheme = localStorage.getItem('student-hub-theme') || 'light';
        } catch (_) {
            savedTheme = 'light';
        }
        applyTheme(savedTheme);

        if (themeButton) {
            themeButton.addEventListener('click', () => {
                const nextTheme = document.body.classList.contains('dark') ? 'light' : 'dark';
                applyTheme(nextTheme);
            });
        }
    }

    // ========================================================
    // 3. RESPONSIVE HAMBURGER NAVIGATION MENU
    // ========================================================
    const menuButton = get('menu-toggle');
    const navLinks = get('nav-links');

    function initHamburgerMenu() {
        if (window.StudentHubTheme) return; // Delegated to universal theme.js
        if (!menuButton || !navLinks) return;

        menuButton.addEventListener('click', () => {
            const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', String(!isExpanded));
            navLinks.classList.toggle('nav-open');
        });

        // Close mobile nav when clicking any link
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('nav-open');
                    menuButton.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    // ========================================================
    // 4. SLIDING BACKGROUND IMAGE BEHIND THE MAIN PAGE
    // ========================================================
    function initBackgroundSlider() {
        const slides = document.querySelectorAll('.bg-slide');
        if (slides.length <= 1) return;

        let currentSlide = 0;

        // Automatically cycle background images every 5 seconds
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000);
    }

    // ========================================================
    // 5. CAMPUS NOTIFICATION BANNER (Fetch data/notices.json)
    // ========================================================
    const noticeBanner = get('notice-banner');
    const noticeBannerText = get('notice-banner-text');
    const dismissNoticeBtn = get('dismiss-notice');

    async function loadNotificationBanner() {
        if (!noticeBanner || !noticeBannerText) return;

        try {
            const notices = await safeFetch('data/notices.json');
            if (notices && notices.length > 0) {
                const latestNotice = notices[0];
                noticeBannerText.innerHTML = `<strong>📢 ${latestNotice.category}:</strong> ${latestNotice.title} — <em>${latestNotice.text}</em>`;
                noticeBanner.style.display = 'flex';
            } else {
                noticeBanner.style.display = 'none';
            }
        } catch (error) {
            console.warn('Could not load campus notice banner:', error);
            noticeBanner.style.display = 'none';
        }

        if (dismissNoticeBtn) {
            dismissNoticeBtn.addEventListener('click', () => {
                noticeBanner.style.display = 'none';
            });
        }
    }

    // ========================================================
    // INITIALIZE ALL INDEX MODULES
    // ========================================================
    initTheme();
    initHamburgerMenu();
    initBackgroundSlider();
    loadNotificationBanner();
});
