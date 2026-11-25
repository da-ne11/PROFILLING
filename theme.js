(function () {
    var key = 'kkTheme';
    var stored = null;
    try { stored = localStorage.getItem(key); } catch (e) {}

    var theme = stored === 'dark' || stored === 'light'
        ? stored
        : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    document.documentElement.setAttribute('data-bs-theme', theme);
    document.documentElement.classList.toggle('dark-mode', theme === 'dark');

    function updateButtons() {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            button.innerHTML = theme === 'dark'
                ? '<i class="bi bi-sun me-2"></i>Light mode'
                : '<i class="bi bi-moon-stars me-2"></i>Dark mode';
        });
        document.querySelectorAll('[data-theme-set]').forEach(function (button) {
            var selected = button.getAttribute('data-theme-set') === theme;
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
            button.classList.toggle('is-active', selected);
        });
    }

    function setTheme(nextTheme) {
        if (nextTheme !== 'dark' && nextTheme !== 'light') return;
        theme = nextTheme;
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.documentElement.classList.toggle('dark-mode', theme === 'dark');
        try { localStorage.setItem(key, theme); } catch (e) {}
        updateButtons();
    }

    window.kkToggleTheme = function () {
        setTheme(theme === 'dark' ? 'light' : 'dark');
    };

    function createLogoutDialog() {
        var style = document.createElement('style');
        style.textContent = [
            '.logout-confirm-dialog{width:min(420px,calc(100% - 32px));max-width:420px;padding:24px;border:1px solid #dce5ef;border-radius:12px;background:#fff;color:#27364a;box-shadow:0 16px 48px rgba(0,0,0,.24)}',
            '.logout-confirm-dialog::backdrop{background:rgba(0,0,0,.5)}',
            '.logout-confirm-dialog h2{margin:0 0 12px;font-size:1.15rem;font-weight:700}',
            '.logout-confirm-dialog p{margin:0 0 22px;color:#27364a}',
            '.logout-confirm-actions{display:flex;justify-content:flex-end;gap:10px}',
            '.logout-confirm-actions button{padding:7px 16px;border:1px solid transparent;border-radius:6px;font:inherit;cursor:pointer}',
            '.logout-confirm-no{border-color:#cbd9e8;background:#fff;color:#355779}',
            '.logout-confirm-yes{background:var(--bs-primary,#2874c8);color:#fff}'
        ].join('');
        document.head.appendChild(style);

        var dialog = document.createElement('dialog');
        dialog.className = 'logout-confirm-dialog';
        dialog.setAttribute('aria-labelledby', 'logoutConfirmTitle');
        dialog.innerHTML = '<h2 id="logoutConfirmTitle">Confirm logout</h2>' +
            '<p>Are you sure you want to log out?</p>' +
            '<div class="logout-confirm-actions">' +
                '<button type="button" class="logout-confirm-no">Cancel</button>' +
                '<button type="button" class="logout-confirm-yes">Yes, Log Out</button>' +
            '</div>';
        document.body.appendChild(dialog);
        dialog.querySelector('.logout-confirm-no').addEventListener('click', function () {
            dialog.close();
        });
        dialog.querySelector('.logout-confirm-yes').addEventListener('click', function () {
            var destination = dialog.dataset.logoutDestination;
            dialog.close();
            if (destination) window.location.assign(destination);
        });
        return dialog;
    }

    var logoutDialog = null;
    document.addEventListener('click', function (event) {
        var logoutLink = event.target.closest('a[href]');
        if (logoutLink && /(?:^|\/)logout\.php(?:[?#].*)?$/.test(logoutLink.getAttribute('href') || '')) {
            event.preventDefault();
            if (!('HTMLDialogElement' in window)
                || typeof window.HTMLDialogElement.prototype.showModal !== 'function') {
                if (window.confirm('Are you sure you want to log out?')) window.location.assign(logoutLink.href);
                return;
            }
            if (!logoutDialog) logoutDialog = createLogoutDialog();
            logoutDialog.dataset.logoutDestination = logoutLink.href;
            if (!logoutDialog.open) logoutDialog.showModal();
            return;
        }
        var button = event.target.closest('[data-theme-set]');
        if (button) setTheme(button.getAttribute('data-theme-set'));
    });

    document.addEventListener('DOMContentLoaded', updateButtons);
})();
