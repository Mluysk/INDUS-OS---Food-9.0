<?php
include __DIR__.'/index_whatsapp_beside_contact_type_patch.php';
?>
<style>
    html,
    body {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    html body #portal-catalog.portal-catalog[data-view="grid"] {
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    }

    @media (max-width: 1500px) {
        html body #portal-catalog.portal-catalog[data-view="grid"] {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 1240px) {
        html body #portal-catalog.portal-catalog[data-view="grid"] {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 620px) {
        html body #portal-catalog.portal-catalog[data-view="grid"] {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 820px) {
        html body {
            min-width: 0 !important;
            -webkit-text-size-adjust: 100%;
        }

        html body .portal-theme-button {
            transform: translateY(0) !important;
        }

        html body .portal-theme-button:hover {
            transform: translateY(-1px) !important;
        }

        html body .topbar {
            gap: 0.75rem !important;
            padding: 0.85rem 0.95rem !important;
            flex-wrap: wrap !important;
        }

        html body .topbar > div {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        html body .topbar .portal-top-actions {
            width: auto !important;
            margin-left: auto !important;
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-end !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }

        html body .topbar .portal-app__profile.is-visible {
            width: auto !important;
            display: inline-flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 0.45rem !important;
        }

        html body:not([data-portal-state="app"]) .topbar .portal-app__profile,
        html body:not([data-portal-state="app"]) .topbar .portal-client-pill-main,
        html body:not([data-portal-state="app"]) .topbar [data-portal-settings],
        html body:not([data-portal-state="app"]) .topbar #portal-logout {
            display: none !important;
        }

        html body:not([data-portal-state="app"]) {
            --primary: var(--portal-system-primary, #2563eb) !important;
        }

        html body:not([data-portal-state="app"]) .portal-floating-support {
            display: none !important;
        }

        html body .topbar .portal-client-pill-main {
            display: inline-flex !important;
            max-width: min(calc(100vw - 8.5rem), 260px) !important;
            margin: 0 !important;
        }

        html body .topbar #portal-logout {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        html body .portal-app__header-links [data-portal-settings] {
            display: none !important;
        }

        html body .topbar [data-portal-settings].portal-mobile-settings-button {
            width: 46px !important;
            height: 46px !important;
            min-width: 46px !important;
            min-height: 46px !important;
            display: inline-grid !important;
            place-items: center !important;
            padding: 0 !important;
            border-radius: 999px !important;
            border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 22%, transparent) !important;
            background: color-mix(in srgb, var(--primary, #2563eb) 12%, #ffffff) !important;
            color: var(--primary, #2563eb) !important;
            box-shadow: 0 14px 26px -24px color-mix(in srgb, var(--primary, #2563eb) 80%, #0f172a) !important;
        }

        html body .topbar [data-portal-settings].portal-mobile-settings-button .portal-app__link-icon {
            width: 36px !important;
            height: 36px !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-grid !important;
            place-items: center !important;
        }

        html body .topbar [data-portal-settings].portal-mobile-settings-button .portal-app__link-icon svg {
            width: 34px !important;
            height: 34px !important;
            color: var(--primary, #2563eb) !important;
            fill: none !important;
            stroke: currentColor !important;
            stroke-width: 1.95 !important;
            stroke-linecap: round !important;
            stroke-linejoin: round !important;
            display: block !important;
        }

        html[data-theme="dark"] body .topbar [data-portal-settings].portal-mobile-settings-button {
            background: color-mix(in srgb, var(--primary, #3b82f6) 20%, rgba(15, 23, 42, 0.9)) !important;
            border-color: color-mix(in srgb, var(--primary, #3b82f6) 38%, transparent) !important;
            color: color-mix(in srgb, var(--primary, #3b82f6) 80%, #ffffff) !important;
        }

        html[data-theme="dark"] body .topbar [data-portal-settings].portal-mobile-settings-button .portal-app__link-icon svg {
            color: color-mix(in srgb, var(--primary, #3b82f6) 80%, #ffffff) !important;
        }

        html body .topbar [data-portal-settings].portal-mobile-settings-button .portal-app__link-text {
            display: none !important;
        }

        html body .portal-hero {
            padding: 1rem 0.8rem 2rem !important;
        }

        html body .portal-hero__content {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
            width: 100% !important;
            max-width: 560px !important;
        }

        html body .portal-brand-card,
        html body .portal-auth-card {
            width: 100% !important;
            border-radius: 20px !important;
            padding: 1.25rem !important;
        }

        html body .portal-brand-card__logo {
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            margin-left: 0 !important;
            margin-bottom: 0.9rem !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }

        html body .portal-logo {
            display: block !important;
            width: auto !important;
            max-width: 100% !important;
            transform: translate(0, var(--portal-logo-y, 0px)) !important;
            object-fit: contain !important;
        }

        html body .portal-logo-filtered {
            width: min(100%, calc(var(--portal-logo-size, 60px) * 3.9)) !important;
            max-width: 100% !important;
            transform: translate(0, var(--portal-logo-y, 0px)) !important;
        }

        html body .portal-brand-card h1 {
            font-size: clamp(1.8rem, 9vw, 2.45rem) !important;
            line-height: 1.05 !important;
        }

        html body .portal-brand-card p,
        html body .auth-panel__subtitle,
        html body .portal-auth-cta {
            font-size: 0.93rem !important;
        }

        html body .auth-panel__header h2 {
            font-size: clamp(1.6rem, 7.6vw, 2rem) !important;
        }

        html body .auth-panel__subtitle {
            max-width: none !important;
            line-height: 1.38 !important;
        }

        html body .portal-auth-cta {
            align-items: stretch !important;
            flex-direction: column !important;
        }

        html body .auth-panel input,
        html body .portal-btn,
        html body input,
        html body select,
        html body textarea {
            min-height: 46px;
            font-size: 16px !important;
        }

        html body .portal-app {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 0.75rem 5rem !important;
        }

        html body .portal-app__header {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.85rem !important;
            align-items: stretch !important;
            padding: 1.2rem 0 0.8rem !important;
        }

        html body .portal-app__welcome h2[data-cliente-nome] {
            display: grid !important;
            grid-template-columns: 1fr !important;
            justify-items: center !important;
            text-align: center !important;
            font-size: clamp(1.55rem, 8vw, 2.2rem) !important;
            line-height: 1.08 !important;
            gap: 0.48rem !important;
            overflow-wrap: anywhere;
        }

        html body .portal-app__welcome h2[data-cliente-nome] .portal-greeting-head {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.56rem !important;
            width: 100% !important;
        }

        html body .portal-app__welcome h2[data-cliente-nome] .portal-greeting-store-name {
            display: block !important;
            max-width: 100% !important;
            white-space: normal !important;
            text-align: center !important;
        }

        html body .portal-app__welcome h2[data-cliente-nome] .portal-greeting-pill {
            justify-self: center !important;
            max-width: min(100%, 320px) !important;
        }

        html body .portal-app__subtitle {
            max-width: none !important;
            font-size: 0.92rem !important;
            line-height: 1.42 !important;
        }

        html body .portal-app__header-links,
        html body .portal-app__profile {
            width: 100% !important;
            min-width: 0 !important;
        }

        html body .portal-app__header-links {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            border-radius: 18px !important;
            padding: 0.35rem !important;
        }

        html body .portal-app__link {
            justify-content: center !important;
            padding: 0.58rem 0.5rem !important;
            min-width: 0 !important;
        }

        html body .portal-app__profile {
            display: inline-flex !important;
            gap: 0.55rem !important;
            justify-content: end !important;
            align-items: center !important;
        }

        html body .portal-app__client {
            width: auto !important;
            min-width: 0 !important;
            max-width: min(100%, 340px) !important;
        }

        html body .portal-app__nav {
            display: none !important;
        }

        html body #portal-settings-modal .portal-settings-modal__dialog {
            width: min(100%, calc(100vw - 18px)) !important;
            max-width: calc(100vw - 18px) !important;
            padding: 1rem !important;
        }

        html body #portal-tab-configuracao,
        html body #portal-tab-configuracao .portal-settings-tab-card,
        html body #portal-tab-configuracao .portal-settings-form,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow: visible !important;
        }

        html body #portal-tab-configuracao .portal-settings-tab-card {
            padding: 1rem !important;
            border-radius: 18px !important;
        }

        html body #portal-tab-configuracao .portal-settings-tab-card__head,
        html body #portal-tab-configuracao .portal-settings-subtabs {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        html body #portal-tab-configuracao .portal-settings-tab-card__head {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.55rem !important;
        }

        html body #portal-tab-configuracao .portal-settings-tab-card__head h3,
        html body #portal-tab-configuracao .portal-settings-tab-card__head p {
            max-width: 100% !important;
            overflow-wrap: anywhere !important;
        }

        html body #portal-tab-configuracao .portal-settings-subtabs {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.45rem !important;
        }

        html body #portal-tab-configuracao .portal-settings-subtab {
            width: 100% !important;
            min-width: 0 !important;
            justify-content: center !important;
            padding-inline: 0.55rem !important;
            white-space: normal !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"],
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-card,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-content,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-form,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-grid,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-document-card,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-document-card .portal-settings-grid,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-info-row,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-address-row,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-city-state-row,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-row,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-line,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-top-grid,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-name-email-row,
        html body #portal-settings-modal [data-settings-section="cadastro"],
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-card,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-content,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-form,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-grid,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-document-card,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-document-card .portal-settings-grid,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-info-row,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-address-row,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-city-state-row,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-contact-row,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-contact-line,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-top-grid,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-name-email-row {
            display: grid !important;
            grid-template-columns: 1fr !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            gap: 0.78rem !important;
            overflow: visible !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-field,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-doc-toggle,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-whatsapp-row,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] [data-settings-doc-field],
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-field,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-contact-type,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-doc-toggle,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-whatsapp-row,
        html body #portal-settings-modal [data-settings-section="cadastro"] [data-settings-doc-field] {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            margin: 0 !important;
            transform: none !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] input,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] select,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] textarea,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-field input,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] #portal-full-cep,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] #portal-full-cidade,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] #portal-full-estado,
        html body #portal-settings-modal [data-settings-section="cadastro"] input,
        html body #portal-settings-modal [data-settings-section="cadastro"] select,
        html body #portal-settings-modal [data-settings-section="cadastro"] textarea,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-field input,
        html body #portal-settings-modal [data-settings-section="cadastro"] #portal-full-cep,
        html body #portal-settings-modal [data-settings-section="cadastro"] #portal-full-cidade,
        html body #portal-settings-modal [data-settings-section="cadastro"] #portal-full-estado {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] input,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] select,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] textarea {
            min-height: 46px !important;
            padding: 0.72rem 0.82rem !important;
            font-size: 16px !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-doc-toggle,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-doc-toggle,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-contact-type {
            align-items: stretch !important;
            padding: 0.72rem !important;
            border-radius: 14px !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-doc-toggle {
            display: grid !important;
            grid-template-columns: 1fr !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.48rem !important;
            padding: 0.6rem !important;
            align-items: center !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-doc-toggle__buttons,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-doc-toggle__buttons,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-contact-type label {
            width: 100% !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type > strong {
            grid-column: 1 / -1 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.42rem !important;
            min-height: 0 !important;
            margin: 0 0 0.1rem !important;
            color: var(--text-muted, #64748b) !important;
            font-size: 0.82rem !important;
            line-height: 1.15 !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type label {
            width: 100% !important;
            min-height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.42rem !important;
            margin: 0 !important;
            padding: 0.42rem 0.58rem !important;
            border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 14%, rgba(148, 163, 184, 0.28)) !important;
            border-radius: 999px !important;
            background: rgba(255, 255, 255, 0.86) !important;
            color: var(--text, #111827) !important;
            font-size: 0.86rem !important;
            font-weight: 850 !important;
            line-height: 1 !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type label:has(input[type="radio"]:checked) {
            border-color: var(--primary, #dc2626) !important;
            background: color-mix(in srgb, var(--primary, #dc2626) 12%, #ffffff) !important;
            color: var(--primary, #dc2626) !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-contact-type input[type="radio"] {
            width: 15px !important;
            height: 15px !important;
            min-width: 15px !important;
            min-height: 15px !important;
            margin: 0 !important;
            padding: 0 !important;
            flex: 0 0 15px !important;
            accent-color: var(--primary, #dc2626) !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-doc-toggle__buttons {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.45rem !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-doc-btn {
            width: 100% !important;
            min-width: 0 !important;
            justify-content: center !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-branch-row {
            grid-template-columns: 1fr !important;
            padding: 0.75rem !important;
            gap: 0.65rem !important;
        }

        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-branch-switch {
            width: 100% !important;
            justify-content: space-between !important;
        }

        html body #portal-tab-configuracao .portal-settings-actions,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-actions,
        html body #portal-settings-modal [data-settings-section="cadastro"] .portal-settings-actions {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.65rem !important;
        }

        html body #portal-tab-configuracao .portal-settings-actions .portal-btn,
        html body #portal-tab-configuracao [data-settings-section="cadastro"] .portal-settings-actions .portal-btn {
            width: 100% !important;
            min-width: 0 !important;
        }

        html body .portal-app__panels {
            gap: 1rem !important;
        }

        html body .portal-catalog-card,
        html body .portal-cart-card,
        html body .portal-orders-card,
        html body .portal-products-card,
        html body .portal-support-card {
            width: 100% !important;
            max-width: 100% !important;
            border-radius: 20px !important;
            padding: 1rem !important;
        }

        html body .portal-catalog-card__head,
        html body .portal-orders-card__head {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.85rem !important;
            margin-bottom: 1rem !important;
        }

        html body .portal-support-card__head {
            grid-template-columns: 1fr !important;
            gap: 0.72rem !important;
        }

        html body .portal-support-card__icon {
            width: 48px !important;
            height: 48px !important;
            border-radius: 15px !important;
        }

        html body .portal-support-card__icon svg {
            width: 26px !important;
            height: 26px !important;
        }

        html body .portal-support-card__head h3 {
            font-size: 1.08rem !important;
            margin-bottom: 0.35rem !important;
        }

        html body .portal-support-card__head p {
            font-size: 0.9rem !important;
        }

        html body .portal-support-card__list {
            margin: 0.95rem 0 0.85rem !important;
            gap: 0.58rem !important;
        }

        html body .portal-support-card__list li {
            min-height: 44px !important;
            padding: 0.64rem 0.68rem 0.64rem 2.85rem !important;
            border-radius: 14px !important;
            font-size: 0.87rem !important;
        }

        html body .portal-support-card__list li::before,
        html body .portal-support-card__list li::after {
            left: 0.72rem !important;
            top: 0.7rem !important;
            width: 28px !important;
            height: 28px !important;
        }

        html body .portal-support-card__actions {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.55rem !important;
            margin-bottom: 0.85rem !important;
        }

        html body .portal-support-card__action {
            width: 100% !important;
            min-height: 42px !important;
        }

        html body .portal-support-card__foot {
            margin-top: 0.85rem !important;
            padding: 0.78rem 0.86rem !important;
            border-radius: 14px !important;
            font-size: 0.88rem !important;
        }

        html body .portal-catalog-card__controls,
        html body .portal-view-toggle {
            width: 100% !important;
        }

        html body .portal-view-toggle {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        html body .portal-view-toggle__btn {
            min-height: 38px !important;
            padding: 0.45rem 0.6rem !important;
        }

        html body .portal-catalog-tools {
            grid-template-columns: 1fr !important;
            gap: 0.65rem !important;
            margin: 0.75rem 0 !important;
        }

        html body .portal-catalog-tools input,
        html body .portal-catalog-tools select {
            min-height: 46px !important;
            border-radius: 15px !important;
        }

        html body .portal-catalog {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.62rem !important;
        }

        html body .portal-product {
            width: 100% !important;
            padding: 0.72rem !important;
            border-radius: 15px !important;
            gap: 0.48rem !important;
            font-size: 0.78rem !important;
        }

        html body .portal-product h3 {
            font-size: 0.86rem !important;
            line-height: 1.16 !important;
            overflow-wrap: anywhere;
        }

        html body .portal-product small,
        html body .portal-product__meta,
        html body .portal-product__label {
            font-size: 0.68rem !important;
            line-height: 1.16 !important;
        }

        html body .portal-product__input-group {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 42px !important;
            gap: 0.38rem !important;
            width: 100% !important;
        }

        html body .portal-product input[data-qtd] {
            min-width: 0 !important;
            min-height: 42px !important;
            font-size: 16px !important;
            padding: 0.48rem 0.5rem !important;
        }

        html body .portal-product button[data-add] {
            width: 42px !important;
            height: 42px !important;
            min-width: 42px !important;
            min-height: 42px !important;
        }

        html body .portal-product__price {
            font-size: 0.88rem !important;
        }

        html body .portal-product .badge {
            padding: 0.16rem 0.42rem !important;
            font-size: 0.6rem !important;
        }

        html body .portal-product__weight-pill {
            padding: 0.22rem 0.48rem !important;
            font-size: 0.62rem !important;
            max-width: 100% !important;
            white-space: nowrap !important;
        }

        @media (max-width: 360px) {
            html body .portal-catalog {
                grid-template-columns: 1fr !important;
            }
        }

        html body .portal-orders-table-wrapper {
            overflow: visible !important;
        }

        html body .portal-orders-table,
        html body .portal-orders-table thead,
        html body .portal-orders-table tbody,
        html body .portal-orders-table tr,
        html body .portal-orders-table th,
        html body .portal-orders-table td {
            display: block !important;
            width: 100% !important;
            min-width: 0 !important;
        }

        html body .portal-orders-table thead {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
        }

        html body .portal-orders-table tbody {
            display: grid !important;
            gap: 0.75rem !important;
        }

        html body .portal-orders-table tbody tr {
            border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
            border-radius: 16px;
            padding: 0.75rem;
            background: color-mix(in srgb, var(--portal-surface, #fff) 88%, transparent);
        }

        html body .portal-orders-table td {
            padding: 0.34rem 0 !important;
            border-bottom: 0 !important;
            overflow-wrap: anywhere;
        }

        html body .portal-orders-table td::before {
            display: block;
            margin-bottom: 0.12rem;
            color: var(--portal-muted);
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        html body .portal-orders-table td:nth-child(1)::before { content: "Pedido"; }
        html body .portal-orders-table td:nth-child(2)::before { content: "Data"; }
        html body .portal-orders-table td:nth-child(3)::before { content: "Total"; }
        html body .portal-orders-table td:nth-child(4)::before { content: "Status"; }
        html body .portal-orders-table td:nth-child(5)::before { content: "Ações"; }

        html body .portal-orders-actions {
            justify-content: stretch !important;
        }

        html body .portal-orders-action {
            flex: 1 1 100% !important;
            justify-content: center !important;
            min-height: 40px !important;
        }

        html body .portal-products-list {
            grid-template-columns: 1fr !important;
        }

        html body .portal-cart-stack {
            padding: max(12px, env(safe-area-inset-top)) 12px max(12px, env(safe-area-inset-bottom)) !important;
        }

        html body .portal-cart-card {
            max-height: min(74vh, 680px) !important;
            overflow-y: auto !important;
        }

        html body .portal-cart-table {
            min-width: 0 !important;
        }

        html body .portal-cart-table th,
        html body .portal-cart-table td {
            font-size: 0.78rem !important;
        }

        html body .portal-cart-float {
            right: max(14px, env(safe-area-inset-right)) !important;
            bottom: max(14px, env(safe-area-inset-bottom)) !important;
        }

        html body .portal-floating-support svg {
            width: 36px !important;
            height: 36px !important;
            display: block !important;
        }

        html body[data-portal-state="app"] .portal-floating-support {
            right: max(12px, env(safe-area-inset-right)) !important;
            bottom: 89px !important;
            width: 56px !important;
            height: 56px !important;
            min-width: 56px !important;
            min-height: 56px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        html body .portal-modal {
            padding: max(12px, env(safe-area-inset-top)) 12px max(12px, env(safe-area-inset-bottom)) !important;
        }

        html body .portal-modal__dialog {
            max-width: 100% !important;
            max-height: calc(100dvh - 24px) !important;
            border-radius: 22px !important;
            padding: 1.2rem !important;
            overflow-y: auto !important;
        }

        html body .portal-modal__dialog--welcome {
            width: min(100%, 348px) !important;
            padding: 1.05rem !important;
            border-radius: 20px !important;
        }

        html body .portal-welcome {
            gap: 0.82rem !important;
        }

        html body .portal-welcome__icon {
            width: 58px !important;
            height: 58px !important;
            border-radius: 18px !important;
        }

        html body .portal-welcome__icon svg {
            width: 28px !important;
            height: 28px !important;
        }

        html body .portal-welcome__tag {
            width: 100% !important;
            justify-content: center !important;
            padding: 0.34rem 0.7rem !important;
        }

        html body .portal-welcome h3 {
            margin: 0.08rem 0 0 !important;
            font-size: 1.22rem !important;
        }

        html body .portal-welcome__lead {
            font-size: 0.9rem !important;
            line-height: 1.45 !important;
        }

        html body .portal-welcome__tips {
            gap: 0.5rem !important;
        }

        html body .portal-welcome__tips li {
            display: block !important;
            padding: 0.66rem 0.74rem 0.66rem 2rem !important;
            border-radius: 14px !important;
            font-size: 0.86rem !important;
            line-height: 1.28 !important;
        }

        html body .portal-welcome__tips li::before {
            left: 0.74rem !important;
            top: 0.82rem !important;
            width: 7px !important;
            height: 7px !important;
        }

        html body .portal-welcome__tips strong {
            display: inline !important;
            white-space: normal !important;
        }

        html body .portal-modal__footer,
        html body .portal-welcome__footer {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.65rem !important;
            margin-top: 0.12rem !important;
        }

        html body .portal-welcome__footer .portal-btn {
            min-height: 42px !important;
            border-radius: 13px !important;
        }

        html body footer {
            padding: 1.6rem 0.9rem 2.2rem !important;
            font-size: 0.8rem !important;
        }
    }

    @media (min-width: 821px) {
        html body .portal-app,
        html body .portal-hero__content {
            min-width: 0;
        }
    }
</style>
<style>
    html body .portal-product .badge {
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 28%, transparent) !important;
        background: color-mix(in srgb, var(--primary, #2563eb) 14%, #ffffff) !important;
        color: color-mix(in srgb, var(--primary, #2563eb) 86%, #0f172a) !important;
        font-weight: 850 !important;
        box-shadow: 0 12px 24px -20px color-mix(in srgb, var(--primary, #2563eb) 55%, transparent) !important;
    }

    html body .portal-app__header-links .portal-app__link.is-active,
    html body .portal-app__header-links .portal-app__link[aria-current="page"] {
        background: var(--primary, #2563eb) !important;
        color: #ffffff !important;
        box-shadow: 0 16px 30px -20px color-mix(in srgb, var(--primary, #2563eb) 72%, transparent) !important;
    }

    html body .portal-app__header-links .portal-app__link.is-active .portal-app__link-icon,
    html body .portal-app__header-links .portal-app__link[aria-current="page"] .portal-app__link-icon,
    html body .portal-app__header-links .portal-app__link.is-active .portal-app__link-icon svg,
    html body .portal-app__header-links .portal-app__link[aria-current="page"] .portal-app__link-icon svg {
        color: #ffffff !important;
    }

    html[data-theme="dark"] body .portal-product .badge {
        background: color-mix(in srgb, var(--primary, #3b82f6) 20%, rgba(15, 23, 42, 0.88)) !important;
        border-color: color-mix(in srgb, var(--primary, #3b82f6) 42%, transparent) !important;
        color: color-mix(in srgb, var(--primary, #3b82f6) 74%, #ffffff) !important;
    }
</style>
<script>
(function () {
    const media = window.matchMedia('(max-width: 820px)');
    const moveSettingsButton = () => {
        const button = document.querySelector('[data-portal-settings]');
        const menu = document.querySelector('.portal-app__header-links');
        const profile = document.querySelector('.topbar .portal-app__profile');
        const pill = profile?.querySelector('.portal-client-pill-main');
        if (!button || !menu || !profile) return;

        if (media.matches) {
            button.classList.add('portal-mobile-settings-button');
            const logout = profile.querySelector('#portal-logout');
            if (logout && button.nextElementSibling !== logout) {
                profile.insertBefore(button, logout);
            } else if (pill && button.parentElement !== profile) {
                pill.insertAdjacentElement('afterend', button);
            } else if (!pill && button.parentElement !== profile) {
                profile.prepend(button);
            }
            return;
        }

        button.classList.remove('portal-mobile-settings-button');
        if (button.parentElement !== menu) {
            menu.appendChild(button);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', moveSettingsButton, { once: true });
    } else {
        moveSettingsButton();
    }
    window.addEventListener('resize', moveSettingsButton);
    window.addEventListener('storage', (event) => {
        if (event.key === 'portal-cliente') {
            setTimeout(moveSettingsButton, 0);
        }
    });
    setTimeout(moveSettingsButton, 80);
    setTimeout(moveSettingsButton, 300);
})();
</script>
<script>
(function () {
    const ensureDashboardLoads = () => {
        if (document.body.getAttribute('data-portal-state') !== 'app') return;
        const dashboard = document.querySelector('[data-portal-dashboard]');
        if (!dashboard || !dashboard.querySelector('.portal-dashboard__loading')) return;
        if (typeof carregarDashboard !== 'function') return;

        dashboardCarregado = false;
        carregarDashboard(true);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureDashboardLoads, { once: true });
    } else {
        ensureDashboardLoads();
    }

    window.setTimeout(ensureDashboardLoads, 250);
    window.setTimeout(ensureDashboardLoads, 1500);
    window.setTimeout(ensureDashboardLoads, 8500);
    window.addEventListener('focus', ensureDashboardLoads);
})();
</script>
<style>
    @media (min-width: 821px) {
        html body .portal-app__header {
            grid-template-rows: auto auto !important;
            row-gap: 1.5rem !important;
            padding-top: 0 !important;
        }

        html body .portal-app__header-links {
            position: relative !important;
            left: 50% !important;
            grid-column: 1 / -1 !important;
            grid-row: 1 !important;
            width: var(--portal-desktop-viewport-width, 100vw) !important;
            max-width: none !important;
            box-sizing: border-box !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-wrap: wrap !important;
            margin: 0 !important;
            border-radius: 0 !important;
            transform: translate(-50%, -1.5rem) !important;
        }

        html body .portal-app__welcome,
        html body .portal-app__profile {
            grid-row: 2 !important;
        }

        html body .portal-app__header-links .portal-app__link {
            width: auto !important;
            min-width: auto !important;
            flex: 0 0 auto !important;
            justify-content: center !important;
        }

        html body .portal-app__header-links .portal-app__link[data-portal-settings] {
            width: 46px !important;
            min-width: 46px !important;
            max-width: 46px !important;
            height: 46px !important;
            min-height: 46px !important;
            max-height: 46px !important;
            aspect-ratio: 1 / 1 !important;
            flex: 0 0 46px !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            border-radius: 50% !important;
            place-items: center !important;
        }

        html body .portal-app__header-links .portal-app__link[data-portal-settings] .portal-app__link-icon,
        html body .portal-app__header-links .portal-app__link[data-portal-settings] .portal-app__link-icon svg {
            width: 25.2px !important;
            height: 25.2px !important;
            flex-basis: 25.2px !important;
        }
    }
</style>
<style>
    html body [data-portal-tab-panel][hidden] {
        display: none !important;
    }

    html body #portal-tab-configuracao [data-settings-section][hidden],
    html body #portal-settings-modal [data-settings-section][hidden] {
        display: none !important;
        visibility: hidden !important;
    }

    html body .portal-app__tag {
        box-sizing: border-box !important;
        min-height: 2.57rem !important;
        padding: 0.64rem 0.8rem !important;
    }

    html body .portal-brand-card__tag {
        min-height: 2.45rem !important;
        padding: 0.476rem 1.156rem !important;
        font-size: 1.341rem !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
    }

    @media (min-width: 981px) {
        html body .portal-app__tag {
            width: 30% !important;
            max-width: 30% !important;
        }
    }

    @media (max-width: 980px) {
        html body .portal-app__tag {
            width: 100% !important;
            max-width: 100% !important;
        }
    }

    html body [data-portal-settings].is-active,
    html body [data-portal-settings][aria-current="page"] {
        background: var(--primary, #2563eb) !important;
        border-color: var(--primary, #2563eb) !important;
        color: #ffffff !important;
        box-shadow: 0 16px 30px -20px color-mix(in srgb, var(--primary, #2563eb) 72%, transparent) !important;
    }

    html body [data-portal-settings].is-active .portal-app__link-icon,
    html body [data-portal-settings].is-active .portal-app__link-icon svg,
    html body [data-portal-settings][aria-current="page"] .portal-app__link-icon,
    html body [data-portal-settings][aria-current="page"] .portal-app__link-icon svg {
        color: #ffffff !important;
    }
</style>
<script>
(function () {
    let currentPortalSection = document.querySelector('[data-portal-tab-trigger].is-active')?.dataset.portalTabTrigger || 'novo-pedido';

    const syncPortalSection = (target) => {
        const available = Array.from(document.querySelectorAll('[data-portal-tab-panel]'));
        const requested = String(target || 'novo-pedido');
        const next = available.some((panel) => panel.dataset.portalTabPanel === requested)
            ? requested
            : 'novo-pedido';

        currentPortalSection = next;
        available.forEach((panel) => {
            const active = panel.dataset.portalTabPanel === next;
            panel.hidden = !active;
            panel.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        document.querySelectorAll('[data-portal-tab-trigger]').forEach((button) => {
            const active = button.dataset.portalTabTrigger === next;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('.portal-app__header-links [data-portal-jump]').forEach((button) => {
            const active = button.dataset.portalJump === next;
            button.classList.toggle('is-active', active);
            if (active) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });

        document.querySelectorAll('[data-portal-settings]').forEach((button) => {
            const active = next === 'configuracao';
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
            if (active) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });
    };

    document.addEventListener('click', (event) => {
        const control = event.target.closest('[data-portal-settings], [data-portal-tab-trigger], [data-portal-jump]');
        if (!control) return;

        const target = control.hasAttribute('data-portal-settings')
            ? 'configuracao'
            : (control.dataset.portalTabTrigger || control.dataset.portalJump);
        if (!target) return;

        window.setTimeout(() => syncPortalSection(target), 0);
    });

    const panelsRoot = document.querySelector('.portal-app__panels');
    if (panelsRoot) {
        new MutationObserver(() => syncPortalSection(currentPortalSection))
            .observe(panelsRoot, { childList: true });
    }

    window.setTimeout(() => syncPortalSection(currentPortalSection), 420);
})();
</script>
<script>
(function () {
    const syncHeaderMenuState = () => {
        const configurationPanel = document.querySelector('[data-portal-tab-panel="configuracao"]');
        const configurationOpen = Boolean(configurationPanel && !configurationPanel.hidden);
        const activeTab = configurationOpen
            ? 'configuracao'
            : (document.querySelector('[data-portal-tab-trigger].is-active')?.dataset.portalTabTrigger || 'novo-pedido');
        document.querySelectorAll('.portal-app__header-links [data-portal-jump]').forEach((button) => {
            const isActive = button.dataset.portalJump === activeTab;
            button.classList.toggle('is-active', isActive);
            if (isActive) {
                button.setAttribute('aria-current', 'page');
            } else {
                button.removeAttribute('aria-current');
            }
        });
        const settingsButton = document.querySelector('[data-portal-settings]');
        const settingsModal = document.getElementById('portal-settings-modal');
        const settingsOpen = configurationOpen || Boolean(settingsModal && !settingsModal.hidden && settingsModal.getAttribute('aria-hidden') !== 'true');
        if (settingsButton) {
            settingsButton.classList.toggle('is-active', settingsOpen);
            settingsButton.setAttribute('aria-pressed', settingsOpen ? 'true' : 'false');
            if (settingsOpen) {
                settingsButton.setAttribute('aria-current', 'page');
            } else if (!settingsButton.dataset.portalJump) {
                settingsButton.removeAttribute('aria-current');
            }
        }
    };

    const bindHeaderMenuState = () => {
        document.querySelectorAll('[data-portal-tab-trigger], .portal-app__header-links [data-portal-jump], [data-portal-settings], [data-portal-settings-close]').forEach((button) => {
            button.addEventListener('click', () => setTimeout(syncHeaderMenuState, 0));
        });
        const settingsModal = document.getElementById('portal-settings-modal');
        if (settingsModal) {
            new MutationObserver(syncHeaderMenuState).observe(settingsModal, { attributes: true, attributeFilter: ['hidden', 'aria-hidden'] });
        }
        syncHeaderMenuState();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindHeaderMenuState, { once: true });
    } else {
        bindHeaderMenuState();
    }
    window.addEventListener('storage', (event) => {
        if (event.key === 'portal-cliente') {
            setTimeout(syncHeaderMenuState, 0);
        }
    });
    setTimeout(syncHeaderMenuState, 120);
})();
</script>
