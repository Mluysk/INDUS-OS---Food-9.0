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
        max-width: min(320px, 100%);
        padding: 0.34rem 0.82rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 30%, transparent);
        background: color-mix(in srgb, var(--primary, #2563eb) 14%, #ffffff);
        box-shadow: 0 18px 38px -28px color-mix(in srgb, var(--primary, #2563eb) 48%, transparent);
        vertical-align: middle;
    }

    html body .portal-greeting-branch {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: color-mix(in srgb, var(--primary, #2563eb) 82%, #0f172a);
        font-size: 0.77em;
        font-weight: 900;
        line-height: 1.12;
        letter-spacing: 0;
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
        display: none !important;
    }

    html body .topbar .portal-client-pill-main {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        width: auto;
        min-width: 0;
        max-width: min(100%, 360px);
        min-height: 0;
        padding: 6px 13px;
        border: 1px solid var(--border, var(--portal-card-border, rgba(15, 23, 42, 0.08)));
        border-radius: 999px;
        background: var(--portal-card-bg, rgba(255, 255, 255, 0.95));
        color: var(--text, #1f2933);
        box-shadow: 0 16px 34px -30px rgba(15, 23, 42, 0.46);
        overflow: hidden;
    }

    html body .portal-client-source-hidden {
        display: none !important;
    }

    html body .portal-client-avatar {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: color-mix(in srgb, var(--primary, #2563eb) 34%, #ffffff);
        color: color-mix(in srgb, var(--primary, #2563eb) 82%, #0f172a);
        font-weight: 900;
        font-size: 1rem;
        letter-spacing: 0;
    }

    html body .portal-client-lines {
        min-width: 0;
        display: grid !important;
        gap: 3px;
        line-height: 1.05;
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
        font-size: 1.05rem;
        font-weight: 900;
    }

    html body .portal-client-branch {
        color: var(--text-muted, #64748b);
        font-size: 0.88rem;
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

        html body .topbar .portal-client-pill-main {
            max-width: min(100%, 340px);
            min-height: 0;
            padding: 6px 13px;
        }

        html body .portal-client-avatar {
            width: 48px;
            height: 48px;
            flex-basis: 48px;
            font-size: 1rem;
        }

        html body .portal-client-store {
            font-size: 1rem;
        }

        html body .portal-client-branch {
            font-size: 0.84rem;
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

    const cleanStoreName = (value, fallback = 'Cliente') => {
        const text = String(value || '').replace(/\s+Cliente$/i, '').trim();
        return text || fallback;
    };

    const getStoreName = (fallback = 'Cliente') => {
        const cliente = readCliente();
        return cleanStoreName(cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome, fallback);
    };

    const getBranchName = () => {
        const cliente = readCliente();
        return String(cliente.nome_filial || cliente.filial_nome || cliente.filial || '').trim();
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
        title.dataset.portalGreetingManaged = '1';
        title.innerHTML = `${icon}<span class="portal-greeting-label">${greetingLabel()},</span><span class="portal-greeting-store-name">${escapeHtml(storeName)}</span>${branchPill}`;
        window.setTimeout(() => { title.dataset.portalGreetingLock = '0'; }, 0);
    };

    const ensureProfilePill = () => {
        const profile = document.querySelector('.topbar .portal-app__profile') || document.querySelector('.portal-app__profile');
        if (!profile) return null;
        const source = profile.querySelector('[data-cliente-badge]');
        if (source) {
            source.classList.add('portal-client-source-hidden');
            source.setAttribute('aria-hidden', 'true');
            source.setAttribute('style', 'display:none!important;');
        }
        let pill = profile.querySelector('.portal-client-pill-main');
        if (!pill) {
            pill = document.createElement('span');
            pill.className = 'portal-client-pill-main';
            pill.setAttribute('aria-live', 'polite');
            if (source) {
                source.insertAdjacentElement('afterend', pill);
            } else {
                profile.prepend(pill);
            }
        }
        return { source, pill };
    };

    const renderProfileBadge = () => {
        const parts = ensureProfilePill();
        if (!parts) return;
        const { source, pill } = parts;
        const current = String(source?.textContent || '').trim();
        const storeName = getStoreName(cleanStoreName(current, 'Cliente'));
        const branchName = getBranchName();
        const branchLine = branchName
            ? `<small class="portal-client-branch" style="display:block!important;font-size:0.88rem!important;font-weight:700!important;line-height:1.05!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;">${escapeHtml(branchName)}</small>`
            : '';
        const renderKey = `${storeName}|${branchName}`;
        if (pill.dataset.portalRenderedKey === renderKey) {
            renderGreeting();
            return;
        }
        pill.dataset.portalRenderedKey = renderKey;
        pill.setAttribute('style', 'display:inline-flex!important;align-items:center!important;justify-content:flex-start!important;gap:10px!important;width:auto!important;min-width:0!important;max-width:min(100%,360px)!important;height:auto!important;min-height:0!important;padding:6px 13px!important;border-radius:999px!important;overflow:hidden!important;box-sizing:border-box!important;');
        pill.innerHTML = `
            <span class="portal-client-avatar" aria-hidden="true" style="width:48px!important;height:48px!important;min-width:48px!important;flex:0 0 48px!important;font-size:1rem!important;font-weight:900!important;line-height:1!important;">${escapeHtml(initialsFrom(storeName))}</span>
            <span class="portal-client-lines" style="display:grid!important;gap:2px!important;line-height:1.05!important;min-width:0!important;">
                <strong class="portal-client-store" style="display:block!important;font-size:1.05rem!important;font-weight:900!important;line-height:1.05!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;">${escapeHtml(storeName)}</strong>
                ${branchLine}
            </span>
        `;
        pill.title = branchName ? `${storeName} - ${branchName}` : storeName;
        renderGreeting();
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
            }
        };

        if (portalApp) {
            new MutationObserver(syncProfile).observe(portalApp, { attributes: true, attributeFilter: ['hidden'] });
        }

        const rawBadge = profile.querySelector('[data-cliente-badge]');
        if (rawBadge) {
            new MutationObserver(() => {
                setTimeout(renderProfileBadge, 0);
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
