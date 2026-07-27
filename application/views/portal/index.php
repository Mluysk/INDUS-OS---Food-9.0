<?php
$portalLoginLogoLightPath = $theme['portal_login_logo_light'] ?? ($theme['logo_light'] ?? 'img/logo.png');
$portalLoginLogoDarkPath = $theme['portal_login_logo_dark'] ?? ($theme['logo_dark'] ?? 'img/logodark.png');
$portalLogoLight = htmlspecialchars($asset($portalLoginLogoLightPath), ENT_QUOTES, 'UTF-8');
$portalLogoDark = htmlspecialchars($asset($portalLoginLogoDarkPath), ENT_QUOTES, 'UTF-8');
$portalLoginLogoFilter = ($theme['portal_login_logo_filter'] ?? '1') === '1';
$portalLoginLogoLightSize = max(24, min(180, (int)($theme['portal_login_logo_light_size'] ?? 69)));
$portalLoginLogoDarkSize = max(24, min(180, (int)($theme['portal_login_logo_dark_size'] ?? 69)));
$portalLoginLogoLightOffsetX = max(-534, min(534, (int)($theme['portal_login_logo_light_offset_x'] ?? 0)));
$portalLoginLogoLightOffsetY = max(-200, min(200, (int)($theme['portal_login_logo_light_offset_y'] ?? 0)));
$portalLoginLogoDarkOffsetX = max(-534, min(534, (int)($theme['portal_login_logo_dark_offset_x'] ?? 0)));
$portalLoginLogoDarkOffsetY = max(-200, min(200, (int)($theme['portal_login_logo_dark_offset_y'] ?? 0)));
$portalLogoFrameSize = $portalLoginLogoFilter ? $portalLoginLogoDarkSize : $portalLoginLogoLightSize;
$portalLogoFrameOffsetX = $portalLoginLogoFilter ? $portalLoginLogoDarkOffsetX : $portalLoginLogoLightOffsetX;
$portalLogoFrameOffsetY = $portalLoginLogoFilter ? $portalLoginLogoDarkOffsetY : $portalLoginLogoLightOffsetY;
$appDisplayName = htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8');
$appTaglineText = htmlspecialchars($appTagline ?? 'Sistema de Controle Industrial (SCI)', ENT_QUOTES, 'UTF-8');
$supportData = $support ?? [];
$supportEmail = isset($supportData['email']) ? trim((string)$supportData['email']) : '';
$supportTelefone = isset($supportData['telefone']) ? trim((string)$supportData['telefone']) : '';
$supportWhatsapp = isset($supportData['whatsapp']) ? trim((string)$supportData['whatsapp']) : '';
$supportHorario = isset($supportData['horario']) ? trim((string)$supportData['horario']) : '';
$supportMensagem = isset($supportData['mensagem']) ? trim((string)$supportData['mensagem']) : '';
$phoneHref = '';
if ($supportTelefone !== '') {
    $phoneDigits = preg_replace('/\D+/', '', $supportTelefone);
    if ($phoneDigits !== '') {
        $phoneHref = $phoneDigits[0] === '+' ? $phoneDigits : '+'.$phoneDigits;
    }
}
$whatsHref = '';
if ($supportWhatsapp !== '') {
    $digits = preg_replace('/\D+/', '', $supportWhatsapp);
    if ($digits !== '') {
        $whatsHref = 'https://wa.me/'.$digits;
    }
}
?>
<section class="portal-hero" data-portal-hero>
    <div class="portal-hero__content">
        <div class="portal-brand-card">
            <div class="portal-brand-card__logo <?= $portalLoginLogoFilter ? 'portal-brand-card__logo--filtered' : 'portal-brand-card__logo--plain'; ?>" style="--portal-logo-size: <?= $portalLogoFrameSize; ?>px; --portal-logo-x: <?= $portalLogoFrameOffsetX; ?>px; --portal-logo-y: <?= $portalLogoFrameOffsetY; ?>px;" aria-hidden="true">
                <?php if ($portalLoginLogoFilter): ?>
                    <span class="portal-logo-filtered" style="--portal-login-logo-url: url('<?= $portalLogoDark; ?>'); --portal-logo-size: <?= $portalLoginLogoDarkSize; ?>px; --portal-logo-x: <?= $portalLoginLogoDarkOffsetX; ?>px; --portal-logo-y: <?= $portalLoginLogoDarkOffsetY; ?>px;"></span>
                <?php else: ?>
                    <img class="portal-logo portal-logo-light" style="--portal-logo-size: <?= $portalLoginLogoLightSize; ?>px; --portal-logo-x: <?= $portalLoginLogoLightOffsetX; ?>px; --portal-logo-y: <?= $portalLoginLogoLightOffsetY; ?>px;" src="<?= $portalLogoLight; ?>" alt="Logotipo modo claro">
                    <img class="portal-logo portal-logo-dark" style="--portal-logo-size: <?= $portalLoginLogoDarkSize; ?>px; --portal-logo-x: <?= $portalLoginLogoDarkOffsetX; ?>px; --portal-logo-y: <?= $portalLoginLogoDarkOffsetY; ?>px;" src="<?= $portalLogoDark; ?>" alt="Logotipo modo escuro">
                <?php endif; ?>
            </div>
            <span class="portal-brand-card__tag">Portal do cliente</span>
            <h1>Bem-vindo ao <?= $appDisplayName; ?></h1>
            <p><?= $appTaglineText; ?>. Consulte disponibilidade em tempo real, validade e preços negociados.</p>
        </div>

        <?php if (!empty($flash['success']) || !empty($flash['error'])): ?>
            <div class="portal-flash <?= !empty($flash['success']) ? 'portal-flash--success' : 'portal-flash--error'; ?>">
                <?= htmlspecialchars($flash['success'] ?? $flash['error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="portal-auth-card" data-auth-card data-mode="login">
            <form id="portal-login-form" class="auth-panel auth-panel--login" autocomplete="on">
                <div class="auth-panel__header">
                    <h2 class="portal-auth-title">
                        <span class="portal-auth-title__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="8.5" cy="7" r="3.6"></circle>
                                <path d="M2.9 19.2a5.8 5.8 0 0 1 9.2-4.7"></path>
                                <rect x="13.2" y="11.1" width="7.6" height="7.5" rx="1.8"></rect>
                                <path d="M15.3 11.1V9.7a2.7 2.7 0 0 1 5.4 0v1.4"></path>
                                <path d="M17 14.8h.01"></path>
                            </svg>
                        </span>
                        <span>Acesse sua conta</span>
                    </h2>
                    <p class="auth-panel__subtitle">Use seu e-mail, CPF ou CNPJ para acompanhar pedidos e gerar novas solicitações.</p>
                </div>
                <label for="login-email">E-mail, CPF ou CNPJ</label>
                <div class="portal-input-shell">
                    <span class="portal-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" id="login-email" name="login" placeholder="cliente@empresa.com" required>
                </div>

                <label for="login-password">Senha</label>
                <div class="portal-input-shell portal-input-shell--password">
                    <span class="portal-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="11" width="16" height="10" rx="2"></rect>
                            <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" id="login-password" name="senha" placeholder="••••••••" required>
                    <button type="button" class="portal-password-toggle" data-password-toggle aria-label="Mostrar senha" aria-pressed="false">
                        <span class="portal-password-toggle__icon portal-password-toggle__icon--show" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </span>
                        <span class="portal-password-toggle__icon portal-password-toggle__icon--hide" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-2.12 2.95"></path>
                                <path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3.5 7 10 7a10.63 10.63 0 0 0 5.39-1.61"></path>
                                <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"></path>
                                <path d="M3 3l18 18"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <button type="submit" class="portal-btn portal-btn--primary">Entrar</button>

                <div class="portal-auth-cta">
                    <span>Primeira vez por aqui?</span>
                    <a class="portal-btn portal-btn--ghost" href="<?= htmlspecialchars($url('portal/registro'), ENT_QUOTES, 'UTF-8'); ?>">Criar conta</a>
                </div>

                <div id="portal-login-feedback" class="portal-feedback" role="status" aria-live="polite"></div>
            </form>
        </div>
    </div>
</section>

<section class="portal-app" id="portal-app" hidden>
    <div class="portal-app__header">
        <div class="portal-app__welcome">
            <span class="portal-app__tag">Bem-vindo</span>
            <h2 data-cliente-nome>Cliente</h2>
            <p class="portal-app__subtitle">Monte seu pedido com disponibilidade FEFO e acompanhe a confirmação em tempo real.</p>
        </div>
        <div class="portal-app__header-links" role="group" aria-label="Atalhos do portal">
            <button type="button" class="portal-app__link" data-portal-jump="dashboard">
                <span class="portal-app__link-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><path d="M14 18h7M17.5 14.5v7"/></svg></span>
                <span class="portal-app__link-text">Dashboard</span>
            </button>
            <button type="button" class="portal-app__link" data-portal-jump="novo-pedido">
                <span class="portal-app__link-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v4.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 9.75v-4.5Zm0 9A2.25 2.25 0 0 1 6.75 12h4.5a2.25 2.25 0 0 1 2.25 2.25v4.5A2.25 2.25 0 0 1 11.25 21h-4.5A2.25 2.25 0 0 1 4.5 18.75v-4.5Zm9.75 0A2.25 2.25 0 0 1 16.5 12h.75A2.25 2.25 0 0 1 19.5 14.25v4.5A2.25 2.25 0 0 1 17.25 21H16.5a2.25 2.25 0 0 1-2.25-2.25v-4.5Z" />
                    </svg>
                </span>
                <span class="portal-app__link-text">Produtos</span>
            </button>
            <button type="button" class="portal-app__link" data-portal-jump="suporte">
                <span class="portal-app__link-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 1.5a9 9 0 0 1 9 9v1.5a1.5 1.5 0 0 1-1.5 1.5H18a.75.75 0 0 0-.75.75v2.25a.75.75 0 0 1-.75.75h-2.818l-3.2 2.772A.75.75 0 0 1 9 19.5V15h-.75A1.5 1.5 0 0 1 6.75 13.5V12a9 9 0 0 1 9-9Z" />
                    </svg>
                </span>
                <span class="portal-app__link-text">Suporte</span>
            </button>
        </div>
        <div class="portal-app__profile">
            <span class="portal-app__client" data-cliente-badge>Cliente</span>
            <button type="button" id="portal-logout" class="portal-btn portal-btn--ghost">Sair</button>
        </div>
    </div>

    <nav class="portal-app__nav" role="tablist" aria-label="Navegação do portal do cliente">
        <button type="button" class="portal-tab is-active" data-portal-tab-trigger="dashboard" aria-controls="portal-tab-dashboard" aria-selected="true">Dashboard</button>
        <button type="button" class="portal-tab" data-portal-tab-trigger="pedidos" aria-controls="portal-tab-pedidos" aria-selected="false">Pedidos</button>
        <button type="button" class="portal-tab" data-portal-tab-trigger="novo-pedido" aria-controls="portal-tab-novo" aria-selected="false">Novo pedido</button>
        <button type="button" class="portal-tab" data-portal-tab-trigger="suporte" aria-controls="portal-tab-suporte" aria-selected="false">Suporte</button>
    </nav>

    <div class="portal-app__panels">
        <section class="portal-panel" id="portal-tab-dashboard" data-portal-tab-panel="dashboard">
            <div class="portal-dashboard" data-portal-dashboard><div class="portal-dashboard__loading">Carregando seu desempenho...</div></div>
        </section>
        <section class="portal-panel" id="portal-tab-pedidos" data-portal-tab-panel="pedidos" hidden>
            <div class="portal-orders-card">
                <div class="portal-orders-card__head">
                    <div>
                        <h3 class="portal-orders-title"><span class="portal-orders-title__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/><path d="m15 16 2 2 4-4"/></svg></span>Pedidos recentes</h3>
                        <p>Acompanhe a produção, separação e expedição das suas solicitações.</p>
                    </div>
                    <div class="portal-orders-card__actions">
                        <button type="button" id="portal-orders-refresh" class="portal-btn portal-btn--ghost">Atualizar</button>
                    </div>
                </div>
                <div id="portal-orders-feedback" class="portal-feedback" role="status" aria-live="polite"></div>
                <div class="portal-orders-table-wrapper">
                    <table class="portal-orders-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Data</th>
                                <th class="portal-orders-table__actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="portal-orders-body">
                            <tr data-orders-empty>
                                <td colspan="5">Nenhum pedido enviado ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="portal-panel" id="portal-tab-editar-pedido" data-portal-tab-panel="editar-pedido" hidden>
            <div class="portal-order-editor" data-portal-order-editor>
                <div class="portal-order-editor__loading">Carregando pedido...</div>
            </div>
        </section>

        <section class="portal-panel" id="portal-tab-detalhe-pedido" data-portal-tab-panel="detalhe-pedido" hidden>
            <div class="portal-order-detail" data-portal-order-detail><div class="portal-order-detail__loading">Carregando pedido...</div></div>
        </section>

        <section class="portal-panel" id="portal-tab-novo" data-portal-tab-panel="novo-pedido" hidden>
            <div class="portal-app__grid">
                <div class="portal-catalog-card">
                    <div class="portal-catalog-card__head">
                        <div>
                            <h3 class="portal-catalog-title">
                                <span class="portal-catalog-title__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="8" cy="21" r="1"></circle>
                                        <circle cx="19" cy="21" r="1"></circle>
                                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h8.42a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                    </svg>
                                </span>
                                <span>Catálogo de produtos</span>
                            </h3>
                            <p>Preços negociados e validade garantida para sua operação.</p>
                        </div>
                        <div class="portal-catalog-card__controls">
                            <span class="portal-catalog-count" data-catalog-count aria-live="polite">0 produtos</span>
                            <div class="portal-view-toggle" role="group" aria-label="Visualização do catálogo">
                                <button type="button" class="portal-view-toggle__btn active" data-catalog-view="grid" aria-pressed="true">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1.5"></rect><rect x="14" y="4" width="6" height="6" rx="1.5"></rect><rect x="4" y="14" width="6" height="6" rx="1.5"></rect><rect x="14" y="14" width="6" height="6" rx="1.5"></rect></svg>
                                    <span>Grade</span>
                                </button>
                                <button type="button" class="portal-view-toggle__btn" data-catalog-view="list" aria-pressed="false">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h12"></path><path d="M8 12h12"></path><path d="M8 18h12"></path><circle cx="4" cy="6" r="1.2"></circle><circle cx="4" cy="12" r="1.2"></circle><circle cx="4" cy="18" r="1.2"></circle></svg>
                                    <span>Lista</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="portal-catalog" class="portal-catalog" data-view="grid"></div>
                </div>
                <div class="portal-cart-stack">
                    <aside class="portal-cart-card">
                        <h3>Carrinho</h3>
                        <table class="portal-cart-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd</th>
                                    <th>Preço</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="portal-cart-lines">
                                <tr><td colspan="4" class="portal-cart-empty">Nenhum item no carrinho.</td></tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td id="portal-cart-total">R$ 0,00</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="portal-order-feedback" class="portal-feedback" role="status" aria-live="polite"></div>
                    </aside>
                    <div class="portal-cart-actions">
                        <button type="button" id="btn-finalizar" class="portal-btn portal-btn--success">Finalizar pedido</button>
                    </div>
                </div>
            </div>
        </section>

        <div class="portal-image-lightbox" id="portal-image-lightbox" aria-hidden="true" role="dialog" aria-label="Imagem ampliada do produto">
            <div class="portal-image-lightbox__dialog">
                <button type="button" class="portal-image-lightbox__close" data-lightbox-close aria-label="Fechar imagem ampliada">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                <button type="button" class="portal-image-lightbox__nav portal-image-lightbox__nav--prev" data-lightbox-prev aria-label="Ver imagem anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                <img src="" alt="">
                <button type="button" class="portal-image-lightbox__nav portal-image-lightbox__nav--next" data-lightbox-next aria-label="Ver proxima imagem">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
                <span class="portal-image-lightbox__caption"></span>
            </div>
        </div>

        <section class="portal-panel" id="portal-tab-suporte" data-portal-tab-panel="suporte" hidden>
            <div class="portal-support-card">
                <div class="portal-support-card__head">
                    <span class="portal-support-card__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M4 12a8 8 0 0 1 16 0" />
                            <path d="M4 12v3.2a2 2 0 0 0 2 2h1.2a1 1 0 0 0 1-1V12a1 1 0 0 0-1-1H6a2 2 0 0 0-2 1Z" />
                            <path d="M20 12v3.2a2 2 0 0 1-2 2h-1.2a1 1 0 0 1-1-1V12a1 1 0 0 1 1-1H18a2 2 0 0 1 2 1Z" />
                            <path d="M18 17.2c0 2-1.7 3.3-4.1 3.3H12" />
                        </svg>
                    </span>
                    <div>
                        <h3>Suporte dedicado</h3>
                        <p>Precisa de ajuda? Nossa equipe acompanha toda a jornada, do pedido à expedição.</p>
                    </div>
                </div>
                <ul class="portal-support-card__list">
                    <?php if ($supportEmail !== ''): ?>
                        <li data-support-kind="email"><strong>E-mail:</strong> <a href="mailto:<?= htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($supportTelefone !== ''): ?>
                        <li data-support-kind="phone"><span class="portal-support-card__item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.69 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0 1 22 16.92Z"/></svg></span><strong>Telefone:</strong> <a href="tel:<?= htmlspecialchars($phoneHref ?: $supportTelefone, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($supportTelefone, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($supportWhatsapp !== ''): ?>
                        <li data-support-kind="whatsapp"><span class="portal-support-card__item-icon" aria-hidden="true"><svg class="portal-whatsapp-icon" viewBox="0 0 16 16"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93a7.898 7.898 0 0 0-2.327-5.607ZM7.994 15.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.25a6.568 6.568 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592Zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.066-.315-.099-.445.099-.133.197-.514.646-.63.775-.116.133-.232.15-.43.05-.197-.1-.836-.308-1.592-.984-.59-.525-.986-1.173-1.102-1.371-.116-.2-.013-.306.087-.405.09-.088.197-.232.296-.346.1-.116.133-.2.198-.33.066-.133.033-.25-.017-.35-.05-.099-.445-1.084-.61-1.47-.16-.389-.323-.335-.445-.34-.116-.006-.247-.006-.38-.006-.133 0-.35.05-.534.25-.182.2-.696.68-.696 1.66s.713 1.931.812 2.064c.099.133 1.404 2.137 3.4 2.997.476.205.847.328 1.136.42.478.152.913.13 1.257.079.383-.058 1.17-.48 1.336-.943.164-.462.164-.86.116-.943-.05-.082-.182-.132-.38-.23Z"/></svg></span><strong>WhatsApp:</strong>
                            <?php if ($whatsHref !== ''): ?>
                                <a href="<?= htmlspecialchars($whatsHref, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><?= htmlspecialchars($supportWhatsapp, ENT_QUOTES, 'UTF-8'); ?></a>
                            <?php else: ?>
                                <?= htmlspecialchars($supportWhatsapp, ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($supportHorario !== ''): ?>
                        <li data-support-kind="hours"><span class="portal-support-card__item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><strong>Horário:</strong> <?= htmlspecialchars($supportHorario, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                </ul>
                <div class="portal-support-card__actions">
                    <?php if ($whatsHref !== ''): ?>
                        <a class="portal-support-card__action portal-support-card__action--primary" href="<?= htmlspecialchars($whatsHref, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><svg class="portal-whatsapp-icon" viewBox="0 0 16 16" aria-hidden="true"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93a7.898 7.898 0 0 0-2.327-5.607ZM7.994 15.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.25a6.568 6.568 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592Zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.066-.315-.099-.445.099-.133.197-.514.646-.63.775-.116.133-.232.15-.43.05-.197-.1-.836-.308-1.592-.984-.59-.525-.986-1.173-1.102-1.371-.116-.2-.013-.306.087-.405.09-.088.197-.232.296-.346.1-.116.133-.2.198-.33.066-.133.033-.25-.017-.35-.05-.099-.445-1.084-.61-1.47-.16-.389-.323-.335-.445-.34-.116-.006-.247-.006-.38-.006-.133 0-.35.05-.534.25-.182.2-.696.68-.696 1.66s.713 1.931.812 2.064c.099.133 1.404 2.137 3.4 2.997.476.205.847.328 1.136.42.478.152.913.13 1.257.079.383-.058 1.17-.48 1.336-.943.164-.462.164-.86.116-.943-.05-.082-.182-.132-.38-.23Z"/></svg><span>Enviar WhatsApp</span></a>
                    <?php endif; ?>
                    <?php if ($supportEmail !== ''): ?>
                        <a class="portal-support-card__action" href="mailto:<?= htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><span>Enviar e-mail</span></a>
                    <?php endif; ?>
                    <?php if ($supportTelefone !== ''): ?>
                        <a class="portal-support-card__action" href="tel:<?= htmlspecialchars($phoneHref ?: $supportTelefone, ENT_QUOTES, 'UTF-8'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.69 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0 1 22 16.92Z"/></svg><span>Ligar</span></a>
                    <?php endif; ?>
                </div>
                <?php if ($supportMensagem !== ''): ?>
                    <p class="portal-support-card__foot"><?= htmlspecialchars($supportMensagem, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>

<button type="button" class="portal-floating-support" data-portal-jump="suporte" aria-label="Abrir suporte">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 3a7 7 0 0 0-7 7v2.25A2.75 2.75 0 0 0 7.75 15H9a1 1 0 0 0 1-1v-3.5a1 1 0 0 0-1-1H6.5A5.5 5.5 0 0 1 12 4.5a5.5 5.5 0 0 1 5.5 5H15a1 1 0 0 0-1 1V14a1 1 0 0 0 1 1h2.5v.5a2 2 0 0 1-2 2H14a2 2 0 0 0-2-1.5h-1a2 2 0 1 0 0 4h1a2 2 0 0 0 1.73-1h1.77a3.5 3.5 0 0 0 3.5-3.5V10a7 7 0 0 0-7-7Z" />
    </svg>
</button>

<div id="portal-welcome-modal" class="portal-modal" hidden>
    <div class="portal-modal__backdrop" data-welcome-dismiss></div>
    <div class="portal-modal__dialog portal-modal__dialog--welcome" role="dialog" aria-modal="true" aria-labelledby="portal-welcome-title">
        <button type="button" class="portal-modal__close" aria-label="Fechar" data-welcome-dismiss>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6.225 5.811a.8.8 0 0 1 1.132 0L12 10.455l4.643-4.644a.8.8 0 1 1 1.132 1.131L13.132 11.586l4.643 4.643a.8.8 0 1 1-1.132 1.132L12 12.718l-4.643 4.643a.8.8 0 0 1-1.132-1.132l4.644-4.643-4.644-4.644a.8.8 0 0 1 0-1.131Z" />
            </svg>
        </button>
        <div class="portal-welcome">
            <div class="portal-welcome__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2a7 7 0 0 1 7 7v1a7 7 0 0 1-7 7H8.414l-2.853 2.854A.5.5 0 0 1 5 19.793V17A7 7 0 0 1 12 2Z" />
                </svg>
            </div>
            <span class="portal-welcome__tag">Portal do cliente</span>
            <h3 id="portal-welcome-title">Boas-vindas!</h3>
            <p class="portal-welcome__lead">Olá, <strong data-welcome-name>Cliente</strong>. Seu painel personalizado está pronto para receber novos pedidos.</p>
            <ul class="portal-welcome__tips">
                <li>Confira o catálogo atualizado na aba <strong>Novo pedido</strong>.</li>
                <li>Acompanhe o status das solicitações em <strong>Pedidos</strong>.</li>
                <li>Precisa de ajuda? Acesse <strong>Suporte</strong> para falar com nossa equipe.</li>
            </ul>
            <div class="portal-welcome__footer">
                <button type="button" class="portal-btn portal-btn--ghost" data-welcome-dismiss>Explorar depois</button>
                <button type="button" class="portal-btn portal-btn--primary" data-welcome-go="novo-pedido">Novo pedido</button>
            </div>
        </div>
    </div>
</div>

<div id="order-success-modal" class="portal-modal" hidden>
    <div class="portal-modal__backdrop" data-modal-dismiss></div>
    <div class="portal-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="order-success-title">
        <button type="button" class="portal-modal__close" aria-label="Fechar" data-modal-dismiss>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6.225 5.811a.8.8 0 0 1 1.132 0L12 10.455l4.643-4.644a.8.8 0 1 1 1.132 1.131L13.132 11.586l4.643 4.643a.8.8 0 1 1-1.132 1.132L12 12.718l-4.643 4.643a.8.8 0 0 1-1.132-1.132l4.644-4.643-4.644-4.644a.8.8 0 0 1 0-1.131Z" />
            </svg>
        </button>
        <div class="portal-modal__icon" aria-hidden="true">
            <span class="portal-modal__pulse"></span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M9.55 16.45 6.4 13.3l1.125-1.125L9.55 14.2l6.925-6.925L17.6 8.4 9.55 16.45Zm2.45 5.55q-2.225 0-4.175-.837-1.95-.838-3.388-2.275-1.437-1.438-2.275-3.388Q2.325 13.55 2.325 11.325q0-2.225.837-4.175.838-1.95 2.275-3.388 1.438-1.437 3.388-2.275Q10.775.65 13 .65q2.225 0 4.175.837 1.95.838 3.388 2.275 1.437 1.438 2.275 3.388.837 1.95.837 4.175t-.837 4.175q-.838 1.95-2.275 3.388-1.438 1.437-3.388 2.275-1.95.837-4.175.837Z" />
            </svg>
        </div>
        <div class="portal-modal__body">
            <span class="portal-modal__tag">Pedido enviado</span>
            <h3 id="order-success-title">Recebemos seu pedido!</h3>
            <p class="portal-modal__lead">Nossa equipe está revisando as reservas de estoque. Você receberá a confirmação em breve no e-mail cadastrado.</p>
            <div class="portal-modal__meta">
                <div>
                    <span class="portal-modal__meta-label">Número interno</span>
                    <strong data-order-code>#0000</strong>
                </div>
                <div>
                    <span class="portal-modal__meta-label">Data do envio</span>
                    <strong data-order-date>-</strong>
                </div>
                <div>
                    <span class="portal-modal__meta-label">Total estimado</span>
                    <strong data-order-total>R$ 0,00</strong>
                </div>
            </div>
            <div class="portal-modal__section">
                <h4>Resumo dos itens</h4>
                <ul class="portal-modal__list" data-order-items></ul>
            </div>
            <div class="portal-modal__section portal-modal__section--backorder" data-order-backorder hidden>
                <h4>Itens encaminhados para produção</h4>
                <p class="portal-modal__muted">Reservamos tudo o que estava disponível. Os itens abaixo entrarão em fila de produção automática.</p>
                <ul class="portal-modal__list" data-order-backorder-list></ul>
            </div>
        </div>
        <div class="portal-modal__footer">
            <button type="button" class="portal-btn portal-btn--ghost" data-modal-dismiss>Fechar</button>
            <button type="button" class="portal-btn portal-btn--primary" data-modal-dismiss data-modal-focus="#btn-finalizar">Acompanhar pedidos</button>
        </div>
    </div>
</div>

<style>
    :root {
        --portal-hero-gradient: radial-gradient(120% 120% at 10% 0%, color-mix(in srgb, var(--primary, #2563eb) 14%, transparent), rgba(15, 23, 42, 0));
        --portal-card-bg: rgba(255, 255, 255, 0.95);
        --portal-card-border: rgba(15, 23, 42, 0.08);
        --portal-muted: rgba(71, 85, 105, 0.8);
        --portal-surface: rgba(255, 255, 255, 0.85);
    }

    [data-theme="dark"] {
        --portal-hero-gradient: radial-gradient(120% 120% at 10% 0%, color-mix(in srgb, var(--primary, #2563eb) 24%, transparent), rgba(15, 23, 42, 0));
        --portal-card-bg: rgba(15, 23, 42, 0.78);
        --portal-card-border: rgba(148, 163, 184, 0.16);
        --portal-muted: rgba(148, 163, 184, 0.78);
        --portal-surface: rgba(30, 41, 59, 0.76);
    }

    .portal-hero {
        padding: 4.5rem 1.5rem 3rem;
        background: var(--portal-hero-gradient);
    }

    .portal-hero__content {
        max-width: 1180px;
        margin: 0 auto;
        display: grid;
        gap: 2.5rem;
    }

    @media (min-width: 992px) {
        .portal-hero__content {
            grid-template-columns: minmax(0, 1fr) minmax(0, 420px);
            align-items: stretch;
        }
    }

    .portal-brand-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-card-border);
        border-radius: 26px;
        padding: 2.5rem;
        box-shadow: 0 32px 70px -45px rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(12px);
        overflow: hidden;
    }

    .portal-brand-card__logo {
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        container-type: inline-size;
        width: calc(100% + 5rem);
        margin: 0 0 1.5rem -2.5rem;
        min-height: calc(var(--portal-logo-size, 60px) + 24px);
        padding: 12px 6px;
        overflow: visible;
    }

    .portal-logo {
        height: var(--portal-logo-size, 60px);
        width: auto;
        max-width: 100%;
        object-fit: contain;
        transform: translate(
            clamp(
                calc((100% - 100cqw) / 2),
                var(--portal-logo-x, 0px),
                calc((100cqw - 100%) / 2)
            ),
            var(--portal-logo-y, 0px)
        );
        transition: transform 180ms ease;
    }

    .portal-logo-filtered {
        display: block;
        width: min(calc(var(--portal-logo-size, 60px) * 3.9), 100%);
        max-width: 100%;
        height: var(--portal-logo-size, 60px);
        flex: 0 1 min(calc(var(--portal-logo-size, 60px) * 3.9), 100%);
        background: var(--primary);
        transform: translate(
            clamp(
                calc((100% - 100cqw) / 2),
                var(--portal-logo-x, 0px),
                calc((100cqw - 100%) / 2)
            ),
            var(--portal-logo-y, 0px)
        );
        -webkit-mask: var(--portal-login-logo-url) center / contain no-repeat;
        mask: var(--portal-login-logo-url) center / contain no-repeat;
    }

    .portal-logo-dark {
        display: none;
    }

    [data-theme="dark"] .portal-logo-light {
        display: none;
    }

    [data-theme="dark"] .portal-logo-dark {
        display: block;
    }

    .portal-brand-card__logo--plain .portal-logo-light,
    [data-theme="dark"] .portal-brand-card__logo--plain .portal-logo-light {
        display: block;
    }

    .portal-brand-card__logo--plain .portal-logo-dark,
    [data-theme="dark"] .portal-brand-card__logo--plain .portal-logo-dark {
        display: none;
    }

    .portal-brand-card__tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary, #2563eb) 14%, transparent);
        color: color-mix(in srgb, var(--primary, #2563eb) 84%, #0f172a);
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .portal-brand-card h1 {
        margin: 1rem 0 0.65rem;
        font-size: clamp(2.1rem, 4vw, 3.1rem);
    }

    .portal-brand-card p {
        color: var(--portal-muted);
        font-size: 1.05rem;
        max-width: 560px;
    }

    .portal-auth-card {
        position: relative;
        background:
            linear-gradient(135deg, color-mix(in srgb, var(--primary, #2563eb) 7%, transparent), transparent 42%),
            var(--portal-card-bg);
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 18%, var(--portal-card-border));
        border-radius: 26px;
        box-shadow: 0 32px 70px -44px rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(12px);
        padding: clamp(1.45rem, 3vw, 2.45rem);
        display: flex;
        flex-direction: column;
        gap: 1.35rem;
        overflow: hidden;
    }

    .portal-auth-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 5px;
        background: linear-gradient(90deg, var(--primary, #2563eb), color-mix(in srgb, var(--primary, #2563eb) 55%, #ffffff));
    }

    .portal-flash {
        border-radius: 18px;
        padding: 0.9rem 1.2rem;
        font-weight: 600;
        margin: 1rem 0 1.4rem;
        border: 1px solid transparent;
    }

    .portal-flash--success {
        background: rgba(34, 197, 94, 0.16);
        color: #166534;
        border-color: rgba(22, 197, 94, 0.4);
    }

    .portal-flash--error {
        background: rgba(248, 113, 113, 0.18);
        color: #b91c1c;
        border-color: rgba(248, 113, 113, 0.4);
    }

    .auth-toggle {
        display: inline-flex;
        background: var(--portal-surface);
        border-radius: 16px;
        padding: 0.4rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        gap: 0.35rem;
    }

    .auth-toggle__btn {
        flex: 1;
        border: none;
        border-radius: 12px;
        padding: 0.65rem 1.1rem;
        font-weight: 600;
        font-size: 0.95rem;
        background: transparent;
        color: var(--portal-muted);
        cursor: pointer;
        transition: background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
    }

    .auth-toggle__btn.active {
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #2563eb) 16%, transparent), color-mix(in srgb, var(--primary, #2563eb) 32%, transparent));
        color: color-mix(in srgb, var(--primary, #2563eb) 76%, #0f172a);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--primary, #2563eb) 18%, transparent);
    }

    [data-theme="dark"] .auth-toggle__btn.active {
        color: color-mix(in srgb, var(--primary, #2563eb) 34%, #ffffff);
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #2563eb) 22%, transparent), color-mix(in srgb, var(--primary, #2563eb) 32%, transparent));
    }

    .auth-panels {
        display: flex;
        width: 200%;
        transform: translateX(0%);
        transition: transform 0.45s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .portal-auth-card[data-mode="register"] .auth-panels {
        transform: translateX(-50%);
    }

    .auth-panel {
        width: 100%;
        padding-right: 0;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        transition: opacity 0.3s ease, transform 0.3s ease;
        opacity: 1;
        transform: none;
    }

    .portal-auth-card[data-mode="login"] .auth-panel--login,
    .portal-auth-card[data-mode="register"] .auth-panel--register {
        opacity: 1;
        transform: translateY(0);
    }

    .auth-panel label {
        margin-top: 0.2rem;
        font-weight: 850;
        font-size: 0.95rem;
        color: var(--portal-muted);
    }

    .auth-panel input {
        width: 100%;
        min-height: 54px;
        border-radius: 16px;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 18%, rgba(148, 163, 184, 0.42));
        padding: 0.9rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.92);
        color: inherit;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }

    .auth-panel input:focus {
        outline: none;
        border-color: var(--primary, #2563eb);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary, #2563eb) 14%, transparent);
        transform: translateY(-1px);
    }

    .portal-input-shell {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .portal-input-shell input {
        padding-left: 3.15rem;
    }

    .portal-input-shell--password input {
        padding-right: 3.2rem;
    }

    .portal-input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.28rem;
        height: 1.28rem;
        color: color-mix(in srgb, var(--primary, #2563eb) 72%, var(--portal-muted));
        transform: translateY(-50%);
        pointer-events: none;
    }

    .portal-input-icon svg,
    .portal-password-toggle svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .portal-password-toggle {
        position: absolute;
        right: 0.68rem;
        top: 50%;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 16%, transparent);
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary, #2563eb) 8%, #ffffff);
        color: color-mix(in srgb, var(--primary, #2563eb) 78%, #334155);
        cursor: pointer;
        padding: 0;
        box-shadow: none;
        transform: translateY(-50%) !important;
        transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
    }

    .portal-password-toggle:hover,
    .portal-password-toggle:focus-visible {
        outline: none;
        background: color-mix(in srgb, var(--primary, #2563eb) 14%, #ffffff);
        color: var(--primary, #2563eb);
        border-color: color-mix(in srgb, var(--primary, #2563eb) 30%, transparent);
        box-shadow: none;
        transform: translateY(-50%) !important;
    }

    .portal-password-toggle__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 1.05rem;
        width: 1.05rem;
        height: 1.05rem;
        color: currentColor;
        pointer-events: none;
    }

    .portal-password-toggle__icon--hide {
        display: none !important;
    }

    .portal-password-toggle.is-visible .portal-password-toggle__icon--show {
        display: none !important;
    }

    .portal-password-toggle.is-visible .portal-password-toggle__icon--hide {
        display: inline-flex !important;
    }

    [data-theme="dark"] .auth-panel input {
        background: rgba(15, 23, 42, 0.82);
        border-color: rgba(71, 85, 105, 0.45);
    }

    [data-theme="dark"] .portal-password-toggle {
        background: color-mix(in srgb, var(--primary, #2563eb) 18%, rgba(15, 23, 42, 0.88));
        border-color: color-mix(in srgb, var(--primary, #2563eb) 24%, rgba(148, 163, 184, 0.18));
        color: color-mix(in srgb, var(--primary, #2563eb) 66%, #ffffff);
    }

    .auth-panel__header h2 {
        margin: 0;
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        line-height: 1.05;
        letter-spacing: 0;
    }

    .portal-auth-title {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .portal-auth-title__icon {
        width: 62px;
        height: 62px;
        flex: 0 0 62px;
        display: inline-grid;
        place-items: center;
        border-radius: 14px;
        color: var(--primary, #2563eb);
        background: color-mix(in srgb, var(--primary, #2563eb) 13%, #ffffff);
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 18%, transparent);
        box-shadow: 0 16px 30px -24px color-mix(in srgb, var(--primary, #2563eb) 70%, transparent);
    }

    .portal-auth-title__icon svg {
        width: 44px;
        height: 44px;
    }

    [data-theme="dark"] .portal-auth-title__icon {
        color: color-mix(in srgb, var(--primary, #2563eb) 72%, #ffffff);
        background: color-mix(in srgb, var(--primary, #2563eb) 20%, rgba(15, 23, 42, 0.9));
        border-color: color-mix(in srgb, var(--primary, #2563eb) 28%, rgba(255, 255, 255, 0.14));
    }

    .auth-panel__subtitle {
        margin: 0.55rem 0 1.1rem;
        color: var(--portal-muted);
        font-size: 1rem;
        line-height: 1.42;
        max-width: 36ch;
    }

    .auth-panel .portal-btn--primary {
        min-height: 54px;
        margin-top: 0.35rem;
        width: 100%;
        border-radius: 16px;
        font-weight: 850;
    }

    .portal-auth-cta {
        margin-top: 0.95rem;
        padding-top: 1rem;
        border-top: 1px solid color-mix(in srgb, var(--primary, #2563eb) 12%, var(--portal-card-border));
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: space-between;
        font-size: 0.95rem;
        color: var(--portal-muted);
    }

    .portal-auth-cta .portal-btn {
        min-height: 44px;
        padding: 0.68rem 1.05rem;
        border-radius: 14px;
        font-weight: 850;
    }

    .auth-panel__switch {
        margin-top: 0.35rem;
        font-size: 0.9rem;
        color: var(--portal-muted);
    }

    .auth-panel__switch a {
        color: inherit;
        font-weight: 600;
        text-decoration: underline;
    }

    .document-toggle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(148, 163, 184, 0.1);
        padding: 0.65rem 0.85rem;
        border-radius: 14px;
        margin-top: 0.35rem;
    }

    .document-toggle span {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--portal-muted);
    }

    .document-toggle__buttons {
        display: inline-flex;
        gap: 0.45rem;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 12px;
        padding: 0.25rem;
    }

    [data-theme="dark"] .document-toggle__buttons {
        background: rgba(15, 23, 42, 0.65);
    }

    .document-toggle__btn {
        border: none;
        border-radius: 10px;
        padding: 0.45rem 0.8rem;
        font-weight: 600;
        font-size: 0.85rem;
        background: transparent;
        cursor: pointer;
        color: var(--portal-muted);
        transition: background 0.2s ease, color 0.2s ease;
    }

    .document-toggle__btn.active {
        background: rgba(37, 99, 235, 0.18);
        color: rgba(30, 64, 175, 0.92);
    }

    [data-theme="dark"] .document-toggle__btn.active {
        background: rgba(96, 165, 250, 0.24);
        color: rgba(191, 219, 254, 0.92);
    }

    .document-fields {
        display: grid;
        gap: 0.9rem;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .field-group--hidden {
        display: none;
    }

    .address-grid {
        display: grid;
        gap: 0.8rem;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        margin-top: 0.4rem;
    }

    .field-grid {
        display: grid;
        gap: 0.8rem;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .cep-feedback {
        margin: 0.2rem 0 0.4rem;
        font-size: 0.85rem;
        color: var(--portal-muted);
    }

    .cep-feedback[data-state="error"] {
        color: #dc2626;
    }

    .portal-btn {
        border: none;
        border-radius: 14px;
        padding: 0.85rem 1.3rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .portal-btn--primary {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95), rgba(30, 64, 175, 0.92));
        color: #fff;
        box-shadow: 0 18px 35px -20px rgba(37, 99, 235, 0.9);
    }

    .portal-btn--success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.94), rgba(5, 150, 105, 0.9));
        color: #fff;
        box-shadow: 0 20px 35px -20px rgba(5, 150, 105, 0.8);
    }

    .portal-btn--ghost {
        background: rgba(148, 163, 184, 0.16);
        color: inherit;
        border: 1px solid rgba(148, 163, 184, 0.3);
    }

    .portal-feedback {
        min-height: 1.4rem;
        font-size: 0.9rem;
        color: var(--portal-muted);
    }

    .portal-feedback[data-state="success"] {
        color: #15803d;
    }

    .portal-feedback[data-state="error"] {
        color: #b91c1c;
    }

    .portal-app {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 1.5rem 4.5rem;
        display: none;
    }

    .portal-app__header {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 1.5rem;
        align-items: center;
        padding: 3rem 0 2rem;
    }

    .portal-app__welcome {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .portal-app__tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.3rem 0.8rem;
        border-radius: 999px;
        background: rgba(59, 130, 246, 0.12);
        color: rgba(30, 64, 175, 0.85);
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }

    .portal-app__subtitle {
        margin: 0.55rem 0 0;
        color: var(--portal-muted);
        max-width: 520px;
    }

    .portal-app__header-links {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.4rem;
        border-radius: 999px;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.18);
    }

    .portal-app__link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        border: none;
        background: rgba(255, 255, 255, 0.92);
        color: rgba(37, 99, 235, 0.92);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .portal-app__link:focus-visible {
        outline: 2px solid rgba(37, 99, 235, 0.28);
        outline-offset: 2px;
        transform: translateY(-1px);
    }

    .portal-app__link:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 25px -15px rgba(37, 99, 235, 0.55);
    }

    .portal-app__link-icon {
        display: inline-flex;
        width: 22px;
        height: 22px;
    }

    .portal-app__link-icon svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }

    .portal-app__link-text {
        white-space: nowrap;
    }

    .portal-app__profile {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .portal-app__client {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        color: rgba(30, 64, 175, 0.9);
        font-weight: 600;
        font-size: 0.9rem;
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .portal-app__nav {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin: 0 0 2.25rem;
    }

    .portal-tab {
        appearance: none;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: rgba(255, 255, 255, 0.88);
        color: var(--portal-muted);
        padding: 0.55rem 1.2rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.92rem;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .portal-tab.is-active {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.92), rgba(59, 130, 246, 0.95));
        color: #fff;
        box-shadow: 0 14px 30px -20px rgba(37, 99, 235, 0.8);
    }

    .portal-tab:focus-visible {
        outline: 2px solid rgba(37, 99, 235, 0.32);
        outline-offset: 2px;
        transform: translateY(-1px);
    }

    [data-theme="dark"] .portal-app__header-links {
        background: rgba(96, 165, 250, 0.16);
        border-color: rgba(147, 197, 253, 0.25);
    }

    [data-theme="dark"] .portal-app__link {
        background: rgba(30, 41, 59, 0.86);
        color: rgba(147, 197, 253, 0.95);
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.18);
    }

    [data-theme="dark"] .portal-app__client {
        background: rgba(96, 165, 250, 0.12);
        color: rgba(191, 219, 254, 0.95);
    }

    [data-theme="dark"] .portal-tab {
        background: rgba(30, 41, 59, 0.8);
        color: var(--portal-muted);
        border-color: rgba(71, 85, 105, 0.45);
    }

    [data-theme="dark"] .portal-tab.is-active {
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.88), rgba(37, 99, 235, 0.92));
        box-shadow: 0 16px 32px -22px rgba(37, 99, 235, 0.75);
    }

    .portal-app__panels {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }

    .portal-panel[hidden] {
        display: none !important;
    }

    .portal-app__grid {
        display: grid;
        gap: 2rem;
    }

    .portal-cart-stack {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .portal-cart-actions {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-card-border);
        border-radius: 22px;
        box-shadow: 0 20px 50px -36px rgba(15, 23, 42, 0.55);
        padding: 1.5rem;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .portal-cart-actions .portal-btn {
        min-width: 220px;
        width: 100%;
    }

    @media (min-width: 1080px) {
        .portal-app__grid {
            grid-template-columns: minmax(0, 1fr) 340px;
            align-items: start;
        }
        .portal-cart-actions .portal-btn {
            width: auto;
        }
    }

    .portal-catalog-card,
    .portal-cart-card,
    .portal-orders-card,
    .portal-products-card,
    .portal-support-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-card-border);
        border-radius: 26px;
        box-shadow: 0 30px 60px -42px rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(10px);
        padding: 2.2rem;
    }

    .portal-catalog-card__head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1.8rem;
    }

    .portal-catalog-title {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        font-size: calc(1.17em + 3px);
    }

    .portal-catalog-title__icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 38px;
        border-radius: 12px;
        background: var(--primary);
        color: #fff;
        padding: 6px;
        box-sizing: border-box;
    }

    .portal-catalog-title__icon svg {
        width: 18px;
        height: 18px;
    }

    .portal-catalog-card__controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .portal-catalog-count {
        min-width: 104px;
        padding: 8px 11px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary) 10%, transparent);
        color: var(--primary);
        font-size: 0.82rem;
        font-weight: 900;
        text-align: center;
        white-space: nowrap;
    }

    .portal-view-toggle {
        display: inline-flex;
        align-items: stretch;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        background: rgba(255, 255, 255, 0.88);
        padding: 0.15rem;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.06);
    }

    [data-theme="dark"] .portal-view-toggle {
        background: rgba(30, 41, 59, 0.86);
        border-color: rgba(71, 85, 105, 0.45);
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .portal-view-toggle__btn {
        appearance: none;
        border: none;
        background: transparent;
        color: var(--portal-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.38rem;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }

    .portal-view-toggle__btn svg {
        width: 1rem;
        height: 1rem;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        flex: 0 0 auto;
    }

    .portal-view-toggle__btn.active {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.82), rgba(59, 130, 246, 0.92));
        color: #fff;
        box-shadow: 0 8px 18px -12px rgba(37, 99, 235, 0.8);
    }

    .portal-view-toggle__btn:focus-visible {
        outline: 2px solid rgba(37, 99, 235, 0.35);
        outline-offset: 2px;
    }

    .portal-catalog {
        display: grid;
        gap: 1.4rem;
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }

    .portal-catalog[data-view="list"] {
        display: flex;
        flex-direction: column;
    }

    .portal-product {
        content-visibility: auto;
        contain-intrinsic-size: 320px 430px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        padding: 1.2rem 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        background: rgba(255, 255, 255, 0.88);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .portal-product:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 30px -26px rgba(15, 23, 42, 0.45);
    }

    .portal-product__media {
        position: relative;
        display: grid;
        place-items: center;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        margin: -0.45rem -0.65rem 0;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary, #2563eb) 9%, #fff);
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 15%, transparent);
    }

    .portal-product__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .portal-product__media-zoom {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        border: 0;
        background: rgba(15, 23, 42, 0.02);
        color: #fff;
        cursor: zoom-in;
        opacity: 0;
        transition: opacity 0.18s ease, background 0.18s ease;
    }
    .portal-product__media:hover .portal-product__media-zoom {
        opacity: 1;
        background: rgba(15, 23, 42, 0.07);
    }
    .portal-product__media-zoom-mark {
        display: inline-grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border: 1px solid rgba(255, 255, 255, 0.52);
        border-radius: 50%;
        background: color-mix(in srgb, var(--primary, #2563eb) 48%, transparent);
        backdrop-filter: blur(3px);
        box-shadow: 0 14px 28px -22px rgba(15, 23, 42, 0.58);
    }
    .portal-product__media-zoom svg {
        width: 25px;
        height: 25px;
        stroke: currentColor;
    }

    .portal-product__media-placeholder {
        display: grid;
        place-items: center;
        width: 62px;
        height: 62px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.78);
        color: var(--primary, #2563eb);
    }

    .portal-product__media-placeholder svg {
        width: 30px;
        height: 30px;
        stroke: currentColor;
    }
    .portal-product__media-toggle {
        position: absolute;
        right: -18px;
        top: 50%;
        transform: translateY(-50%);
        display: inline-grid;
        place-items: center start;
        width: 48px;
        height: 48px;
        min-width: 48px;
        min-height: 48px;
        aspect-ratio: 1 / 1;
        padding: 0 0 0 12px;
        border: 1px solid rgba(255, 255, 255, 0.52);
        border-radius: 50%;
        background: color-mix(in srgb, var(--primary, #2563eb) 48%, transparent);
        backdrop-filter: blur(3px);
        color: #fff;
        cursor: pointer;
        box-shadow: 0 14px 28px -22px rgba(15, 23, 42, 0.58);
        z-index: 2;
        transition: none;
    }
    .portal-product__media-toggle:hover,
    .portal-product__media-toggle:focus-visible {
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        background: color-mix(in srgb, var(--primary, #2563eb) 48%, transparent);
    }
    .portal-product__media-toggle svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
    }
    .portal-product__media-caption {
        position: absolute;
        left: 10px;
        bottom: 10px;
        max-width: calc(100% - 20px);
        padding: 5px 9px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.68);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 850;
        z-index: 2;
    }
    .portal-image-lightbox {
        position: fixed;
        inset: 0;
        z-index: 1600;
        display: none;
        align-items: center;
        justify-content: center;
        padding: clamp(18px, 4vw, 46px);
        background: rgba(15, 23, 42, 0.78);
        backdrop-filter: blur(8px);
    }
    .portal-image-lightbox.is-open {
        display: flex;
    }
    .portal-image-lightbox__dialog {
        position: relative;
        display: grid;
        gap: 12px;
        width: min(980px, 100%);
        overflow: visible;
    }
    .portal-image-lightbox img {
        width: 100%;
        max-height: min(76vh, 760px);
        object-fit: contain;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 28px 70px -24px rgba(0, 0, 0, 0.78);
    }
    .portal-image-lightbox__caption {
        justify-self: center;
        padding: 7px 13px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
        color: #0f172a;
        font-weight: 850;
    }
    .portal-image-lightbox__close {
        position: absolute;
        right: 14px;
        top: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        place-items: center;
        width: 38px;
        height: 38px;
        min-width: 38px;
        min-height: 38px;
        aspect-ratio: 1 / 1;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, 0.72);
        border-radius: 50%;
        background: #dc2626;
        backdrop-filter: blur(4px);
        color: #fff;
        cursor: pointer;
        box-shadow: 0 16px 30px -20px rgba(127, 29, 29, 0.82);
        z-index: 3;
        transition: none;
    }
    .portal-image-lightbox__close svg {
        display: block;
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        stroke: currentColor;
    }
    .portal-image-lightbox__nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        display: inline-grid;
        place-items: center;
        width: 62px;
        height: 62px;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, 0.62);
        border-radius: 50%;
        background: color-mix(in srgb, var(--primary, #2563eb) 58%, transparent);
        backdrop-filter: blur(6px);
        color: #fff;
        cursor: pointer;
        box-shadow: 0 18px 36px -24px rgba(15, 23, 42, 0.72);
        z-index: 3;
    }
    .portal-image-lightbox__nav--prev {
        left: 16px;
    }
    .portal-image-lightbox__nav--next {
        right: 16px;
    }
    .portal-image-lightbox__nav:hover,
    .portal-image-lightbox__nav:focus-visible {
        transform: translateY(-50%);
    }
    .portal-image-lightbox__close:hover,
    .portal-image-lightbox__close:focus-visible {
        transform: none;
    }
    .portal-image-lightbox__nav[hidden] {
        display: none;
    }
    .portal-image-lightbox__nav svg {
        width: 19px;
        height: 19px;
        stroke: currentColor;
    }
    @media (max-width: 640px) {
        .portal-image-lightbox__nav {
            width: 52px;
            height: 52px;
        }
        .portal-image-lightbox__nav--prev {
            left: 10px;
        }
        .portal-image-lightbox__nav--next {
            right: 10px;
        }
    }
    @media (hover: none) {
        .portal-product__media-zoom {
            opacity: 1;
            inset: auto 10px 10px auto;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
        }
        .portal-product__media-zoom-mark {
            width: 36px;
            height: 36px;
        }
        .portal-product__media-zoom svg {
            width: 19px;
            height: 19px;
        }
    }

    [data-theme="dark"] .portal-product {
        background: rgba(30, 41, 59, 0.78);
        border-color: rgba(71, 85, 105, 0.35);
        box-shadow: 0 16px 36px -28px rgba(15, 23, 42, 0.85);
    }

    [data-theme="dark"] .portal-product__media {
        background: color-mix(in srgb, var(--primary, #2563eb) 16%, rgba(15, 23, 42, 0.8));
    }

    .portal-product__header {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .portal-product h3 {
        margin: 0;
        font-size: 1.15rem;
    }

    .portal-product small {
        color: var(--portal-muted);
    }

    .portal-product__meta {
        margin: 0;
        color: var(--portal-muted);
        font-size: 0.93rem;
    }

    .portal-product .badge {
        align-self: flex-start;
        padding: 0.25rem 0.65rem;
        background: rgba(37, 99, 235, 0.12);
        color: rgba(37, 99, 235, 0.9);
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .portal-product__weight-pill {
        display: inline-flex;
        align-items: center;
        align-self: flex-start;
        gap: 0.35rem;
        width: fit-content;
        padding: 0.28rem 0.7rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 26%, transparent);
        background: color-mix(in srgb, var(--primary, #2563eb) 13%, #ffffff);
        color: var(--primary, #2563eb);
        font-size: 0.76rem;
        font-weight: 850;
        line-height: 1;
        box-shadow: 0 12px 24px -20px color-mix(in srgb, var(--primary, #2563eb) 55%, transparent);
    }

    [data-theme="dark"] .portal-product__weight-pill {
        background: color-mix(in srgb, var(--primary, #3b82f6) 18%, rgba(15, 23, 42, 0.88));
        border-color: color-mix(in srgb, var(--primary, #3b82f6) 38%, transparent);
        color: color-mix(in srgb, var(--primary, #3b82f6) 74%, #ffffff);
    }

    .portal-product__footer {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .portal-product__price {
        font-size: 1.12rem;
        font-weight: 700;
    }

    .portal-product__controls {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .portal-product__label {
        font-weight: 600;
        font-size: 0.85rem;
    }

    .portal-product__input-group {
        display: flex;
        gap: 0.6rem;
        align-items: center;
    }

    .portal-product__input-group input[type="number"] {
        flex: 1;
        min-width: 80px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.6rem 0.75rem;
    }

    [data-theme="dark"] .portal-product__input-group input[type="number"] {
        background: rgba(15, 23, 42, 0.75);
        color: inherit;
    }

    .portal-product__input-group button {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.92), rgba(5, 150, 105, 0.9));
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.55rem 1rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .portal-product__input-group button:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px -18px rgba(5, 150, 105, 0.8);
    }

    .portal-product--list {
        display: grid;
        grid-template-columns: 132px minmax(0, 1fr);
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.15rem;
    }

    .portal-product--list .portal-product__media {
        grid-row: 1 / span 4;
        width: 132px;
        height: 96px;
        aspect-ratio: auto;
        margin: 0;
        border-radius: 12px;
    }

    .portal-product--list .portal-product__media-placeholder {
        width: 46px;
        height: 46px;
        border-radius: 14px;
    }

    .portal-product--list .portal-product__media-placeholder svg {
        width: 23px;
        height: 23px;
    }

    .portal-product--list .portal-product__media-zoom-mark {
        width: 38px;
        height: 38px;
    }

    .portal-product--list .portal-product__media-zoom svg {
        width: 20px;
        height: 20px;
    }

    .portal-product--list .portal-product__media-toggle {
        right: -14px;
        width: 40px;
        height: 40px;
        min-width: 40px;
        min-height: 40px;
        padding-left: 10px;
    }

    .portal-product--list .portal-product__media-toggle:hover,
    .portal-product--list .portal-product__media-toggle:focus-visible {
        width: 40px;
        height: 40px;
    }

    .portal-product--list .portal-product__media-caption {
        left: 7px;
        bottom: 7px;
        padding: 4px 7px;
        font-size: 0.62rem;
    }

    .portal-product--list .portal-product__header,
    .portal-product--list .portal-product__weight-pill,
    .portal-product--list .portal-product__meta,
    .portal-product--list .portal-product__footer {
        min-width: 0;
    }

    .portal-product--list .portal-product__footer {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .portal-product--list .portal-product__controls {
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
    }

    .portal-product--list .portal-product__input-group {
        flex: 1;
    }

    .portal-product--list .portal-product__price {
        font-size: 1.05rem;
    }

    @media (max-width: 640px) {
        .portal-product--list {
            grid-template-columns: 104px minmax(0, 1fr);
            padding: 0.9rem;
        }

        .portal-product--list .portal-product__media {
            width: 104px;
            height: 82px;
        }
    }

    .portal-orders-card__head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
        margin-bottom: 1.6rem;
        flex-wrap: wrap;
    }

    .portal-orders-card__head p {
        margin: 0.4rem 0 0;
        color: var(--portal-muted);
        max-width: 520px;
    }

    .portal-orders-card__actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .portal-orders-table-wrapper {
        overflow-x: auto;
    }

    .portal-orders-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
        min-width: 640px;
    }

    .portal-orders-table th,
    .portal-orders-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        text-align: left;
    }

    .portal-orders-table thead th {
        font-size: 0.82rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgba(71, 85, 105, 0.75);
    }

    .portal-orders-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.04);
    }

    [data-theme="dark"] .portal-orders-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.08);
    }

    [data-theme="dark"] .portal-orders-table th,
    [data-theme="dark"] .portal-orders-table td {
        border-color: rgba(71, 85, 105, 0.35);
    }

    .portal-orders-table__actions {
        text-align: right;
        width: auto;
    }

    .portal-orders-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .portal-orders-action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(148, 163, 184, 0.18);
        color: inherit;
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-radius: 12px;
        padding: 0.45rem 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .portal-orders-action:hover:not([disabled]) {
        background: rgba(37, 99, 235, 0.12);
        color: rgba(30, 64, 175, 0.9);
        transform: translateY(-1px);
    }

    .portal-orders-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .portal-orders-action svg {
        width: 1rem;
        height: 1rem;
    }

    .portal-orders-action--icon {
        box-sizing: border-box;
        position: relative;
        width: 39px;
        min-width: 39px;
        max-width: 39px;
        height: 39px;
        min-height: 39px;
        max-height: 39px;
        padding: 0;
        justify-content: center;
        flex: 0 0 39px;
        border: 0;
        border-radius: 12px;
        background: var(--surface-muted, #f3f4f6);
        color: var(--portal-text, #020617);
        line-height: 1;
    }

    .portal-orders-action--icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.25;
    }

    .portal-orders-action--icon:hover:not([disabled]) {
        background: var(--surface-muted, #f3f4f6);
        color: var(--portal-text, #020617);
        filter: brightness(.98);
        transform: none;
    }

    .portal-orders-action--icon::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 50%;
        bottom: calc(100% + 8px);
        transform: translateX(-50%) translateY(4px);
        padding: 7px 10px;
        border-radius: 8px;
        background: #111827;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .16s ease, transform .16s ease;
        z-index: 40;
    }

    .portal-orders-action--icon:hover::after,
    .portal-orders-action--icon:focus-visible::after {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .portal-orders-title {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .portal-orders-title__icon {
        display: inline-grid;
        place-items: center;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        border: 1px solid color-mix(in srgb, var(--primary) 28%, transparent);
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary) 10%, transparent);
        color: var(--primary);
    }

    .portal-orders-title__icon svg {
        width: 22px;
        height: 22px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .portal-order-editor{display:grid;gap:16px;padding:22px;border:1px solid var(--portal-card-border);border-radius:24px;background:var(--portal-card-bg);box-shadow:0 24px 62px -42px rgba(15,23,42,.5)}
    .portal-order-editor__head{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}.portal-order-editor__title{display:flex;align-items:center;gap:11px}.portal-order-editor__title-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:14px;background:color-mix(in srgb,var(--primary) 11%,transparent);color:var(--primary)}.portal-order-editor__title-icon svg{width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.portal-order-editor__title h3{margin:0}.portal-order-editor__title p{margin:3px 0 0;color:var(--portal-muted)}.portal-order-editor__back{display:inline-flex;align-items:center;gap:7px}.portal-order-editor__back svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:2.2}.portal-order-editor__items{display:grid;gap:9px}.portal-order-editor__item{display:grid;grid-template-columns:minmax(0,1fr) 130px auto;align-items:center;gap:12px;padding:12px;border:1px solid var(--portal-card-border);border-radius:14px}.portal-order-editor__item strong,.portal-order-editor__item small{display:block}.portal-order-editor__item small{margin-top:3px;color:var(--portal-muted)}.portal-order-editor__item input{width:100%;min-height:42px;border:1px solid var(--portal-card-border);border-radius:11px;padding:8px 10px;background:var(--portal-card-bg);color:var(--portal-text);font-weight:800}.portal-order-editor__remove{display:grid;place-items:center;width:38px;height:38px;border:1px solid #fecaca;border-radius:11px;background:#fff1f2;color:#dc2626;cursor:pointer}.portal-order-editor__remove svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:2}.portal-order-editor__footer{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding-top:12px;border-top:1px solid var(--portal-card-border)}.portal-order-editor__total strong{font-size:1.2rem}.portal-order-editor__loading{padding:32px;text-align:center;color:var(--portal-muted)}@media(max-width:620px){.portal-order-editor{padding:15px;border-radius:18px}.portal-order-editor__head{align-items:stretch;flex-direction:column}.portal-order-editor__back{justify-content:center}.portal-order-editor__item{grid-template-columns:minmax(0,1fr) 82px}.portal-order-editor__remove{grid-column:2;justify-self:end}.portal-order-editor__footer{align-items:stretch;flex-direction:column}.portal-order-editor__footer .portal-btn{width:100%;justify-content:center}}
    .portal-order-editor__meta{display:flex;gap:8px;flex-wrap:wrap}.portal-order-editor__meta span{padding:7px 10px;border-radius:999px;background:color-mix(in srgb,var(--primary) 8%,transparent);color:var(--portal-text);font-size:.78rem;font-weight:800}.portal-order-editor__section{display:grid;gap:10px;padding:16px;border:1px solid var(--portal-card-border);border-radius:17px;background:color-mix(in srgb,var(--portal-card-bg) 96%,var(--primary) 4%)}.portal-order-editor__section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}.portal-order-editor__section h4{margin:0}.portal-order-editor__search{position:relative;display:flex;align-items:center;min-width:min(100%,360px)}.portal-order-editor__search>svg{position:absolute;left:13px;width:17px;height:17px;fill:none;stroke:var(--portal-muted);stroke-width:2;pointer-events:none}.portal-order-editor__search input{width:100%;min-height:44px;padding:9px 42px;border:1px solid var(--portal-card-border);border-radius:12px;background:var(--portal-card-bg);color:var(--portal-text)}.portal-order-editor__search:focus-within>svg{stroke:var(--primary)}.portal-order-editor__clear{position:absolute;right:7px;display:grid;place-items:center;width:30px;height:30px;border:0;border-radius:9px;background:transparent;color:var(--portal-muted);cursor:pointer}.portal-order-editor__catalog{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;max-height:228px;overflow:auto}.portal-order-editor__product{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:9px;padding:10px 11px;border:1px solid var(--portal-card-border);border-radius:12px;background:var(--portal-card-bg)}.portal-order-editor__product strong,.portal-order-editor__product small{display:block}.portal-order-editor__product small{margin-top:2px;color:var(--portal-muted)}.portal-order-editor__add{display:grid;place-items:center;width:34px;height:34px;border:1px solid color-mix(in srgb,var(--primary) 30%,transparent);border-radius:10px;background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary);cursor:pointer}.portal-order-editor__add svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.3}.portal-order-editor__empty{grid-column:1/-1;padding:18px;text-align:center;color:var(--portal-muted)}.portal-order-editor__notes{display:grid;gap:7px}.portal-order-editor__notes label{font-weight:800}.portal-order-editor__notes textarea{min-height:96px;resize:vertical;padding:12px;border:1px solid var(--portal-card-border);border-radius:12px;background:var(--portal-card-bg);color:var(--portal-text)}.portal-order-editor__summary{display:flex;align-items:center;gap:18px;flex-wrap:wrap}.portal-order-editor__summary span{display:grid;gap:2px;color:var(--portal-muted);font-size:.78rem}.portal-order-editor__summary strong{color:var(--portal-text);font-size:1.05rem}.portal-order-editor__actions{display:flex;gap:9px;flex-wrap:wrap}@media(max-width:720px){.portal-order-editor__catalog{grid-template-columns:1fr}}@media(max-width:620px){.portal-order-editor{padding:15px;border-radius:18px}.portal-order-editor__head{align-items:stretch;flex-direction:column}.portal-order-editor__back{justify-content:center}.portal-order-editor__section{padding:12px}.portal-order-editor__search{min-width:100%}.portal-order-editor__item{grid-template-columns:minmax(0,1fr) 82px}.portal-order-editor__remove{grid-column:2;justify-self:end}.portal-order-editor__footer,.portal-order-editor__actions{align-items:stretch;flex-direction:column}.portal-order-editor__footer .portal-btn,.portal-order-editor__actions .portal-btn{width:100%;justify-content:center}}
    .portal-order-builder{display:grid;gap:18px}.portal-order-builder__title strong{display:block;color:var(--portal-text);font-size:1.05rem}.portal-order-builder__title p{margin:2px 0 0;color:var(--portal-muted)}.portal-order-builder__entry{display:grid;grid-template-columns:minmax(96px,.42fr) minmax(250px,1.45fr) minmax(110px,.52fr) minmax(110px,.5fr) minmax(130px,.62fr) minmax(140px,.65fr) minmax(145px,auto);gap:12px;align-items:end}.portal-order-builder__field{position:relative;display:flex;flex-direction:column;gap:6px;min-width:0;color:var(--portal-muted);font-weight:700}.portal-order-builder__field input,.portal-order-builder__readonly{box-sizing:border-box;width:100%;min-height:46px;padding:10px 14px;border:1px solid var(--portal-card-border);border-radius:12px;background:var(--portal-card-bg);color:var(--portal-text);font-weight:800}.portal-order-builder__readonly{display:flex;align-items:center;background:rgba(249,250,251,.8);white-space:nowrap}.portal-order-builder__code{justify-content:center;color:var(--portal-muted)}.portal-order-builder__suggestions{position:absolute;z-index:50;top:calc(100% + 6px);left:0;right:0;max-height:260px;overflow:auto;margin:0;padding:6px;list-style:none;border:1px solid var(--portal-card-border);border-radius:12px;background:var(--portal-card-bg);box-shadow:0 18px 38px rgba(15,23,42,.14)}.portal-order-builder__suggestions:empty{display:none}.portal-order-builder__suggestions button{width:100%;padding:10px 12px;border:0;border-radius:9px;background:transparent;color:var(--portal-text);text-align:left;font-weight:800;cursor:pointer}.portal-order-builder__suggestions button:hover{background:rgba(249,250,251,.9)}.portal-order-builder__suggestions small{display:block;margin-top:2px;color:var(--portal-muted);font-weight:600}.portal-order-builder__add{min-height:46px;justify-content:center;white-space:nowrap}.portal-order-builder__list h4{margin:0 0 12px}.portal-order-builder__table{overflow:hidden;border:1px solid var(--portal-card-border);border-radius:14px;background:var(--portal-card-bg);box-shadow:0 12px 28px rgba(15,23,42,.06)}.portal-order-builder__head,.portal-order-editor__item{display:grid;grid-template-columns:minmax(92px,.42fr) minmax(240px,1.55fr) minmax(120px,.52fr) minmax(108px,.48fr) minmax(125px,.58fr) minmax(130px,.6fr) 52px;gap:11px;align-items:center}.portal-order-builder__head{min-height:46px;padding:0 16px;background:rgba(249,250,251,.8);color:var(--portal-muted);font-size:.78rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid var(--portal-card-border)}.portal-order-editor__items{display:grid;gap:0}.portal-order-editor__item{min-height:58px;padding:9px 16px;border:0;border-bottom:1px solid var(--portal-card-border);border-radius:0}.portal-order-editor__item:last-child{border-bottom:0}.portal-order-editor__item>span{min-width:0;font-weight:800}.portal-order-editor__item-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.portal-order-editor__item input{min-height:38px;padding:7px 10px;border-radius:10px}.portal-order-editor__item-subtotal{font-weight:900}.portal-order-editor__empty-list{padding:16px;color:var(--portal-muted)}@media(max-width:1180px){.portal-order-builder__entry{grid-template-columns:repeat(3,minmax(160px,1fr))}}@media(max-width:980px){.portal-order-builder__entry{grid-template-columns:1fr 1fr}.portal-order-builder__table{overflow-x:auto}.portal-order-builder__head,.portal-order-editor__item{min-width:940px}}@media(max-width:640px){.portal-order-builder__entry{grid-template-columns:1fr}.portal-order-builder__add{width:100%}}

    .portal-orders-action--confirm {
        background: rgba(22, 163, 74, 0.16);
        border-color: rgba(22, 163, 74, 0.42);
        color: #15803d;
    }
    .portal-orders-action--confirm[data-received-confirmed]{background:#e5e7eb!important;border-color:#cbd5e1!important;color:#64748b!important;cursor:not-allowed!important;opacity:1!important;box-shadow:none!important}

    .portal-orders-action--problem {
        background: rgba(251, 191, 36, 0.18);
        border-color: rgba(217, 119, 6, 0.4);
        color: #b45309;
    }

    [data-theme="dark"] .portal-orders-actions {
        gap: 0.45rem;
    }

    [data-theme="dark"] .portal-orders-action {
        background: rgba(37, 99, 235, 0.18);
        border-color: rgba(59, 130, 246, 0.34);
        color: rgba(191, 219, 254, 0.9);
    }

    [data-theme="dark"] .portal-orders-action:hover:not([disabled]) {
        background: rgba(59, 130, 246, 0.24);
        color: rgba(224, 231, 255, 0.95);
    }

    [data-theme="dark"] .portal-orders-action--confirm {
        background: rgba(22, 163, 74, 0.26);
        border-color: rgba(74, 222, 128, 0.4);
        color: rgba(187, 247, 208, 0.92);
    }
    [data-theme="dark"] .portal-orders-action--confirm[data-received-confirmed]{background:#334155!important;border-color:#475569!important;color:#94a3b8!important}

    [data-theme="dark"] .portal-orders-action--problem {
        background: rgba(234, 179, 8, 0.26);
        border-color: rgba(249, 115, 22, 0.45);
        color: rgba(254, 215, 170, 0.92);
    }

    .portal-order-editor form { display: grid; gap: 18px; }
    .portal-order-editor__summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(130px, 1fr));
        gap: 10px;
        flex: 1 1 520px;
    }
    .portal-order-editor__summary > span {
        position: relative;
        display: grid;
        gap: 5px;
        min-height: 78px;
        padding: 13px 15px;
        overflow: hidden;
        border: 1px solid var(--portal-card-border);
        border-radius: 15px;
        background: rgba(249, 250, 251, 0.86);
        color: var(--portal-muted);
        font-size: .78rem;
        font-weight: 750;
    }
    .portal-order-editor__summary-icon {
        position: absolute;
        top: 12px;
        right: 13px;
        z-index: 1;
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border: 1px solid color-mix(in srgb, var(--primary) 18%, var(--portal-card-border));
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 7%, var(--portal-card-bg));
        color: color-mix(in srgb, var(--primary) 68%, var(--portal-muted));
    }
    .portal-order-editor__summary-icon svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.1;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    .portal-order-editor__summary > span::after {
        content: '';
        position: absolute;
        right: -18px;
        bottom: -24px;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: color-mix(in srgb, var(--primary) 8%, transparent);
    }
    .portal-order-editor__summary > span::before {
        content: '';
        position: absolute;
        top: 14px;
        right: 15px;
        z-index: 2;
        width: 19px;
        height: 19px;
        background: color-mix(in srgb, var(--primary) 62%, var(--portal-muted));
        -webkit-mask: center / contain no-repeat;
        mask: center / contain no-repeat;
    }
    .portal-order-editor__summary > span:nth-child(1)::before {
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16ZM3.3 7 12 12l8.7-5M12 22V12'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16ZM3.3 7 12 12l8.7-5M12 22V12'/%3E%3C/svg%3E");
    }
    .portal-order-editor__summary > span:nth-child(2)::before {
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='2' stroke-linecap='round' d='M4 9h16M4 15h16M10 3 8 21m8-18-2 18'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='2' stroke-linecap='round' d='M4 9h16M4 15h16M10 3 8 21m8-18-2 18'/%3E%3C/svg%3E");
    }
    .portal-order-editor__summary > span:nth-child(3)::before {
        background: var(--primary);
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M2 10h20M16 15h.01M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M2 10h20M16 15h.01M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z'/%3E%3C/svg%3E");
    }
    .portal-order-editor__summary > span strong {
        position: relative;
        z-index: 1;
        color: var(--portal-text);
        font-size: 1.28rem;
        font-weight: 900;
        line-height: 1.05;
        font-variant-numeric: tabular-nums;
    }
    .portal-order-editor__summary > span:last-child {
        border-color: color-mix(in srgb, var(--primary) 30%, var(--portal-card-border));
        background: color-mix(in srgb, var(--primary) 9%, var(--portal-card-bg));
        color: color-mix(in srgb, var(--primary) 76%, var(--portal-text));
    }
    .portal-order-editor__summary > span:last-child strong {
        color: var(--primary);
        font-size: 1.42rem;
    }
    .portal-order-editor__summary > span:last-child .portal-order-editor__summary-icon {
        border-color: color-mix(in srgb, var(--primary) 30%, transparent);
        background: color-mix(in srgb, var(--primary) 13%, var(--portal-card-bg));
        color: var(--primary);
    }
    [data-theme="dark"] .portal-order-editor__summary > span { background: rgba(30, 41, 59, .72); }
    @media (max-width: 760px) {
        .portal-order-editor__summary { grid-template-columns: 1fr; width: 100%; }
        .portal-order-editor__summary > span { min-height: 70px; }
    }
    .portal-order-builder__add {
        border: 0;
        background: rgba(249, 250, 251, 0.8);
        color: #0f172a;
        font-weight: 800;
    }
    .portal-order-builder__add:not(:disabled):hover { background: rgba(241, 245, 249, 0.95); color: #0f172a; }
    [data-theme="dark"] .portal-order-builder__readonly,
    [data-theme="dark"] .portal-order-builder__head,
    [data-theme="dark"] .portal-order-builder__add { background: rgba(30, 41, 59, 0.7); color: #e2e8f0; }

    /* Mesmo acabamento final dos botões da lista principal de Pedidos. */
    html body .portal-orders-action.portal-orders-action--icon {
        border: 0;
        background: rgba(249, 250, 251, 0.8);
        color: #0f172a;
        box-shadow: none;
    }

    html body .portal-orders-action.portal-orders-action--icon:hover:not([disabled]),
    html body .portal-orders-action.portal-orders-action--icon:focus-visible {
        border: 0;
        background: rgba(249, 250, 251, 0.8);
        color: #0f172a;
        box-shadow: none;
        filter: brightness(.98);
        transform: none;
    }

    [data-theme="dark"] .portal-orders-action.portal-orders-action--icon {
        background: rgba(30, 41, 59, 0.7);
        color: #e2e8f0;
    }

    [data-theme="dark"] .portal-orders-action.portal-orders-action--icon:hover:not([disabled]),
    [data-theme="dark"] .portal-orders-action.portal-orders-action--icon:focus-visible {
        background: rgba(30, 41, 59, 0.7);
        color: #e2e8f0;
    }

    .portal-orders-status {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        border: 0 !important;
        box-shadow: none !important;
    }

    .portal-orders-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .portal-orders-status--novo {
        background: rgba(37, 99, 235, 0.14) !important;
        color: #1e40af !important;
    }

    .portal-orders-status--aprovacao {
        background: rgba(217, 119, 6, 0.14) !important;
        color: #b45309 !important;
    }
    .portal-orders-status--aprovacao::before{width:18px!important;height:18px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:18px 18px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b45309' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 6v6l3.64 1.82'/%3E%3Cpath d='M21.92 13.27A10 10 0 1 0 13.27 21.92'/%3E%3Cpath d='M16 19h6M19 16v6'/%3E%3C/svg%3E")!important}

    .portal-orders-status--producao {
        background: rgba(34, 197, 94, 0.16) !important;
        color: #15803d !important;
    }
    .portal-orders-status--producao::before{width:16px!important;height:16px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:16px 16px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2315803d' stroke-width='2.3' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='m8 12 2.7 2.7L16.5 9'/%3E%3C/svg%3E")!important}

    .portal-orders-status--separacao {
        background: #d1fae5 !important;
        color: #047857 !important;
    }
    .portal-orders-status--separando{background:rgba(168,85,247,.16)!important;color:#7e22ce!important}
    .portal-orders-status--separando::before{width:18px!important;height:18px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:18px 18px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%237e22ce' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m16.5 9.4-9-5.2'/%3E%3Cpath d='M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z'/%3E%3Cpath d='M3.3 7 12 12l8.7-5'/%3E%3Cpath d='M12 22V12'/%3E%3C/svg%3E")!important}
    .portal-orders-status--separacao::before{width:18px!important;height:18px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:18px 18px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2.2 21 7v10l-9 4.8L3 17V7l9-4.8Z' fill='%23047857'/%3E%3Cpath d='M3.7 7.2 12 11.6l8.3-4.4M12 11.6v9.1M8.2 4.3l8.5 4.6' fill='none' stroke='%23a7f3d0' stroke-width='1.35' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='m7.4 15.1 2.7 2.7 6.3-6.2' fill='none' stroke='%23fff' stroke-width='2.33' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E")!important}

    .portal-orders-status--expedicao {
        background: rgba(14, 165, 233, 0.16) !important;
        color: #0369a1 !important;
    }
    .portal-orders-status--expedicao::before{width:18px!important;height:18px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:18px 18px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230369a1' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M10 17h4V5H2v12h3'/%3E%3Cpath d='M14 9h4l4 4v4h-3'/%3E%3Ccircle cx='7.5' cy='17.5' r='2.5'/%3E%3Ccircle cx='16.5' cy='17.5' r='2.5'/%3E%3C/svg%3E")!important}

    .portal-orders-status--faturado {
        background: rgba(22, 163, 74, 0.16) !important;
        color: #15803d !important;
    }
    .portal-orders-status--faturado::before{width:22px!important;height:22px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:22px 22px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 28 26' fill='none' stroke='%2315803d' stroke-width='2.15' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 2.5h12a2 2 0 0 1 2 2v10.2l-5.2 3.1H9V4.5a2 2 0 0 1 2-2Z'/%3E%3Cpath d='M14 2.5v5.8l2-1.3 2 1.3V2.5'/%3E%3Cpath d='m6.8 16 3.8-2c2.2-1.1 4.3 1.5 7.2 1.5h2.1c1.7 0 2.8 1.1 2.8 2.5s-1.1 2.5-2.8 2.5h-5.8'/%3E%3Cpath d='m8 22.3 6 1.3c1.7.4 3.4.1 4.8-.8l7-4.7c1.3-.9 1.5-2.5.6-3.5-.8-.9-2-.9-3.1-.2l-3.4 2.1'/%3E%3Cpath d='m2.2 17 3.7-1.4 3 8-3.7 1.4z'/%3E%3C/svg%3E")!important}
    .portal-orders-status--entregue{background:#dcfce7!important;color:#15803d!important}.portal-orders-status--entregue::before{width:25px!important;height:18px!important;border-radius:0!important;background:transparent center/25px 18px no-repeat!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 22' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cg stroke='%2315803d' stroke-width='1.8'%3E%3Cpath d='M12 4h10v11.5H12z'/%3E%3Cpath d='M22 8h4l4 4v3.5h-2M22 15.5h2'/%3E%3Ccircle cx='15' cy='17' r='1.7'/%3E%3Ccircle cx='26' cy='17' r='1.7'/%3E%3Cpath d='M12 6v7.5'/%3E%3C/g%3E%3Cg fill='%2315803d'%3E%3Crect x='-.05' y='11.85' width='8.45' height='8.45' rx='.9'/%3E%3Crect x='6.92' y='9.52' width='6.76' height='6.76' rx='.75'/%3E%3C/g%3E%3Cg stroke='%23dcfce7' stroke-width='1.1'%3E%3Cpath d='M.7 14.45h6.95M4.18 12.5v7.05'/%3E%3Cpath d='M7.55 11.6h5.5M10.3 10.05v5.45'/%3E%3C/g%3E%3C/svg%3E")!important;transform:scaleX(-1)!important}

    .portal-orders-status--cancelado {
        background: rgba(220, 38, 38, 0.16) !important;
        color: #b91c1c !important;
    }
    .portal-orders-status--revisao{background:rgba(220,38,38,.10)!important;color:#b91c1c!important}
    .portal-orders-status--revisao::before{width:18px!important;height:18px!important;border-radius:0!important;background-color:transparent!important;background-repeat:no-repeat!important;background-position:center!important;background-size:18px 18px!important;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M10.3 3.6 2.2 18a2 2 0 0 0 1.8 3h16a2 2 0 0 0 1.8-3L13.7 3.6a2 2 0 0 0-3.4 0Z'/%3E%3Cpath d='M12 9v4'/%3E%3Cpath d='M12 17h.01'/%3E%3C/svg%3E")!important}
    .portal-orders-status--aprovacao::before,.portal-orders-status--separando::before,.portal-orders-status--separacao::before,.portal-orders-status--expedicao::before,.portal-orders-status--revisao::before{width:16px!important;height:16px!important;background-size:16px 16px!important}
    .portal-orders-status--separacao::before{width:19px!important;height:19px!important;background-size:19px 19px!important}

    .portal-visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .portal-products-card__head {
        margin-bottom: 1.5rem;
    }

    .portal-products-card__head p {
        margin: 0.4rem 0 0;
        color: var(--portal-muted);
        max-width: 560px;
    }

    .portal-products-list {
        display: grid;
        gap: 1.1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .portal-products-list__item {
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 18px;
        padding: 1.1rem 1.3rem;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 38px -32px rgba(15, 23, 42, 0.4);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    [data-theme="dark"] .portal-products-list__item {
        background: rgba(30, 41, 59, 0.78);
        border-color: rgba(71, 85, 105, 0.4);
    }

    .portal-products-list__item h4 {
        margin: 0;
        font-size: 1.05rem;
    }

    .portal-products-list__item dl {
        margin: 0;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.3rem 0.75rem;
        font-size: 0.92rem;
        color: var(--portal-muted);
    }

    .portal-products-list__item dt {
        font-weight: 600;
    }

    .portal-products-list__item dd {
        margin: 0;
    }

    .portal-support-card__list {
        list-style: none;
        margin: 1.15rem 0 1rem;
        padding: 0;
        display: grid;
        gap: 0.72rem;
    }

    .portal-support-card__head {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .portal-support-card__head h3 {
        margin: 0 0 0.42rem;
        color: var(--text, #0f172a);
        font-size: 1.2rem;
    }

    .portal-support-card__head p {
        margin: 0;
        color: var(--portal-muted);
        line-height: 1.48;
    }

    .portal-support-card__icon {
        width: 52px;
        height: 52px;
        display: inline-grid;
        place-items: center;
        border-radius: 17px;
        background: color-mix(in srgb, var(--primary, #2563eb) 12%, #ffffff);
        color: var(--primary, #2563eb);
        box-shadow: 0 18px 36px -30px color-mix(in srgb, var(--primary, #2563eb) 80%, transparent);
    }

    .portal-support-card__icon svg {
        width: 28px;
        height: 28px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .portal-support-card__list li {
        position: relative;
        min-height: 48px;
        display: grid;
        gap: 0.2rem;
        padding: 0.72rem 0.8rem 0.72rem 3.1rem;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 12%, rgba(148, 163, 184, 0.22));
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary, #2563eb) 4%, #ffffff);
        color: var(--portal-muted);
        overflow-wrap: anywhere;
    }

    .portal-support-card__list li::before {
        content: '';
        position: absolute;
        left: 0.82rem;
        top: 0.78rem;
        width: 30px;
        height: 30px;
        border-radius: 11px;
        background: color-mix(in srgb, var(--primary, #2563eb) 14%, #ffffff);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--primary, #2563eb) 18%, transparent);
    }

    .portal-support-card__list li::after {
        position: absolute;
        left: 0.82rem;
        top: 0.78rem;
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        color: var(--primary, #2563eb);
        font-size: 0.78rem;
        font-weight: 950;
    }

    .portal-support-card__list li[data-support-kind="email"]::after { content: '@'; }
    .portal-support-card__list li[data-support-kind="phone"]::after { content: 'T'; }
    .portal-support-card__list li[data-support-kind="whatsapp"]::after { content: 'W'; }
    .portal-support-card__list li:not([data-support-kind])::after { content: 'H'; }

    .portal-support-card__list li[data-support-kind="phone"]::after,
    .portal-support-card__list li[data-support-kind="whatsapp"]::after,
    .portal-support-card__list li[data-support-kind="hours"]::after { content: none; }

    .portal-support-card__item-icon {
        position: absolute;
        left: 0.82rem;
        top: 0.78rem;
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        color: var(--primary, #2563eb);
        z-index: 1;
    }

    .portal-support-card__item-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .portal-support-card__item-icon svg.portal-whatsapp-icon,
    .portal-support-card__action svg.portal-whatsapp-icon {
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .portal-whatsapp-icon .portal-whatsapp-handset {
        fill: currentColor;
        stroke: none;
    }

    .portal-support-card__list strong {
        display: block;
        color: var(--text, #0f172a);
        font-size: 0.76rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .portal-support-card__list a {
        color: var(--text, #0f172a);
        text-decoration: none;
        border-bottom: 1px dashed color-mix(in srgb, var(--primary, #2563eb) 34%, rgba(148, 163, 184, 0.4));
        font-weight: 800;
    }

    .portal-support-card__list a:hover {
        color: var(--primary, #2563eb);
    }

    .portal-support-card__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.62rem;
        margin: 0.3rem 0 1.1rem;
    }

    .portal-support-card__action {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 20%, rgba(148, 163, 184, 0.34));
        border-radius: 999px;
        padding: 0.62rem 0.92rem;
        background: color-mix(in srgb, var(--primary, #2563eb) 6%, #ffffff);
        color: var(--primary, #2563eb);
        text-decoration: none;
        font-weight: 900;
        box-shadow: 0 14px 28px -24px color-mix(in srgb, var(--primary, #2563eb) 72%, transparent);
        gap: 0.45rem;
    }

    .portal-support-card__action svg {
        width: 17px;
        height: 17px;
        flex: 0 0 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .portal-support-card__action--primary {
        background: var(--primary, #2563eb);
        border-color: var(--primary, #2563eb);
        color: #fff;
    }

    .portal-support-card__foot {
        margin: 1rem 0 0;
        padding: 0.9rem 1rem;
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary, #2563eb) 5%, #ffffff);
        color: var(--portal-muted);
        line-height: 1.45;
    }

    html[data-theme="dark"] body .portal-support-card__list li {
        border-color: color-mix(in srgb, var(--primary, #3b82f6) 26%, rgba(148, 163, 184, 0.22));
        background: color-mix(in srgb, var(--primary, #3b82f6) 7%, #111827);
        color: #cbd5e1;
    }

    html[data-theme="dark"] body .portal-support-card__list li::before {
        background: color-mix(in srgb, var(--primary, #3b82f6) 18%, #172033);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--primary, #3b82f6) 38%, transparent);
    }

    html[data-theme="dark"] body .portal-support-card__list strong,
    html[data-theme="dark"] body .portal-support-card__list a {
        color: #f1f5f9;
    }

    html[data-theme="dark"] body .portal-support-card__action:not(.portal-support-card__action--primary) {
        border-color: color-mix(in srgb, var(--primary, #3b82f6) 32%, rgba(148, 163, 184, 0.3));
        background: color-mix(in srgb, var(--primary, #3b82f6) 10%, #111827);
        color: color-mix(in srgb, var(--primary, #3b82f6) 72%, #ffffff);
    }

    html[data-theme="dark"] body .portal-support-card__foot {
        border: 1px solid color-mix(in srgb, var(--primary, #3b82f6) 22%, rgba(148, 163, 184, 0.2));
        background: color-mix(in srgb, var(--primary, #3b82f6) 6%, #111827);
        color: #cbd5e1;
    }

    .portal-cart-card h3 {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    .portal-cart-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .portal-cart-table th,
    .portal-cart-table td {
        text-align: left;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    }

    .portal-cart-table th:last-child,
    .portal-cart-table td:last-child {
        text-align: right;
    }

    .portal-cart-table tfoot td {
        font-weight: 700;
        font-size: 1.05rem;
    }

    .portal-cart-empty {
        text-align: center;
        color: var(--portal-muted);
    }

    body[data-portal-state="app"] [data-portal-hero] {
        display: none;
    }

    body[data-portal-state="app"] #portal-app {
        display: block;
    }

    .portal-floating-support {
        display: none;
        position: fixed;
        right: 18px;
        bottom: 92px;
        width: 56px;
        height: 56px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--primary, #2563eb), color-mix(in srgb, var(--primary, #2563eb) 72%, #ffffff));
        color: #fff;
        box-shadow: 0 22px 46px -20px color-mix(in srgb, var(--primary, #2563eb) 70%, transparent);
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1120;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .portal-floating-support svg {
        width: 25px;
        height: 25px;
        fill: currentColor;
    }

    .portal-floating-support:hover,
    .portal-floating-support:focus-visible {
        transform: translateY(-2px);
        box-shadow: 0 28px 54px -22px color-mix(in srgb, var(--primary, #2563eb) 78%, transparent);
        outline: none;
    }

    body[data-portal-state="app"] .portal-floating-support {
        display: none;
    }

    @media (max-width: 720px) {
        .portal-auth-card {
            padding: 1.6rem 1.7rem;
        }

        .portal-auth-title {
            gap: 0.6rem;
        }

        .portal-auth-title__icon {
            width: 58px;
            height: 58px;
            flex-basis: 58px;
            border-radius: 13px;
        }

        .portal-auth-title__icon svg {
            width: 41px;
            height: 41px;
        }

        .auth-panel {
            padding-right: 1rem;
        }

        .portal-app__header {
            flex-direction: column;
        }

        .portal-app__actions {
            align-self: flex-end;
        }
    }

    .portal-modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(1.5rem, 4vw, 3rem);
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(6px);
        z-index: 1400;
        opacity: 0;
        visibility: hidden;
        transition: opacity 220ms ease, visibility 220ms ease;
    }

    .portal-modal[aria-hidden="false"],
    .portal-modal.portal-modal--open {
        opacity: 1;
        visibility: visible;
    }

    .portal-modal__backdrop {
        position: absolute;
        inset: 0;
    }

    .portal-modal__dialog {
        position: relative;
        max-width: clamp(320px, 96vw, 640px);
        width: 100%;
        max-height: clamp(420px, 92vh, 720px);
        background: var(--portal-card-bg);
        border-radius: 28px;
        border: 1px solid var(--portal-card-border);
        box-shadow: 0 40px 120px -60px rgba(15, 23, 42, 0.55);
        padding: clamp(2rem, 4vw, 2.75rem);
        transform: translateY(24px);
        transition: transform 240ms ease;
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
        overflow: hidden;
        z-index: 1;
    }

    .portal-modal.portal-modal--open .portal-modal__dialog {
        transform: translateY(0);
    }

    .portal-modal__dialog--welcome {
        max-width: 460px;
        gap: 0;
        padding: clamp(1.55rem, 3.6vw, 2.15rem);
        border-radius: 28px;
        background:
            radial-gradient(120% 80% at 0% 0%, color-mix(in srgb, var(--primary, #2563eb) 10%, transparent), transparent 58%),
            var(--portal-card-bg, #ffffff);
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 12%, var(--portal-card-border, rgba(15, 23, 42, 0.08)));
    }

    .portal-welcome {
        display: grid;
        gap: 1rem;
    }

    .portal-welcome__icon {
        width: 64px;
        height: 64px;
        border-radius: 22px;
        background: color-mix(in srgb, var(--primary, #2563eb) 13%, #ffffff);
        display: grid;
        place-items: center;
        color: var(--primary, #2563eb);
        box-shadow: 0 18px 34px -28px color-mix(in srgb, var(--primary, #2563eb) 80%, transparent);
    }

    .portal-welcome__icon svg {
        width: 30px;
        height: 30px;
        fill: currentColor;
    }

    .portal-welcome__tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        width: fit-content;
        padding: 0.36rem 0.82rem;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary, #2563eb) 12%, #ffffff);
        color: var(--primary, #2563eb);
        font-weight: 800;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }

    .portal-welcome h3 {
        margin: 0.25rem 0 0;
        font-size: clamp(1.45rem, 4vw, 1.85rem);
        line-height: 1.05;
        color: var(--text, #0f172a);
    }

    .portal-welcome__lead {
        margin: 0;
        color: var(--portal-muted);
        line-height: 1.55;
        font-size: 0.98rem;
    }

    .portal-welcome__lead strong {
        color: var(--text, #0f172a);
        font-weight: 900;
    }

    .portal-welcome__tips {
        list-style: none;
        margin: 0.1rem 0 0;
        padding: 0;
        display: grid;
        gap: 0.62rem;
    }

    .portal-welcome__tips li {
        position: relative;
        display: block;
        padding: 0.74rem 0.84rem 0.74rem 2.35rem;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 12%, rgba(148, 163, 184, 0.22));
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary, #2563eb) 4%, #ffffff);
        color: var(--portal-muted);
        line-height: 1.34;
        font-weight: 650;
    }

    .portal-welcome__tips li::before {
        content: '';
        position: absolute;
        left: 0.86rem;
        top: 0.92rem;
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--primary, #2563eb);
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--primary, #2563eb) 12%, transparent);
    }

    .portal-welcome__tips strong {
        color: var(--text, #0f172a);
        font-weight: 900;
        white-space: nowrap;
    }

    .portal-welcome__footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 600px) {
        .portal-welcome__footer {
            flex-direction: column;
        }

        .portal-welcome__footer .portal-btn {
            width: 100%;
        }
    }

    .portal-modal__close {
        position: absolute;
        top: 18px;
        right: 18px;
        background: transparent;
        border: none;
        padding: 0.4rem;
        border-radius: 999px;
        cursor: pointer;
        color: var(--portal-muted);
        display: inline-flex;
        transition: background 160ms ease, color 160ms ease;
    }

    .portal-modal__close:hover,
    .portal-modal__close:focus-visible {
        background: rgba(59, 130, 246, 0.08);
        color: rgba(37, 99, 235, 0.95);
    }

    .portal-modal__close svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .portal-modal__icon {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 24px;
        background: rgba(37, 99, 235, 0.12);
        color: rgba(37, 99, 235, 0.9);
        display: grid;
        place-items: center;
        margin-bottom: -0.5rem;
    }

    .portal-modal__icon svg {
        width: 40px;
        height: 40px;
        fill: currentColor;
    }

    .portal-modal__pulse {
        position: absolute;
        inset: 0;
        border-radius: 24px;
        background: currentColor;
        opacity: 0.18;
        animation: portalPulse 1.8s ease infinite;
    }

    @keyframes portalPulse {
        0% { opacity: 0.2; transform: scale(0.94); }
        60% { opacity: 0; transform: scale(1.35); }
        100% { opacity: 0; transform: scale(1.35); }
    }

    .portal-modal__body {
        display: grid;
        gap: 1.4rem;
        flex: 1 1 auto;
        overflow-y: auto;
        padding-right: clamp(0rem, 1vw, 0.4rem);
        margin-right: -0.2rem;
        scrollbar-width: thin;
    }

    .portal-modal__body::-webkit-scrollbar {
        width: 6px;
    }

    .portal-modal__body::-webkit-scrollbar-thumb {
        background: rgba(15, 23, 42, 0.18);
        border-radius: 999px;
    }

    [data-theme="dark"] .portal-modal__body::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35);
    }

    .portal-modal__tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        color: rgba(37, 99, 235, 0.9);
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.02em;
    }

    .portal-modal__lead {
        color: var(--portal-muted);
        line-height: 1.55;
    }

    .portal-modal__meta {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        padding: 1rem;
        border-radius: 18px;
        background: rgba(15, 23, 42, 0.035);
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    [data-theme="dark"] .portal-modal__meta {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .portal-modal__meta-label {
        display: block;
        color: var(--portal-muted);
        font-size: 0.78rem;
        margin-bottom: 0.25rem;
    }

    .portal-modal__section h4 {
        font-size: 1rem;
        margin-bottom: 0.65rem;
    }

    .portal-modal__list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.5rem;
    }

    .portal-modal__list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--portal-surface);
        border-radius: 14px;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    [data-theme="dark"] .portal-modal__list li {
        border-color: rgba(148, 163, 184, 0.14);
    }

    .portal-modal__list span {
        display: block;
    }

    .portal-modal__list small {
        display: block;
        color: var(--portal-muted);
        margin-top: 0.15rem;
        font-size: 0.78rem;
    }

    .portal-modal__list strong {
        font-weight: 600;
    }

    .portal-modal__list-label {
        flex: 1;
        min-width: 0;
        margin-right: 1rem;
    }

    .portal-modal__list-label span {
        font-weight: 600;
    }

    .portal-modal__muted {
        color: var(--portal-muted);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .portal-modal__footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    @media (max-width: 900px) {
        .portal-app__header {
            grid-template-columns: minmax(0, 1fr);
            align-items: stretch;
        }

        .portal-app__header-links,
        .portal-app__profile {
            justify-content: flex-start;
        }

        .portal-app__profile {
            padding-top: 0.5rem;
        }

        .portal-app__header-links {
            flex-wrap: wrap;
        }

        .portal-app__link[data-portal-jump="suporte"] {
            display: none;
        }

        body[data-portal-state="app"] .portal-floating-support {
            display: inline-flex;
        }
    }

    @media (max-width: 1500px) {
        .portal-catalog {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }

    @media (max-width: 1240px) {
        .portal-catalog {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 980px) {
        .portal-catalog {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .portal-modal__dialog {
            padding: 2rem 1.5rem;
            border-radius: 22px;
            max-height: calc(100vh - 2rem);
        }

        .portal-modal__footer {
            flex-direction: column;
        }

        .portal-modal__footer .portal-btn {
            width: 100%;
        }
    }

    @media (max-height: 640px) {
        .portal-modal__dialog {
            max-height: calc(100vh - 2rem);
        }
    }

    .portal-dashboard{display:grid;gap:18px}.portal-dashboard__hero,.portal-dashboard__section{padding:22px;border:1px solid var(--portal-card-border);border-radius:24px;background:var(--portal-card-bg);box-shadow:0 26px 54px -42px rgba(15,23,42,.55)}.portal-dashboard__hero{display:flex;align-items:center;justify-content:space-between;gap:18px;background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 10%,var(--portal-card-bg)),var(--portal-card-bg))}.portal-dashboard__eyebrow{display:flex;align-items:center;gap:7px;color:var(--primary);font-size:.76rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em}.portal-dashboard__hero h3{margin:6px 0;font-size:1.55rem}.portal-dashboard__hero p,.portal-dashboard__section-head p{margin:0;color:var(--portal-muted)}.portal-dashboard__refresh{display:inline-flex;align-items:center;gap:7px}.portal-dashboard__refresh svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:2}.portal-dashboard__loading,.portal-dashboard__empty{padding:34px;text-align:center;color:var(--portal-muted);border:1px dashed var(--portal-card-border);border-radius:20px}.portal-dashboard__periods{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.portal-dashboard__metric{position:relative;overflow:hidden;padding:17px;border:1px solid var(--portal-card-border);border-radius:19px;background:var(--portal-card-bg)}.portal-dashboard__metric::after{content:'';position:absolute;width:72px;height:72px;right:-28px;top:-30px;border-radius:50%;background:color-mix(in srgb,var(--primary) 10%,transparent)}.portal-dashboard__metric-label{display:flex;align-items:center;justify-content:space-between;gap:8px;color:var(--portal-muted);font-size:.78rem;font-weight:850;text-transform:uppercase}.portal-dashboard__metric strong{display:block;margin:11px 0 4px;font-size:1.42rem}.portal-dashboard__metric small{color:var(--portal-muted)}.portal-dashboard__delta{display:inline-flex;align-items:center;gap:4px;padding:4px 7px;border-radius:999px;font-size:.72rem;font-weight:900}.portal-dashboard__delta.up{color:#15803d;background:#dcfce7}.portal-dashboard__delta.down{color:#b91c1c;background:#fee2e2}.portal-dashboard__delta.neutral{color:#64748b;background:#e2e8f0}.portal-dashboard__grid{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(300px,.75fr);gap:18px}.portal-dashboard__section-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:18px}.portal-dashboard__section-head h4{margin:0 0 4px;font-size:1.05rem}.portal-dashboard__summary{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px}.portal-dashboard__summary div{padding:12px;border-radius:14px;background:color-mix(in srgb,var(--primary) 5%,var(--portal-card-bg));border:1px solid color-mix(in srgb,var(--primary) 14%,var(--portal-card-border))}.portal-dashboard__summary span{display:block;color:var(--portal-muted);font-size:.72rem;text-transform:uppercase;font-weight:850}.portal-dashboard__summary strong{display:block;margin-top:5px;font-size:1.05rem}.portal-dashboard__bars{height:210px;display:flex;align-items:flex-end;gap:9px;padding-top:22px}.portal-dashboard__bar-item{flex:1;min-width:0;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:7px}.portal-dashboard__bar-value{font-size:.68rem;font-weight:850;color:var(--portal-muted)}.portal-dashboard__bar{width:min(34px,72%);min-height:5px;border-radius:9px 9px 4px 4px;background:linear-gradient(180deg,color-mix(in srgb,var(--primary) 72%,#fff),var(--primary));box-shadow:0 12px 24px -16px var(--primary);transition:height .25s ease}.portal-dashboard__bar-label{font-size:.68rem;color:var(--portal-muted);text-transform:uppercase}.portal-dashboard__donut-wrap{display:grid;place-items:center;gap:14px}.portal-dashboard__donut{--value:0;width:160px;height:160px;display:grid;place-items:center;border-radius:50%;background:conic-gradient(var(--primary) calc(var(--value)*1%),color-mix(in srgb,var(--primary) 12%,var(--portal-card-border)) 0);position:relative}.portal-dashboard__donut::before{content:'';position:absolute;inset:17px;border-radius:50%;background:var(--portal-card-bg)}.portal-dashboard__donut-content{position:relative;text-align:center}.portal-dashboard__donut-content strong{display:block;font-size:1.45rem}.portal-dashboard__donut-content span{color:var(--portal-muted);font-size:.72rem}.portal-dashboard__ranking{display:grid;gap:10px}.portal-dashboard__product{display:grid;grid-template-columns:32px minmax(0,1fr) auto;gap:11px;align-items:center;padding:11px;border:1px solid var(--portal-card-border);border-radius:14px}.portal-dashboard__rank{width:30px;height:30px;display:grid;place-items:center;border-radius:10px;background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary);font-weight:950}.portal-dashboard__product-name{min-width:0}.portal-dashboard__product-name strong,.portal-dashboard__product-name small{display:block}.portal-dashboard__product-name strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.portal-dashboard__product-name small{margin-top:3px;color:var(--portal-muted)}.portal-dashboard__product-total{text-align:right}.portal-dashboard__product-total strong,.portal-dashboard__product-total small{display:block}.portal-dashboard__product-total small{margin-top:3px;color:var(--portal-muted)}html[data-theme="dark"] body .portal-dashboard__delta.up{background:rgba(22,163,74,.2);color:#86efac}html[data-theme="dark"] body .portal-dashboard__delta.down{background:rgba(220,38,38,.2);color:#fca5a5}html[data-theme="dark"] body .portal-dashboard__delta.neutral{background:rgba(100,116,139,.24);color:#cbd5e1}@media(max-width:1050px){.portal-dashboard__periods{grid-template-columns:repeat(2,1fr)}.portal-dashboard__grid{grid-template-columns:1fr}}@media(max-width:620px){.portal-dashboard__hero{align-items:flex-start;flex-direction:column}.portal-dashboard__periods,.portal-dashboard__summary{grid-template-columns:1fr}.portal-dashboard__hero,.portal-dashboard__section{padding:16px}.portal-dashboard__bars{gap:4px}.portal-dashboard__bar-value{display:none}}
    .portal-dashboard__pagination{display:grid;grid-template-columns:minmax(120px,1fr) auto minmax(250px,1fr);align-items:center;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid var(--portal-card-border);color:var(--portal-muted);font-size:.82rem;font-weight:800}.portal-dashboard__pagination-pages,.portal-dashboard__pagination-size{display:flex;align-items:center;justify-content:center;gap:8px;white-space:nowrap}.portal-dashboard__pagination-size{justify-content:flex-end}.portal-dashboard__page-btn,.portal-dashboard__page-input,.portal-dashboard__page-size{height:38px;min-height:38px;border:1px solid var(--portal-card-border);border-radius:999px;background:var(--portal-card-bg);color:var(--portal-text);font-weight:850}.portal-dashboard__page-btn{display:grid;place-items:center;min-width:38px;padding:7px 12px;cursor:pointer}.portal-dashboard__page-btn:hover{border-color:var(--primary);color:var(--primary)}.portal-dashboard__page-btn.active{border-color:var(--primary);background:var(--primary);color:#fff}.portal-dashboard__page-input{width:68px;padding:7px 10px;text-align:center}.portal-dashboard__page-size{box-sizing:border-box;width:calc(3ch + 42px);min-width:calc(3ch + 42px);max-width:calc(3ch + 42px);padding:7px 30px 7px 11px}.portal-dashboard__page-ellipsis{min-width:22px;text-align:center}@media(max-width:820px){.portal-dashboard__pagination{grid-template-columns:1fr}.portal-dashboard__pagination-pages,.portal-dashboard__pagination-size{justify-content:flex-start;flex-wrap:wrap}}
    .portal-dashboard__ranking-title,.portal-dashboard__evolution-title{display:flex;align-items:center;gap:11px}.portal-dashboard__ranking-title-icon,.portal-dashboard__evolution-title-icon{display:inline-grid;place-items:center;width:42px;height:42px;flex:0 0 42px;border:1px solid color-mix(in srgb,var(--primary) 28%,transparent);border-radius:14px;background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)}.portal-dashboard__ranking-title-icon svg,.portal-dashboard__evolution-title-icon svg{width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
    .portal-dashboard__bar-delta{display:inline-flex;align-items:center;justify-content:center;min-width:42px;padding:3px 6px;border-radius:999px;font-size:.62rem;font-weight:900;line-height:1}.portal-dashboard__bar-delta.up{color:#15803d;background:#dcfce7}.portal-dashboard__bar-delta.down{color:#b91c1c;background:#fee2e2}.portal-dashboard__bar-delta.neutral{color:#64748b;background:#e2e8f0}html[data-theme="dark"] body .portal-dashboard__bar-delta.up{color:#86efac;background:rgba(22,163,74,.2)}html[data-theme="dark"] body .portal-dashboard__bar-delta.down{color:#fca5a5;background:rgba(220,38,38,.2)}html[data-theme="dark"] body .portal-dashboard__bar-delta.neutral{color:#cbd5e1;background:rgba(100,116,139,.24)}
    .portal-dashboard__evolution-nav{display:inline-flex;align-items:center;gap:5px;padding:4px;border:1px solid var(--portal-card-border);border-radius:999px;background:var(--portal-card-bg)}.portal-dashboard__evolution-nav button{display:grid;place-items:center;width:32px;height:32px;padding:0;border:0;border-radius:50%;background:transparent;color:var(--portal-text);cursor:pointer}.portal-dashboard__evolution-nav button:hover{background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)}.portal-dashboard__evolution-nav svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.3;stroke-linecap:round;stroke-linejoin:round}.portal-dashboard__evolution-nav strong{min-width:72px;text-align:center;color:var(--portal-text);font-size:.72rem;text-transform:uppercase}@media(max-width:620px){.portal-dashboard__evolution-nav{margin-top:8px}.portal-dashboard__bars{overflow-x:auto}.portal-dashboard__bar-item{min-width:42px}}
    .portal-dashboard__summary>div{display:grid;grid-template-columns:38px minmax(0,1fr);grid-template-rows:auto auto;column-gap:10px;align-items:center}.portal-dashboard__summary>div>span:not(.portal-dashboard__summary-icon),.portal-dashboard__summary>div>strong{grid-column:2}.portal-dashboard__summary-icon{grid-column:1;grid-row:1/3;display:grid!important;place-items:center;width:38px;height:38px;margin:0!important;border:1px solid color-mix(in srgb,var(--primary) 26%,transparent);border-radius:12px;background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary)!important}.portal-dashboard__summary-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2.1;stroke-linecap:round;stroke-linejoin:round}
    .portal-dashboard__chart-tooltip{position:fixed;z-index:2200;width:min(330px,calc(100vw - 24px));padding:14px;border:1px solid var(--portal-card-border);border-radius:16px;background:var(--portal-card-bg);color:var(--portal-text);box-shadow:0 22px 55px rgba(15,23,42,.22);pointer-events:none;opacity:0;visibility:hidden;transform:translateY(6px);transition:opacity .14s ease,transform .14s ease,visibility .14s ease}.portal-dashboard__chart-tooltip.is-visible{opacity:1;visibility:visible;transform:translateY(0)}.portal-dashboard__tooltip-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:11px}.portal-dashboard__tooltip-head strong{font-size:.88rem;text-transform:capitalize}.portal-dashboard__tooltip-status{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border-radius:999px;font-size:.67rem;font-weight:900}.portal-dashboard__tooltip-status.up{color:#15803d;background:#dcfce7}.portal-dashboard__tooltip-status.down{color:#b91c1c;background:#fee2e2}.portal-dashboard__tooltip-status.neutral{color:#64748b;background:#e2e8f0}.portal-dashboard__tooltip-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.portal-dashboard__tooltip-item{padding:9px;border:1px solid var(--portal-card-border);border-radius:11px;background:color-mix(in srgb,var(--primary) 4%,var(--portal-card-bg))}.portal-dashboard__tooltip-item span{display:block;color:var(--portal-muted);font-size:.63rem;font-weight:800;text-transform:uppercase}.portal-dashboard__tooltip-item strong{display:block;margin-top:4px;font-size:.82rem}.portal-dashboard__tooltip-item.positive strong{color:#15803d}.portal-dashboard__tooltip-item.negative strong{color:#b91c1c}html[data-theme="dark"] body .portal-dashboard__tooltip-status.up{color:#86efac;background:rgba(22,163,74,.2)}html[data-theme="dark"] body .portal-dashboard__tooltip-status.down{color:#fca5a5;background:rgba(220,38,38,.2)}html[data-theme="dark"] body .portal-dashboard__tooltip-status.neutral{color:#cbd5e1;background:rgba(100,116,139,.24)}
    .portal-dashboard__bars.is-daily{justify-content:flex-start;overflow-x:auto;overscroll-behavior-inline:contain;scrollbar-width:thin;scrollbar-color:color-mix(in srgb,var(--primary) 38%,#fff) color-mix(in srgb,var(--primary) 7%,#fff);padding-bottom:8px}.portal-dashboard__bars.is-daily::-webkit-scrollbar{height:9px}.portal-dashboard__bars.is-daily::-webkit-scrollbar-track{border-radius:999px;background:color-mix(in srgb,var(--primary) 7%,#fff)}.portal-dashboard__bars.is-daily::-webkit-scrollbar-thumb{border:2px solid color-mix(in srgb,var(--primary) 7%,#fff);border-radius:999px;background:color-mix(in srgb,var(--primary) 38%,#fff)}.portal-dashboard__bars.is-daily::-webkit-scrollbar-thumb:hover{background:color-mix(in srgb,var(--primary) 58%,#fff)}.portal-dashboard__bars.is-daily .portal-dashboard__bar-item{flex:0 0 54px;min-width:54px}.portal-dashboard__bars.is-daily .portal-dashboard__bar-label{font-size:.64rem}
    .portal-dashboard__tooltip-metric{display:flex!important;align-items:center;justify-content:space-between;gap:6px}.portal-dashboard__tooltip-metric-delta{display:inline-flex;padding:3px 5px;border-radius:999px;font-size:.6rem;font-weight:900}.portal-dashboard__tooltip-metric-delta.up{color:#15803d;background:#dcfce7}.portal-dashboard__tooltip-metric-delta.down{color:#b91c1c;background:#fee2e2}.portal-dashboard__tooltip-metric-delta.neutral{color:#64748b;background:#e2e8f0}
    .portal-dashboard__hero-actions{display:flex;align-items:center;justify-content:flex-end;gap:9px;flex-wrap:wrap}.portal-dashboard__order-btn{display:inline-flex;align-items:center;gap:8px}.portal-dashboard__order-btn svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2.1;stroke-linecap:round;stroke-linejoin:round}@media(max-width:620px){.portal-dashboard__hero-actions{width:100%;display:grid;grid-template-columns:1fr 1fr}.portal-dashboard__hero-actions .portal-btn{justify-content:center;width:100%}}
    .portal-dashboard__product-nav-group{display:flex;align-items:center;justify-content:flex-end;gap:7px;flex-wrap:wrap}.portal-dashboard__product-nav-group .portal-dashboard__evolution-nav strong{min-width:68px}.portal-dashboard__product-nav-group .portal-dashboard__product-month-nav strong{min-width:78px}@media(max-width:720px){.portal-dashboard__product-nav-group{width:100%;justify-content:flex-start}}
    .portal-dashboard__delta.portal-dashboard__evolution-average{min-height:32px;padding:5px 9px;font-size:.907rem;line-height:1}
    .portal-dashboard__empty>strong,.portal-dashboard__empty>span{display:block}.portal-dashboard__empty>strong{margin-bottom:6px;color:var(--portal-text);font-size:1rem}.portal-dashboard__empty>.portal-btn{display:inline-flex;margin-top:14px}
    .portal-dashboard__product-mode{display:inline-flex;align-items:center;gap:4px;padding:4px;border:1px solid var(--portal-card-border);border-radius:999px;background:var(--portal-card-bg)}.portal-dashboard__product-mode button{min-height:32px;padding:6px 12px;border:0;border-radius:999px;background:transparent;color:var(--portal-muted);font-size:.72rem;font-weight:900;cursor:pointer}.portal-dashboard__product-mode button.is-active{background:var(--primary);color:#fff;box-shadow:0 10px 22px -16px var(--primary)}
    .portal-dashboard__product-delta{display:inline-flex!important;align-items:center;justify-content:center;width:max-content;margin:4px 0 0 auto!important;padding:4px 7px;border-radius:999px;font-size:.66rem!important;font-weight:900}.portal-dashboard__product-delta.up{color:#15803d!important;background:#dcfce7}.portal-dashboard__product-delta.down{color:#b91c1c!important;background:#fee2e2}.portal-dashboard__product-delta.neutral{color:#64748b!important;background:#e2e8f0}html[data-theme="dark"] body .portal-dashboard__product-delta.up{color:#86efac!important;background:rgba(22,163,74,.2)}html[data-theme="dark"] body .portal-dashboard__product-delta.down{color:#fca5a5!important;background:rgba(220,38,38,.2)}html[data-theme="dark"] body .portal-dashboard__product-delta.neutral{color:#cbd5e1!important;background:rgba(100,116,139,.24)}
    @media(max-width:620px){
        html body #portal-tab-dashboard,html body .portal-dashboard,html body .portal-dashboard>*{box-sizing:border-box;min-width:0;max-width:100%}
        html body .portal-dashboard{gap:12px;width:100%;overflow:hidden}
        html body .portal-dashboard__hero,html body .portal-dashboard__section{width:100%;padding:15px;border-radius:18px;overflow:hidden}
        html body .portal-dashboard__hero{gap:14px}
        html body .portal-dashboard__hero h3{font-size:1.28rem;line-height:1.18}
        html body .portal-dashboard__hero p{font-size:.82rem;line-height:1.4}
        html body .portal-dashboard__hero-actions{grid-template-columns:1fr;width:100%;gap:7px}
        html body .portal-dashboard__hero-actions .portal-btn{min-width:0;min-height:42px;padding-inline:10px}
        html body .portal-dashboard__periods{grid-template-columns:1fr;gap:8px}
        html body .portal-dashboard__metric{padding:14px;border-radius:15px}
        html body .portal-dashboard__metric strong{font-size:1.22rem}
        html body .portal-dashboard__grid{grid-template-columns:minmax(0,1fr);gap:12px}
        html body .portal-dashboard__section-head{flex-direction:column;align-items:stretch;gap:10px;margin-bottom:14px}
        html body .portal-dashboard__section-head>div:first-child{min-width:0}
        html body .portal-dashboard__ranking-title,html body .portal-dashboard__evolution-title{gap:9px;font-size:.98rem;line-height:1.25}
        html body .portal-dashboard__ranking-title-icon,html body .portal-dashboard__evolution-title-icon{width:38px;height:38px;flex-basis:38px;border-radius:12px}
        html body .portal-dashboard__delta.portal-dashboard__evolution-average{align-self:flex-start;max-width:100%;white-space:normal;font-size:.78rem}
        html body .portal-dashboard__evolution-nav{align-self:flex-start;max-width:100%}
        html body .portal-dashboard__summary{grid-template-columns:1fr;gap:7px}
        html body .portal-dashboard__summary>div{width:100%;padding:11px}
        html body .portal-dashboard__bars{width:100%;height:220px;overflow-x:auto;overflow-y:hidden;padding-bottom:10px}
        html body .portal-dashboard__bars .portal-dashboard__bar-item{flex:0 0 52px;min-width:52px}
        html body .portal-dashboard__donut{width:142px;height:142px}
        html body .portal-dashboard__product-nav-group{display:grid;grid-template-columns:1fr;width:100%;gap:7px}
        html body .portal-dashboard__product-mode{display:grid;grid-template-columns:1fr 1fr;width:100%}
        html body .portal-dashboard__product-year-nav,html body .portal-dashboard__product-month-nav{display:grid;grid-template-columns:32px minmax(0,1fr) 32px;width:100%;box-sizing:border-box}
        html body .portal-dashboard__product-nav-group .portal-dashboard__evolution-nav strong{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        html body .portal-dashboard__product{grid-template-columns:32px minmax(0,1fr);gap:9px;padding:10px}
        html body .portal-dashboard__product-name strong{white-space:normal;overflow-wrap:anywhere;line-height:1.2}
        html body .portal-dashboard__product-total{grid-column:2;display:flex;align-items:center;justify-content:space-between;gap:8px;text-align:left}
        html body .portal-dashboard__product-delta{margin:0!important;flex:0 0 auto}
        html body .portal-dashboard__pagination{grid-template-columns:1fr;gap:10px;overflow:visible}
        html body .portal-dashboard__pagination-pages,html body .portal-dashboard__pagination-size{justify-content:flex-start;max-width:100%;overflow-x:auto;padding-bottom:3px}
        html body .portal-dashboard__pagination>span{font-size:.76rem}
        html body .portal-dashboard__chart-tooltip{width:calc(100vw - 20px);padding:11px;border-radius:14px}
        html body .portal-dashboard__tooltip-grid{grid-template-columns:1fr;gap:6px}
        html body .portal-dashboard__tooltip-head{align-items:flex-start;flex-direction:column}
    }
.portal-order-detail{background:#fff;border:1px solid #e5e7eb;border-radius:24px;padding:28px;box-shadow:0 18px 45px rgba(15,23,42,.08);color:#172033}.portal-order-detail__head{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;margin-bottom:24px}.portal-order-detail__head h3{margin:0 0 6px;font-size:25px}.portal-order-detail__head p{margin:0;color:#64748b}.portal-order-detail__actions{display:flex;gap:10px;flex-wrap:wrap}.portal-order-detail__actions svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2}.portal-order-detail__summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:22px}.portal-order-detail__summary div,.portal-order-detail__card{border:1px solid #e5e7eb;border-radius:16px;background:#f8fafc;padding:16px}.portal-order-detail__summary span{display:block;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;margin-bottom:7px}.portal-order-detail__summary strong{font-size:16px}.portal-order-detail__grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(280px,.8fr);gap:18px}.portal-order-detail__card{background:#fff}.portal-order-detail__card h4{margin:0 0 14px;font-size:17px}.portal-order-detail__table{width:100%;border-collapse:collapse}.portal-order-detail__table th,.portal-order-detail__table td{text-align:left;padding:11px 8px;border-bottom:1px solid #edf0f4}.portal-order-detail__table th{font-size:12px;color:#64748b;text-transform:uppercase}.portal-order-detail__table td:last-child,.portal-order-detail__table th:last-child{text-align:right}.portal-order-detail__manifest dl{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:0}.portal-order-detail__manifest dt{color:#64748b;font-size:12px}.portal-order-detail__manifest dd{margin:3px 0 0;font-weight:700}.portal-order-history{margin-top:18px}.portal-order-history__item{display:grid;grid-template-columns:12px 1fr;gap:12px;padding:10px 0}.portal-order-history__dot{width:10px;height:10px;border-radius:50%;background:var(--portal-primary,#2563eb);margin-top:6px}.portal-order-history__item strong{display:block}.portal-order-history__item small{color:#64748b}.portal-order-history__reason{margin:5px 0 0;color:#334155}.portal-order-detail__empty{color:#64748b;padding:8px 0}.portal-orders-action--icon svg{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}@media(max-width:800px){.portal-order-detail{padding:18px;border-radius:18px}.portal-order-detail__head{display:block}.portal-order-detail__actions{margin-top:16px}.portal-order-detail__summary{grid-template-columns:1fr 1fr}.portal-order-detail__grid{grid-template-columns:1fr}.portal-order-detail__table{font-size:13px}}@media print{body *{visibility:hidden!important}.portal-order-detail,.portal-order-detail *{visibility:visible!important}.portal-order-detail{position:absolute;inset:0;width:100%;box-shadow:none;border:0;padding:0}.portal-order-detail__actions{display:none!important}.portal-order-detail__grid{grid-template-columns:1fr}.portal-order-detail__card{break-inside:avoid}}
    .portal-order-editor__remove{width:38px;height:38px;padding:0;border:1px solid color-mix(in srgb,#dc2626 22%,transparent);border-radius:50%;background:color-mix(in srgb,#dc2626 7%,var(--portal-card-bg));color:#dc2626;transition:transform .18s ease,background .18s ease,border-color .18s ease,box-shadow .18s ease}
    .portal-order-editor__remove:hover{transform:translateY(-1px);border-color:color-mix(in srgb,#dc2626 42%,transparent);background:color-mix(in srgb,#dc2626 13%,var(--portal-card-bg));box-shadow:0 8px 18px -12px rgba(220,38,38,.8)}
    .portal-order-editor__remove:focus-visible{outline:3px solid color-mix(in srgb,#dc2626 22%,transparent);outline-offset:2px}
    .portal-order-editor__remove svg{width:18px;height:18px;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
</style>
<?php
$catalogoPayload = json_encode(array_map(static function ($produto) use ($asset) {
    return [
        'id' => (int) ($produto['id'] ?? 0),
        'nome' => $produto['nome'] ?? '',
        'sku' => $produto['sku'] ?? '',
        'preco' => (float) ($produto['preco'] ?? 0),
        'unidade' => $produto['unidade'] ?? '',
        'peso_liquido' => (float) ($produto['peso_liquido'] ?? 0),
        'shelf_life_dias' => (int) ($produto['shelf_life_dias'] ?? 0),
        'categoria' => $produto['categoria'] ?? '',
        'tipo' => $produto['tipo'] ?? 'PA',
        'imagem_url' => !empty($produto['imagem_path']) ? $asset((string)$produto['imagem_path']) : '',
        'imagem_pronto_url' => !empty($produto['imagem_pronto_path']) ? $asset((string)$produto['imagem_pronto_path']) : '',
    ];
}, $catalogo ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<script>
    const catalogo = <?= $catalogoPayload ?>;
    const catalogoCompleto = catalogo.slice();
    const portalClientGroups = <?= json_encode(array_values($portalClientGroups ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const basePath = <?= json_encode($basePath ?: '') ?>;

    const state = {
        token: localStorage.getItem('portal-token') || '',
        cliente: null,
    };

    const loginForm = document.getElementById('portal-login-form');
    const registerForm = document.getElementById('portal-register-form');
    const authCard = document.querySelector('[data-auth-card]');
    const portalHero = document.querySelector('[data-portal-hero]');
    const portalAppSection = document.getElementById('portal-app');
    const loginFeedback = document.getElementById('portal-login-feedback');
    const registerFeedback = document.getElementById('portal-register-feedback');
    const catalogContainer = document.getElementById('portal-catalog');
    const catalogCount = document.querySelector('[data-catalog-count]');
    const cartLines = document.getElementById('portal-cart-lines');
    const cartTotal = document.getElementById('portal-cart-total');
    const orderFeedback = document.getElementById('portal-order-feedback');
    const welcomeName = document.querySelector('[data-cliente-nome]');
    const clienteBadge = document.querySelector('[data-cliente-badge]');
    const logoutButton = document.getElementById('portal-logout');
    const cepInput = document.getElementById('registro-cep');
    const cepStatus = document.querySelector('[data-cep-feedback]');
    const estadoInput = document.getElementById('registro-estado');
    const authToggleButtons = document.querySelectorAll('[data-auth-target]');
    const authSwitchLinks = document.querySelectorAll('[data-auth-switch]');
    const docButtons = document.querySelectorAll('[data-doc-option]');
    const docFields = document.querySelectorAll('[data-doc-field]');
    const viewToggleButtons = document.querySelectorAll('[data-catalog-view]');
    const portalTabButtons = document.querySelectorAll('[data-portal-tab-trigger]');
    const portalTabPanels = document.querySelectorAll('[data-portal-tab-panel]');
    const portalHeaderJumps = document.querySelectorAll('[data-portal-jump]');
    const ordersTableBody = document.getElementById('portal-orders-body');
    const dashboardContainer = document.querySelector('[data-portal-dashboard]');
    const ordersFeedback = document.getElementById('portal-orders-feedback');
    const ordersRefreshButton = document.getElementById('portal-orders-refresh');
    const orderEditor = document.querySelector('[data-portal-order-editor]');
    const orderDetail = document.querySelector('[data-portal-order-detail]');
    const productsListContainer = document.getElementById('portal-products-list');
    const imageLightbox = document.getElementById('portal-image-lightbox');
    const imageLightboxImg = imageLightbox?.querySelector('img');
    const imageLightboxCaption = imageLightbox?.querySelector('.portal-image-lightbox__caption');
    const imageLightboxClose = imageLightbox?.querySelector('[data-lightbox-close]');
    const imageLightboxPrev = imageLightbox?.querySelector('[data-lightbox-prev]');
    const imageLightboxNext = imageLightbox?.querySelector('[data-lightbox-next]');
    let imageLightboxGallery = [];
    let imageLightboxIndex = 0;
    const cart = [];
    let catalogView = localStorage.getItem('portal-catalog-view') === 'list' ? 'list' : 'grid';
    const welcomeModal = document.getElementById('portal-welcome-modal');
    const welcomeModalName = welcomeModal?.querySelector('[data-welcome-name]');
    const welcomeDismissTriggers = welcomeModal ? welcomeModal.querySelectorAll('[data-welcome-dismiss]') : [];
    const welcomeGoButtons = welcomeModal ? welcomeModal.querySelectorAll('[data-welcome-go]') : [];
    const orderModal = document.getElementById('order-success-modal');
    const orderModalCode = orderModal?.querySelector('[data-order-code]');
    const orderModalDate = orderModal?.querySelector('[data-order-date]');
    const orderModalTotal = orderModal?.querySelector('[data-order-total]');
    const orderModalItems = orderModal?.querySelector('[data-order-items]');
    const orderModalBackorder = orderModal?.querySelector('[data-order-backorder]');
    const orderModalBackorderList = orderModal?.querySelector('[data-order-backorder-list]');
    const orderModalLead = orderModal?.querySelector('.portal-modal__lead');
    const orderModalTag = orderModal?.querySelector('.portal-modal__tag');
    const orderModalTitle = orderModal?.querySelector('#order-success-title');
    const modalDismissTriggers = orderModal ? orderModal.querySelectorAll('[data-modal-dismiss]') : [];
    let lastFocusedElement = null;
    let activeTab = 'dashboard';
    let catalogNeedsRender = true;
    let pedidosCarregados = false;
    let pedidosAtualizando = false;
    let pedidosSignature = '';
    let dashboardCarregado = false;
    let dashboardAtualizando = false;
    let dashboardOrdersCache = [];
    let dashboardProductPage = 1;
    let dashboardProductPerPage = 9;
    let dashboardEvolutionMode = 0;
    let dashboardProductMonthOffset = 0;
    let dashboardProductPeriodMode = 'month';

    const formatCurrency = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
    const formatWeight = (value) => {
        const numeric = Number(value || 0);
        if (!numeric) {
            return '';
        }
        if (numeric < 1) {
            return `${(numeric * 1000).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })} g`;
        }
        return `${numeric.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} kg`;
    };
    const formatOrderCode = (codigo, id) => {
        if (typeof codigo === 'string' && codigo.trim() !== '') {
            const trimmed = codigo.trim();
            if (/^#/.test(trimmed) || /[A-Za-z]/.test(trimmed)) {
                return trimmed;
            }
            if (/^\d+$/.test(trimmed)) {
                return `#${trimmed.padStart(4, '0')}`;
            }
            return trimmed;
        }
        const numericId = Number(id);
        if (Number.isFinite(numericId) && numericId > 0) {
            return `#${String(numericId).padStart(4, '0')}`;
        }
        return '—';
    };
    const quantityFormatter = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 3 });
    const integerQuantityFormatter = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    const formatQuantity = (value) => {
        const numeric = Number(value) || 0;
        return Number.isInteger(numeric) ? integerQuantityFormatter.format(numeric) : quantityFormatter.format(numeric);
    };

    let welcomeFocusReturn = null;

    const closeWelcomeModal = () => {
        if (!welcomeModal) return;
        welcomeModal.classList.remove('portal-modal--open');
        welcomeModal.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (welcomeModal.getAttribute('aria-hidden') === 'true') {
                welcomeModal.setAttribute('hidden', '');
            }
        }, 200);
        if (welcomeFocusReturn && typeof welcomeFocusReturn.focus === 'function') {
            try {
                welcomeFocusReturn.focus({ preventScroll: true });
            } catch (focusError) {
                welcomeFocusReturn.focus();
            }
        }
        welcomeFocusReturn = null;
    };

    const openWelcomeModal = (cliente) => {
        if (!welcomeModal) return;
        const displayName = cliente && (cliente.nome_fantasia || cliente.razao_social || cliente.nome)
            ? (cliente.nome_fantasia || cliente.razao_social || cliente.nome)
            : 'Cliente';
        if (welcomeModalName) {
            welcomeModalName.textContent = displayName;
        }
        welcomeFocusReturn = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        welcomeModal.removeAttribute('hidden');
        requestAnimationFrame(() => {
            welcomeModal.classList.add('portal-modal--open');
            welcomeModal.setAttribute('aria-hidden', 'false');
            const primary = welcomeModal.querySelector('[data-welcome-go]');
            if (primary && typeof primary.focus === 'function') {
                try {
                    primary.focus({ preventScroll: true });
                } catch (focusError) {
                    primary.focus();
                }
            }
        });
    };

    const formatDateTime = (value) => {
        if (!value) {
            return '-';
        }
        const normalised = typeof value === 'string' ? value.replace(' ', 'T') : value;
        const parsed = new Date(normalised);
        if (Number.isNaN(parsed.getTime())) {
            return value;
        }
        return parsed.toLocaleString('pt-BR', {
            dateStyle: 'short',
            timeStyle: 'short',
        });
    };

    const closeOrderModal = (focusTarget) => {
        if (!orderModal) return;
        orderModal.classList.remove('portal-modal--open');
        orderModal.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (orderModal.getAttribute('aria-hidden') === 'true') {
                orderModal.setAttribute('hidden', '');
            }
        }, 240);
        const target = focusTarget
            ? (typeof focusTarget === 'string' ? document.querySelector(focusTarget) : focusTarget)
            : lastFocusedElement;
        if (target && typeof target.focus === 'function') {
            try {
                target.focus({ preventScroll: false });
            } catch (focusError) {
                target.focus();
            }
        }
        lastFocusedElement = null;
    };

    const showOrderSuccessModal = (summary) => {
        if (!orderModal) return;
        lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        const pedidoId = summary.pedidoId ?? summary.pedido_id ?? null;
        const statusKey = String(summary.status || '').toLowerCase();
        const resolvedStatusLabel = summary.statusLabel || orderStatusLabels[statusKey] || '';
        const createdAtValue = summary.createdAt
            || summary.created_at
            || summary.dataCriacao
            || summary.created
            || summary.data
            || null;

        if (orderModalCode) {
            orderModalCode.textContent = formatOrderCode(summary.codigo, pedidoId);
        }

        if (orderModalTag) {
            orderModalTag.textContent = resolvedStatusLabel || 'Pedido enviado';
        }

        if (orderModalTitle) {
            const resolvedTitle = summary.title
                || (statusKey === 'faturado'
                    ? 'Pedido concluído!'
                    : statusKey === 'cancelado'
                        ? 'Pedido cancelado'
                        : (statusKey === 'aprovacao' || statusKey === 'novo')
                            ? 'Recebemos seu pedido!'
                            : 'Acompanhe seu pedido!');
            orderModalTitle.textContent = resolvedTitle;
        }

        if (orderModalDate) {
            const fallbackDate = new Date();
            orderModalDate.textContent = createdAtValue
                ? formatDateTime(createdAtValue)
                : formatDateTime(fallbackDate);
        }

        if (orderModalTotal) {
            const totalValue = Number(summary.total ?? 0);
            orderModalTotal.textContent = formatCurrency(Number.isFinite(totalValue) ? totalValue : 0);
        }

        if (orderModalLead) {
            orderModalLead.textContent = resolveOrderLead({ ...summary, status: statusKey, statusLabel: resolvedStatusLabel });
        }
        if (orderModalItems) {
            orderModalItems.innerHTML = '';
            const items = Array.isArray(summary.itens) ? summary.itens : [];
            if (items.length === 0) {
                const empty = document.createElement('li');
                const text = document.createElement('span');
                text.textContent = 'Nenhum item encontrado.';
                empty.appendChild(text);
                orderModalItems.appendChild(empty);
            } else {
                items.slice(0, 4).forEach((item) => {
                    const line = document.createElement('li');
                    const label = document.createElement('div');
                    label.classList.add('portal-modal__list-label');
                    const title = document.createElement('span');
                    title.textContent = item.nome || `SKU ${item.sku || item.id}`;
                    const meta = document.createElement('small');
                    meta.textContent = `${formatQuantity(item.quantidade)} × ${formatCurrency(item.preco)}`;
                    label.appendChild(title);
                    label.appendChild(meta);
                    const totalLine = document.createElement('strong');
                    totalLine.textContent = formatCurrency((item.quantidade || 0) * (item.preco || 0));
                    line.appendChild(label);
                    line.appendChild(totalLine);
                    orderModalItems.appendChild(line);
                });
                if (items.length > 4) {
                    const remaining = document.createElement('li');
                    const label = document.createElement('div');
                    label.classList.add('portal-modal__list-label');
                    const title = document.createElement('span');
                    title.textContent = `+${items.length - 4} item(s) adicional(is)`;
                    label.appendChild(title);
                    remaining.appendChild(label);
                    orderModalItems.appendChild(remaining);
                }
            }
        }
        if (orderModalBackorder && orderModalBackorderList) {
            orderModalBackorderList.innerHTML = '';
            const filas = Array.isArray(summary.backorder) ? summary.backorder : [];
            if (filas.length === 0) {
                orderModalBackorder.setAttribute('hidden', '');
            } else {
                const resolveName = (produtoId) => {
                    const fromItems = (summary.itens || []).find((item) => item.id === produtoId);
                    if (fromItems) {
                        return fromItems.nome || `SKU ${fromItems.sku || produtoId}`;
                    }
                    const fromCatalog = catalogo.find((produto) => produto.id === produtoId);
                    if (fromCatalog) {
                        return fromCatalog.nome || `SKU ${fromCatalog.sku || produtoId}`;
                    }
                    return `SKU ${produtoId}`;
                };
                orderModalBackorder.removeAttribute('hidden');
                filas.forEach((item) => {
                    const line = document.createElement('li');
                    const label = document.createElement('div');
                    label.classList.add('portal-modal__list-label');
                    const title = document.createElement('span');
                    title.textContent = resolveName(item.produto_id);
                    const meta = document.createElement('small');
                    const unidade = ((summary.itens || []).find((info) => info.id === item.produto_id)?.unidade)
                        || (catalogo.find((produto) => produto.id === item.produto_id)?.unidade)
                        || '';
                    const faltanteFormatado = formatQuantity(item.faltante || 0);
                    meta.textContent = unidade
                        ? `Faltante: ${faltanteFormatado} ${unidade}`
                        : `Faltante: ${faltanteFormatado}`;
                    label.appendChild(title);
                    label.appendChild(meta);
                    const totalLine = document.createElement('strong');
                    totalLine.textContent = unidade ? `${faltanteFormatado} ${unidade}` : faltanteFormatado;
                    line.appendChild(label);
                    line.appendChild(totalLine);
                    orderModalBackorderList.appendChild(line);
                });
            }
        }
        orderModal.removeAttribute('hidden');
        requestAnimationFrame(() => {
            orderModal.classList.add('portal-modal--open');
            orderModal.setAttribute('aria-hidden', 'false');
            const primary = orderModal.querySelector('.portal-modal__footer .portal-btn--primary');
            if (primary && typeof primary.focus === 'function') {
                try {
                    primary.focus({ preventScroll: true });
                } catch (focusError) {
                    primary.focus();
                }
            }
        });
    };

    if (orderModal) {
        orderModal.setAttribute('aria-hidden', 'true');
        modalDismissTriggers.forEach((button) => {
            button.addEventListener('click', (event) => {
                const focusTarget = event.currentTarget.dataset.modalFocus;
                closeOrderModal(focusTarget || null);
            });
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }
        let handled = false;
        if (orderModal && orderModal.classList.contains('portal-modal--open')) {
            closeOrderModal();
            handled = true;
        }
        if (!handled && welcomeModal && welcomeModal.classList.contains('portal-modal--open')) {
            closeWelcomeModal();
        }
    });

    const setPortalState = (mode, preferredTab = null) => {
        const normalised = mode === 'app' ? 'app' : 'auth';
        document.body.setAttribute('data-portal-state', normalised);
        if (normalised === 'app') {
            portalAppSection?.removeAttribute('hidden');
            portalHero?.setAttribute('hidden', '');
            const nextTab = preferredTab || 'dashboard';
            setActiveTab(nextTab);
        } else {
            portalAppSection?.setAttribute('hidden', '');
            portalHero?.removeAttribute('hidden');
            localStorage.removeItem('portal-primary-hex');
            localStorage.removeItem('portal-primary-name');
            const systemPrimary = getComputedStyle(document.documentElement).getPropertyValue('--portal-system-primary').trim() || '#2563eb';
            document.documentElement.style.setProperty('--primary', systemPrimary);
            document.documentElement.style.setProperty('--portal-primary', systemPrimary);
            let mobileThemeColor = document.querySelector('meta[name="theme-color"][data-portal-mobile-bar]');
            if (!mobileThemeColor) {
                mobileThemeColor = document.createElement('meta');
                mobileThemeColor.name = 'theme-color';
                mobileThemeColor.media = '(max-width: 767px)';
                mobileThemeColor.dataset.portalMobileBar = '1';
                document.head.appendChild(mobileThemeColor);
            }
            mobileThemeColor.content = systemPrimary;
            pedidosCarregados = false;
            pedidosSignature = '';
            renderPedidos([]);
            setFeedback(ordersFeedback, '', '');
            const nextTab = preferredTab || 'novo-pedido';
            setActiveTab(nextTab);
            closeWelcomeModal();
        }
    };

    const setAuthMode = (mode) => {
        const target = mode === 'register' ? 'register' : 'login';
        authCard.dataset.mode = target;
        authToggleButtons.forEach((button) => {
            const active = button.dataset.authTarget === target;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        const enableForm = (form, enabled) => {
            if (!form) {
                return;
            }
            form.querySelectorAll('input, select, textarea').forEach((field) => {
                if (enabled) {
                    field.removeAttribute('disabled');
                } else {
                    field.setAttribute('disabled', 'disabled');
                }
            });
        };
        enableForm(loginForm, target === 'login');
        enableForm(registerForm, target === 'register');
    };

    const setDocumentType = (type) => {
        const selected = type === 'cpf' ? 'cpf' : 'cnpj';
        docButtons.forEach((button) => {
            const isActive = button.dataset.docOption === selected;
            button.classList.toggle('active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
        docFields.forEach((field) => {
            const matches = field.dataset.docField === selected;
            field.classList.toggle('field-group--hidden', !matches);
            const input = field.querySelector('input');
            if (input) {
                if (matches) {
                    input.removeAttribute('disabled');
                } else {
                    input.value = '';
                    input.setAttribute('disabled', 'disabled');
                }
            }
        });
    };

    const clearFeedback = (element) => {
        if (!element) return;
        element.dataset.state = '';
        element.textContent = '';
    };

    const setFeedback = (element, message, state = '') => {
        if (!element) return;
        element.textContent = message;
        element.dataset.state = state;
    };

    const orderStatusOptions = [
        { value: 'novo', label: 'Recebido' },
        { value: 'aprovacao', label: 'Em aprovação' },
        { value: 'producao', label: 'Aprovado' },
        { value: 'separacao', label: 'Separado' },
        { value: 'expedicao', label: 'Em transporte' },
        { value: 'faturado', label: 'Pedido recebido' },
        { value: 'entregue', label: 'Entregue' },
        { value: 'cancelado', label: 'Cancelado' },
    ];

    const orderStatusLabels = {
        novo: 'Recebido',
        aprovacao: 'Em aprovação',
        separando: 'Em separação',
        producao: 'Aprovado',
        separacao: 'Separado',
        expedicao: 'Em transporte',
        faturado: 'Pedido recebido',
        entregue: 'Entregue',
        cancelado: 'Cancelado',
    };

    const orderStatusOptionMap = orderStatusOptions.reduce((acc, option) => {
        acc[option.value] = option.label;
        return acc;
    }, {});

    const orderStatusUpdatable = new Set(orderStatusOptions.map((option) => option.value));

    const receiptEligibleStatuses = new Set(['entregue']);

    const defaultOrderLead = 'Nossa equipe está revisando as reservas de estoque. Você receberá a confirmação em breve no e-mail cadastrado.';
    const orderStatusLead = {
        novo: 'Recebemos seu pedido e estamos validando a disponibilidade em estoque.',
        aprovacao: defaultOrderLead,
        producao: 'Pedido aprovado! Os itens necessários já estão seguindo para produção.',
        separando: 'Seu pedido está em separação. Alguns produtos ainda aguardam conferência.',
        separacao: 'Seu pedido foi separado e conferido. Agora ele está pronto para seguir para a expedição.',
        expedicao: 'Seu pedido está em transporte. Assim que houver rastreamento disponível, enviaremos por e-mail.',
        faturado: 'Pedido concluído! Obrigado pela parceria.',
        entregue: 'Entrega confirmada pelo responsável. Obrigado pela parceria.',
        cancelado: 'Este pedido foi cancelado. Em caso de dúvidas, fale com nossa equipe de suporte.',
    };

    const resolveOrderLead = (summary = {}) => {
        if (typeof summary.lead === 'string' && summary.lead.trim() !== '') {
            return summary.lead.trim();
        }

        const hasBackorder = Array.isArray(summary.backorder) && summary.backorder.length > 0;
        if (hasBackorder) {
            return 'Reservamos o que estava disponível e já encaminhamos os demais itens para produção. Você receberá atualizações por e-mail.';
        }

        const status = String(summary.status || '').toLowerCase();
        if (status && orderStatusLead[status]) {
            return orderStatusLead[status];
        }

        return defaultOrderLead;
    };

    const renderListaProdutos = () => {
        if (!productsListContainer) {
            return;
        }
        productsListContainer.innerHTML = '';
        if (!catalogo.length) {
            const empty = document.createElement('div');
            empty.className = 'portal-products-list__item';
            empty.setAttribute('role', 'listitem');
            empty.innerHTML = '<p>Nenhum produto disponível no momento.</p>';
            productsListContainer.appendChild(empty);
            return;
        }
        catalogo.forEach((produto) => {
            const card = document.createElement('article');
            card.className = 'portal-products-list__item';
            card.setAttribute('role', 'listitem');
            const shelf = Number(produto.shelf_life_dias ?? 0);
            card.innerHTML = `
                <h4>${produto.nome}</h4>
                <dl>
                    <dt>SKU</dt><dd>${produto.sku}</dd>
                    <dt>Unidade</dt><dd>${produto.unidade || '—'}</dd>
                    <dt>Preço</dt><dd>${formatCurrency(produto.preco || 0)}</dd>
                    <dt>Validade ( dias )</dt><dd>${shelf > 0 ? `${shelf} dia(s)` : 'Não informado'}</dd>
                </dl>
            `;
            productsListContainer.appendChild(card);
        });
    };

    const renderPedidos = (lista) => {
        if (!ordersTableBody) {
            return;
        }
        ordersTableBody.innerHTML = '';
        const pedidos = Array.isArray(lista) ? lista : [];
        if (pedidos.length === 0) {
            const empty = document.createElement('tr');
            empty.dataset.ordersEmpty = 'true';
            const cell = document.createElement('td');
            cell.colSpan = 5;
            cell.textContent = 'Nenhum pedido enviado ainda.';
            empty.appendChild(cell);
            ordersTableBody.appendChild(empty);
            return;
        }
        pedidos.forEach((pedido) => {
            const row = document.createElement('tr');
            const codigo = pedido.codigo || `#${String(pedido.id ?? '').padStart(4, '0')}`;
            const statusKey = String(pedido.status || 'novo').toLowerCase();
            const statusClass = `portal-orders-status portal-orders-status--${pedido.data_confirmacao_cliente ? 'faturado' : statusKey}`;
            const statusLabel = orderStatusLabels[statusKey] || (pedido.status ? String(pedido.status) : '—');
            const statusMarkup = pedido.data_confirmacao_cliente ? 'Recebido' : dashboardEscape(statusLabel);
            const total = Number(pedido.total ?? 0);
            const dataCriacao = formatDateTime(pedido.created_at ?? pedido.createdAt ?? pedido.created_at);
            const showReceipt = receiptEligibleStatuses.has(statusKey);
            const receiptAttributes = showReceipt ? (pedido.data_confirmacao_cliente ? 'disabled aria-disabled="true" data-received-confirmed="1"' : '') : 'hidden';
            const editButton = ['novo','aprovacao'].includes(statusKey) ? `<button type="button" class="portal-orders-action portal-orders-action--icon" data-order-edit="${pedido.id}" data-tooltip="Editar" aria-label="Editar pedido" title="Editar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path><path d="m15 5 4 4"></path></svg></button>` : '';
            row.innerHTML = `
                <td><strong>${codigo}</strong></td>
                <td>
                    <span class="${statusClass}" data-order-status-label>${statusMarkup}</span>
                </td>
                <td>${formatCurrency(total)}</td>
                <td>${dataCriacao}</td>
                <td class="portal-orders-table__actions">
                    <div class="portal-orders-actions">
                        ${editButton}
                        <button type="button" class="portal-orders-action portal-orders-action--icon" data-order-view="${pedido.id}" data-tooltip="Ver pedido" aria-label="Ver pedido completo">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.7"></circle></svg>
                        </button>
                        <button type="button" class="portal-orders-action portal-orders-action--confirm" data-order-received="${pedido.id}" data-status="${statusKey}" ${receiptAttributes}>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.285 6.708a1 1 0 0 0-1.414-1.415L9 15.164l-3.87-3.87a1 1 0 1 0-1.415 1.414l4.577 4.577a1 1 0 0 0 1.414 0l10.579-10.577Z"/></svg>
                            <span>Recebido</span>
                        </button>
                        <button type="button" class="portal-orders-action portal-orders-action--problem" data-order-report="${pedido.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm0 4a1.25 1.25 0 1 1-1.25 1.25A1.25 1.25 0 0 1 12 6Zm1 11h-2v-7h2Z"/></svg>
                            <span>Relatar problema</span>
                        </button>
                    </div>
                </td>
            `;
            ordersTableBody.appendChild(row);
        });
        ordersTableBody.querySelectorAll('[data-order-edit]').forEach((button) => button.addEventListener('click', () => abrirEditorPedido(Number(button.dataset.orderEdit))));
        ordersTableBody.querySelectorAll('[data-order-view]').forEach((button) => {
            button.addEventListener('click', (event) => {
                const pedidoId = Number(event.currentTarget.dataset.orderView);
                if (pedidoId > 0) {
                    visualizarPedido(pedidoId);
                }
            });
        });
        ordersTableBody.querySelectorAll('[data-order-received]').forEach((button) => {
            if (button.hasAttribute('hidden')) {
                return;
            }
            button.addEventListener('click', async (event) => {
                const trigger = event.currentTarget;
                const pedidoId = Number(trigger.dataset.orderReceived);
                if (!pedidoId || !state.token) return;
                trigger.setAttribute('disabled', 'disabled');
                setFeedback(ordersFeedback, 'Confirmando recebimento…');
                try {
                    const response=await fetch(`${basePath}/api/pedidos/${pedidoId}/recebimento`,{method:'POST',headers:{'Authorization':`Bearer ${state.token}`,'Accept':'application/json'}});
                    const json=await response.json().catch(()=>({}));
                    if(response.status===401){leaveApp();throw new Error('Sua sessão expirou. Entre novamente.')}
                    if(!response.ok)throw new Error(json.message||'Não foi possível confirmar o recebimento.');
                    const row=trigger.closest('tr'),badge=row?.querySelector('[data-order-status-label]');
                    if(badge){badge.textContent='Recebido';badge.className='portal-orders-status portal-orders-status--faturado'}
                    trigger.setAttribute('disabled','disabled');trigger.setAttribute('aria-disabled','true');trigger.dataset.receivedConfirmed='1';
                    setFeedback(ordersFeedback,`Recebimento confirmado em ${formatDateTime(json.recebido_em)}.`,'success');
                } catch(error) {
                    trigger.removeAttribute('disabled');
                    setFeedback(ordersFeedback,error.message||'Não foi possível confirmar o recebimento.','error');
                }
            });
        });
        ordersTableBody.querySelectorAll('[data-order-report]').forEach((button) => {
            button.addEventListener('click', () => {
                setActiveTab('suporte');
                window.setTimeout(() => {
                    const supportPanel = document.querySelector('#portal-tab-suporte');
                    if (supportPanel) {
                        supportPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 120);
            });
        });
    };

    const atualizarStatusPedido = async (pedidoId, nextStatus, selectElement, previousStatus = '', triggerElement = null) => {
        if (!state.token) {
            if (selectElement) {
                selectElement.value = previousStatus || selectElement.dataset.currentStatus || '';
            }
            if (triggerElement) {
                triggerElement.removeAttribute('disabled');
            }
            setFeedback(ordersFeedback, 'Faça login novamente para atualizar o status do pedido.', 'error');
            return;
        }

        const status = String(nextStatus || '').toLowerCase();
        if (!orderStatusUpdatable.has(status)) {
            if (selectElement) {
                selectElement.value = previousStatus || selectElement.dataset.currentStatus || '';
            }
            if (triggerElement) {
                triggerElement.removeAttribute('disabled');
            }
            setFeedback(ordersFeedback, 'Status selecionado inválido.', 'error');
            return;
        }

        setFeedback(ordersFeedback, 'Atualizando status do pedido…');
        if (selectElement) {
            selectElement.setAttribute('disabled', 'disabled');
        }
        if (triggerElement) {
            triggerElement.setAttribute('disabled', 'disabled');
        }

        try {
            const response = await fetch(`${basePath}/api/pedidos/${pedidoId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${state.token}`,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status }),
            });

            let json = {};
            try {
                json = await response.json();
            } catch (error) {
                json = {};
            }

            if (response.status === 401) {
                leaveApp();
                setFeedback(ordersFeedback, 'Sua sessão expirou. Entre novamente para continuar.', 'error');
                if (selectElement) {
                    selectElement.value = previousStatus || selectElement.dataset.currentStatus || '';
                }
                if (triggerElement) {
                    triggerElement.removeAttribute('disabled');
                }
                return;
            }

            if (!response.ok) {
                throw new Error(json.message || 'Não foi possível atualizar o status do pedido.');
            }

            const badge = (selectElement?.closest('td') || triggerElement?.closest('td'))?.querySelector('[data-order-status-label]');
            const label = json.status_label || orderStatusLabels[status] || orderStatusOptionMap[status] || status;
            if (badge) {
                badge.textContent = label;
                badge.className = `portal-orders-status portal-orders-status--${status}`;
            }
            if (selectElement) {
                selectElement.value = status;
                selectElement.dataset.currentStatus = status;
            }
            const row = selectElement?.closest('tr') || triggerElement?.closest('tr') || null;
            const receiveButton = row?.querySelector('[data-order-received]') || null;
            if (receiveButton) {
                receiveButton.dataset.status = status;
                const shouldShow = receiptEligibleStatuses.has(status) && status !== 'faturado';
                receiveButton.hidden = !shouldShow;
                receiveButton.removeAttribute('disabled');
            }
            const reportButton = row?.querySelector('[data-order-report]') || null;
            if (reportButton) {
                reportButton.removeAttribute('disabled');
            }
            const message = status === 'faturado'
                ? 'Pedido marcado como recebido. Obrigado pela confirmação!'
                : (json.message || 'Status atualizado com sucesso.');
            setFeedback(ordersFeedback, message, 'success');
        } catch (error) {
            if (selectElement) {
                selectElement.value = previousStatus || selectElement.dataset.currentStatus || '';
            }
            if (triggerElement) {
                triggerElement.removeAttribute('disabled');
                if ('status' in triggerElement.dataset) {
                    triggerElement.dataset.status = previousStatus || triggerElement.dataset.status || '';
                }
            }
            setFeedback(ordersFeedback, error.message || 'Não foi possível atualizar o status do pedido.', 'error');
        } finally {
            if (selectElement) {
                selectElement.removeAttribute('disabled');
            }
            if (triggerElement && !triggerElement.hidden) {
                triggerElement.removeAttribute('disabled');
            }
        }
    };

    const visualizarPedido = async (pedidoId) => {
        if (!state.token) {
            setFeedback(ordersFeedback, 'Faça login novamente para visualizar os detalhes do pedido.', 'error');
            return;
        }
        setFeedback(ordersFeedback, 'Carregando detalhes do pedido…');
        setActiveTab('detalhe-pedido');
        orderDetail.innerHTML = '<div class="portal-order-detail__loading">Carregando pedido...</div>';
        try {
            const response = await fetch(`${basePath}/api/pedidos/${pedidoId}`, {
                headers: {
                    'Authorization': `Bearer ${state.token}`,
                    'Accept': 'application/json',
                },
            });
            if (response.status === 401) {
                leaveApp();
                setFeedback(ordersFeedback, 'Sua sessão expirou. Entre novamente para continuar.', 'error');
                return;
            }
            if (!response.ok) {
                throw new Error('Não foi possível carregar os detalhes do pedido.');
            }
            const json = await response.json();
            const pedido = json.pedido || {};
            const itens = Array.isArray(json.itens) ? json.itens : [];
            const statusKey = String(pedido.status || '').toLowerCase();
            const romaneio = json.romaneio || null;
            const historico = Array.isArray(json.historico) ? json.historico : [];
            const actionLabels = {create:'Pedido criado',update:'Pedido ajustado',status:'Status atualizado',expedir:'Expedição registrada',receber:'Entrega confirmada'};
            const fieldLabels = {total:'Valor total',frete_valor:'Frete',observacoes:'Observações',status:'Status',itens:'Produtos',forma_pagto:'Pagamento',desconto:'Desconto'};
            const historyMarkup = historico.length ? historico.map(entry => {
                const fields = Object.keys(entry.mudancas || {}).map(key => fieldLabels[key] || key.replaceAll('_',' '));
                const complement = fields.length ? `Alterado: ${fields.join(', ')}.` : '';
                const reason = String(entry.motivo || '').trim();
                return `<div class="portal-order-history__item"><span class="portal-order-history__dot"></span><div><strong>${dashboardEscape(actionLabels[entry.acao] || 'Atualização do pedido')}</strong><small>${dashboardEscape(formatDateTime(entry.created_at))}</small>${complement ? `<p class="portal-order-history__reason">${dashboardEscape(complement)}</p>` : ''}${reason ? `<p class="portal-order-history__reason"><b>Motivo:</b> ${dashboardEscape(reason)}</p>` : ''}</div></div>`;
            }).join('') : '<div class="portal-order-detail__empty">Nenhum ajuste registrado.</div>';
            const itemsMarkup = itens.map(item => { const qty=Number(item.quantidade||0), price=Number(item.preco_unit??item.preco??0); return `<tr><td>${dashboardEscape(item.sku || item.produto_id || '-')}</td><td>${dashboardEscape(item.nome || item.produto_nome || 'Produto')}</td><td>${qty.toLocaleString('pt-BR',{maximumFractionDigits:2})}</td><td>${formatCurrency(price * qty)}</td></tr>`; }).join('');
            const manifestMarkup = romaneio ? `<dl><div><dt>Número do romaneio</dt><dd>${dashboardEscape(romaneio.romaneio || `#${romaneio.id}`)}</dd></div><div><dt>Status</dt><dd>${romaneio.data_confirmacao_cliente?'Recebido':(romaneio.data_recebimento?'Entregue':'Expedido')}</dd></div><div><dt>Data da separação</dt><dd>${dashboardEscape(formatDateTime(romaneio.data_separacao))}</dd></div><div><dt>Data da expedição</dt><dd>${dashboardEscape(formatDateTime(romaneio.data_expedicao))}</dd></div><div><dt>Volumes</dt><dd>${Number(romaneio.volumes||0)}</dd></div><div><dt>Peso total</dt><dd>${Number(romaneio.peso_total||0).toLocaleString('pt-BR',{minimumFractionDigits:3,maximumFractionDigits:3})} kg</dd></div><div><dt>Transportadora</dt><dd>${dashboardEscape(romaneio.transportadora || 'Não informada')}</dd></div><div><dt>Motorista</dt><dd>${dashboardEscape(romaneio.motorista_nome || 'Não informado')}</dd></div><div><dt>Responsável pela expedição</dt><dd>${dashboardEscape(romaneio.responsavel_expedicao_nome || 'Não informado')}</dd></div>${romaneio.data_recebimento?`<div><dt>Entrega registrada em</dt><dd>${dashboardEscape(formatDateTime(romaneio.data_recebimento))}</dd></div>`:''}${romaneio.data_confirmacao_cliente?`<div><dt>Recebido pelo cliente em</dt><dd>${dashboardEscape(formatDateTime(romaneio.data_confirmacao_cliente))}</dd></div>`:''}</dl>` : '<div class="portal-order-detail__empty">O romaneio ainda não foi gerado.</div>';
            orderDetail.innerHTML = `<div class="portal-order-detail__head"><div><h3>Pedido ${dashboardEscape(pedido.codigo || `#${pedidoId}`)}</h3><p>Detalhes, documentos e histórico completo.</p></div><div class="portal-order-detail__actions"><button type="button" class="portal-btn portal-btn--ghost" data-order-detail-back><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>Voltar</button><button type="button" class="portal-btn portal-btn--primary" data-order-detail-pdf><svg viewBox="0 0 24 24"><path d="M6 9V2h9l3 3v4M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>PDF</button></div></div><div class="portal-order-detail__summary"><div><span>Status</span><strong>${dashboardEscape(pedido.status_label || orderStatusLabels[statusKey] || pedido.status || '-')}</strong></div><div><span>Criado em</span><strong>${dashboardEscape(formatDateTime(pedido.created_at))}</strong></div><div><span>Frete</span><strong>${Number(pedido.frete_valor||0)>0?formatCurrency(pedido.frete_valor):'Sem frete'}</strong></div><div><span>Total</span><strong>${formatCurrency(pedido.total||0)}</strong></div></div><div class="portal-order-detail__grid"><section class="portal-order-detail__card"><h4>Produtos do pedido</h4><table class="portal-order-detail__table"><thead><tr><th>Código</th><th>Produto</th><th>Qtd.</th><th>Subtotal</th></tr></thead><tbody>${itemsMarkup}</tbody></table></section><section class="portal-order-detail__card portal-order-detail__manifest"><h4>Romaneio</h4>${manifestMarkup}</section></div><section class="portal-order-detail__card portal-order-history"><h4>Histórico de ajustes e motivos</h4>${historyMarkup}</section>`;
            const pedidoPdfButton = orderDetail.querySelector('[data-order-detail-pdf]');
            if (pedidoPdfButton) {
                pedidoPdfButton.dataset.orderDocument = 'nota';
                pedidoPdfButton.removeAttribute('data-order-detail-pdf');
                if (pedidoPdfButton.lastChild) pedidoPdfButton.lastChild.textContent = 'PDF do pedido';
                if (romaneio) {
                    const romaneioPdfButton = pedidoPdfButton.cloneNode(true);
                    romaneioPdfButton.dataset.orderDocument = 'romaneio';
                    if (romaneioPdfButton.lastChild) romaneioPdfButton.lastChild.textContent = 'PDF do romaneio';
                    pedidoPdfButton.after(romaneioPdfButton);
                }
            }
            orderDetail.querySelector('[data-order-detail-back]')?.addEventListener('click', () => setActiveTab('pedidos'));
            orderDetail.querySelectorAll('[data-order-document]').forEach(documentButton => documentButton.addEventListener('click', async (event) => {
                const button = event.currentTarget;
                const documentType = button.dataset.orderDocument === 'romaneio' ? 'romaneio' : 'nota';
                const popup = window.open('', '_blank');
                if (!popup) {
                    setFeedback(ordersFeedback, 'Permita a abertura de pop-ups para visualizar o PDF.', 'error');
                    return;
                }
                popup.document.write('<!doctype html><html><head><title>Carregando pedido...</title></head><body style="font-family:Arial;padding:32px">Preparando o documento...</body></html>');
                popup.document.close();
                button.setAttribute('disabled', 'disabled');
                try {
                    const noteResponse = await fetch(`${basePath}/api/pedidos/${pedidoId}/${documentType}`, {
                        cache: 'no-store',
                        headers: {'Authorization': `Bearer ${state.token}`, 'Accept': 'text/html'},
                    });
                    if (noteResponse.status === 401) {
                        popup.close();
                        leaveApp();
                        return;
                    }
                    if (!noteResponse.ok) throw new Error(documentType === 'romaneio' ? 'Não foi possível abrir o romaneio.' : 'Não foi possível gerar o PDF deste pedido.');
                    const noteHtml = await noteResponse.text();
                    popup.document.open();
                    popup.document.write(noteHtml);
                    popup.document.close();
                    popup.focus();
                } catch (noteError) {
                    popup.close();
                    setFeedback(ordersFeedback, noteError.message || 'Não foi possível gerar o PDF.', 'error');
                } finally {
                    button.removeAttribute('disabled');
                }
            }));
            setFeedback(ordersFeedback, 'Detalhes carregados com sucesso.', 'success');
        } catch (error) {
            setActiveTab('pedidos');
            setFeedback(ordersFeedback, error.message || 'Não foi possível abrir o pedido.', 'error');
        }
    };

    const abrirEditorPedido = async (pedidoId) => {
        if (!orderEditor || !pedidoId) return;
        localStorage.setItem('portal-edit-order-id', String(pedidoId));
        setActiveTab('editar-pedido');
        orderEditor.innerHTML = '<div class="portal-order-editor__loading">Carregando pedido...</div>';
        try {
            const response = await fetch(`${basePath}/api/pedidos/${pedidoId}`, { cache: 'no-store', headers: { 'Authorization': `Bearer ${state.token}`, 'Accept': 'application/json' } });
            const json = await response.json();
            if (!response.ok) throw new Error(json.message || 'Não foi possível abrir o pedido.');
            const pedido = json.pedido || {};
            const itens = Array.isArray(json.itens) ? json.itens : [];
            const editorStatusKey = String(pedido.status || 'aprovacao').toLowerCase().replace(/[^a-z0-9_-]/g, '');
            const createdAt = pedido.created_at ? new Date(String(pedido.created_at).replace(' ', 'T')).toLocaleString('pt-BR') : 'Data não informada';
            const itemMarkup = (item) => {
                const productId = Number(item.produto_id || item.id) || 0;
                const catalogProduct = catalogo.find(product => Number(product.id) === productId) || {};
                const price = Number(item.preco_unit ?? item.preco ?? catalogProduct.preco) || 0;
                const quantity = Number(item.quantidade) || 1;
                const name = item.nome || item.produto_nome || catalogProduct.nome || `Produto ${productId}`;
                const sku = item.sku || catalogProduct.sku || '-';
                const weight = Number(item.peso_liquido ?? catalogProduct.peso_liquido) || 0;
                return `<div class="portal-order-editor__item" data-edit-item data-price="${price}"><span>${dashboardEscape(sku)}</span><span class="portal-order-editor__item-name" title="${dashboardEscape(name)}">${dashboardEscape(name)}</span><input type="number" min="0.01" step="0.01" value="${quantity}" data-edit-quantity data-product-id="${productId}" aria-label="Quantidade de ${dashboardEscape(name)}"><span>${dashboardEscape(formatWeight(weight) || '-')}</span><span>${formatCurrency(price)}</span><span class="portal-order-editor__item-subtotal" data-edit-subtotal>${formatCurrency(price * quantity)}</span><button type="button" class="portal-order-editor__remove" data-edit-remove aria-label="Remover ${dashboardEscape(name)}" title="Remover produto"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="m6.5 7 1 13h9l1-13"/><path d="M10 11v5M14 11v5"/></svg></button></div>`;
            };
            orderEditor.innerHTML = `<div class="portal-order-editor__head"><div class="portal-order-editor__title"><span class="portal-order-editor__title-icon"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></span><div><h3>Editar pedido ${dashboardEscape(pedido.codigo || `#${pedidoId}`)}</h3><p>Revise os produtos e quantidades antes de salvar.</p></div></div><button type="button" class="portal-btn portal-btn--ghost portal-order-editor__back" data-order-editor-back><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>Voltar</button></div><div class="portal-order-editor__meta"><span>${dashboardEscape(pedido.status_label || pedido.status || 'Em análise')}</span><span>${dashboardEscape(createdAt)}</span></div><form data-order-editor-form><div class="portal-order-builder"><div class="portal-order-builder__title"><strong>Itens do pedido</strong><p>Use os produtos disponíveis no seu catálogo e informe a quantidade.</p></div><div class="portal-order-builder__entry"><label class="portal-order-builder__field">Código<span class="portal-order-builder__readonly portal-order-builder__code" data-builder-code>-</span></label><label class="portal-order-builder__field">Produtos<input type="text" autocomplete="off" placeholder="Produto..." data-order-product-search><ul class="portal-order-builder__suggestions" data-order-product-results></ul></label><label class="portal-order-builder__field">Quantidade<input type="number" min="0.01" step="0.01" value="1" data-builder-quantity></label><label class="portal-order-builder__field">Peso<span class="portal-order-builder__readonly" data-builder-weight>-</span></label><label class="portal-order-builder__field">Valor<span class="portal-order-builder__readonly" data-builder-price>R$ 0,00</span></label><label class="portal-order-builder__field">Subtotal<span class="portal-order-builder__readonly" data-builder-subtotal>R$ 0,00</span></label><button type="button" class="portal-btn portal-order-builder__add" data-builder-add disabled>Adicionar item</button></div><div class="portal-order-builder__list"><h4>Lista de Produtos</h4><div class="portal-order-builder__table"><div class="portal-order-builder__head"><span>Código</span><span>Produtos</span><span>Quantidade</span><span>Peso</span><span>Valor</span><span>Subtotal</span><span></span></div><div class="portal-order-editor__items" data-order-editor-items>${itens.map(itemMarkup).join('')}</div><div class="portal-order-editor__empty-list" data-order-editor-empty hidden>Nenhum produto adicionado.</div></div></div></div><div class="portal-order-editor__notes"><label for="portal-order-notes">Observações do pedido</label><textarea id="portal-order-notes" maxlength="1000" placeholder="Inclua orientações importantes para este pedido" data-order-editor-notes>${dashboardEscape(pedido.observacoes || '')}</textarea></div><div class="portal-feedback" data-order-editor-feedback></div><div class="portal-order-editor__footer"><div class="portal-order-editor__summary"><span>Produtos<strong data-order-editor-products>0</strong></span><span>Quantidade total<strong data-order-editor-quantity>0</strong></span><span>Total atualizado<strong data-order-editor-total>R$ 0,00</strong></span></div><div class="portal-order-editor__actions"><button type="button" class="portal-btn portal-btn--ghost" data-order-editor-cancel>Cancelar</button><button type="submit" class="portal-btn portal-btn--primary"><svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>Salvar alterações</button></div></div></form>`;
            const editorStatusBadge = orderEditor.querySelector('.portal-order-editor__meta span:first-child');
            editorStatusBadge?.classList.add('portal-orders-status', `portal-orders-status--${editorStatusKey}`);
            const form = orderEditor.querySelector('[data-order-editor-form]');
            const feedback = orderEditor.querySelector('[data-order-editor-feedback]');
            const itemsContainer = orderEditor.querySelector('[data-order-editor-items]');
            const results = orderEditor.querySelector('[data-order-product-results]');
            const search = orderEditor.querySelector('[data-order-product-search]');
            const builderQuantity = orderEditor.querySelector('[data-builder-quantity]');
            const builderAdd = orderEditor.querySelector('[data-builder-add]');
            builderAdd?.removeAttribute('disabled');
            let selectedProduct = null;
            const updateBuilder = () => {
                const quantity = Math.max(0, Number(builderQuantity?.value) || 0);
                const set = (selector, value) => { const target = orderEditor.querySelector(selector); if (target) target.textContent = value; };
                set('[data-builder-code]', selectedProduct?.sku || '-');
                set('[data-builder-weight]', selectedProduct ? (formatWeight(selectedProduct.peso_liquido) || '-') : '-');
                set('[data-builder-price]', formatCurrency(selectedProduct?.preco || 0));
                set('[data-builder-subtotal]', formatCurrency((selectedProduct?.preco || 0) * quantity));
                if (builderAdd) {
                    builderAdd.removeAttribute('aria-disabled');
                    builderAdd.dataset.productId = selectedProduct ? String(selectedProduct.id) : '';
                }
            };
            const updateSummary = () => {
                const rows = Array.from(orderEditor.querySelectorAll('[data-edit-item]'));
                const quantity = rows.reduce((sum, row) => sum + (Number(row.querySelector('[data-edit-quantity]')?.value) || 0), 0);
                const total = rows.reduce((sum, row) => sum + (Number(row.dataset.price) || 0) * (Number(row.querySelector('[data-edit-quantity]')?.value) || 0), 0);
                rows.forEach(row => { const subtotal = row.querySelector('[data-edit-subtotal]'); if (subtotal) subtotal.textContent = formatCurrency((Number(row.dataset.price) || 0) * (Number(row.querySelector('[data-edit-quantity]')?.value) || 0)); });
                const set = (selector, value) => { const target = orderEditor.querySelector(selector); if (target) target.textContent = value; };
                set('[data-order-editor-products]', String(rows.length));
                set('[data-order-editor-quantity]', quantity.toLocaleString('pt-BR', { maximumFractionDigits: 2 }));
                set('[data-order-editor-total]', formatCurrency(total));
                const empty = orderEditor.querySelector('[data-order-editor-empty]');
                if (empty) empty.hidden = rows.length > 0;
            };
            const renderAvailable = () => {
                const term = String(search?.value || '').trim().toLocaleLowerCase('pt-BR');
                const selected = new Set(Array.from(orderEditor.querySelectorAll('[data-edit-quantity]')).map(input => Number(input.dataset.productId)));
                const available = term ? catalogo.filter(product => !selected.has(Number(product.id)) && `${product.nome} ${product.sku} ${product.categoria}`.toLocaleLowerCase('pt-BR').includes(term)).slice(0, 12) : [];
                results.innerHTML = available.map(product => `<li><button type="button" data-order-product-select="${Number(product.id)}">${dashboardEscape(product.nome)}<small>${dashboardEscape(product.sku || '')} · ${formatCurrency(product.preco || 0)}</small></button></li>`).join('');
                return available;
            };
            const selectProduct = (product) => {
                selectedProduct = product || null;
                if (search && selectedProduct) search.value = selectedProduct.nome;
                results.innerHTML = '';
                updateBuilder();
            };
            const addSelectedProduct = () => {
                const selectedId = Number(builderAdd?.dataset.productId || 0);
                if (selectedId > 0) selectedProduct = catalogo.find(product => Number(product.id) === selectedId) || selectedProduct;
                if (!selectedProduct) {
                    const term = String(search?.value || '').trim().toLocaleLowerCase('pt-BR');
                    selectedProduct = catalogo.find(product => [product.nome, product.sku].some(value => String(value || '').trim().toLocaleLowerCase('pt-BR') === term))
                        || catalogo.find(product => `${product.nome || ''} ${product.sku || ''}`.trim().toLocaleLowerCase('pt-BR').includes(term))
                        || null;
                }
                if (!selectedProduct) {
                    setFeedback(feedback, 'Selecione um produto da lista antes de adicionar.', 'error');
                    return false;
                }
                const quantity = Math.max(0, Number(builderQuantity?.value) || 0);
                if (quantity <= 0) {
                    setFeedback(feedback, 'Informe uma quantidade maior que zero.', 'error');
                    return false;
                }
                try {
                    itemsContainer.insertAdjacentHTML('beforeend', itemMarkup({ ...selectedProduct, produto_id: selectedProduct.id, preco_unit: selectedProduct.preco, quantidade: quantity }));
                    selectedProduct = null;
                    if (search) search.value = '';
                    if (builderQuantity) builderQuantity.value = '1';
                    if (builderAdd) builderAdd.dataset.productId = '';
                    results.innerHTML = '';
                    setFeedback(feedback, 'Produto adicionado ao pedido.', 'success');
                    updateBuilder();
                    updateSummary();
                    search?.focus();
                    return true;
                } catch (error) {
                    setFeedback(feedback, `Não foi possível adicionar o produto: ${error.message || 'erro inesperado'}.`, 'error');
                    return false;
                }
            };
            const goBack = () => setActiveTab('pedidos');
            orderEditor.querySelector('[data-order-editor-back]')?.addEventListener('click', goBack);
            orderEditor.querySelector('[data-order-editor-cancel]')?.addEventListener('click', goBack);
            orderEditor.oninput = (event) => {
                if (event.target.matches('[data-edit-quantity]')) updateSummary();
                if (event.target.matches('[data-order-product-search]')) {
                    selectedProduct = null;
                    const available = renderAvailable();
                    const term = String(search?.value || '').trim().toLocaleLowerCase('pt-BR');
                    const exact = available.find(product => [product.nome, product.sku].some(value => String(value || '').trim().toLocaleLowerCase('pt-BR') === term));
                    if (exact) selectProduct(exact); else updateBuilder();
                }
                if (event.target.matches('[data-builder-quantity]')) updateBuilder();
            };
            search?.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter') return;
                event.preventDefault();
                if (!selectedProduct) {
                    const firstResult = results.querySelector('[data-order-product-select]');
                    if (firstResult) selectProduct(catalogo.find(item => Number(item.id) === Number(firstResult.dataset.orderProductSelect)) || null);
                }
                addSelectedProduct();
            });
            if (builderAdd) builderAdd.onclick = (event) => {
                event.preventDefault();
                event.stopPropagation();
                addSelectedProduct();
            };
            orderEditor.onclick = (event) => {
                const remove = event.target.closest('[data-edit-remove]');
                if (remove) { remove.closest('[data-edit-item]')?.remove(); updateSummary(); renderAvailable(); return; }
                const select = event.target.closest('[data-order-product-select]');
                if (select) { selectProduct(catalogo.find(item => Number(item.id) === Number(select.dataset.orderProductSelect)) || null); return; }
            };
            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const itensPayload = Array.from(orderEditor.querySelectorAll('[data-edit-quantity]')).map(input => ({ produto_id: Number(input.dataset.productId), quantidade: Number(input.value) })).filter(item => item.produto_id > 0 && item.quantidade > 0);
                if (!itensPayload.length) { setFeedback(feedback, 'Mantenha pelo menos um produto no pedido.', 'error'); return; }
                const submit = form.querySelector('[type="submit"]');
                submit?.setAttribute('disabled', 'disabled');
                setFeedback(feedback, 'Salvando alterações...');
                try {
                    const save = await fetch(`${basePath}/api/pedidos/${pedidoId}`, { method: 'PATCH', headers: { 'Authorization': `Bearer ${state.token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ itens: itensPayload, observacoes: orderEditor.querySelector('[data-order-editor-notes]')?.value || '' }) });
                    const result = await save.json();
                    if (!save.ok) throw new Error(result.message || 'Não foi possível atualizar o pedido.');
                    pedidosCarregados = false; pedidosSignature = ''; dashboardCarregado = false;
                    await carregarPedidos(true, true); setActiveTab('pedidos'); setFeedback(ordersFeedback, 'Pedido atualizado com sucesso.', 'success');
                } catch (error) { setFeedback(feedback, error.message || 'Não foi possível atualizar o pedido.', 'error'); submit?.removeAttribute('disabled'); }
            });
            updateSummary();
            updateBuilder();
            renderAvailable();
        } catch(error) { orderEditor.innerHTML=`<div class="portal-order-editor__loading">${dashboardEscape(error.message||'Não foi possível abrir o pedido.')}<br><button type="button" class="portal-btn portal-btn--ghost" data-order-editor-back>Voltar</button></div>`;orderEditor.querySelector('[data-order-editor-back]')?.addEventListener('click',()=>setActiveTab('pedidos')); }
    };

    const dashboardEscape = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const dashboardDate = (value) => {
        if (!value) return null;
        const parsed = new Date(String(value).replace(' ', 'T'));
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    };
    const dashboardStartDay = (date) => new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const dashboardStartWeek = (date) => { const day=dashboardStartDay(date);const offset=(day.getDay()+6)%7;day.setDate(day.getDate()-offset);return day; };
    const dashboardStartMonth = (date) => new Date(date.getFullYear(), date.getMonth(), 1);
    const dashboardStartYear = (date) => new Date(date.getFullYear(), 0, 1);
    const dashboardAdd = (date, unit, amount) => { const copy=new Date(date);if(unit==='day')copy.setDate(copy.getDate()+amount);if(unit==='week')copy.setDate(copy.getDate()+(7*amount));if(unit==='month')copy.setMonth(copy.getMonth()+amount);if(unit==='year')copy.setFullYear(copy.getFullYear()+amount);return copy; };
    const dashboardDelta = (current, previous) => previous > 0 ? ((current-previous)/previous)*100 : (current > 0 ? 100 : 0);
    const dashboardDeltaHtml = (value) => { const number=Number.isFinite(value)?value:0;const state=number>0?'up':(number<0?'down':'neutral');const arrow=number>0?'↑':(number<0?'↓':'—');return `<span class="portal-dashboard__delta ${state}">${arrow} ${Math.abs(number).toLocaleString('pt-BR',{maximumFractionDigits:1})}%</span>`; };
    const dashboardOrdersIn = (orders, start, end) => orders.filter((order) => { const date=dashboardDate(order.created_at||order.createdAt);return date&&date>=start&&date<end&&String(order.status||'').toLowerCase()!=='cancelado'; });
    const dashboardTotal = (orders) => orders.reduce((sum, order) => sum + (Number(order.total)||0), 0);
    const dashboardPeriod = (orders, start, end, previousStart) => { const current=dashboardOrdersIn(orders,start,end);const previous=dashboardOrdersIn(orders,previousStart,start);const total=dashboardTotal(current);const previousTotal=dashboardTotal(previous);return {orders:current.length,total,delta:dashboardDelta(total,previousTotal),orderDelta:dashboardDelta(current.length,previous.length)}; };

    const renderDashboard = (allOrders) => {
        if (!dashboardContainer) return;
        const orders=Array.isArray(allOrders)?allOrders:[];
        const now=new Date();
        const dayStart=dashboardStartDay(now),weekStart=dashboardStartWeek(now),monthStart=dashboardStartMonth(now),yearStart=dashboardStartYear(now);
        const periods=[
            ['Hoje',dayStart,dashboardAdd(dayStart,'day',1),dashboardAdd(dayStart,'day',-1)],
            ['Semana',weekStart,dashboardAdd(weekStart,'week',1),dashboardAdd(weekStart,'week',-1)],
            ['Mês',monthStart,dashboardAdd(monthStart,'month',1),dashboardAdd(monthStart,'month',-1)],
            ['Ano',yearStart,dashboardAdd(yearStart,'year',1),dashboardAdd(yearStart,'year',-1)],
        ].map(([label,start,end,previous])=>({label,...dashboardPeriod(orders,start,end,previous)}));
        const month=periods[2],year=periods[3];
        const allValid=orders.filter((order)=>String(order.status||'').toLowerCase()!=='cancelado');
        const lifetimeTotal=dashboardTotal(allValid);
        const lifetimeQuantity=allValid.reduce((sum,order)=>sum+(Array.isArray(order.itens)?order.itens:[]).reduce((itemSum,item)=>itemSum+(Number(item.quantidade)||0),0),0);
        const monthlyShare=year.total>0?Math.min(100,(month.total/year.total)*100):0;
        const evolutionModes=['30 dias','4 semanas','12 meses','6 anos'];
        let lastDays=[];
        const evolutionItemCount=(source)=>source.reduce((sum,order)=>sum+(Array.isArray(order.itens)?order.itens:[]).reduce((itemSum,item)=>itemSum+(Number(item.quantidade)||0),0),0);
        if(dashboardEvolutionMode===1){const currentWeek=dashboardStartWeek(now);lastDays=Array.from({length:4},(_,index)=>{const start=dashboardAdd(currentWeek,'week',index-3);const end=dashboardAdd(start,'week',1);const previousStart=dashboardAdd(start,'week',-1);const periodOrders=dashboardOrdersIn(orders,start,end);const previousOrders=dashboardOrdersIn(orders,previousStart,start);const total=dashboardTotal(periodOrders);const previousTotal=dashboardTotal(previousOrders);return {label:`${String(start.getDate()).padStart(2,'0')}/${String(start.getMonth()+1).padStart(2,'0')}`,period:`Semana de ${start.toLocaleDateString('pt-BR')}`,total,previousTotal,orderCount:periodOrders.length,previousOrderCount:previousOrders.length,itemCount:evolutionItemCount(periodOrders),previousItemCount:evolutionItemCount(previousOrders),delta:dashboardDelta(total,previousTotal)};});}
        else if(dashboardEvolutionMode===2){lastDays=Array.from({length:12},(_,index)=>{const start=new Date(now.getFullYear(),index,1);const end=dashboardAdd(start,'month',1);const previousStart=dashboardAdd(start,'month',-1);const periodOrders=dashboardOrdersIn(orders,start,end);const previousOrders=dashboardOrdersIn(orders,previousStart,start);const total=dashboardTotal(periodOrders);const previousTotal=dashboardTotal(previousOrders);return {label:start.toLocaleDateString('pt-BR',{month:'short'}).replace('.',''),period:start.toLocaleDateString('pt-BR',{month:'long',year:'numeric'}),total,previousTotal,orderCount:periodOrders.length,previousOrderCount:previousOrders.length,itemCount:evolutionItemCount(periodOrders),previousItemCount:evolutionItemCount(previousOrders),delta:dashboardDelta(total,previousTotal)};});}
        else if(dashboardEvolutionMode===3){lastDays=Array.from({length:6},(_,index)=>{const start=new Date(now.getFullYear()-5+index,0,1);const end=dashboardAdd(start,'year',1);const previousStart=dashboardAdd(start,'year',-1);const periodOrders=dashboardOrdersIn(orders,start,end);const previousOrders=dashboardOrdersIn(orders,previousStart,start);const total=dashboardTotal(periodOrders);const previousTotal=dashboardTotal(previousOrders);return {label:String(start.getFullYear()),period:`Ano ${start.getFullYear()}`,total,previousTotal,orderCount:periodOrders.length,previousOrderCount:previousOrders.length,itemCount:evolutionItemCount(periodOrders),previousItemCount:evolutionItemCount(previousOrders),delta:dashboardDelta(total,previousTotal)};});}
        else{lastDays=Array.from({length:30},(_,index)=>{const start=dashboardAdd(dayStart,'day',-index);const periodOrders=dashboardOrdersIn(orders,start,dashboardAdd(start,'day',1));const total=dashboardTotal(periodOrders);const previousOrder=orders.map((order)=>({order,date:dashboardDate(order.created_at||order.createdAt)})).filter((entry)=>entry.date&&entry.date<start&&String(entry.order.status||'').toLowerCase()!=='cancelado').sort((a,b)=>b.date-a.date)[0];const previousStart=previousOrder?dashboardStartDay(previousOrder.date):null;const previousOrders=previousStart?dashboardOrdersIn(orders,previousStart,dashboardAdd(previousStart,'day',1)):[];const previousTotal=dashboardTotal(previousOrders);return {label:`${String(start.getDate()).padStart(2,'0')}/${String(start.getMonth()+1).padStart(2,'0')}`,period:start.toLocaleDateString('pt-BR',{weekday:'long',day:'2-digit',month:'2-digit',year:'numeric'}),total,previousTotal,orderCount:periodOrders.length,previousOrderCount:previousOrders.length,itemCount:evolutionItemCount(periodOrders),previousItemCount:evolutionItemCount(previousOrders),delta:total>0?dashboardDelta(total,previousTotal):0};});}
        const maxDay=Math.max(1,...lastDays.map(item=>item.total));
        const productSelectedDate=dashboardAdd(monthStart,'month',dashboardProductMonthOffset);
        const productPeriodStart=dashboardProductPeriodMode==='year'?new Date(productSelectedDate.getFullYear(),0,1):productSelectedDate;
        const productPeriodEnd=dashboardAdd(productPeriodStart,dashboardProductPeriodMode==='year'?'year':'month',1);
        const previousPeriodStart=dashboardAdd(productPeriodStart,dashboardProductPeriodMode==='year'?'year':'month',-1);
        const currentMonthOrders=dashboardOrdersIn(orders,productPeriodStart,productPeriodEnd);
        const previousMonthOrders=dashboardOrdersIn(orders,previousPeriodStart,productPeriodStart);
        const productMonthLabel=productSelectedDate.toLocaleDateString('pt-BR',{month:'long'});
        const productYearLabel=String(productSelectedDate.getFullYear());
        const aggregateProducts=(source)=>{const map=new Map();source.forEach(order=>(Array.isArray(order.itens)?order.itens:[]).forEach(item=>{const id=String(item.produto_id||item.id||item.nome||'produto');const current=map.get(id)||{id,name:item.nome||item.produto_nome||`Produto ${id}`,sku:item.sku||'',quantity:0,total:0};current.quantity+=Number(item.quantidade)||0;current.total+=(Number(item.quantidade)||0)*(Number(item.preco_unit??item.preco)||0);map.set(id,current);}));return map;};
        const products=aggregateProducts(currentMonthOrders),previousProducts=aggregateProducts(previousMonthOrders);const totalProductQty=Array.from(products.values()).reduce((sum,item)=>sum+item.quantity,0);
        const allRanking=Array.from(products.values()).sort((a,b)=>b.quantity-a.quantity);
        const rankingTotalPages=Math.max(1,Math.ceil(allRanking.length/dashboardProductPerPage));
        dashboardProductPage=Math.min(Math.max(1,dashboardProductPage),rankingTotalPages);
        const rankingOffset=(dashboardProductPage-1)*dashboardProductPerPage;
        const ranking=allRanking.slice(rankingOffset,rankingOffset+dashboardProductPerPage);
        const rankingStart=allRanking.length?rankingOffset+1:0;
        const rankingEnd=Math.min(allRanking.length,rankingOffset+dashboardProductPerPage);
        const rankingPageStart=Math.max(1,Math.min(dashboardProductPage-3,rankingTotalPages-6));
        const rankingPages=Array.from({length:Math.min(7,rankingTotalPages)},(_,index)=>rankingPageStart+index);
        const rankingPageButtons=rankingPages.map((page)=>`<button type="button" class="portal-dashboard__page-btn${page===dashboardProductPage?' active':''}" data-dashboard-product-page="${page}"${page===dashboardProductPage?' aria-current="page"':''}>${page}</button>`).join('');
        const rankingSizeOptions=[9,15,25,50].map((size)=>`<option value="${size}"${size===dashboardProductPerPage?' selected':''}>${size}</option>`).join('');
        const rankingPagination=allRanking.length?`<div class="portal-dashboard__pagination" data-dashboard-product-pagination><span>${rankingStart}-${rankingEnd} de ${allRanking.length}</span><div class="portal-dashboard__pagination-pages">${rankingPageButtons}<label>Ir para <input class="portal-dashboard__page-input" type="number" min="1" max="${rankingTotalPages}" value="${dashboardProductPage}" data-dashboard-product-page-input></label></div><label class="portal-dashboard__pagination-size"><span>Mostrar</span><select class="portal-dashboard__page-size" data-dashboard-product-page-size>${rankingSizeOptions}</select><input class="portal-dashboard__page-input" type="number" min="1" max="500" value="${dashboardProductPerPage}" data-dashboard-product-page-size-manual><span>por página</span></label></div>`:'';
        const metricHtml=periods.map((period)=>`<article class="portal-dashboard__metric"><div class="portal-dashboard__metric-label"><span>${period.label}</span>${dashboardDeltaHtml(period.delta)}</div><strong>${formatCurrency(period.total)}</strong><small>${period.orders} pedido${period.orders===1?'':'s'} · ${period.orderDelta>=0?'+':''}${period.orderDelta.toLocaleString('pt-BR',{maximumFractionDigits:1})}% em quantidade</small></article>`).join('');
        const rankingHtml=ranking.length?ranking.map((product,index)=>{const previous=previousProducts.get(product.id)?.quantity||0;const delta=dashboardDelta(product.quantity,previous);const share=totalProductQty>0?(product.quantity/totalProductQty)*100:0;return `<article class="portal-dashboard__product"><span class="portal-dashboard__rank">${index+1}</span><div class="portal-dashboard__product-name"><strong>${dashboardEscape(product.name)}</strong><small>${dashboardEscape(product.sku||'Sem SKU')} · ${share.toLocaleString('pt-BR',{maximumFractionDigits:1})}% do volume</small></div><div class="portal-dashboard__product-total"><strong>${product.quantity.toLocaleString('pt-BR',{maximumFractionDigits:2})}</strong><small>${delta>=0?'+':''}${delta.toLocaleString('pt-BR',{maximumFractionDigits:1})}%</small></div></article>`;}).join(''):'<div class="portal-dashboard__empty">Nenhum produto pedido neste mês.</div>';
        dashboardContainer.innerHTML=`<header class="portal-dashboard__hero"><div><span class="portal-dashboard__eyebrow">◉ Visão executiva</span><h3>Desempenho dos seus pedidos</h3><p>Valores reais, comparados automaticamente com o período anterior.</p></div><button type="button" class="portal-btn portal-btn--ghost portal-dashboard__refresh" data-dashboard-refresh><svg viewBox="0 0 24 24"><path d="M20 6v5h-5M4 18v-5h5"/><path d="M6.1 9a7 7 0 0 1 11.7-2.6L20 9M4 15l2.2 2.6A7 7 0 0 0 17.9 15"/></svg><span>Atualizar</span></button></header><section class="portal-dashboard__periods">${metricHtml}</section><div class="portal-dashboard__grid"><section class="portal-dashboard__section"><div class="portal-dashboard__section-head"><div><h4>Evolução dos últimos 7 dias</h4><p>Volume financeiro diário dos pedidos válidos.</p></div>${dashboardDeltaHtml(month.delta)}</div><div class="portal-dashboard__summary"><div><span>Total histórico</span><strong>${formatCurrency(lifetimeTotal)}</strong></div><div><span>Pedidos realizados</span><strong>${allValid.length.toLocaleString('pt-BR')}</strong></div><div><span>Itens solicitados</span><strong>${lifetimeQuantity.toLocaleString('pt-BR',{maximumFractionDigits:2})}</strong></div></div><div class="portal-dashboard__bars">${lastDays.map(item=>`<div class="portal-dashboard__bar-item"><span class="portal-dashboard__bar-value">${formatCurrency(item.total)}</span><span class="portal-dashboard__bar" style="height:${Math.max(3,(item.total/maxDay)*100)}%"></span><span class="portal-dashboard__bar-label">${item.label}</span></div>`).join('')}</div></section><section class="portal-dashboard__section"><div class="portal-dashboard__section-head"><div><h4>Participação mensal</h4><p>Quanto o mês representa no ano atual.</p></div></div><div class="portal-dashboard__donut-wrap"><div class="portal-dashboard__donut" style="--value:${monthlyShare}"><div class="portal-dashboard__donut-content"><strong>${monthlyShare.toLocaleString('pt-BR',{maximumFractionDigits:1})}%</strong><span>do gasto anual</span></div></div>${dashboardDeltaHtml(month.delta)}<small>${formatCurrency(month.total)} no mês</small></div></section></div><section class="portal-dashboard__section"><div class="portal-dashboard__section-head"><div><h4>Produtos mais pedidos no mês</h4><p>Ranking por quantidade, participação e comparação com o mês anterior.</p></div><strong>${totalProductQty.toLocaleString('pt-BR',{maximumFractionDigits:2})} itens</strong></div><div class="portal-dashboard__ranking">${rankingHtml}</div></section>`;
        const summaryIcons=['<path d="M3 7h18v12H3z"/><path d="M16 11h5v4h-5a2 2 0 0 1 0-4Z"/><path d="M5 7V5h13v2"/>','<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>','<path d="m12 3 8 4-8 4-8-4 8-4Z"/><path d="m4 7 8 4 8-4v10l-8 4-8-4V7Z"/><path d="M12 11v10"/>'];
        dashboardContainer.querySelectorAll('.portal-dashboard__summary>div').forEach((card,index)=>card.insertAdjacentHTML('afterbegin',`<span class="portal-dashboard__summary-icon" aria-hidden="true"><svg viewBox="0 0 24 24">${summaryIcons[index]||summaryIcons[0]}</svg></span>`));
        const dashboardRefreshButton=dashboardContainer.querySelector('[data-dashboard-refresh]');
        if(dashboardRefreshButton){const heroActions=document.createElement('div');heroActions.className='portal-dashboard__hero-actions';dashboardRefreshButton.before(heroActions);heroActions.insertAdjacentHTML('afterbegin','<button type="button" class="portal-btn portal-btn--primary portal-dashboard__order-btn" data-dashboard-new-order><svg viewBox="0 0 24 24"><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 8H7"/><path d="M14 4v6M11 7h6"/></svg><span>Fazer pedido</span></button>');heroActions.appendChild(dashboardRefreshButton);heroActions.querySelector('[data-dashboard-new-order]')?.addEventListener('click',()=>setActiveTab('novo-pedido'));}
        dashboardContainer.querySelector('[data-dashboard-refresh]')?.addEventListener('click',()=>carregarDashboard(true));
        const evolutionBars=dashboardContainer.querySelector('.portal-dashboard__bars');
        if(dashboardEvolutionMode===0&&evolutionBars){evolutionBars.classList.add('is-daily');evolutionBars.scrollLeft=0;evolutionBars.addEventListener('wheel',(event)=>{if(Math.abs(event.deltaY)>Math.abs(event.deltaX)){event.preventDefault();evolutionBars.scrollLeft+=event.deltaY;}},{passive:false});}
        const evolutionComparisonTitle=dashboardEvolutionMode===0?'Comparação com o último dia que teve pedido':(dashboardEvolutionMode===1?'Comparação com a semana anterior':(dashboardEvolutionMode===2?'Comparação com o mês anterior':'Comparação com o ano anterior'));
        dashboardContainer.querySelectorAll('.portal-dashboard__bar-item').forEach((bar,index)=>{const delta=Number(lastDays[index]?.delta)||0;const state=delta>0?'up':(delta<0?'down':'neutral');const sign=delta>0?'+':(delta<0?'−':'');bar.querySelector('.portal-dashboard__bar-value')?.insertAdjacentHTML('afterend',`<span class="portal-dashboard__bar-delta ${state}" title="${evolutionComparisonTitle}">${sign}${Math.abs(delta).toLocaleString('pt-BR',{maximumFractionDigits:1})}%</span>`);});
        document.querySelector('[data-dashboard-chart-tooltip]')?.remove();
        const chartTooltip=document.createElement('div');chartTooltip.className='portal-dashboard__chart-tooltip';chartTooltip.dataset.dashboardChartTooltip='';chartTooltip.setAttribute('role','tooltip');document.body.appendChild(chartTooltip);
        const positionChartTooltip=(x,y)=>{const width=chartTooltip.offsetWidth||330;const height=chartTooltip.offsetHeight||190;chartTooltip.style.left=`${Math.max(12,Math.min(window.innerWidth-width-12,x+14))}px`;chartTooltip.style.top=`${Math.max(12,Math.min(window.innerHeight-height-12,y+14))}px`;};
        const showChartTooltip=(bar,item,event)=>{const delta=Number(item?.delta)||0;const difference=(Number(item?.total)||0)-(Number(item?.previousTotal)||0);const state=delta>0?'up':(delta<0?'down':'neutral');const status=(Number(item?.total)||0)===0?'Sem movimento':(delta>0?'Aumento':(delta<0?'Redução':'Estável'));const sign=delta>0?'+':(delta<0?'−':'');chartTooltip.innerHTML=`<div class="portal-dashboard__tooltip-head"><strong>${dashboardEscape(item?.period||item?.label||'Período')}</strong><span class="portal-dashboard__tooltip-status ${state}">${delta>0?'↑':(delta<0?'↓':'—')} ${status}</span></div><div class="portal-dashboard__tooltip-grid"><div class="portal-dashboard__tooltip-item"><span>Compras no período</span><strong>${formatCurrency(item?.total||0)}</strong></div><div class="portal-dashboard__tooltip-item"><span>Base de comparação</span><strong>${formatCurrency(item?.previousTotal||0)}</strong></div><div class="portal-dashboard__tooltip-item ${difference>0?'positive':(difference<0?'negative':'')}"><span>Diferença em valor</span><strong>${difference>0?'+':''}${formatCurrency(difference)}</strong></div><div class="portal-dashboard__tooltip-item ${state==='up'?'positive':(state==='down'?'negative':'')}"><span>Variação percentual</span><strong>${sign}${Math.abs(delta).toLocaleString('pt-BR',{maximumFractionDigits:1})}%</strong></div></div>`;chartTooltip.classList.add('is-visible');const rect=bar.getBoundingClientRect();positionChartTooltip(event?.clientX??(rect.left+rect.width/2),event?.clientY??rect.top);};
        const showChartTooltipWithCounts=(bar,item,event)=>{showChartTooltip(bar,item,event);const orderDelta=dashboardDelta(Number(item?.orderCount)||0,Number(item?.previousOrderCount)||0);const itemDelta=dashboardDelta(Number(item?.itemCount)||0,Number(item?.previousItemCount)||0);const indicator=(value)=>{const state=value>0?'up':(value<0?'down':'neutral');const sign=value>0?'+':(value<0?'−':'');return `<span class="portal-dashboard__tooltip-metric-delta ${state}">${sign}${Math.abs(value).toLocaleString('pt-BR',{maximumFractionDigits:1})}%</span>`;};chartTooltip.querySelector('.portal-dashboard__tooltip-grid')?.insertAdjacentHTML('beforeend',`<div class="portal-dashboard__tooltip-item"><span>Número de pedidos</span><strong class="portal-dashboard__tooltip-metric"><b>${Number(item?.orderCount||0).toLocaleString('pt-BR')}</b>${indicator(orderDelta)}</strong></div><div class="portal-dashboard__tooltip-item"><span>Quantidade de itens</span><strong class="portal-dashboard__tooltip-metric"><b>${Number(item?.itemCount||0).toLocaleString('pt-BR',{maximumFractionDigits:2})}</b>${indicator(itemDelta)}</strong></div>`);};
        dashboardContainer.querySelectorAll('.portal-dashboard__bar-item').forEach((bar,index)=>{bar.setAttribute('aria-label',`${lastDays[index]?.period||lastDays[index]?.label}: ${formatCurrency(lastDays[index]?.total||0)}`);bar.addEventListener('mouseenter',(event)=>showChartTooltipWithCounts(bar,lastDays[index],event));bar.addEventListener('mousemove',(event)=>positionChartTooltip(event.clientX,event.clientY));bar.addEventListener('mouseleave',()=>chartTooltip.classList.remove('is-visible'));});
        dashboardContainer.querySelectorAll('.portal-dashboard__rank').forEach((rank,index)=>{rank.textContent=String(rankingOffset+index+1);});
        dashboardContainer.querySelectorAll('.portal-dashboard__product').forEach((card,index)=>{const product=ranking[index];if(!product)return;const previous=previousProducts.get(product.id)?.quantity||0;const delta=dashboardDelta(product.quantity,previous);const state=delta>0?'up':(delta<0?'down':'neutral');const indicator=card.querySelector('.portal-dashboard__product-total small');if(indicator){indicator.className=`portal-dashboard__product-delta ${state}`;indicator.textContent=`${delta>0?'↑ +':(delta<0?'↓ −':'— ')}${Math.abs(delta).toLocaleString('pt-BR',{maximumFractionDigits:1})}%`;}});
        const evolutionTitle=dashboardContainer.querySelector('.portal-dashboard__grid .portal-dashboard__section h4');
        if(evolutionTitle){evolutionTitle.textContent=dashboardEvolutionMode===0?'Evolução dos últimos 30 dias':(dashboardEvolutionMode===1?'Evolução das últimas 4 semanas':(dashboardEvolutionMode===2?'Evolução dos 12 meses':'Evolução dos últimos 6 anos'));evolutionTitle.classList.add('portal-dashboard__evolution-title');evolutionTitle.insertAdjacentHTML('afterbegin','<span class="portal-dashboard__evolution-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="m6 15 4-4 3 3 6-7"/><path d="M15 7h4v4"/></svg></span>');}
        const evolutionHead=evolutionTitle?.closest('.portal-dashboard__section-head');
        const evolutionWithMovement=lastDays.filter((item)=>(Number(item.total)||0)>0);
        const evolutionAverage=evolutionWithMovement.length?evolutionWithMovement.reduce((sum,item)=>sum+(Number(item.delta)||0),0)/evolutionWithMovement.length:0;
        const evolutionAverageLabel=['Média diária','Média semanal','Média mensal','Média anual'][dashboardEvolutionMode];
        const evolutionAverageState=evolutionAverage>0?'up':(evolutionAverage<0?'down':'neutral');
        const evolutionAverageSign=evolutionAverage>0?'+':(evolutionAverage<0?'−':'');
        const evolutionAveragePill=evolutionHead?Array.from(evolutionHead.children).find((child)=>child.classList?.contains('portal-dashboard__delta')):null;
        if(evolutionAveragePill){evolutionAveragePill.className=`portal-dashboard__delta portal-dashboard__evolution-average ${evolutionAverageState}`;evolutionAveragePill.textContent=`${evolutionAverage>0?'↑':(evolutionAverage<0?'↓':'—')} ${evolutionAverageLabel} ${evolutionAverageSign}${Math.abs(evolutionAverage).toLocaleString('pt-BR',{maximumFractionDigits:1})}%`;}
        evolutionHead?.insertAdjacentHTML('beforeend',`<div class="portal-dashboard__evolution-nav" aria-label="Navegar pelo período do gráfico"><button type="button" data-dashboard-evolution="prev" aria-label="Período anterior"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button><strong>${evolutionModes[dashboardEvolutionMode]}</strong><button type="button" data-dashboard-evolution="next" aria-label="Próximo período"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button></div>`);
        evolutionHead?.querySelectorAll('[data-dashboard-evolution]').forEach((button)=>button.addEventListener('click',()=>{dashboardEvolutionMode=(dashboardEvolutionMode+(button.dataset.dashboardEvolution==='next'?1:-1)+evolutionModes.length)%evolutionModes.length;renderDashboard(dashboardOrdersCache);}));
        const rankingSection=dashboardContainer.querySelector('.portal-dashboard__ranking')?.closest('.portal-dashboard__section');
        const rankingEmpty=rankingSection?.querySelector('.portal-dashboard__ranking .portal-dashboard__empty');
        if(rankingEmpty)rankingEmpty.textContent=dashboardProductPeriodMode==='year'?'Nenhum produto pedido neste ano.':'Nenhum produto pedido neste mês.';
        const rankingTitle=rankingSection?.querySelector('h4');
        if(rankingTitle){rankingTitle.textContent=dashboardProductPeriodMode==='year'?'Produtos mais pedidos no ano':'Produtos mais pedidos no mês';const rankingSubtitle=rankingTitle.parentElement?.querySelector('p');if(rankingSubtitle)rankingSubtitle.textContent=dashboardProductPeriodMode==='year'?'Ranking por quantidade, participação e comparação com o ano anterior.':'Ranking por quantidade, participação e comparação com o mês anterior.';rankingTitle.classList.add('portal-dashboard__ranking-title');rankingTitle.insertAdjacentHTML('afterbegin','<span class="portal-dashboard__ranking-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/><path d="m3 6 5-3 5 4 7-5"/></svg></span>');}
        const rankingHead=rankingTitle?.closest('.portal-dashboard__section-head');
        const productMonthNavigator=dashboardProductPeriodMode==='month'?`<div class="portal-dashboard__evolution-nav portal-dashboard__product-month-nav" aria-label="Navegar pelos meses do ranking"><button type="button" data-dashboard-product-period="prev-month" aria-label="Mês anterior"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button><strong>${dashboardEscape(productMonthLabel)}</strong><button type="button" data-dashboard-product-period="next-month" aria-label="Próximo mês"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button></div>`:'';
        rankingHead?.insertAdjacentHTML('beforeend',`<div class="portal-dashboard__product-nav-group"><div class="portal-dashboard__product-mode" aria-label="Tipo de comparação dos produtos"><button type="button" data-dashboard-product-mode="month" class="${dashboardProductPeriodMode==='month'?'is-active':''}">Mensal</button><button type="button" data-dashboard-product-mode="year" class="${dashboardProductPeriodMode==='year'?'is-active':''}">Anual</button></div><div class="portal-dashboard__evolution-nav portal-dashboard__product-year-nav" aria-label="Navegar pelos anos do ranking"><button type="button" data-dashboard-product-period="prev-year" aria-label="Ano anterior"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button><strong>${dashboardEscape(productYearLabel)}</strong><button type="button" data-dashboard-product-period="next-year" aria-label="Próximo ano"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button></div>${productMonthNavigator}</div>`);
        rankingHead?.querySelectorAll('[data-dashboard-product-mode]').forEach((button)=>button.addEventListener('click',()=>{dashboardProductPeriodMode=button.dataset.dashboardProductMode==='year'?'year':'month';dashboardProductPage=1;renderDashboard(dashboardOrdersCache);}));
        rankingHead?.querySelectorAll('[data-dashboard-product-period]').forEach((button)=>button.addEventListener('click',()=>{const action=button.dataset.dashboardProductPeriod;dashboardProductMonthOffset+=action==='next-year'?12:(action==='prev-year'?-12:(action==='next-month'?1:-1));dashboardProductPage=1;renderDashboard(dashboardOrdersCache);}));
        if(rankingSection&&rankingPagination)rankingSection.insertAdjacentHTML('beforeend',rankingPagination);
        const pagination=dashboardContainer.querySelector('[data-dashboard-product-pagination]');
        pagination?.addEventListener('click',(event)=>{const button=event.target.closest('[data-dashboard-product-page]');if(!button)return;dashboardProductPage=Number(button.dataset.dashboardProductPage)||1;renderDashboard(dashboardOrdersCache);});
        const applyDashboardPagination=(target)=>{if(target.matches('[data-dashboard-product-page-input]'))dashboardProductPage=Number(target.value)||1;else if(target.matches('[data-dashboard-product-page-size]')){dashboardProductPerPage=Number(target.value)||9;dashboardProductPage=1;}else if(target.matches('[data-dashboard-product-page-size-manual]')){dashboardProductPerPage=Math.min(500,Math.max(1,Number(target.value)||9));dashboardProductPage=1;}else return;renderDashboard(dashboardOrdersCache);};
        pagination?.addEventListener('change',(event)=>applyDashboardPagination(event.target));
        pagination?.addEventListener('keydown',(event)=>{if(event.key==='Enter'&&event.target.matches('input')){event.preventDefault();applyDashboardPagination(event.target);}});
    };

    const carregarDashboard = async (force=false) => {
        if (!dashboardContainer || !state.token || dashboardAtualizando) return;
        if (dashboardCarregado && !force) return;
        const cacheKey = `portal-dashboard-cache:${Number(state.cliente?.id || 0)}`;
        if (!dashboardOrdersCache.length) {
            try {
                const cached = JSON.parse(sessionStorage.getItem(cacheKey) || '[]');
                if (Array.isArray(cached)) dashboardOrdersCache = cached;
            } catch (error) { dashboardOrdersCache = []; }
        }
        if (dashboardOrdersCache.length) renderDashboard(dashboardOrdersCache);
        else dashboardContainer.innerHTML = '<div class="portal-dashboard__loading">Atualizando indicadores...</div>';
        dashboardAtualizando = true;
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(`${basePath}/api/pedidos`, {
                cache: 'no-store',
                signal: controller.signal,
                headers: { 'Authorization': `Bearer ${state.token}`, 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
            });
            if (response.status === 401) { leaveApp(); return; }
            if (!response.ok) throw new Error('Não foi possível carregar o Dashboard.');
            const json = await response.json();
            dashboardOrdersCache = Array.isArray(json.pedidos) ? json.pedidos : [];
            sessionStorage.setItem(cacheKey, JSON.stringify(dashboardOrdersCache));
            dashboardCarregado = true;
            renderDashboard(dashboardOrdersCache);
        } catch(error) {
            dashboardCarregado = dashboardOrdersCache.length > 0;
            if (dashboardOrdersCache.length) {
                renderDashboard(dashboardOrdersCache);
            } else {
                const message = error?.name === 'AbortError' ? 'A conexão demorou mais que o esperado.' : (error.message || 'Não foi possível carregar os indicadores.');
                dashboardContainer.innerHTML = `<div class="portal-dashboard__empty"><strong>${dashboardEscape(message)}</strong><span>Verifique sua conexão e tente novamente.</span><button type="button" class="portal-btn portal-btn--primary" data-dashboard-retry>Tentar novamente</button></div>`;
                dashboardContainer.querySelector('[data-dashboard-retry]')?.addEventListener('click', () => carregarDashboard(true));
            }
        } finally {
            window.clearTimeout(timeout);
            dashboardAtualizando = false;
        }
    };

    const carregarPedidos = async (force = false, silent = false) => {
        if (!ordersTableBody) {
            return;
        }
        if (pedidosAtualizando) {
            return;
        }
        if (!state.token) {
            pedidosCarregados = false;
            pedidosSignature = '';
            renderPedidos([]);
            setFeedback(ordersFeedback, 'Faça login para visualizar seus pedidos.', '');
            return;
        }
        if (pedidosCarregados && !force) {
            return;
        }
        pedidosAtualizando = true;
        if (!silent) {
            setFeedback(ordersFeedback, 'Carregando pedidos…');
        }
        try {
            const response = await fetch(`${basePath}/api/pedidos`, {
                cache: 'no-store',
                headers: {
                    'Authorization': `Bearer ${state.token}`,
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache',
                },
            });
            if (response.status === 401) {
                leaveApp();
                setFeedback(ordersFeedback, 'Sua sessão expirou. Entre novamente para continuar.', 'error');
                return;
            }
            if (!response.ok) {
                throw new Error('Não foi possível carregar a lista de pedidos.');
            }
            const json = await response.json();
            const lista = Array.isArray(json.pedidos) ? json.pedidos : [];
            const nextSignature = JSON.stringify(lista);
            const changed = nextSignature !== pedidosSignature;
            pedidosCarregados = true;
            pedidosSignature = nextSignature;
            if (changed || !silent) {
                renderPedidos(lista);
            }
            if (!silent) {
                setFeedback(ordersFeedback, lista.length ? 'Pedidos atualizados.' : 'Nenhum pedido enviado ainda.', lista.length ? 'success' : '');
            }
        } catch (error) {
            pedidosCarregados = false;
            if (!silent) {
                setFeedback(ordersFeedback, error.message || 'Não foi possível atualizar seus pedidos.', 'error');
            }
        } finally {
            pedidosAtualizando = false;
        }
    };

    const setActiveTab = (target) => {
        if (!portalTabButtons.length || !portalTabPanels.length) {
            return;
        }
        const validTabs = Array.from(portalTabPanels, panel => panel.dataset.portalTabPanel).filter(Boolean);
        const nextTab = validTabs.includes(target) ? target : 'dashboard';
        activeTab = nextTab;
        portalTabButtons.forEach((button) => {
            const isActive = button.dataset.portalTabTrigger === nextTab;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        portalTabPanels.forEach((panel) => {
            if (panel.dataset.portalTabPanel === nextTab) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', '');
            }
        });
        if (document.body.getAttribute('data-portal-state') === 'app') {
            localStorage.setItem('portal-active-tab', nextTab);
            if (nextTab !== 'editar-pedido') localStorage.removeItem('portal-edit-order-id');
        }
        if (nextTab === 'dashboard') {
            carregarDashboard();
        } else if (nextTab === 'pedidos') {
            carregarPedidos();
        } else if (nextTab === 'novo-pedido' && catalogNeedsRender) {
            desenharCatalogo(true);
        }
    };

    if (welcomeModal) {
        welcomeModal.setAttribute('aria-hidden', 'true');
        welcomeDismissTriggers.forEach((button) => {
            button.addEventListener('click', () => closeWelcomeModal());
        });
        welcomeGoButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                const targetTab = event.currentTarget.dataset.welcomeGo;
                closeWelcomeModal();
                if (targetTab) {
                    setActiveTab(targetTab);
                }
            });
        });
    }

    portalHeaderJumps.forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.dataset.portalJump;
            if (target) {
                closeWelcomeModal();
                setActiveTab(target);
            }
        });
    });

    const atualizarCarrinho = () => {
        cartLines.innerHTML = '';
        if (cart.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="4" class="portal-cart-empty">Nenhum item no carrinho.</td>';
            cartLines.appendChild(row);
            cartTotal.textContent = 'R$ 0,00';
            return;
        }
        let total = 0;
        cart.forEach((item) => {
            const subtotal = item.quantidade * item.preco;
            total += subtotal;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.nome}</td>
                <td>${item.quantidade.toFixed(2)}</td>
                <td>${formatCurrency(item.preco)}</td>
                <td>${formatCurrency(subtotal)}</td>
            `;
            cartLines.appendChild(row);
        });
        cartTotal.textContent = formatCurrency(total);
    };

    const syncCatalogView = () => {
        if (catalogContainer) {
            catalogContainer.dataset.view = catalogView;
        }
        viewToggleButtons.forEach((button) => {
            const active = button.dataset.catalogView === catalogView;
            button.classList.toggle('active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };

    const updateImageLightbox = () => {
        if (!imageLightbox || !imageLightboxImg) return;
        const currentImage = imageLightboxGallery[imageLightboxIndex] || {};
        const caption = currentImage.label || 'Imagem do produto';
        imageLightboxImg.src = currentImage.url || '';
        imageLightboxImg.alt = caption || 'Imagem ampliada do produto';
        if (imageLightboxCaption) {
            imageLightboxCaption.textContent = caption;
        }
        const hasNavigation = imageLightboxGallery.length > 1;
        imageLightboxPrev?.toggleAttribute('hidden', !hasNavigation);
        imageLightboxNext?.toggleAttribute('hidden', !hasNavigation);
    };

    const moveImageLightbox = (direction) => {
        if (imageLightboxGallery.length < 2) return;
        imageLightboxIndex = (imageLightboxIndex + direction + imageLightboxGallery.length) % imageLightboxGallery.length;
        updateImageLightbox();
    };

    const openImageLightbox = (images, index = 0) => {
        if (!imageLightbox || !imageLightboxImg) return;
        imageLightboxGallery = Array.isArray(images) ? images.filter((image) => image?.url) : [];
        if (!imageLightboxGallery.length) return;
        imageLightboxIndex = Math.min(Math.max(Number(index) || 0, 0), imageLightboxGallery.length - 1);
        updateImageLightbox();
        imageLightbox.classList.add('is-open');
        imageLightbox.setAttribute('aria-hidden', 'false');
        imageLightboxClose?.focus();
    };

    const closeImageLightbox = () => {
        if (!imageLightbox || !imageLightboxImg) return;
        imageLightbox.classList.remove('is-open');
        imageLightbox.setAttribute('aria-hidden', 'true');
        imageLightboxImg.src = '';
        imageLightboxGallery = [];
        imageLightboxIndex = 0;
    };

    const updateCatalogCount = () => {
        if (!catalogContainer || !catalogCount) return;
        const visibleCount = Array.from(catalogContainer.querySelectorAll('.portal-product'))
            .filter((card) => !card.hidden && window.getComputedStyle(card).display !== 'none')
            .length;
        catalogCount.textContent = visibleCount === 1 ? '1 produto' : `${visibleCount} produtos`;
    };

    const desenharCatalogo = (force = false) => {
        if (!catalogContainer) return;
        if (!force && activeTab !== 'novo-pedido') {
            catalogNeedsRender = true;
            return;
        }
        catalogNeedsRender = false;
        syncCatalogView();
        catalogContainer.innerHTML = '';
        const fragment = document.createDocumentFragment();
        catalogo.forEach((produto) => {
            const card = document.createElement('article');
            card.className = `portal-product portal-product--${catalogView}`;
            card.dataset.category = produto.categoria || '';
            card.dataset.name = produto.nome || '';
            card.dataset.sku = produto.sku || '';
            card.dataset.price = String(Number(produto.preco || 0));
            card.dataset.validity = String(Number(produto.shelf_life_dias || 0));
            const pesoFormatado = formatWeight(produto.peso_liquido);
            const productImages = [
                { url: produto.imagem_url, label: 'Principal' },
                { url: produto.imagem_pronto_url, label: 'Pronto / frito' },
            ].filter((image) => image.url);
            const imageMarkup = productImages.length
                ? `<div class="portal-product__media" data-product-gallery='${JSON.stringify(productImages).replace(/'/g, '&apos;')}' data-gallery-index="0">
                    <img src="${productImages[0].url}" alt="${produto.nome || 'Produto'} - ${productImages[0].label}" loading="lazy" decoding="async" fetchpriority="low">
                    <button type="button" class="portal-product__media-zoom" data-gallery-zoom aria-label="Expandir imagem do produto"><span class="portal-product__media-zoom-mark"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"></circle><path d="m16 16 4.5 4.5"></path><path d="M10.5 7.6v5.8"></path><path d="M7.6 10.5h5.8"></path></svg></span></button>
                    <span class="portal-product__media-caption">${productImages[0].label}</span>
                    ${productImages.length > 1 ? `<button type="button" class="portal-product__media-toggle" data-gallery-next aria-label="Ver outra foto do produto"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button>` : ''}
                </div>`
                : `<div class="portal-product__media"><span class="portal-product__media-placeholder"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"></path></svg></span></div>`;
            card.innerHTML = `
                ${imageMarkup}
                <div class="portal-product__header">
                    <div>
                        <h3>${produto.nome}</h3>
                        <small>SKU ${produto.sku} · ${produto.unidade}</small>
                    </div>
                    <span class="badge">${produto.tipo}</span>
                </div>
                ${pesoFormatado ? `<span class="portal-product__weight-pill">Peso ${pesoFormatado}</span>` : ''}
                <p class="portal-product__meta">Validade ( dias ): ${produto.shelf_life_dias || 0} dias</p>
                <div class="portal-product__footer">
                    <div class="portal-product__price">${formatCurrency(produto.preco)}</div>
                    <div class="portal-product__controls">
                        <span class="portal-product__label">Quantidade</span>
                        <div class="portal-product__input-group">
                            <input type="number" min="1" step="1" value="1" data-qtd="${produto.id}">
                            <button type="button" data-add="${produto.id}">Adicionar ao carrinho</button>
                        </div>
                    </div>
                </div>
            `;
            fragment.appendChild(card);
        });
        catalogContainer.appendChild(fragment);
        updateCatalogCount();
        catalogContainer.querySelectorAll('button[data-add]').forEach((button) => {
            button.addEventListener('click', (event) => {
                const id = Number(event.currentTarget.dataset.add);
                const produto = catalogo.find((item) => item.id === id);
                if (!produto) return;
                const card = event.currentTarget.closest('.portal-product');
                const input = card?.querySelector(`input[data-qtd="${id}"]`);
                const quantidade = Math.max(1, parseFloat(input?.value || '1'));
                const existente = cart.find((item) => item.id === id);
                if (existente) {
                    existente.quantidade += quantidade;
                } else {
                    cart.push({ ...produto, quantidade });
                }
                atualizarCarrinho();
            });
        });
        catalogContainer.querySelectorAll('[data-gallery-next]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const media = event.currentTarget.closest('.portal-product__media');
                if (!media) return;
                const images = JSON.parse(media.dataset.productGallery || '[]');
                if (images.length < 2) return;
                const nextIndex = (Number(media.dataset.galleryIndex || 0) + 1) % images.length;
                media.dataset.galleryIndex = String(nextIndex);
                const img = media.querySelector('img');
                const caption = media.querySelector('.portal-product__media-caption');
                if (img) {
                    img.src = images[nextIndex].url;
                    img.alt = images[nextIndex].label;
                }
                if (caption) {
                    caption.textContent = images[nextIndex].label;
                }
            });
        });
        catalogContainer.querySelectorAll('[data-gallery-zoom]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const media = event.currentTarget.closest('.portal-product__media');
                if (!media) return;
                const images = JSON.parse(media.dataset.productGallery || '[]');
                const currentIndex = Number(media.dataset.galleryIndex || 0);
                if (images.length) {
                    openImageLightbox(images, currentIndex);
                }
            });
        });
    };

    imageLightboxClose?.addEventListener('click', closeImageLightbox);
    imageLightboxPrev?.addEventListener('click', () => moveImageLightbox(-1));
    imageLightboxNext?.addEventListener('click', () => moveImageLightbox(1));
    imageLightbox?.addEventListener('click', (event) => {
        if (event.target === imageLightbox) {
            closeImageLightbox();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (!imageLightbox?.classList.contains('is-open')) return;
        if (event.key === 'Escape') {
            closeImageLightbox();
        }
        if (event.key === 'ArrowLeft') {
            moveImageLightbox(-1);
        }
        if (event.key === 'ArrowRight') {
            moveImageLightbox(1);
        }
    });

    const notifyPortalStorage = (key) => {
        try {
            window.dispatchEvent(new StorageEvent('storage', { key }));
        } catch (error) {
            const event = new Event('storage');
            event.key = key;
            window.dispatchEvent(event);
        }
    };

    const persistCliente = (cliente) => {
        if (!cliente) {
            localStorage.removeItem('portal-cliente');
            state.cliente = null;
            notifyPortalStorage('portal-cliente');
            return;
        }
        try {
            localStorage.setItem('portal-cliente', JSON.stringify(cliente));
        } catch (error) {
            // ignore storage failures
        }
        state.cliente = cliente;
        notifyPortalStorage('portal-cliente');
    };

    const enterApp = (cliente, options = {}) => {
        const { showWelcome = false, preferredTab = null } = options;
        const restoredTab = preferredTab || localStorage.getItem('portal-active-tab') || 'dashboard';
        persistCliente(cliente);
        const groupIds=Array.isArray(cliente?.grupos_produtos_ids)&&cliente.grupos_produtos_ids.length?cliente.grupos_produtos_ids.map(String):[String(cliente?.grupo_produtos_id||'')].filter(Boolean);
        const matchedGroups=portalClientGroups.filter(item=>groupIds.includes(String(item?.id||'')));
        const allowed=Array.isArray(cliente?.grupo_produtos_product_ids)
            ? new Set(cliente.grupo_produtos_product_ids.map(Number).filter(id=>id>0))
            : (matchedGroups.length
                ? new Set(matchedGroups.flatMap(group=>(Array.isArray(group.product_ids)?group.product_ids:[]).map(Number).filter(id=>id>0)))
                : null);
        catalogo.splice(0,catalogo.length,...catalogoCompleto.filter(produto=>!allowed||allowed.has(Number(produto.id))));
        desenharCatalogo();
        const displayName = cliente && (cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome)
            ? (cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome)
            : 'Cliente';
        if (welcomeName && welcomeName.dataset.portalGreetingManaged !== '1') {
            welcomeName.textContent = displayName;
        }
        if (clienteBadge) {
            clienteBadge.textContent = displayName;
        }
        pedidosCarregados = false;
        pedidosSignature = '';
        dashboardCarregado = false;
        setPortalState('app', restoredTab);
        renderListaProdutos();
        atualizarCarrinho();
        if (showWelcome) {
            openWelcomeModal(cliente);
        }
    };

    window.addEventListener('portal:cliente-atualizado', (event) => {
        const cliente = event.detail?.cliente && typeof event.detail.cliente === 'object'
            ? event.detail.cliente
            : state.cliente;
        if (!cliente) return;

        persistCliente(cliente);
        const explicitAllowed = Array.isArray(event.detail?.allowedProductIds)
            ? event.detail.allowedProductIds
            : cliente.grupo_produtos_product_ids;
        const groupIds = Array.isArray(cliente.grupos_produtos_ids) && cliente.grupos_produtos_ids.length
            ? cliente.grupos_produtos_ids.map(String)
            : [String(cliente.grupo_produtos_id || '')].filter(Boolean);
        const matchedGroups = portalClientGroups.filter((item) => groupIds.includes(String(item?.id || '')));
        let allowed = Array.isArray(explicitAllowed)
            ? new Set(explicitAllowed.map(Number).filter((id) => id > 0))
            : (matchedGroups.length
                ? new Set(matchedGroups.flatMap((group) => (Array.isArray(group.product_ids) ? group.product_ids : []).map(Number).filter((id) => id > 0)))
                : null);

        catalogo.splice(0, catalogo.length, ...catalogoCompleto.filter((produto) => !allowed || allowed.has(Number(produto.id))));
        desenharCatalogo();
        renderListaProdutos();
        atualizarCarrinho();

        const displayName = cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome || 'Cliente';
        if (welcomeName && welcomeName.dataset.portalGreetingManaged !== '1') welcomeName.textContent = displayName;
        if (clienteBadge) clienteBadge.textContent = displayName;
    });

    const leaveApp = () => {
        state.token = '';
        dashboardCarregado = false;
        persistCliente(null);
        localStorage.removeItem('portal-token');
        notifyPortalStorage('portal-token');
        setPortalState('auth', 'novo-pedido');
        setAuthMode('login');
        renderListaProdutos();
        atualizarCarrinho();
        if (clienteBadge) {
            clienteBadge.textContent = 'Cliente';
        }
        closeWelcomeModal();
    };

    const loadStoredCliente = () => {
        if (!state.token) return;
        try {
            const raw = localStorage.getItem('portal-cliente');
            if (raw) {
                const data = JSON.parse(raw);
                if (data && typeof data === 'object') {
                    state.cliente = data;
                    const storedName = data.nome_fantasia || data.nome_loja || data.razao_social || data.nome || 'Cliente';
                    if (welcomeName && welcomeName.dataset.portalGreetingManaged !== '1') {
                        welcomeName.textContent = storedName;
                    }
                    if (clienteBadge) {
                        clienteBadge.textContent = storedName;
                    }
                }
            }
        } catch (error) {
            // ignore parse errors
        }
    };

    const refreshClienteFromApi = async () => {
        if (!state.token) return null;
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 8000);
        try {
            const response = await fetch(`${basePath}/api/auth/cliente`, {
                cache: 'no-store',
                signal: controller.signal,
                headers: { 'Authorization': `Bearer ${state.token}`, 'Accept': 'application/json', 'Cache-Control': 'no-cache' },
            });
            const json = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(json.message || 'NÃ£o foi possÃ­vel sincronizar o cliente.');
            if (json.cliente) {
                persistCliente(json.cliente);
                return json.cliente;
            }
        } catch (error) {
            // Mantem os dados locais quando estiver offline ou com token expirado.
        } finally {
            window.clearTimeout(timeout);
        }
        return null;
    };

    let groupSyncInFlight = false;
    const sincronizarGrupoDoCliente = async () => {
        if (!state.token || document.visibilityState !== 'visible' || groupSyncInFlight) return;
        groupSyncInFlight = true;
        try {
            const cliente = await refreshClienteFromApi();
            if (cliente) {
                window.dispatchEvent(new CustomEvent('portal:cliente-atualizado', { detail: { cliente } }));
            }
        } finally {
            groupSyncInFlight = false;
        }
    };

    const aplicarCep = async (cep) => {
        if (!cepStatus) return;
        setFeedback(cepStatus, 'Consultando CEP…');
        cepStatus.dataset.state = '';
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            if (!response.ok) throw new Error('CEP não encontrado');
            const json = await response.json();
            if (json.erro) throw new Error('CEP inválido');
            document.getElementById('registro-endereco').value = json.logradouro || '';
            document.getElementById('registro-bairro').value = json.bairro || '';
            document.getElementById('registro-cidade').value = json.localidade || '';
            document.getElementById('registro-estado').value = (json.uf || '').toUpperCase();
            setFeedback(cepStatus, 'Endereço preenchido automaticamente.', 'success');
        } catch (error) {
            setFeedback(cepStatus, error.message || 'Não foi possível localizar o CEP.', 'error');
        }
    };

    if (cepInput) {
        cepInput.addEventListener('blur', () => {
            const somente = cepInput.value.replace(/\D+/g, '');
            if (somente.length === 8) {
                aplicarCep(somente);
            }
        });
    }

    if (estadoInput) {
        estadoInput.addEventListener('input', (event) => {
            const value = event.target.value.replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 2);
            event.target.value = value;
        });
    }

    authToggleButtons.forEach((button) => {
        button.addEventListener('click', () => setAuthMode(button.dataset.authTarget));
    });

    authSwitchLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            setAuthMode(link.dataset.authSwitch);
        });
    });

    docButtons.forEach((button) => {
        button.addEventListener('click', () => setDocumentType(button.dataset.docOption));
    });

    viewToggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetView = button.dataset.catalogView === 'list' ? 'list' : 'grid';
            if (catalogView !== targetView) {
                catalogView = targetView;
                localStorage.setItem('portal-catalog-view', catalogView);
                desenharCatalogo();
            }
        });
    });

    if (catalogContainer) {
        const catalogCountObserver = new MutationObserver(() => {
            window.requestAnimationFrame(updateCatalogCount);
        });
        catalogCountObserver.observe(catalogContainer, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style', 'hidden'],
        });
    }

    portalTabButtons.forEach((button) => {
        button.addEventListener('click', () => setActiveTab(button.dataset.portalTabTrigger));
    });

    if (ordersRefreshButton) {
        ordersRefreshButton.addEventListener('click', () => carregarPedidos(true));
    }

    window.setInterval(() => {
        if (activeTab === 'pedidos' && state.token && document.visibilityState === 'visible') {
            carregarPedidos(true, true);
        }
    }, 4000);

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && activeTab === 'pedidos' && state.token) {
            carregarPedidos(true, true);
        }
    });

    window.addEventListener('focus', () => {
        sincronizarGrupoDoCliente();
        if (activeTab === 'pedidos' && state.token) {
            carregarPedidos(true, true);
        }
    });

    window.setInterval(() => sincronizarGrupoDoCliente(), 4000);

    const atualizarTokenState = async () => {
        if (state.token) {
            loadStoredCliente();
            const syncedCliente = await refreshClienteFromApi();
            enterApp(syncedCliente || state.cliente || {}, { showWelcome: false });
            const restoredTab = localStorage.getItem('portal-active-tab');
            const restoredOrderId = Number(localStorage.getItem('portal-edit-order-id') || 0);
            if (restoredTab === 'editar-pedido' && restoredOrderId > 0) {
                abrirEditorPedido(restoredOrderId);
            }
        } else {
            leaveApp();
        }
    };

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const shell = button.closest('.portal-input-shell');
        const input = shell?.querySelector('input[type="password"], input[type="text"]');
        if (!input) return;
        button.addEventListener('click', () => {
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.classList.toggle('is-visible', !visible);
            button.setAttribute('aria-pressed', String(!visible));
            button.setAttribute('aria-label', visible ? 'Mostrar senha' : 'Ocultar senha');
            input.focus();
        });
    });

    if (loginForm) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFeedback(loginFeedback);
            setFeedback(loginFeedback, 'Autenticando…');
            try {
                const body = {
                    login: loginForm.login.value.trim(),
                    senha: loginForm.senha.value,
                };
                const response = await fetch(`${basePath}/api/auth/token`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body),
                });
                const json = await response.json();
                if (!response.ok) {
                    throw new Error(json.message || 'Não foi possível autenticar');
                }
                state.token = json.access_token;
                localStorage.setItem('portal-token', state.token);
                notifyPortalStorage('portal-token');
                persistCliente(json.cliente || null);
                setFeedback(loginFeedback, 'Autenticação concluída! Bem-vindo.', 'success');
                enterApp(json.cliente || null, { showWelcome: true, preferredTab: 'novo-pedido' });
                loginForm.reset();
                setTimeout(() => clearFeedback(loginFeedback), 2200);
            } catch (error) {
                setFeedback(loginFeedback, error.message || 'Falha na autenticação.', 'error');
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFeedback(registerFeedback);
            setFeedback(registerFeedback, 'Enviando dados…');
            const formData = Object.fromEntries(new FormData(registerForm).entries());
            try {
                const response = await fetch(`${basePath}/portal/cadastro`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData),
                });
                const json = await response.json();
                if (!response.ok) {
                    throw new Error(json.message || 'Não foi possível concluir o cadastro');
                }
                setFeedback(registerFeedback, json.message || 'Cadastro realizado com sucesso! Faça login para continuar.', 'success');
                localStorage.removeItem('portal-primary-hex');
                localStorage.removeItem('portal-primary-name');
                localStorage.removeItem('portal-cart-hex');
                registerForm.reset();
                setDocumentType('cnpj');
                setAuthMode('login');
            } catch (error) {
                setFeedback(registerFeedback, error.message || 'Não foi possível concluir o cadastro.', 'error');
            }
        });
    }

    document.getElementById('btn-finalizar')?.addEventListener('click', async () => {
        clearFeedback(orderFeedback);
        if (cart.length === 0) {
            setFeedback(orderFeedback, 'Adicione produtos ao carrinho antes de enviar o pedido.', 'error');
            return;
        }
        if (!state.token) {
            setFeedback(orderFeedback, 'Faça login para gerar o token antes de enviar o pedido.', 'error');
            setAuthMode('login');
            return;
        }
        setFeedback(orderFeedback, 'Enviando pedido…');
        const orderSnapshot = cart.map((item) => ({
            id: item.id,
            nome: item.nome,
            sku: item.sku,
            unidade: item.unidade,
            quantidade: Number(item.quantidade),
            preco: Number(item.preco),
        }));
        const itens = orderSnapshot.map((item) => ({
            produto_id: item.id,
            quantidade: item.quantidade,
            preco_unit: item.preco,
            desconto: 0,
        }));
        const total = orderSnapshot.reduce((acc, item) => acc + (item.quantidade * item.preco), 0);
        try {
            const response = await fetch(`${basePath}/api/pedidos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${state.token}`,
                },
                body: JSON.stringify({ itens, total }),
            });

            let json = {};
            try {
                json = await response.json();
            } catch (parseError) {
                json = {};
            }

            if (response.status === 401 || /token/i.test(json.message || '')) {
                leaveApp();
                setFeedback(orderFeedback, 'Sua sessão expirou. Faça login novamente para concluir o pedido.', 'error');
                return;
            }

            if (response.status === 422 && /cliente/i.test(json.message || '')) {
                leaveApp();
                setFeedback(orderFeedback, json.message || 'Não foi possível validar o cliente. Faça login novamente.', 'error');
                return;
            }

            if (!response.ok) {
                throw new Error(json.message || 'Falha ao registrar pedido');
            }

            const pedidoResposta = json.pedido || {};
            const statusKey = String(json.status ?? pedidoResposta.status ?? 'aprovacao').toLowerCase();
            const summary = {
                pedidoId: json.pedido_id ?? null,
                codigo: json.codigo ?? pedidoResposta.codigo ?? null,
                createdAt: json.created_at ?? pedidoResposta.created_at ?? null,
                total: Number.isFinite(Number(json.total ?? pedidoResposta.total ?? total)) ? Number(json.total ?? pedidoResposta.total ?? total) : total,
                itens: orderSnapshot,
                backorder: Array.isArray(json.backorder) ? json.backorder : [],
                status: statusKey,
                statusLabel: json.status_label ?? pedidoResposta.status_label ?? orderStatusLabels[statusKey] ?? '',
            };
            cart.splice(0, cart.length);
            dashboardCarregado = false;
            atualizarCarrinho();
            setFeedback(orderFeedback, 'Pedido enviado com sucesso! Confira o resumo abaixo.', 'success');
            showOrderSuccessModal(summary);
        } catch (error) {
            setFeedback(orderFeedback, error.message || 'Não foi possível concluir o pedido.', 'error');
        }
    });

    logoutButton?.addEventListener('click', () => {
        leaveApp();
        setFeedback(loginFeedback, 'Sessão encerrada. Faça login novamente.', 'success');
    });

    setDocumentType('cnpj');
    desenharCatalogo();
    renderListaProdutos();
    atualizarCarrinho();
    if (state.token) {
        atualizarTokenState();
    } else {
        setPortalState('auth', 'novo-pedido');
        setAuthMode('login');
    }
</script>
<script>
(function () {
    const whatsappOutline = '<path d="M20.5 11.5a8.5 8.5 0 0 1-12.55 7.48L3.5 20.5l1.52-4.45A8.5 8.5 0 1 1 20.5 11.5Z"></path><g class="portal-whatsapp-handset" transform="translate(7 7) scale(.42)"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.69 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0 1 22 16.92Z"></path></g>';
    document.querySelectorAll('.portal-whatsapp-icon').forEach((icon) => {
        icon.setAttribute('viewBox', '0 0 24 24');
        icon.innerHTML = whatsappOutline;
    });
})();
</script>
