<?php
$portalLoginLogoLightPath = $theme['portal_login_logo_light'] ?? ($theme['logo_light'] ?? 'img/logo.png');
$portalLoginLogoDarkPath = $theme['portal_login_logo_dark'] ?? ($theme['logo_dark'] ?? 'img/logodark.png');
$portalLogoLight = htmlspecialchars($asset($portalLoginLogoLightPath), ENT_QUOTES, 'UTF-8');
$portalLogoDark = htmlspecialchars($asset($portalLoginLogoDarkPath), ENT_QUOTES, 'UTF-8');
$portalLoginLogoFilter = ($theme['portal_login_logo_filter'] ?? '0') === '1';
$portalLoginLogoLightSize = max(24, min(180, (int)($theme['portal_login_logo_light_size'] ?? 60)));
$portalLoginLogoDarkSize = max(24, min(180, (int)($theme['portal_login_logo_dark_size'] ?? 60)));
$portalLoginLogoLightOffsetX = max(-200, min(200, (int)($theme['portal_login_logo_light_offset_x'] ?? 0)));
$portalLoginLogoLightOffsetY = max(-200, min(200, (int)($theme['portal_login_logo_light_offset_y'] ?? 0)));
$portalLoginLogoDarkOffsetX = max(-200, min(200, (int)($theme['portal_login_logo_dark_offset_x'] ?? 0)));
$portalLoginLogoDarkOffsetY = max(-200, min(200, (int)($theme['portal_login_logo_dark_offset_y'] ?? 0)));
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
            <div class="portal-brand-card__logo <?= $portalLoginLogoFilter ? 'portal-brand-card__logo--filtered' : 'portal-brand-card__logo--plain'; ?>" aria-hidden="true">
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
                <input type="text" id="login-email" name="login" placeholder="cliente@empresa.com" required>

                <label for="login-password">Senha</label>
                <input type="password" id="login-password" name="senha" placeholder="••••••••" required>

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
        <button type="button" class="portal-tab" data-portal-tab-trigger="pedidos" aria-controls="portal-tab-pedidos" aria-selected="false">Pedidos</button>
        <button type="button" class="portal-tab is-active" data-portal-tab-trigger="novo-pedido" aria-controls="portal-tab-novo" aria-selected="true">Fazer novo pedido</button>
        <button type="button" class="portal-tab" data-portal-tab-trigger="suporte" aria-controls="portal-tab-suporte" aria-selected="false">Suporte</button>
    </nav>

    <div class="portal-app__panels">
        <section class="portal-panel" id="portal-tab-pedidos" data-portal-tab-panel="pedidos" hidden>
            <div class="portal-orders-card">
                <div class="portal-orders-card__head">
                    <div>
                        <h3>Pedidos recentes</h3>
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

        <section class="portal-panel" id="portal-tab-novo" data-portal-tab-panel="novo-pedido">
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
                            <div class="portal-view-toggle" role="group" aria-label="Visualização do catálogo">
                                <button type="button" class="portal-view-toggle__btn active" data-catalog-view="grid" aria-pressed="true">Grade</button>
                                <button type="button" class="portal-view-toggle__btn" data-catalog-view="list" aria-pressed="false">Lista</button>
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

        <section class="portal-panel" id="portal-tab-suporte" data-portal-tab-panel="suporte" hidden>
            <div class="portal-support-card">
                <h3>Suporte dedicado</h3>
                <p>Precisa de ajuda? Nossa equipe acompanha toda a jornada, do pedido à expedição.</p>
                <ul class="portal-support-card__list">
                    <?php if ($supportEmail !== ''): ?>
                        <li><strong>E-mail:</strong> <a href="mailto:<?= htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($supportTelefone !== ''): ?>
                        <li><strong>Telefone:</strong> <a href="tel:<?= htmlspecialchars($phoneHref ?: $supportTelefone, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($supportTelefone, ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($supportWhatsapp !== ''): ?>
                        <li><strong>WhatsApp:</strong>
                            <?php if ($whatsHref !== ''): ?>
                                <a href="<?= htmlspecialchars($whatsHref, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><?= htmlspecialchars($supportWhatsapp, ENT_QUOTES, 'UTF-8'); ?></a>
                            <?php else: ?>
                                <?= htmlspecialchars($supportWhatsapp, ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($supportHorario !== ''): ?>
                        <li><strong>Horário:</strong> <?= htmlspecialchars($supportHorario, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endif; ?>
                </ul>
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
                <li>Confira o catálogo atualizado na aba <strong>Fazer novo pedido</strong>.</li>
                <li>Acompanhe o status das solicitações em <strong>Pedidos</strong>.</li>
                <li>Precisa de ajuda? Acesse <strong>Suporte</strong> para falar com nossa equipe.</li>
            </ul>
            <div class="portal-welcome__footer">
                <button type="button" class="portal-btn portal-btn--ghost" data-welcome-dismiss>Explorar depois</button>
                <button type="button" class="portal-btn portal-btn--primary" data-welcome-go="novo-pedido">Fazer novo pedido</button>
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
    }

    .portal-brand-card__logo {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 1.5rem;
        min-height: var(--portal-logo-size, 60px);
        overflow: visible;
    }

    .portal-logo {
        height: var(--portal-logo-size, 60px);
        width: auto;
        transform: translate(var(--portal-logo-x, 0), var(--portal-logo-y, 0));
        transition: transform 180ms ease;
    }

    .portal-logo-filtered {
        display: block;
        width: min(calc(var(--portal-logo-size, 60px) * 3.9), 100%);
        height: var(--portal-logo-size, 60px);
        background: var(--primary);
        transform: translate(var(--portal-logo-x, 0), var(--portal-logo-y, 0));
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

    [data-theme="dark"] .auth-panel input {
        background: rgba(15, 23, 42, 0.82);
        border-color: rgba(71, 85, 105, 0.45);
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
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: inline-grid;
        place-items: center;
        border-radius: 14px;
        color: var(--primary, #2563eb);
        background: color-mix(in srgb, var(--primary, #2563eb) 13%, #ffffff);
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 18%, transparent);
        box-shadow: 0 16px 30px -24px color-mix(in srgb, var(--primary, #2563eb) 70%, transparent);
    }

    .portal-auth-title__icon svg {
        width: 26px;
        height: 26px;
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
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
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
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }

    .portal-catalog[data-view="list"] {
        display: flex;
        flex-direction: column;
    }

    .portal-product {
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

    [data-theme="dark"] .portal-product {
        background: rgba(30, 41, 59, 0.78);
        border-color: rgba(71, 85, 105, 0.35);
        box-shadow: 0 16px 36px -28px rgba(15, 23, 42, 0.85);
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
        gap: 1rem;
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

    .portal-orders-action--confirm {
        background: rgba(22, 163, 74, 0.16);
        border-color: rgba(22, 163, 74, 0.42);
        color: #15803d;
    }

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

    [data-theme="dark"] .portal-orders-action--problem {
        background: rgba(234, 179, 8, 0.26);
        border-color: rgba(249, 115, 22, 0.45);
        color: rgba(254, 215, 170, 0.92);
    }

    .portal-orders-status {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
    }

    .portal-orders-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .portal-orders-status--novo {
        background: rgba(37, 99, 235, 0.14);
        color: rgba(30, 64, 175, 0.92);
    }

    .portal-orders-status--aprovacao {
        background: rgba(217, 119, 6, 0.14);
        color: rgba(180, 83, 9, 0.95);
    }

    .portal-orders-status--producao {
        background: rgba(34, 197, 94, 0.16);
        color: rgba(22, 163, 74, 0.95);
    }

    .portal-orders-status--separacao {
        background: rgba(168, 85, 247, 0.16);
        color: rgba(126, 34, 206, 0.9);
    }

    .portal-orders-status--expedicao {
        background: rgba(14, 165, 233, 0.16);
        color: rgba(3, 105, 161, 0.9);
    }

    .portal-orders-status--faturado {
        background: rgba(22, 163, 74, 0.16);
        color: #15803d;
    }

    .portal-orders-status--cancelado {
        background: rgba(220, 38, 38, 0.16);
        color: #b91c1c;
    }

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
        margin: 1.1rem 0 1.8rem;
        padding: 0;
        display: grid;
        gap: 0.6rem;
    }

    .portal-support-card__list a {
        color: inherit;
        text-decoration: none;
        border-bottom: 1px dashed rgba(148, 163, 184, 0.4);
    }

    .portal-support-card__list a:hover {
        color: rgba(37, 99, 235, 0.85);
    }

    .portal-support-card__foot {
        margin-top: 1.2rem;
        color: var(--portal-muted);
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
            width: 40px;
            height: 40px;
            flex-basis: 40px;
            border-radius: 13px;
        }

        .portal-auth-title__icon svg {
            width: 23px;
            height: 23px;
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
        gap: 1.5rem;
        padding: clamp(2rem, 3.6vw, 2.5rem);
    }

    .portal-welcome {
        display: grid;
        gap: 1.5rem;
    }

    .portal-welcome__icon {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.92), rgba(59, 130, 246, 0.95));
        display: grid;
        place-items: center;
        color: #fff;
        box-shadow: 0 28px 70px -48px rgba(37, 99, 235, 0.9);
    }

    .portal-welcome__icon svg {
        width: 34px;
        height: 34px;
        fill: currentColor;
    }

    .portal-welcome__tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        color: rgba(37, 99, 235, 0.92);
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }

    .portal-welcome__lead {
        color: var(--portal-muted);
        line-height: 1.6;
    }

    .portal-welcome__tips {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.75rem;
    }

    .portal-welcome__tips li {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        color: var(--portal-muted);
    }

    .portal-welcome__tips li::before {
        content: '';
        width: 8px;
        height: 8px;
        margin-top: 0.4rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.35);
        flex-shrink: 0;
    }

    .portal-welcome__footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
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
</style>
<?php
$catalogoPayload = json_encode(array_map(static function ($produto) {
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
    ];
}, $catalogo ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<script>
    const catalogo = <?= $catalogoPayload ?>;
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
    const ordersFeedback = document.getElementById('portal-orders-feedback');
    const ordersRefreshButton = document.getElementById('portal-orders-refresh');
    const productsListContainer = document.getElementById('portal-products-list');
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
    let activeTab = 'novo-pedido';
    let pedidosCarregados = false;

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
            const nextTab = preferredTab || 'pedidos';
            setActiveTab(nextTab);
        } else {
            portalAppSection?.setAttribute('hidden', '');
            portalHero?.removeAttribute('hidden');
            pedidosCarregados = false;
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
        { value: 'separacao', label: 'Em separação' },
        { value: 'expedicao', label: 'Em transporte' },
        { value: 'faturado', label: 'Pedido recebido' },
        { value: 'cancelado', label: 'Cancelado' },
    ];

    const orderStatusLabels = {
        novo: 'Recebido',
        aprovacao: 'Em aprovação',
        producao: 'Aprovado',
        separacao: 'Em separação',
        expedicao: 'Em transporte',
        faturado: 'Pedido recebido',
        cancelado: 'Cancelado',
    };

    const orderStatusOptionMap = orderStatusOptions.reduce((acc, option) => {
        acc[option.value] = option.label;
        return acc;
    }, {});

    const orderStatusUpdatable = new Set(orderStatusOptions.map((option) => option.value));

    const receiptEligibleStatuses = new Set(['producao', 'separacao', 'expedicao']);

    const defaultOrderLead = 'Nossa equipe está revisando as reservas de estoque. Você receberá a confirmação em breve no e-mail cadastrado.';
    const orderStatusLead = {
        novo: 'Recebemos seu pedido e estamos validando a disponibilidade em estoque.',
        aprovacao: defaultOrderLead,
        producao: 'Pedido aprovado! Os itens necessários já estão seguindo para produção.',
        separacao: 'Estamos separando seu pedido conforme o critério FEFO para garantir a melhor validade.',
        expedicao: 'Seu pedido está em transporte. Assim que houver rastreamento disponível, enviaremos por e-mail.',
        faturado: 'Pedido concluído! Obrigado pela parceria.',
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
            const statusClass = `portal-orders-status portal-orders-status--${statusKey}`;
            const statusLabel = orderStatusLabels[statusKey] || (pedido.status ? String(pedido.status) : '—');
            const total = Number(pedido.total ?? 0);
            const dataCriacao = formatDateTime(pedido.created_at ?? pedido.createdAt ?? pedido.created_at);
            const showReceipt = receiptEligibleStatuses.has(statusKey) && statusKey !== 'faturado';
            const receiptAttributes = showReceipt ? '' : 'hidden';
            row.innerHTML = `
                <td><strong>${codigo}</strong></td>
                <td>
                    <span class="${statusClass}" data-order-status-label>${statusLabel}</span>
                </td>
                <td>${formatCurrency(total)}</td>
                <td>${dataCriacao}</td>
                <td class="portal-orders-table__actions">
                    <div class="portal-orders-actions">
                        <button type="button" class="portal-orders-action" data-order-view="${pedido.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c-5 0-9.27 3.11-11 7.5C2.73 16.89 7 20 12 20s9.27-3.11 11-7.5C21.27 8.11 17 5 12 5Zm0 12a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Zm0-7.2A2.7 2.7 0 1 0 14.7 12 2.7 2.7 0 0 0 12 9.8Z"/></svg>
                            <span>Ver detalhes</span>
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
            button.addEventListener('click', (event) => {
                const trigger = event.currentTarget;
                const pedidoId = Number(trigger.dataset.orderReceived);
                if (!pedidoId) {
                    return;
                }
                const previous = trigger.dataset.status || '';
                trigger.setAttribute('disabled', 'disabled');
                atualizarStatusPedido(pedidoId, 'faturado', null, previous, trigger);
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
            const statusKey = String(pedido.status ?? '').toLowerCase();
            const itensNormalizados = itens.map((item) => ({
                id: item.produto_id ?? item.id ?? 0,
                nome: item.nome ?? item.produto_nome ?? '',
                sku: item.sku ?? item.produto_id ?? '',
                quantidade: Number(item.quantidade ?? 0),
                preco: Number(item.preco_unit ?? item.preco ?? 0),
                unidade: item.unidade ?? '',
            }));
            const totalPedido = Number(pedido.total ?? itensNormalizados.reduce((totalizador, item) => totalizador + ((Number(item.quantidade ?? 0)) * (Number(item.preco ?? 0))), 0));
            const resumo = {
                pedidoId,
                codigo: pedido.codigo ?? null,
                createdAt: pedido.created_at ?? pedido.createdAt ?? pedido.created ?? null,
                total: Number.isFinite(totalPedido) ? totalPedido : 0,
                itens: itensNormalizados,
                backorder: Array.isArray(json.backorder) ? json.backorder : [],
                status: statusKey,
                statusLabel: pedido.status_label ?? orderStatusLabels[statusKey] ?? '',
                title: 'Detalhes do pedido',
            };
            showOrderSuccessModal(resumo);
            setFeedback(ordersFeedback, 'Detalhes carregados com sucesso.', 'success');
        } catch (error) {
            setFeedback(ordersFeedback, error.message || 'Não foi possível abrir o pedido.', 'error');
        }
    };

    const carregarPedidos = async (force = false) => {
        if (!ordersTableBody) {
            return;
        }
        if (!state.token) {
            pedidosCarregados = false;
            renderPedidos([]);
            setFeedback(ordersFeedback, 'Faça login para visualizar seus pedidos.', '');
            return;
        }
        if (pedidosCarregados && !force) {
            return;
        }
        setFeedback(ordersFeedback, 'Carregando pedidos…');
        try {
            const response = await fetch(`${basePath}/api/pedidos`, {
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
                throw new Error('Não foi possível carregar a lista de pedidos.');
            }
            const json = await response.json();
            const lista = Array.isArray(json.pedidos) ? json.pedidos : [];
            pedidosCarregados = true;
            renderPedidos(lista);
            setFeedback(ordersFeedback, lista.length ? 'Pedidos atualizados.' : 'Nenhum pedido enviado ainda.', lista.length ? 'success' : '');
        } catch (error) {
            pedidosCarregados = false;
            setFeedback(ordersFeedback, error.message || 'Não foi possível atualizar seus pedidos.', 'error');
        }
    };

    const setActiveTab = (target) => {
        if (!portalTabButtons.length || !portalTabPanels.length) {
            return;
        }
        const validTabs = ['pedidos', 'novo-pedido', 'suporte'];
        const nextTab = validTabs.includes(target) ? target : 'novo-pedido';
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
        if (nextTab === 'pedidos') {
            carregarPedidos();
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

    const desenharCatalogo = () => {
        if (!catalogContainer) return;
        syncCatalogView();
        catalogContainer.innerHTML = '';
        catalogo.forEach((produto) => {
            const card = document.createElement('article');
            card.className = `portal-product portal-product--${catalogView}`;
            card.dataset.category = produto.categoria || '';
            const pesoFormatado = formatWeight(produto.peso_liquido);
            card.innerHTML = `
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
            catalogContainer.appendChild(card);
        });
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
    };

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
        const { showWelcome = false, preferredTab = 'novo-pedido' } = options;
        persistCliente(cliente);
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
        setPortalState('app', preferredTab);
        renderListaProdutos();
        atualizarCarrinho();
        if (showWelcome) {
            openWelcomeModal(cliente);
        }
    };

    const leaveApp = () => {
        state.token = '';
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

    portalTabButtons.forEach((button) => {
        button.addEventListener('click', () => setActiveTab(button.dataset.portalTabTrigger));
    });

    if (ordersRefreshButton) {
        ordersRefreshButton.addEventListener('click', () => carregarPedidos(true));
    }

    const atualizarTokenState = () => {
        if (state.token) {
            loadStoredCliente();
            enterApp(state.cliente || {}, { showWelcome: false });
        } else {
            leaveApp();
        }
    };

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
        loadStoredCliente();
        enterApp(state.cliente || {}, { showWelcome: false });
    } else {
        setPortalState('auth', 'novo-pedido');
        setAuthMode('login');
    }
</script>
