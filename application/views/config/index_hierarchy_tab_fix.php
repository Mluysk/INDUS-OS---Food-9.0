<?php
include __DIR__.'/index.php';
?>
<style>
    html body [data-tab-panel="users"].active .user-subpanel {
        display: none !important;
    }

    html body [data-tab-panel="users"].active .user-subpanel.active {
        display: block !important;
    }

    html body [data-tab-panel="users"].active .user-subtab {
        pointer-events: auto !important;
        position: relative !important;
        z-index: 3 !important;
    }

    html body [data-user-panel="hierarchy"].active .hierarchy-form,
    html body [data-user-panel="hierarchy"].active .role-grid,
    html body [data-user-panel="hierarchy"].active .role-card {
        pointer-events: auto !important;
    }
</style>
<script>
(function () {
    const validUserTabs = new Set(['create', 'hierarchy']);

    const activateMainUsersTab = () => {
        document.querySelectorAll('.tab-button').forEach((button) => {
            const active = button.dataset.tab === 'users';
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('.tab-panel').forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.tabPanel === 'users');
        });

        document.querySelectorAll('input[name="active_tab"]').forEach((input) => {
            input.value = 'users';
        });

        localStorage.setItem('configTab', 'users');
    };

    const activateUserSubtab = (key) => {
        const target = validUserTabs.has(key) ? key : 'create';
        document.querySelectorAll('.user-subtab').forEach((button) => {
            const active = button.dataset.userTab === target;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('.user-subpanel').forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.userPanel === target);
        });

        localStorage.setItem('configUserTab', target);
    };

    const boot = () => {
        document.querySelectorAll('.user-subtab').forEach((button) => {
            if (button.dataset.hierarchyTabFixReady === '1') return;
            button.dataset.hierarchyTabFixReady = '1';
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                activateMainUsersTab();
                activateUserSubtab(button.dataset.userTab || 'create');
            }, true);
        });

        const stored = localStorage.getItem('configUserTab');
        if (stored && !validUserTabs.has(stored)) {
            localStorage.removeItem('configUserTab');
        }

        const activeButton = document.querySelector('.user-subtab.active');
        const activePanel = document.querySelector('.user-subpanel.active');
        if (!activeButton || !activePanel) {
            activateUserSubtab(validUserTabs.has(stored || '') ? stored : 'create');
        }

        document.querySelectorAll('.hierarchy-form').forEach((form) => {
            if (form.dataset.hierarchySubmitReady === '1') return;
            form.dataset.hierarchySubmitReady = '1';
            form.addEventListener('submit', () => {
                localStorage.setItem('configTab', 'users');
                localStorage.setItem('configUserTab', 'hierarchy');
                form.querySelectorAll('input[name="active_tab"]').forEach((input) => {
                    input.value = 'users';
                });
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
</script>
