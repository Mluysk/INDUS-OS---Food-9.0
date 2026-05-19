<?php
include __DIR__.'/index_tag_patch.php';
?>
<style>
    html body .portal-app__header-links {
        display: inline-flex !important;
        align-items: center;
        justify-content: flex-end;
        gap: 0.55rem;
        flex-wrap: wrap;
    }

    html body .portal-app__header-links .portal-app__link {
        white-space: nowrap;
    }

    html body .portal-tab {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 0.48rem;
    }

    html body .portal-tab__icon {
        width: 1.05em;
        height: 1.05em;
        display: inline-flex;
        flex: 0 0 auto;
    }

    html body .portal-tab__icon svg {
        width: 100%;
        height: 100%;
        display: block;
        fill: currentColor;
    }

    @media (max-width: 720px) {
        html body .portal-app__header-links {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
<script>
(function () {
    const addCircleIcon = `
        <span class="portal-tab__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm.75 5.5a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
            </svg>
        </span>`;

    const listTabIcon = `
        <span class="portal-tab__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M7 5.25A1.25 1.25 0 0 1 8.25 4h10.5A1.25 1.25 0 0 1 20 5.25v.5A1.25 1.25 0 0 1 18.75 7H8.25A1.25 1.25 0 0 1 7 5.75v-.5Zm0 6A1.25 1.25 0 0 1 8.25 10h10.5A1.25 1.25 0 0 1 20 11.25v.5A1.25 1.25 0 0 1 18.75 13H8.25A1.25 1.25 0 0 1 7 11.75v-.5Zm0 6A1.25 1.25 0 0 1 8.25 16h10.5A1.25 1.25 0 0 1 20 17.25v.5A1.25 1.25 0 0 1 18.75 19H8.25A1.25 1.25 0 0 1 7 17.75v-.5ZM4 4.25a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Zm0 6a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Zm0 6a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Z" />
            </svg>
        </span>`;

    const supportTabIcon = `
        <span class="portal-tab__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2.5a8.5 8.5 0 0 0-8.5 8.5v3.25A2.75 2.75 0 0 0 6.25 17H7a1 1 0 0 0 1-1v-4.5a1 1 0 0 0-1-1H5.05a6.95 6.95 0 0 1 13.9 0H17a1 1 0 0 0-1 1V16a1 1 0 0 0 1 1h1.5a2.5 2.5 0 0 1-2.5 2.5h-2.25a1.25 1.25 0 0 0 0 2.5H16a5 5 0 0 0 5-5v-6a8.5 8.5 0 0 0-9-8.5Z" />
            </svg>
        </span>`;

    const listIcon = `
        <span class="portal-app__link-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M7 5.25A1.25 1.25 0 0 1 8.25 4h10.5A1.25 1.25 0 0 1 20 5.25v.5A1.25 1.25 0 0 1 18.75 7H8.25A1.25 1.25 0 0 1 7 5.75v-.5Zm0 6A1.25 1.25 0 0 1 8.25 10h10.5A1.25 1.25 0 0 1 20 11.25v.5A1.25 1.25 0 0 1 18.75 13H8.25A1.25 1.25 0 0 1 7 11.75v-.5Zm0 6A1.25 1.25 0 0 1 8.25 16h10.5A1.25 1.25 0 0 1 20 17.25v.5A1.25 1.25 0 0 1 18.75 19H8.25A1.25 1.25 0 0 1 7 17.75v-.5ZM4 4.25a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Zm0 6a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Zm0 6a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Z" />
            </svg>
        </span>`;

    const setButtonText = (button, text) => {
        if (!button) return;
        const label = button.querySelector('.portal-app__link-text');
        if (label) {
            label.textContent = text;
        } else {
            button.textContent = text;
        }
    };

    const setTabContent = (button, icon, text) => {
        if (!button) return;
        button.innerHTML = `${icon}<span>${text}</span>`;
    };

    const makeOrdersButton = () => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'portal-app__link';
        button.dataset.portalJump = 'pedidos';
        button.innerHTML = `${listIcon}<span class="portal-app__link-text">Meus pedidos</span>`;
        button.addEventListener('click', () => {
            const trigger = document.querySelector('[data-portal-tab-trigger="pedidos"]');
            trigger?.click();
        });
        return button;
    };

    const organizeHeaderMenu = () => {
        const menu = document.querySelector('.portal-app__header-links');
        if (!menu) return;
        const newOrder = menu.querySelector('[data-portal-jump="novo-pedido"]');
        const support = menu.querySelector('[data-portal-jump="suporte"]');
        let orders = menu.querySelector('[data-portal-jump="pedidos"]');
        if (!orders) {
            orders = makeOrdersButton();
        }
        setButtonText(newOrder, 'Fazer novo pedido');
        setButtonText(orders, 'Meus pedidos');
        setButtonText(support, 'Suporte');
        [newOrder, orders, support].filter(Boolean).forEach((button) => menu.appendChild(button));
    };

    const organizeTabs = () => {
        const nav = document.querySelector('.portal-app__nav');
        if (!nav) return;
        const newOrder = nav.querySelector('[data-portal-tab-trigger="novo-pedido"]');
        const orders = nav.querySelector('[data-portal-tab-trigger="pedidos"]');
        const support = nav.querySelector('[data-portal-tab-trigger="suporte"]');
        setTabContent(newOrder, addCircleIcon, 'Fazer novo pedido');
        setTabContent(orders, listTabIcon, 'Meus pedidos');
        setTabContent(support, supportTabIcon, 'Suporte');
        [newOrder, orders, support].filter(Boolean).forEach((button) => nav.appendChild(button));
    };

    const updateWelcomeTexts = () => {
        document.querySelectorAll('.portal-welcome__tips strong').forEach((item) => {
            if (item.textContent.trim() === 'Pedidos') {
                item.textContent = 'Meus pedidos';
            }
        });
    };

    const run = () => {
        organizeHeaderMenu();
        organizeTabs();
        updateWelcomeTexts();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
