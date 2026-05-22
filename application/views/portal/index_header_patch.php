<?php
include __DIR__.'/index_with_patch.php';
?>
<style>
    html body .portal-app__welcome {
        min-height: calc(100% + 0.6px) !important;
        padding-top: 0.6px !important;
        padding-bottom: 0.6px !important;
    }

    html body .portal-app__welcome h2[data-cliente-nome] {
        font-size: clamp(1.24rem, 2.37vw, 1.98rem) !important;
        gap: 0.78rem !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        line-height: 1.08 !important;
    }

    html body .portal-greeting-icon {
        width: clamp(58px, 7.72vw, 87px) !important;
        height: clamp(58px, 7.72vw, 87px) !important;
        border-radius: 24px !important;
    }

    html body .portal-greeting-label,
    html body .portal-greeting-store-name {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    html body .portal-greeting-store-name {
        font-weight: 950;
        color: var(--text, #0f172a);
    }

    html body .portal-greeting-pill {
        display: inline-flex;
        align-items: center;
        max-width: min(360px, 100%);
        padding: 0.38rem 0.88rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 24%, transparent);
        background: color-mix(in srgb, var(--primary, #dc2626) 11%, #ffffff);
        box-shadow: 0 18px 38px -28px color-mix(in srgb, var(--primary, #dc2626) 42%, transparent);
        vertical-align: middle;
    }

    html body .portal-greeting-branch {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--text-muted, #64748b);
        font-size: 0.46em;
        font-weight: 900;
        line-height: 1.12;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    html body .portal-app__header-links {
        display: none !important;
    }

    html body .portal-top-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        margin-left: auto;
    }

    html body .topbar > .portal-theme-button {
        margin-left: 0;
    }

    html body .topbar .portal-app__profile {
        display: none;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
    }

    html body .topbar .portal-app__profile.is-visible {
        display: inline-flex;
    }

    html body .topbar .portal-app__client {
        display: inline-flex;
        align-items: center;
        gap: 0.58rem;
        width: auto;
        min-width: 0;
        max-width: min(100%, 250px);
        min-height: 64px;
        padding: 0.42rem 1rem 0.42rem 0.5rem;
        border: 1px solid var(--border, var(--portal-card-border, rgba(15, 23, 42, 0.08)));
        border-radius: 999px;
        background: var(--portal-card-bg, rgba(255, 255, 255, 0.95));
        color: var(--text, #1f2933);
        box-shadow: 0 16px 34px -30px rgba(15, 23, 42, 0.46);
        overflow: hidden;
    }

    html body .portal-client-avatar {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: color-mix(in srgb, var(--primary, #2563eb) 34%, #ffffff);
        color: color-mix(in srgb, var(--primary, #2563eb) 82%, #0f172a);
        font-weight: 900;
        font-size: 0.86rem;
        letter-spacing: 0;
    }

    html body .portal-client-lines {
        min-width: 0;
        display: grid !important;
        gap: 0.1rem;
        line-height: 1.12;
        overflow: hidden;
    }

    html body .portal-client-store,
    html body .portal-client-branch {
        display: block;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    html body .portal-client-store {
        color: var(--text, #0f172a);
        font-size: 1rem;
        font-weight: 900;
    }

    html body .portal-client-branch {
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 700;
    }

    html body .topbar #portal-logout {
        min-height: 44px;
        padding: 0.72rem 1rem;
        border-radius: 999px;
        background: var(--surface-alt, rgba(148, 163, 184, 0.16));
        border: 1px solid var(--border, rgba(148, 163, 184, 0.3));
        color: var(--text, #1f2933);
        font-weight: 850;
        box-shadow: 0 14px 26px -24px rgba(15, 23, 42, 0.4);
    }

    html body .portal-app__header > .portal-app__profile {
        display: none !important;
    }

    @media (max-width: 720px) {
        html body .portal-app__welcome h2[data-cliente-nome] {
            font-size: clamp(0.99rem, 5.43vw, 1.41rem) !important;
            gap: 0.56rem !important;
        }

        html body .portal-greeting-icon {
            width: 58px !important;
            height: 58px !important;
            border-radius: 19px !important;
        }

        html body .portal-greeting-pill {
            max-width: 100%;
            padding: 0.34rem 0.72rem;
        }

        html body .portal-greeting-branch {
            font-size: 0.5em;
        }

        html body .portal-top-actions {
            width: 100%;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        html body .topbar .portal-app__client {
            max-width: min(100%, 236px);
            min-height: 58px;
            padding: 0.36rem 0.78rem 0.36rem 0.42rem;
        }

        html body .portal-client-avatar {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
            font-size: 0.78rem;
        }

        html body .portal-client-store {
            font-size: 0.88rem;
        }

        html body .portal-client-branch {
            font-size: 0.7rem;
        }

        html body .topbar #portal-logout {
            min-height: 40px;
            padding: 0.62rem 0.78rem;
            font-size: 0.84rem;
        }
    }
</style>

<script>
(function () {
    const escapeHtml = (value) => String(value || '').replace(/[&<>"]/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;'
    }[char]));

    const readCliente = () => {
        try {
            return JSON.parse(localStorage.getItem('portal-cliente') || '{}') || {};
        } catch (error) {
            return {};
        }
    };

    const getStoreName = (fallback = 'Cliente') => {
        const cliente = readCliente();
        return String(cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome || fallback || 'Cliente').trim();
    };

    const getBranchName = () => {
        const cliente = readCliente();
        return String(cliente.nome_filial || cliente.filial_nome || cliente.im || '').trim();
    };

    const initialsFrom = (value) => {
        const words = String(value || 'Cliente').trim().split(/\s+/).filter(Boolean);
        return words.slice(0, 2).map((word) => word.charAt(0).toUpperCase()).join('') || 'CL';
    };

    const greetingLabel = () => {
        const hour = new Date().getHours();
        if (hour >= 5 && hour < 12) return 'Bom dia';
        if (hour >= 12 && hour < 18) return 'Boa tarde';
        return 'Boa noite';
    };

    const renderGreeting = () => {
        const title = document.querySelector('[data-cliente-nome]');
        if (!title) return;
        const storeName = getStoreName();
        const branchName = getBranchName();
        const icon = title.querySelector('.portal-greeting-icon')?.outerHTML || '';
        const branchPill = branchName
            ? `<span class="portal-greeting-pill"><small class="portal-greeting-branch">${escapeHtml(branchName)}</small></span>`
            : '';
        title.dataset.portalGreetingLock = '1';
        title.innerHTML = `${icon}<span class="portal-greeting-label">${greetingLabel()},</span><span class="portal-greeting-store-name">${escapeHtml(storeName)}</span>${branchPill}`;
        window.setTimeout(() => { delete title.dataset.portalGreetingLock; }, 0);
    };

    const renderProfileBadge = () => {
        const badge = document.querySelector('.topbar .portal-app__client');
        if (!badge) return;
        const current = String(badge.textContent || '').trim();
        const storeName = getStoreName(current || 'Cliente');
        const branchName = getBranchName();
        const subline = branchName || 'Cliente';
        const renderKey = `${storeName}|${subline}`;
        if (badge.dataset.portalRenderedKey === renderKey && badge.querySelector('.portal-client-lines')) {
            renderGreeting();
            window.setTimeout(renderGreeting, 80);
            return;
        }
        badge.dataset.portalRenderedKey = renderKey;
        badge.innerHTML = `
            <span class="portal-client-avatar" aria-hidden="true">${escapeHtml(initialsFrom(storeName))}</span>
            <span class="portal-client-lines">
                <strong class="portal-client-store">${escapeHtml(storeName)}</strong>
                <small class="portal-client-branch">${escapeHtml(subline)}</small>
            </span>
        `;
        badge.title = branchName ? `${storeName} - ${branchName}` : storeName;
        renderGreeting();
        window.setTimeout(renderGreeting, 80);
    };

    const run = () => {
        const topbar = document.querySelector('.topbar');
        const themeButton = document.querySelector('.portal-theme-button');
        const profile = document.querySelector('.portal-app__profile');
        const portalApp = document.getElementById('portal-app');
        if (!topbar || !themeButton || !profile || profile.dataset.topbarReady === '1') return;

        profile.dataset.topbarReady = '1';
        let actions = document.querySelector('.portal-top-actions');
        if (!actions) {
            actions = document.createElement('div');
            actions.className = 'portal-top-actions';
            topbar.appendChild(actions);
        }

        actions.appendChild(themeButton);
        actions.appendChild(profile);

        const syncProfile = () => {
            const visible = portalApp && !portalApp.hidden;
            profile.classList.toggle('is-visible', Boolean(visible));
            if (visible) {
                setTimeout(renderProfileBadge, 0);
                setTimeout(renderProfileBadge, 80);
                setTimeout(renderGreeting, 160);
            }
        };

        if (portalApp) {
            new MutationObserver(syncProfile).observe(portalApp, { attributes: true, attributeFilter: ['hidden'] });
        }

        const rawBadge = profile.querySelector('[data-cliente-badge]');
        if (rawBadge) {
            new MutationObserver(() => {
                setTimeout(renderProfileBadge, 0);
                setTimeout(renderGreeting, 120);
            }).observe(rawBadge, { childList: true, characterData: true, subtree: true });
        }

        const title = document.querySelector('[data-cliente-nome]');
        if (title) {
            new MutationObserver(() => {
                if (title.dataset.portalGreetingLock === '1') return;
                setTimeout(renderGreeting, 0);
            }).observe(title, { childList: true, characterData: true, subtree: true });
        }

        syncProfile();
        renderGreeting();
    };

    window.addEventListener('storage', (event) => {
        if (event.key === 'portal-cliente') {
            renderProfileBadge();
            renderGreeting();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
