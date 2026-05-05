/**
 * darkmode.js — Grocer Ease Dark / Light Mode Toggle
 *
 * How to use:
 *  1. Add <link rel="stylesheet" href="darkmode.css"> in your <head>.
 *  2. Add <script src="darkmode.js" defer></script> before </body>.
 *  3. Add a toggle button anywhere in the topbar:
 *       <button id="dark-toggle" title="Toggle dark mode">
 *           <i class="fa-solid fa-circle-half-stroke"></i>
 *       </button>
 *
 * The script remembers the user's choice in localStorage so it
 * survives page reloads and navigation between pages.
 */

(function () {
    const STORAGE_KEY = 'grocer-ease-theme';
    const DARK_CLASS  = 'dark-mode';

    // ── Apply saved theme BEFORE paint (avoids flash) ──────────
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'dark') {
        document.body.classList.add(DARK_CLASS);
    }

    // ── Wire up the button once the DOM is ready ────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('dark-toggle');
        if (!btn) return;

        btn.addEventListener('click', function () {
            const isNowDark = document.body.classList.toggle(DARK_CLASS);
            localStorage.setItem(STORAGE_KEY, isNowDark ? 'dark' : 'light');
        });
    });
})();