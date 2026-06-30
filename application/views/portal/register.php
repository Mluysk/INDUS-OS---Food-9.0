<?php
$portalLoginLogoLightPath = $theme['portal_login_logo_light'] ?? ($theme['logo_light'] ?? 'img/logo.png');
$portalLoginLogoDarkPath = $theme['portal_login_logo_dark'] ?? ($theme['logo_dark'] ?? 'img/logodark.png');
$portalLogoLight = htmlspecialchars($asset($portalLoginLogoLightPath), ENT_QUOTES, 'UTF-8');
$portalLogoDark = htmlspecialchars($asset($portalLoginLogoDarkPath), ENT_QUOTES, 'UTF-8');
$portalLoginLogoFilter = ($theme['portal_login_logo_filter'] ?? '0') === '1';
$portalLoginLogoLightSize = max(24, min(180, (int)($theme['portal_login_logo_light_size'] ?? 60)));
$portalLoginLogoDarkSize = max(24, min(180, (int)($theme['portal_login_logo_dark_size'] ?? 60)));
$portalLoginLogoLightOffsetX = max(-534, min(534, (int)($theme['portal_login_logo_light_offset_x'] ?? 0)));
$portalLoginLogoLightOffsetY = max(-60, min(60, (int)($theme['portal_login_logo_light_offset_y'] ?? 0)));
$portalLoginLogoDarkOffsetX = max(-534, min(534, (int)($theme['portal_login_logo_dark_offset_x'] ?? 0)));
$portalLoginLogoDarkOffsetY = max(-60, min(60, (int)($theme['portal_login_logo_dark_offset_y'] ?? 0)));
$portalRegisterLogoLightOffsetY = $portalLoginLogoLightOffsetY;
$portalRegisterLogoDarkOffsetY = $portalLoginLogoDarkOffsetY;
$portalLogoFrameSize = $portalLoginLogoFilter ? $portalLoginLogoDarkSize : $portalLoginLogoLightSize;
$portalLogoFrameOffsetX = $portalLoginLogoFilter ? $portalLoginLogoDarkOffsetX : $portalLoginLogoLightOffsetX;
$portalLogoFrameOffsetY = $portalLoginLogoFilter ? $portalRegisterLogoDarkOffsetY : $portalRegisterLogoLightOffsetY;
$appDisplayName = htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8');
$appTaglineText = htmlspecialchars($appTagline ?? 'Sistema de Controle Industrial (SCI)', ENT_QUOTES, 'UTF-8');
$actionUrl = htmlspecialchars($url('portal/cadastro'), ENT_QUOTES, 'UTF-8');
$loginUrl = htmlspecialchars($url('portal'), ENT_QUOTES, 'UTF-8');
?>
<section class="portal-hero portal-hero--register" data-portal-hero>
    <div class="portal-hero__content">
        <div class="portal-brand-card">
            <div class="portal-brand-card__logo <?= $portalLoginLogoFilter ? 'portal-brand-card__logo--filtered' : 'portal-brand-card__logo--plain'; ?>" style="--portal-logo-size: <?= $portalLogoFrameSize; ?>px; --portal-logo-x: <?= $portalLogoFrameOffsetX; ?>px; --portal-logo-y: <?= $portalLogoFrameOffsetY; ?>px;" aria-hidden="true">
                <?php if ($portalLoginLogoFilter): ?>
                    <span class="portal-logo-filtered" style="--portal-login-logo-url: url('<?= $portalLogoDark; ?>'); --portal-logo-size: <?= $portalLoginLogoDarkSize; ?>px; --portal-logo-x: <?= $portalLoginLogoDarkOffsetX; ?>px; --portal-logo-y: <?= $portalRegisterLogoDarkOffsetY; ?>px;"></span>
                <?php else: ?>
                    <img class="portal-logo portal-logo-light" style="--portal-logo-size: <?= $portalLoginLogoLightSize; ?>px; --portal-logo-x: <?= $portalLoginLogoLightOffsetX; ?>px; --portal-logo-y: <?= $portalRegisterLogoLightOffsetY; ?>px;" src="<?= $portalLogoLight; ?>" alt="Logotipo modo claro">
                    <img class="portal-logo portal-logo-dark" style="--portal-logo-size: <?= $portalLoginLogoDarkSize; ?>px; --portal-logo-x: <?= $portalLoginLogoDarkOffsetX; ?>px; --portal-logo-y: <?= $portalRegisterLogoDarkOffsetY; ?>px;" src="<?= $portalLogoDark; ?>" alt="Logotipo modo escuro">
                <?php endif; ?>
            </div>
            <span class="portal-brand-card__tag">Portal do cliente</span>
            <h1>Crie sua conta no <?= $appDisplayName; ?></h1>
            <p><?= $appTaglineText; ?>. Cadastre-se em poucos passos e acompanhe a produção em tempo real.</p>
        </div>

        <?php if (!empty($flash['success']) || !empty($flash['error'])): ?>
            <div class="portal-flash <?= !empty($flash['success']) ? 'portal-flash--success' : 'portal-flash--error'; ?>">
                <?= htmlspecialchars($flash['success'] ?? $flash['error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="portal-auth-card" data-auth-card>
            <form id="portal-register-form" class="auth-panel auth-panel--register" method="post" action="<?= $actionUrl; ?>" novalidate>
                <div class="auth-panel__header">
                    <h2 class="portal-auth-title">
                        <span class="portal-auth-title__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9.5" cy="7" r="4"></circle>
                                <path d="M19 8v6"></path>
                                <path d="M22 11h-6"></path>
                            </svg>
                        </span>
                        <span>Crie sua conta</span>
                    </h2>
                    <h2>Dados do responsável</h2>
                    <p class="auth-panel__subtitle">Informe seus dados de contato para ativarmos o portal.</p>
                </div>

                <div class="field-group portal-field-email">
                    <label for="registro-email">E-mail</label>
                    <input type="email" id="registro-email" name="email" required>
                </div>

                <div class="field-group portal-field-contact">
                    <label for="registro-telefone">Telefone</label>
                    <input type="tel" id="registro-telefone" name="telefone" placeholder="(11) 99999-0000" required>
                </div>

                <div class="document-toggle" role="radiogroup" aria-label="Tipo de documento">
                    <span>Documento</span>
                    <div class="document-toggle__buttons">
                        <button type="button" class="document-toggle__btn active" data-doc-option="cnpj" aria-pressed="true">CNPJ</button>
                        <button type="button" class="document-toggle__btn" data-doc-option="cpf" aria-pressed="false">CPF</button>
                    </div>
                </div>

                <div class="document-fields">
                    <div class="field-group" data-doc-field="cnpj">
                        <label for="registro-cnpj">CNPJ</label>
                        <input type="text" id="registro-cnpj" name="cnpj" placeholder="00.000.000/0000-00" required>
                    </div>
                    <div class="field-group field-group--hidden" data-doc-field="cpf">
                        <label for="registro-cpf">CPF</label>
                        <input type="text" id="registro-cpf" name="cpf" placeholder="000.000.000-00" disabled>
                    </div>
                </div>

                <div class="field-group portal-field-razao">
                    <label for="registro-razao">Razão social / Nome fiscal</label>
                    <input type="text" id="registro-razao" name="razao_social" placeholder="Use para emissão fiscal">
                </div>

                <div class="field-group portal-field-store">
                    <label for="registro-fantasia">Nome fantasia</label>
                    <input type="text" id="registro-fantasia" name="nome_fantasia" placeholder="Como prefere ser chamado">
                </div>

                <div class="address-grid">
                    <div class="field-group address-grid__cep">
                        <label for="registro-cep">CEP</label>
                        <input type="text" id="registro-cep" name="cep" placeholder="00000-000" maxlength="9" inputmode="numeric" required>
                    </div>
                    <div class="field-group">
                        <label for="registro-endereco">Endereço</label>
                        <input type="text" id="registro-endereco" name="endereco" placeholder="Rua, avenida" required>
                    </div>
                    <div class="field-group">
                        <label for="registro-numero">Número</label>
                        <input type="text" id="registro-numero" name="numero" placeholder="123" maxlength="5" inputmode="numeric" required>
                    </div>
                    <div class="field-group">
                        <label for="registro-bairro">Bairro</label>
                        <input type="text" id="registro-bairro" name="bairro" placeholder="Centro" required>
                    </div>
                    <div class="field-group">
                        <label for="registro-cidade">Cidade</label>
                        <input type="text" id="registro-cidade" name="cidade" placeholder="São Paulo" required>
                    </div>
                    <div class="field-group">
                        <label for="registro-estado">Estado (UF)</label>
                        <input type="text" id="registro-estado" name="estado" placeholder="SP" maxlength="2" required>
                    </div>
                </div>
                <p class="cep-feedback" data-cep-feedback></p>

                <div class="field-grid">
                    <div class="field-group">
                        <label for="registro-senha">Senha</label>
                        <input type="password" id="registro-senha" name="senha" required minlength="8" placeholder="Mínimo 8 caracteres">
                    </div>
                    <div class="field-group">
                        <label for="registro-confirma">Confirme a senha</label>
                        <input type="password" id="registro-confirma" name="confirmacao" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="portal-btn portal-btn--primary">Cadastrar</button>

                <div class="portal-auth-cta">
                    <span>Já possui acesso?</span>
                    <a class="portal-btn portal-btn--ghost" href="<?= $loginUrl; ?>">Fazer login</a>
                </div>

                <div id="portal-register-feedback" class="portal-feedback" role="status" aria-live="polite"></div>
            </form>
        </div>
    </div>
</section>

<style>
    :root {
        --portal-hero-gradient: radial-gradient(120% 120% at 10% 0%, rgba(59, 130, 246, 0.12), rgba(15, 23, 42, 0));
        --portal-card-bg: rgba(255, 255, 255, 0.95);
        --portal-card-border: rgba(15, 23, 42, 0.08);
        --portal-muted: rgba(71, 85, 105, 0.82);
    }

    [data-theme="dark"] {
        --portal-hero-gradient: radial-gradient(120% 120% at 10% 0%, rgba(37, 99, 235, 0.22), rgba(15, 23, 42, 0));
        --portal-card-bg: rgba(15, 23, 42, 0.78);
        --portal-card-border: rgba(148, 163, 184, 0.16);
        --portal-muted: rgba(148, 163, 184, 0.78);
    }

    .portal-hero--register {
        padding: 4.5rem 1.5rem 3rem;
        background: var(--portal-hero-gradient);
    }

    .portal-hero__content {
        display: grid;
        gap: 1.75rem;
        max-width: 1080px;
        margin: 0 auto;
    }

    .portal-brand-card {
        background: rgba(255, 255, 255, 0.92);
        border-radius: 32px;
        padding: 2.4rem 2.6rem;
        box-shadow: 0 28px 60px -42px rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.18);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        overflow: hidden;
    }

    [data-theme="dark"] .portal-brand-card {
        background: rgba(15, 23, 42, 0.82);
        border-color: rgba(148, 163, 184, 0.35);
    }

    .portal-brand-card__logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        box-sizing: border-box;
        container-type: size;
        width: calc(100% + 5.2rem);
        margin-left: -2.6rem;
        height: 133px;
        min-height: 133px;
        padding: 6px;
        overflow: visible;
    }

    .portal-logo {
        height: var(--portal-logo-size, 60px);
        width: auto;
        max-width: 100%;
        max-height: 121px;
        display: block;
        object-fit: contain;
        transform: translate(
            clamp(
                calc((100% - 100cqw) / 2),
                var(--portal-logo-x, 0px),
                calc((100cqw - 100%) / 2)
            ),
            clamp(
                calc((100% - 100cqh) / 2),
                var(--portal-logo-y, 0px),
                calc((100cqh - 100%) / 2)
            )
        );
    }

    .portal-logo-filtered {
        --portal-logo-render-size: min(var(--portal-logo-size, 60px), 121px);
        display: block;
        width: min(calc(var(--portal-logo-render-size) * 3.9), 100%);
        max-width: 100%;
        height: var(--portal-logo-render-size);
        flex: 0 1 min(calc(var(--portal-logo-render-size) * 3.9), 100%);
        background: var(--primary);
        transform: translate(
            clamp(
                calc((100% - 100cqw) / 2),
                var(--portal-logo-x, 0px),
                calc((100cqw - 100%) / 2)
            ),
            clamp(
                calc((100% - 100cqh) / 2),
                var(--portal-logo-y, 0px),
                calc((100cqh - 100%) / 2)
            )
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
        gap: 0.35rem;
        padding: 0.35rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        color: rgba(37, 99, 235, 0.8);
        font-weight: 600;
    }

    [data-theme="dark"] .portal-brand-card__tag {
        background: rgba(96, 165, 250, 0.22);
        color: rgba(191, 219, 254, 0.92);
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
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-card-border);
        border-radius: 26px;
        box-shadow: 0 28px 60px -42px rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(12px);
        padding: 2rem 2.25rem;
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
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

    .auth-panel label {
        display: block;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .auth-panel input {
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.75rem 0.95rem;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.92);
        color: inherit;
    }

    [data-theme="dark"] .auth-panel input {
        background: rgba(15, 23, 42, 0.82);
        border-color: rgba(71, 85, 105, 0.45);
    }

    .auth-panel__header h2 {
        margin: 0;
        font-size: 1.45rem;
    }

    .auth-panel__subtitle {
        margin: 0.35rem 0 0.9rem;
        color: var(--portal-muted);
        font-size: 0.95rem;
    }

    .portal-auth-cta {
        margin-top: 1.2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: space-between;
        font-size: 0.95rem;
        color: var(--portal-muted);
    }

    .portal-auth-cta .portal-btn,
    .portal-auth-cta .portal-btn:hover,
    .portal-auth-cta .portal-btn:focus-visible {
        text-decoration: none;
    }

    .document-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.85rem;
    }

    .document-toggle__buttons {
        display: inline-flex;
        gap: 0.5rem;
        background: rgba(148, 163, 184, 0.1);
        padding: 0.3rem;
        border-radius: 12px;
    }

    .document-toggle__btn {
        border: none;
        background: transparent;
        padding: 0.45rem 0.9rem;
        border-radius: 10px;
        font-weight: 600;
        color: var(--portal-muted);
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .document-toggle__btn.active {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.16), rgba(37, 99, 235, 0.3));
        color: rgba(30, 64, 175, 0.92);
    }

    [data-theme="dark"] .document-toggle__btn.active {
        color: rgba(191, 219, 254, 0.92);
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.22), rgba(59, 130, 246, 0.32));
    }

    .document-fields {
        display: grid;
        gap: 1rem;
    }

    .field-group--hidden {
        display: none;
    }

    .address-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .address-grid {
            grid-template-columns: minmax(128px, 0.58fr) minmax(0, 1.42fr);
        }
    }

    @media (min-width: 960px) {
        .address-grid {
            grid-template-columns: minmax(132px, 0.58fr) minmax(0, 0.705fr) minmax(0, 1.645fr) minmax(92px, 0.42fr);
        }

        .address-grid__cep,
        .address-grid .field-group:has(#registro-cep) {
            grid-column: 1;
            max-width: 11.5rem;
        }

        .address-grid .field-group:has(#registro-endereco) {
            grid-column: 2 / 4;
        }

        .address-grid .field-group:has(#registro-numero) {
            grid-column: 4;
            max-width: 7.5rem;
        }

        .address-grid .field-group:has(#registro-bairro) {
            grid-column: 1 / 3;
        }

        .address-grid .field-group:has(#registro-cidade) {
            grid-column: 3;
        }

        .address-grid .field-group:has(#registro-estado) {
            grid-column: 4;
        }
    }

    .field-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 720px) {
        .field-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .portal-btn {
        border: none;
        border-radius: 12px;
        padding: 0.85rem 1.3rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .portal-btn--primary {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95), rgba(59, 130, 246, 0.95));
        color: #fff;
        box-shadow: 0 16px 32px -20px rgba(37, 99, 235, 0.7);
    }

    .portal-btn--ghost {
        background: transparent;
        color: rgba(37, 99, 235, 0.88);
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    [data-theme="dark"] .portal-btn--ghost {
        color: rgba(191, 219, 254, 0.9);
        border-color: rgba(148, 163, 184, 0.35);
        background: rgba(15, 23, 42, 0.6);
    }

    .portal-btn:hover {
        transform: translateY(-1px);
    }

    .portal-feedback {
        margin-top: 1rem;
        border-radius: 12px;
        padding: 0.75rem 0.9rem;
        font-size: 0.95rem;
        background: rgba(148, 163, 184, 0.12);
        color: var(--portal-muted);
        display: none;
    }

    .portal-feedback[data-state] {
        display: block;
    }

    .portal-feedback[data-state="success"] {
        background: rgba(34, 197, 94, 0.16);
        color: #166534;
    }

    .portal-feedback[data-state="error"] {
        background: rgba(248, 113, 113, 0.18);
        color: #b91c1c;
    }

    .cep-feedback {
        font-size: 0.85rem;
        color: var(--portal-muted);
        margin: 0.25rem 0 0;
    }

    .cep-feedback[data-state="error"] {
        color: #b91c1c;
    }

    .cep-feedback[data-state="success"] {
        color: #15803d;
    }

    .auth-panel__header > h2:not(.portal-auth-title) {
        display: none;
    }

    .portal-hero--register {
        background: radial-gradient(120% 120% at 10% 0%, color-mix(in srgb, var(--primary, #2563eb) 14%, transparent), rgba(15, 23, 42, 0));
    }

    [data-theme="dark"] .portal-hero--register {
        background: radial-gradient(120% 120% at 10% 0%, color-mix(in srgb, var(--primary, #2563eb) 22%, transparent), rgba(15, 23, 42, 0));
    }

    .portal-brand-card__tag {
        background: color-mix(in srgb, var(--primary, #2563eb) 12%, transparent);
        color: color-mix(in srgb, var(--primary, #2563eb) 82%, var(--text, #0f172a));
        min-height: 38px;
        padding: 0.47rem 1.06rem;
        font-size: 1.13rem;
        font-weight: 800;
    }

    [data-theme="dark"] .portal-brand-card__tag {
        background: color-mix(in srgb, var(--primary, #2563eb) 22%, transparent);
        color: color-mix(in srgb, var(--primary, #2563eb) 70%, #ffffff);
    }

    .portal-auth-card {
        border-radius: 28px;
        padding: clamp(1.65rem, 3vw, 2.5rem);
        box-shadow: 0 30px 70px -44px rgba(15, 23, 42, 0.58);
    }

    .auth-panel--register {
        display: grid;
        gap: 1.05rem;
    }

    .auth-panel label {
        margin: 0 0 3px;
        color: var(--text, #111827);
        font-weight: 800;
    }

    .auth-panel input {
        width: 100%;
        min-height: 52px;
        border-radius: 16px;
        border-color: color-mix(in srgb, var(--primary, #2563eb) 16%, rgba(148, 163, 184, 0.42));
        padding: 0.82rem 1rem;
        font-weight: 700;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }

    .auth-panel input:focus {
        outline: none;
        border-color: var(--primary, #2563eb);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary, #2563eb) 14%, transparent);
        transform: translateY(-1px);
    }

    .auth-panel input[aria-invalid="true"] {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
    }

    .portal-input-shell {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .portal-input-shell input {
        width: 100%;
    }

    .portal-input-shell--password input {
        padding-right: 3.35rem;
    }

    .portal-password-toggle {
        position: absolute;
        right: 0.66rem;
        top: 50%;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 16%, transparent);
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary, #2563eb) 8%, #ffffff);
        color: color-mix(in srgb, var(--primary, #2563eb) 78%, #334155);
        box-shadow: none;
        cursor: pointer;
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
        width: 1.05rem;
        height: 1.05rem;
        color: currentColor;
        pointer-events: none;
    }

    .portal-password-toggle__icon svg {
        width: 100%;
        height: 100%;
        display: block;
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

    [data-theme="dark"] .portal-password-toggle {
        background: color-mix(in srgb, var(--primary, #2563eb) 18%, rgba(15, 23, 42, 0.88));
        border-color: color-mix(in srgb, var(--primary, #2563eb) 24%, rgba(148, 163, 184, 0.18));
        color: color-mix(in srgb, var(--primary, #2563eb) 66%, #ffffff);
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

    .auth-panel__header h2 {
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        line-height: 1.05;
        letter-spacing: 0;
    }

    .auth-panel__subtitle {
        margin: 0.55rem 0 1.35rem;
        font-size: 1rem;
        line-height: 1.42;
    }

    .document-toggle {
        margin: 0.35rem 0;
        padding: 0.9rem 1rem;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 12%, var(--portal-card-border));
        border-radius: 18px;
        background: color-mix(in srgb, var(--primary, #2563eb) 6%, transparent);
    }

    .document-toggle__buttons {
        background: color-mix(in srgb, var(--primary, #2563eb) 9%, rgba(148, 163, 184, 0.1));
    }

    .document-toggle__btn.active {
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #2563eb) 18%, #ffffff), var(--primary, #2563eb));
        color: #fff;
    }

    [data-theme="dark"] .document-toggle__btn.active {
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #2563eb) 36%, rgba(15, 23, 42, 0.8)), var(--primary, #2563eb));
        color: #fff;
    }

    .document-fields,
    .address-grid,
    .field-grid {
        gap: 1.15rem;
    }

    .portal-btn {
        border-radius: 16px;
        padding: 0.95rem 1.35rem;
        font-weight: 850;
    }

    .portal-btn--primary {
        background: linear-gradient(135deg, var(--primary, #2563eb), color-mix(in srgb, var(--primary, #2563eb) 78%, #ffffff));
        box-shadow: var(--primary-shadow, 0 16px 32px -20px rgba(37, 99, 235, 0.7));
    }

    .portal-btn--ghost {
        background: color-mix(in srgb, var(--primary, #2563eb) 7%, transparent);
        color: color-mix(in srgb, var(--primary, #2563eb) 84%, var(--text, #0f172a));
        border-color: color-mix(in srgb, var(--primary, #2563eb) 26%, transparent);
    }

    @media (max-width: 720px) {
        .portal-brand-card__logo {
            justify-content: center;
            width: 100%;
            margin-left: 0;
        }

        .portal-logo,
        .portal-logo-filtered {
            transform: translate(
                0,
                clamp(
                    calc((100% - 100cqh) / 2),
                    var(--portal-logo-y, 0px),
                    calc((100cqh - 100%) / 2)
                )
            );
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
    }
</style>

<script>
    (() => {
        const docButtons = document.querySelectorAll('[data-doc-option]');
        const docFields = document.querySelectorAll('[data-doc-field]');
        const cepInput = document.getElementById('registro-cep');
        const estadoInput = document.getElementById('registro-estado');
        const cepStatus = document.querySelector('[data-cep-feedback]');
        const form = document.getElementById('portal-register-form');
        const feedback = document.getElementById('portal-register-feedback');
        const actionUrl = <?= json_encode($url('portal/cadastro')); ?>;
        const loginUrl = <?= json_encode($url('portal')); ?>;

        const mountPasswordToggle = (input) => {
            if (!input || input.dataset.passwordMounted === 'true') return;
            input.dataset.passwordMounted = 'true';
            const shell = document.createElement('div');
            shell.className = 'portal-input-shell portal-input-shell--password';
            input.parentNode.insertBefore(shell, input);
            shell.appendChild(input);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'portal-password-toggle';
            button.dataset.passwordToggle = 'true';
            button.setAttribute('aria-label', 'Mostrar senha');
            button.setAttribute('aria-pressed', 'false');
            button.innerHTML = '<span class="portal-password-toggle__icon portal-password-toggle__icon--show" data-lucide="eye" aria-hidden="true"></span><span class="portal-password-toggle__icon portal-password-toggle__icon--hide" data-lucide="eye-off" aria-hidden="true"></span>';
            shell.appendChild(button);
            button.addEventListener('click', () => {
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.classList.toggle('is-visible', !visible);
                button.setAttribute('aria-pressed', String(!visible));
                button.setAttribute('aria-label', visible ? 'Mostrar senha' : 'Ocultar senha');
                input.focus();
            });
        };

        ['registro-senha', 'registro-confirma'].forEach((id) => mountPasswordToggle(document.getElementById(id)));
        if (window.lucide) {
            window.lucide.createIcons();
        }

        const setFeedback = (element, message, state = '') => {
            if (!element) return;
            element.textContent = message;
            if (state) {
                element.dataset.state = state;
            } else {
                delete element.dataset.state;
            }
        };

        const setDocumentType = (type) => {
            const selected = type === 'cpf' ? 'cpf' : 'cnpj';
            docButtons.forEach((button) => {
                const isActive = button.dataset.docOption === selected;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
            docFields.forEach((wrapper) => {
                const matches = wrapper.dataset.docField === selected;
                wrapper.classList.toggle('field-group--hidden', !matches);
                const input = wrapper.querySelector('input');
                if (input) {
                    if (matches) {
                        input.removeAttribute('disabled');
                        input.setAttribute('required', 'required');
                    } else {
                        input.value = '';
                        input.setAttribute('disabled', 'disabled');
                        input.removeAttribute('required');
                        input.removeAttribute('aria-invalid');
                    }
                }
            });
        };

        const validateRequiredFields = () => {
            [
                'registro-email',
                'registro-telefone',
                'registro-razao',
                'registro-fantasia',
                'registro-cep',
                'registro-endereco',
                'registro-numero',
                'registro-bairro',
                'registro-cidade',
                'registro-estado',
                'registro-senha',
                'registro-confirma',
                'registro-whatsapp',
            ].forEach((id) => {
                const input = document.getElementById(id);
                if (input) {
                    input.setAttribute('required', 'required');
                }
            });

            const controls = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'))
                .filter((control) => !control.disabled);
            let firstInvalid = null;

            controls.forEach((control) => {
                const value = String(control.value || '').trim();
                const valid = value !== '' && control.checkValidity();
                control.setAttribute('aria-invalid', valid ? 'false' : 'true');
                if (!valid && !firstInvalid) {
                    firstInvalid = control;
                }
            });

            if (firstInvalid) {
                setFeedback(feedback, 'Preencha todos os campos obrigatorios antes de cadastrar.', 'error');
                firstInvalid.focus({ preventScroll: true });
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            return true;
        };

        const aplicarCep = async (cep) => {
            if (!cepStatus) return;
            setFeedback(cepStatus, 'Consultando CEP…');
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                if (!response.ok) throw new Error('CEP não encontrado');
                const data = await response.json();
                if (data.erro) throw new Error('CEP inválido');
                document.getElementById('registro-endereco').value = data.logradouro || '';
                document.getElementById('registro-bairro').value = data.bairro || '';
                document.getElementById('registro-cidade').value = data.localidade || '';
                document.getElementById('registro-estado').value = (data.uf || '').toUpperCase();
                setFeedback(cepStatus, 'Endereço preenchido automaticamente.', 'success');
            } catch (error) {
                setFeedback(cepStatus, error.message || 'Não foi possível localizar o CEP.', 'error');
            }
        };

        docButtons.forEach((button) => {
            button.addEventListener('click', () => setDocumentType(button.dataset.docOption));
        });

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

        if (form) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!validateRequiredFields()) {
                    return;
                }
                setFeedback(feedback, 'Enviando dados…');
                const formData = new FormData(form);
                const payload = Object.fromEntries(formData.entries());
                try {
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                    const json = await response.json();
                    if (!response.ok) {
                        throw new Error(json.message || 'Não foi possível concluir o cadastro.');
                    }
                    setFeedback(feedback, json.message || 'Cadastro realizado com sucesso! Redirecionando…', 'success');
                    form.reset();
                    setDocumentType('cnpj');
                    setTimeout(() => {
                        window.location.href = loginUrl;
                    }, 1800);
                } catch (error) {
                    setFeedback(feedback, error.message || 'Não foi possível concluir o cadastro.', 'error');
                }
            });
        }

        setDocumentType('cnpj');
    })();
</script>
