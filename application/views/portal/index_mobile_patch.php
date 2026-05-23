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

    @media (max-width: 820px) {
        html body {
            min-width: 0 !important;
            -webkit-text-size-adjust: 100%;
        }

        html body .topbar {
            gap: 0.75rem !important;
            padding: 0.85rem 0.95rem !important;
        }

        html body .topbar > div {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            margin-bottom: 0.9rem !important;
        }

        html body .portal-logo {
            max-height: 46px !important;
        }

        html body .portal-login-logo {
            width: min(230px, 82%) !important;
            height: 58px !important;
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
            font-size: clamp(1.55rem, 8vw, 2.2rem) !important;
            line-height: 1.08 !important;
            overflow-wrap: anywhere;
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

        html body .portal-modal__footer,
        html body .portal-welcome__footer {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0.65rem !important;
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
