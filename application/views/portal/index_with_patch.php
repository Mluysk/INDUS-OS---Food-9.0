<?php
include __DIR__.'/index.php';
?>
<style>
    html body .portal-cart-card .portal-cart-remove svg {
        width: 9.9px !important;
        height: 9.9px !important;
    }

    html body .topbar {
        gap: 1rem;
    }

    html body .topbar .theme-toggle:not(.portal-theme-switch) {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    html body .portal-theme-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 10px 18px;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 999px;
        background: var(--portal-card-bg, rgba(255, 255, 255, 0.95));
        color: var(--text, #1f2933);
        font-weight: 600;
        box-shadow: 0 18px 38px -28px rgba(15, 23, 42, 0.45);
    }

    html body .portal-theme-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 42px -30px rgba(15, 23, 42, 0.55);
    }

    html body .portal-theme-button svg {
        width: 100%;
        height: 100%;
        stroke: currentColor;
    }

    html body .portal-theme-button .theme-icon {
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    html body .portal-theme-button .theme-icon > svg {
        display: block;
    }

    html body .portal-theme-button .theme-icon-dark {
        display: none;
    }

    html[data-theme="dark"] body .portal-theme-button .theme-icon-dark {
        display: inline-flex;
    }

    html[data-theme="dark"] body .portal-theme-button .theme-icon-light {
        display: none;
    }

    html body .portal-app__welcome {
        min-width: 0;
    }

    html body .portal-app__welcome h2[data-cliente-nome] {
        display: flex;
        align-items: center;
        gap: 0.72rem;
        margin-top: 0.18rem;
        font-size: clamp(2rem, 4vw, 3.35rem) !important;
        line-height: 1.03 !important;
        letter-spacing: 0 !important;
        color: var(--text, #0f172a);
    }

    html body .portal-greeting-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: clamp(38px, 5vw, 56px);
        height: clamp(38px, 5vw, 56px);
        flex: 0 0 auto;
        border-radius: 18px;
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #fff);
        color: var(--primary, #dc2626);
        box-shadow: 0 16px 34px -24px rgba(15, 23, 42, 0.5);
    }

    html body .portal-greeting-icon svg {
        width: 58%;
        height: 58%;
        stroke: currentColor;
    }

    html body .portal-greeting-text {
        min-width: 0;
    }

    html body .portal-app__tag {
        font-size: 0.86rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    html body .portal-app__grid {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    html body .portal-cart-stack {
        position: fixed !important;
        inset: 0 !important;
        z-index: 1200 !important;
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        max-width: none !important;
        max-height: none !important;
        padding: 22px !important;
        pointer-events: none !important;
    }

    html body .portal-cart-stack.is-open {
        display: flex !important;
        pointer-events: auto !important;
    }

    html body .portal-cart-stack::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.48);
        backdrop-filter: blur(5px);
    }

    html body .portal-cart-card {
        position: relative !important;
        z-index: 1 !important;
        width: min(100%, 620px) !important;
        max-height: min(76vh, 680px) !important;
        overflow: hidden !important;
        padding: 1.45rem !important;
        border-radius: 24px !important;
        box-shadow: 0 32px 80px -28px rgba(15, 23, 42, 0.55) !important;
    }

    html body .portal-cart-card h3 {
        padding-right: 44px !important;
        font-size: 1.22rem !important;
    }

    html body .portal-cart-actions {
        position: relative !important;
        z-index: 1 !important;
        width: min(100%, 620px) !important;
        margin-top: 12px !important;
        padding: 1rem !important;
        border-radius: 20px !important;
        background: var(--portal-card-bg, rgba(255, 255, 255, 0.95)) !important;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08)) !important;
        box-shadow: 0 24px 60px -34px rgba(15, 23, 42, 0.45) !important;
    }

    html body .portal-cart-actions #btn-finalizar {
        width: 100% !important;
    }

    html body .portal-cart-close {
        position: absolute;
        z-index: 2;
        top: 18px;
        right: 18px;
        display: inline-grid;
        place-items: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        box-shadow: none;
    }

    html body .portal-cart-close:hover {
        transform: translateY(-1px);
        background: rgba(239, 68, 68, 0.18);
        box-shadow: 0 14px 26px -20px rgba(220, 38, 38, 0.8);
    }

    html body .portal-cart-close svg {
        width: 15px;
        height: 15px;
        stroke: currentColor;
    }

    html body .portal-cart-float {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 1100;
        display: inline-grid;
        place-items: center;
        width: 62px;
        height: 62px;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--primary, #dc2626), #b91c1c);
        color: #fff;
        box-shadow: 0 22px 42px -18px rgba(220, 38, 38, 0.78);
    }

    html body .portal-cart-float[hidden] {
        display: none !important;
    }

    html body .portal-cart-float svg {
        width: 25px;
        height: 25px;
        stroke: currentColor;
    }

    html body .portal-cart-float__count {
        position: absolute;
        top: -5px;
        right: -4px;
        min-width: 24px;
        height: 24px;
        display: inline-grid;
        place-items: center;
        padding: 0 6px;
        border-radius: 999px;
        background: #fff;
        color: var(--primary, #dc2626);
        border: 2px solid var(--primary, #dc2626);
        font-size: 0.75rem;
        line-height: 1;
        font-weight: 900;
        font-variant-numeric: tabular-nums;
    }

    html body .portal-cart-float__total {
        position: absolute;
        right: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%);
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        background: var(--portal-card-bg, #fff);
        color: var(--text, #1f2933);
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        box-shadow: 0 14px 28px -24px rgba(15, 23, 42, 0.5);
        font-size: 0.82rem;
        font-weight: 850;
        white-space: nowrap;
    }

    html body .portal-cart-stack.is-open + .portal-cart-float,
    html body .portal-cart-float.is-open {
        opacity: 0;
        pointer-events: none;
    }

    @media (max-width: 720px) {
        html body .portal-theme-button {
            min-height: 40px;
            padding: 0.62rem 0.85rem;
            font-size: 0.86rem;
        }

        html body .portal-app__welcome h2[data-cliente-nome] {
            align-items: flex-start;
            font-size: clamp(1.7rem, 9vw, 2.4rem) !important;
        }

        html body .portal-greeting-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            margin-top: 0.08em;
        }

        html body .portal-cart-stack {
            align-items: flex-end !important;
            padding: 12px !important;
        }

        html body .portal-cart-card,
        html body .portal-cart-actions {
            width: 100% !important;
        }

        html body .portal-cart-card {
            max-height: 72vh !important;
            border-radius: 22px !important;
            padding: 1.15rem !important;
        }

        html body .portal-cart-float {
            right: 16px;
            bottom: 16px;
            width: 56px;
            height: 56px;
        }

        html body .portal-cart-float__total {
            display: none;
        }
    }
</style>

<script>
(function () {
    const themeIcon = {
        dark: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 7.4A9 9 0 1 1 12 3Z"></path></svg>',
        light: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>',
    };

    const greetingIcons = {
        morning: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.15" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>',
        afternoon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.15" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18h18"></path><path d="M5 14a7 7 0 0 1 14 0"></path><path d="M12 4v3"></path><path d="m4.22 7.22 2.12 2.12"></path><path d="m19.78 7.22-2.12 2.12"></path></svg>',
        night: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.15" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 7.4A9 9 0 1 1 12 3Z"></path></svg>',
    };

    const getGreetingInfo = () => {
        const hour = new Date().getHours();
        if (hour < 12) return { text: 'Bom dia', icon: greetingIcons.morning };
        if (hour < 18) return { text: 'Boa tarde', icon: greetingIcons.afternoon };
        return { text: 'Boa noite', icon: greetingIcons.night };
    };

    const getStoredClientName = () => {
        try {
            const data = JSON.parse(localStorage.getItem('portal-cliente') || 'null');
            return data?.nome_fantasia || data?.nome_loja || data?.razao_social || data?.nome || '';
        } catch (error) {
            return '';
        }
    };

    const cleanClientName = (value, fallback = 'Cliente') => {
        const text = String(value || '').replace(/\s+Cliente$/i, '').trim();
        return text || fallback;
    };

    const setupThemeButton = () => {
        const topbar = document.querySelector('.topbar');
        const toggle = document.getElementById('toggle-theme');
        if (!topbar || !toggle || document.querySelector('.portal-theme-switch') || document.querySelector('.portal-theme-button')) return;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'portal-theme-button';
        topbar.appendChild(button);

        const sync = () => {
            button.innerHTML = `<span class="theme-icon theme-icon-light">${themeIcon.dark}</span><span class="theme-icon theme-icon-dark">${themeIcon.light}</span><span>Alternar tema</span>`;
            button.setAttribute('aria-label', 'Alternar tema');
        };

        button.addEventListener('click', () => {
            toggle.checked = !toggle.checked;
            toggle.dispatchEvent(new Event('change', { bubbles: true }));
            window.setTimeout(sync, 0);
        });

        toggle.addEventListener('change', sync);
        new MutationObserver(sync).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        sync();
    };

    const setupGreeting = () => {
        const title = document.querySelector('[data-cliente-nome]');
        const badge = document.querySelector('[data-cliente-badge]');
        if (!title || title.dataset.greetingReady === '1') return;
        title.dataset.greetingReady = '1';

        const apply = () => {
            if (title.dataset.portalGreetingManaged === '1') return;
            const current = String(badge?.textContent || '').trim();
            const stored = getStoredClientName();
            const name = current && current !== 'Cliente' ? cleanClientName(current) : cleanClientName(stored);
            const greeting = getGreetingInfo();
            title.innerHTML = `<span class="portal-greeting-icon">${greeting.icon}</span><span class="portal-greeting-text">${greeting.text}, ${name}</span>`;
        };

        apply();
        if (badge) {
            new MutationObserver(apply).observe(badge, { childList: true, characterData: true, subtree: true });
        }
        window.setInterval(apply, 60000);
        window.addEventListener('storage', apply);
    };

    const runHeaderEnhancements = () => {
        setupThemeButton();
        setupGreeting();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runHeaderEnhancements, { once: true });
    } else {
        runHeaderEnhancements();
    }
})();
</script>

<script>
(function () {
    const run = () => {
        const portalApp = document.getElementById('portal-app');
        const cartStack = document.querySelector('.portal-cart-stack');
        const cartCard = document.querySelector('.portal-cart-card');
        const cartLines = document.getElementById('portal-cart-lines');
        const cartTotal = document.getElementById('portal-cart-total');
        if (!portalApp || !cartStack || !cartCard || !cartLines || !cartTotal || cartStack.dataset.popupReady === '1') {
            return;
        }

        cartStack.dataset.popupReady = '1';
        cartStack.setAttribute('role', 'dialog');
        cartStack.setAttribute('aria-modal', 'true');
        cartStack.setAttribute('aria-label', 'Carrinho');
        cartStack.setAttribute('aria-hidden', 'true');

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'portal-cart-close';
        closeButton.setAttribute('aria-label', 'Fechar carrinho');
        closeButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
        cartCard.appendChild(closeButton);

        const floatButton = document.createElement('button');
        floatButton.type = 'button';
        floatButton.className = 'portal-cart-float';
        floatButton.setAttribute('aria-label', 'Abrir carrinho');
        floatButton.innerHTML = '<span class="portal-cart-float__total">R$ 0,00</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h8.42a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg><span class="portal-cart-float__count">0</span>';
        document.body.appendChild(floatButton);

        const floatCount = floatButton.querySelector('.portal-cart-float__count');
        const floatTotal = floatButton.querySelector('.portal-cart-float__total');

        const getItemCount = () => {
            const empty = cartLines.querySelector('.portal-cart-empty');
            if (empty) return 0;
            return cartLines.querySelectorAll('tr').length;
        };

        const syncButton = () => {
            const appVisible = !portalApp.hidden;
            const count = getItemCount();
            floatButton.hidden = !appVisible;
            floatCount.textContent = String(count);
            floatTotal.textContent = cartTotal.textContent || 'R$ 0,00';
            floatButton.classList.toggle('has-items', count > 0);
        };

        const openCart = () => {
            cartStack.classList.add('is-open');
            floatButton.classList.add('is-open');
            cartStack.setAttribute('aria-hidden', 'false');
            closeButton.focus();
        };

        const closeCart = () => {
            cartStack.classList.remove('is-open');
            floatButton.classList.remove('is-open');
            cartStack.setAttribute('aria-hidden', 'true');
        };

        floatButton.addEventListener('click', openCart);
        closeButton.addEventListener('click', closeCart);
        cartStack.addEventListener('click', (event) => {
            if (event.target === cartStack) closeCart();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && cartStack.classList.contains('is-open')) closeCart();
        });
        document.getElementById('btn-finalizar')?.addEventListener('click', () => {
            window.setTimeout(syncButton, 80);
        });

        new MutationObserver(syncButton).observe(cartLines, { childList: true, subtree: true });
        new MutationObserver(syncButton).observe(cartTotal, { childList: true, characterData: true, subtree: true });
        new MutationObserver(syncButton).observe(portalApp, { attributes: true, attributeFilter: ['hidden'] });
        syncButton();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
