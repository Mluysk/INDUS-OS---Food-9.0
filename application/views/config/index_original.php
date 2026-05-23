<?php /** @var array $configs */ ?>
<?php
$alertaPadrao = $alertaPadrao ?? '15';
$baixaImediata = $baixaImediata ?? 'false';
$themeDefaults = $themeDefaults ?? [];
$themeMode = $themeDefaults['THEME_MODE'] ?? 'light';
$themePrimaryName = $themeDefaults['THEME_PRIMARY_NAME'] ?? 'Grafite Suave';
$themePrimaryHex = $themeDefaults['THEME_PRIMARY_HEX'] ?? '#5b616f';
$themeTitle = $themeDefaults['THEME_APP_TITLE'] ?? 'IndusOS Food';
$themeTagline = $themeDefaults['THEME_APP_TAGLINE'] ?? 'Sistema de Controle Industrial (SCI)';
$titleSize = $themeDefaults['THEME_TITLE_SIZE'] ?? '24';
$titleOffsetX = $themeDefaults['THEME_TITLE_OFFSET_X'] ?? '0';
$titleOffsetY = $themeDefaults['THEME_TITLE_OFFSET_Y'] ?? '0';
$titleColorize = $themeDefaults['THEME_TITLE_COLORIZE'] ?? '0';
$logoFilter = $themeDefaults['THEME_LOGO_FILTER'] ?? '0';
$themeLogoLight = $themeDefaults['THEME_LOGO_LIGHT'] ?? 'uploads/branding/logodark.png';
$themeLogoDark = $themeDefaults['THEME_LOGO_DARK'] ?? 'uploads/branding/logowhite.png';
$compactFallback = $themeDefaults['THEME_LOGO_COMPACT'] ?? 'img/logosmall.png';
$themeLogoCompactLight = $themeDefaults['THEME_LOGO_COMPACT_LIGHT'] ?? $compactFallback;
$themeLogoCompactDark = $themeDefaults['THEME_LOGO_COMPACT_DARK'] ?? $compactFallback;
$themeLogoFooter = $themeDefaults['THEME_LOGO_FOOTER'] ?? 'uploads/branding/logo.png';
$logoLightSize = $themeDefaults['THEME_LOGO_LIGHT_SIZE'] ?? '50';
$logoDarkSize = $themeDefaults['THEME_LOGO_DARK_SIZE'] ?? '50';
$logoCompactLightSize = $themeDefaults['THEME_LOGO_COMPACT_LIGHT_SIZE'] ?? '40';
$logoCompactDarkSize = $themeDefaults['THEME_LOGO_COMPACT_DARK_SIZE'] ?? '40';
$logoLightOffsetX = $themeDefaults['THEME_LOGO_LIGHT_OFFSET_X'] ?? '0';
$logoLightOffsetY = $themeDefaults['THEME_LOGO_LIGHT_OFFSET_Y'] ?? '0';
$logoDarkOffsetX = $themeDefaults['THEME_LOGO_DARK_OFFSET_X'] ?? '0';
$logoDarkOffsetY = $themeDefaults['THEME_LOGO_DARK_OFFSET_Y'] ?? '0';
$logoCompactLightOffsetX = $themeDefaults['THEME_LOGO_COMPACT_LIGHT_OFFSET_X'] ?? '0';
$logoCompactLightOffsetY = $themeDefaults['THEME_LOGO_COMPACT_LIGHT_OFFSET_Y'] ?? '0';
$logoCompactDarkOffsetX = $themeDefaults['THEME_LOGO_COMPACT_DARK_OFFSET_X'] ?? '0';
$logoCompactDarkOffsetY = $themeDefaults['THEME_LOGO_COMPACT_DARK_OFFSET_Y'] ?? '0';
$portalLoginLogoFilter = $themeDefaults['PORTAL_LOGIN_LOGO_FILTER'] ?? '0';
$portalLoginLogoLight = $themeDefaults['PORTAL_LOGIN_LOGO_LIGHT'] ?? $themeLogoLight;
$portalLoginLogoDark = $themeDefaults['PORTAL_LOGIN_LOGO_DARK'] ?? $themeLogoDark;
$portalLoginLogoLightSize = $themeDefaults['PORTAL_LOGIN_LOGO_LIGHT_SIZE'] ?? '60';
$portalLoginLogoDarkSize = $themeDefaults['PORTAL_LOGIN_LOGO_DARK_SIZE'] ?? '60';
$portalLoginLogoLightOffsetX = $themeDefaults['PORTAL_LOGIN_LOGO_LIGHT_OFFSET_X'] ?? '0';
$portalLoginLogoLightOffsetY = $themeDefaults['PORTAL_LOGIN_LOGO_LIGHT_OFFSET_Y'] ?? '0';
$portalLoginLogoDarkOffsetX = $themeDefaults['PORTAL_LOGIN_LOGO_DARK_OFFSET_X'] ?? '0';
$portalLoginLogoDarkOffsetY = $themeDefaults['PORTAL_LOGIN_LOGO_DARK_OFFSET_Y'] ?? '0';
$noteDefaults = $noteDefaults ?? [];
$noteNome = $noteDefaults['NOTE_FABRICA_NOME'] ?? $themeTitle;
$noteDoc = $noteDefaults['NOTE_FABRICA_DOC'] ?? 'CNPJ 00.000.000/0000-00';
$noteEndereco = $noteDefaults['NOTE_FABRICA_ENDERECO'] ?? 'Rua Exemplo, 123 - Bairro - Cidade/UF';
$noteContato = $noteDefaults['NOTE_FABRICA_CONTATO'] ?? '(11) 3000-0000';
$noteEmail = $noteDefaults['NOTE_FABRICA_EMAIL'] ?? 'contato@indusos.com.br';
$noteSite = $noteDefaults['NOTE_FABRICA_SITE'] ?? 'indusos.com.br';
$noteLogoPath = $noteDefaults['NOTE_LOGO'] ?? $themeLogoLight;
$notePixEnabled = $noteDefaults['NOTE_PIX_ENABLED'] ?? '0';
$notePixKey = $noteDefaults['NOTE_PIX_KEY'] ?? '';
$notePixType = $noteDefaults['NOTE_PIX_KEY_TYPE'] ?? 'cnpj';

$assetHelper = $asset ?? static fn(string $path) => $path;
$logoLightUrl = $assetHelper($themeLogoLight);
$logoDarkUrl = $assetHelper($themeLogoDark);
$logoCompactLightUrl = $assetHelper($themeLogoCompactLight);
$logoCompactDarkUrl = $assetHelper($themeLogoCompactDark);
$logoFooterUrl = $assetHelper($themeLogoFooter);
$portalLoginLogoLightUrl = $assetHelper($portalLoginLogoLight);
$portalLoginLogoDarkUrl = $assetHelper($portalLoginLogoDark);
$noteLogoUrl = $assetHelper($noteLogoPath);

$portalSupport = $portalSupport ?? ($support ?? []);
$supportEmail = $portalSupport['email'] ?? 'suporte@indusos.com.br';
$supportTelefone = $portalSupport['telefone'] ?? '(11) 3000-0000';
$supportWhatsapp = $portalSupport['whatsapp'] ?? '+55 11 99999-9999';
$supportHorario = $portalSupport['horario'] ?? 'Segunda a sexta, 8h às 18h';
$supportMensagem = $portalSupport['mensagem'] ?? 'Fale conosco sempre que precisar ajustar volumes, lotes ou agendar coletas.';
$metricUnitsRaw = $metricUnitsRaw ?? 'kg, un, cx';
$estoqueBaixoMin = isset($estoqueBaixoMin) ? (int)$estoqueBaixoMin : 4;
$estoqueBaixoMax = isset($estoqueBaixoMax) ? (int)$estoqueBaixoMax : 8;
$backupFull = $backupFull ?? '1';
$backupModulesSelected = $backupModulesSelected ?? [];
$backupModuleOptions = $backupModuleOptions ?? [];
$backupModulesRaw = $backupModulesRaw ?? '';
$performanceIncludeSaturday = $performanceIncludeSaturday ?? '1';
$performanceIncludeSunday = $performanceIncludeSunday ?? '1';
$allowNegativeStock = $allowNegativeStock ?? '0';
$financeiroAlertaVencimentoDias = isset($financeiroAlertaVencimentoDias) ? (int)$financeiroAlertaVencimentoDias : 7;
$listPageSizeOptions = $listPageSizeOptions ?? [15, 20, 30, 40, 50, 100];
$listPageSizeDefault = isset($listPageSizeDefault) ? (int)$listPageSizeDefault : 15;
$listPageSizeDefaults = $listPageSizeDefaults ?? [
    'clientes' => $listPageSizeDefault,
    'produtos' => $listPageSizeDefault,
    'financeiro_entrada' => $listPageSizeDefault,
    'financeiro_saida' => $listPageSizeDefault,
];

$palette = [
    ['name' => 'Grafite Suave', 'hex' => '#4F535C'],
    ['name' => 'Azul Primário', 'hex' => '#2563EB'],
    ['name' => 'Vermelho Energia', 'hex' => '#DC2626'],
    ['name' => 'Amarelo Solar', 'hex' => '#FACC15'],
    ['name' => 'Verde Selva', 'hex' => '#2F855A'],
    ['name' => 'Vinho Urbano', 'hex' => '#8C1C3C'],
    ['name' => 'Lavanda Nebulosa', 'hex' => '#8A7AAE'],
    ['name' => 'Turquesa Calmante', 'hex' => '#2AA9A1'],
    ['name' => 'Terracota Quente', 'hex' => '#D97941'],
    ['name' => 'Oliva Moderna', 'hex' => '#8A9A5B'],
    ['name' => 'Marfim Suave', 'hex' => '#F5E6CC'],
    ['name' => 'Carvão Noturno', 'hex' => '#222831'],
    ['name' => 'Ciano Boreal', 'hex' => '#3AA7D6'],
    ['name' => 'Ameixa Profunda', 'hex' => '#5C2A72'],
    ['name' => 'Cobre Acetinado', 'hex' => '#BD7449'],
    ['name' => 'Neve Polar', 'hex' => '#ECF2F8'],
    ['name' => 'Cedro Clássico', 'hex' => '#7A5230'],
    ['name' => 'Rosé Antigo', 'hex' => '#CE6F8C'],
    ['name' => 'Índigo Elétrico', 'hex' => '#3949AB'],
    ['name' => 'Pistache Claro', 'hex' => '#C3D18A'],
    ['name' => 'Slate Elegante', 'hex' => '#5B6D7D'],
    ['name' => 'Brisa Perolada', 'hex' => '#E7F1F8'],
    ['name' => 'Âmbar Dourado', 'hex' => '#E8A03F'],
    ['name' => 'Menta Serena', 'hex' => '#9FD7C5'],
];

$roleLabels = $roleLabels ?? [];
$hierarchyModules = $hierarchyModules ?? [];
$roleHierarchy = $roleHierarchy ?? [];
$defaultTab = $defaultTab ?? 'rules';

if (!function_exists('app_logo_matrix_values')) {
    function app_logo_matrix_values(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            $hex = '5B616F';
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $row = static fn(float $factor): array => [
            sprintf('%.6f', 0.2126 * $factor),
            sprintf('%.6f', 0.7152 * $factor),
            sprintf('%.6f', 0.0722 * $factor),
            '0',
            '0',
        ];
        $values = array_merge($row($r), $row($g), $row($b), ['0', '0', '0', '1', '0']);
        return implode(' ', $values);
    }
}

if (!function_exists('shade_hex')) {
    function shade_hex(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#5B616F';
        }

        $result = '#';
        $amount = $percent / 100;
        foreach (str_split($hex, 2) as $pair) {
            $channel = hexdec($pair);
            if ($percent >= 0) {
                $channel = $channel + (255 - $channel) * $amount;
            } else {
                $channel = $channel + $channel * $amount;
            }
            $channel = max(0, min(255, (int)round($channel)));
            $result .= str_pad(dechex($channel), 2, '0', STR_PAD_LEFT);
        }

        return strtoupper($result);
    }
}

$logoTintPreviewHex = $themePrimaryHex;
$previewMatrixDefault = app_logo_matrix_values($logoTintPreviewHex);
?>
<section class="config-wrap">
    <header class="config-header">
        <div>
            <h1>Configurações do Sistema</h1>
            <p>Personalize regras operacionais e identidade visual do painel.</p>
        </div>
    </header>

    <nav class="config-tabs" role="tablist">
        <button type="button" class="tab-button active" data-tab="rules" aria-selected="true">Regras operacionais</button>
        <button type="button" class="tab-button" data-tab="backup" aria-selected="false">Backup</button>
        <button type="button" class="tab-button" data-tab="performance" aria-selected="false">Desempenho</button>
        <button type="button" class="tab-button" data-tab="users" aria-selected="false">Usuários</button>
        <button type="button" class="tab-button" data-tab="admin" aria-selected="false">Admin</button>
        <button type="button" class="tab-button" data-tab="appearance" aria-selected="false">Personalização visual</button>
        <button type="button" class="tab-button" data-tab="nota" aria-selected="false">Nota</button>
    </nav>

    <div class="tab-panels">
        <form class="card tab-panel active" data-tab-panel="rules" method="post" action="<?= htmlspecialchars($url('config/save'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="active_tab" value="rules">
            <h2>Regras operacionais</h2>
            <p class="card-subtitle">Separe parâmetros de produção e finança para controlar alertas, estoque e vencimentos.</p>

            <div class="rules-subtabs" role="tablist" aria-label="Regras operacionais">
                <button type="button" class="rules-subtab active" data-rules-tab="producao" aria-selected="true">Produção</button>
                <button type="button" class="rules-subtab" data-rules-tab="financa" aria-selected="false">Finança</button>
            </div>

            <div class="rules-subpanel active" data-rules-panel="producao">
                <h3>Produção</h3>
                <p class="card-subtitle">Defina a antecedência dos avisos de validade, unidades de medida e regras de estoque.</p>
                <div class="field">
                    <label for="alerta">Dias para alerta de validade</label>
                    <input type="number" min="1" id="alerta" name="config[VALIDADE_ALERTA_DIAS]" value="<?= htmlspecialchars($alertaPadrao, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="field">
                    <label for="metric-units">Medida métrica (lista de unidades)</label>
                    <input id="metric-units" name="config[METRIC_UNITS]" value="<?= htmlspecialchars($metricUnitsRaw, ENT_QUOTES, 'UTF-8'); ?>" placeholder="kg, un, cx">
                    <p class="field-hint">As unidades informadas aqui aparecem pré-cadastradas no formulário de produtos.</p>
                </div>
                <div class="field">
                    <label>Faixa de estoque baixo</label>
                    <div class="range-inline">
                        <div class="range-box">
                            <span>Mínimo</span>
                            <input type="number" min="0" id="low-min" name="config[ESTOQUE_BAIXO_MIN]" value="<?= htmlspecialchars((string)$estoqueBaixoMin, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="range-box">
                            <span>Máximo</span>
                            <input type="number" min="0" id="low-max" name="config[ESTOQUE_BAIXO_MAX]" value="<?= htmlspecialchars((string)$estoqueBaixoMax, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                    <p class="field-hint">Define quando um item é marcado como "Estoque baixo" na posição de estoque. Ex.: 4 a 8 unidades.</p>
                </div>
                <div class="field">
                    <label for="sku-digits">Casas do sequencial de SKU</label>
                    <input id="sku-digits" type="number" min="2" max="8" name="config[SKU_DIGITS]" value="<?= htmlspecialchars((string)($skuDigits ?? 3), ENT_QUOTES, 'UTF-8'); ?>" placeholder="3">
                    <p class="field-hint">Define quantos dígitos o sistema usa ao gerar SKUs automáticos (ex.: 3 → A-001, MP-001).</p>
                </div>

                <div class="preview-card" aria-live="polite">
                    <div>
                        <p class="preview-label">Prévia de produção antes de salvar</p>
                        <h3 class="preview-title">Alerta em <span id="preview-alerta"><?= htmlspecialchars($alertaPadrao ?? '15', ENT_QUOTES, 'UTF-8'); ?></span> dias</h3>
                        <p class="preview-line">Unidades: <span id="preview-unidades"><?= htmlspecialchars($metricUnitsRaw, ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <p class="preview-line">Estoque baixo: <span id="preview-low"><?= htmlspecialchars($estoqueBaixoMin, ENT_QUOTES, 'UTF-8'); ?> a <?= htmlspecialchars($estoqueBaixoMax, ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <p class="preview-line">Sequencial SKU: <span id="preview-sku"><?= htmlspecialchars((string)($skuDigits ?? 3), ENT_QUOTES, 'UTF-8'); ?> dígitos</span></p>
                    </div>
                    <div class="preview-icon" aria-hidden="true">👁️</div>
                </div>
            </div>

            <div class="rules-subpanel" data-rules-panel="financa">
                <h3>Finança</h3>
                <p class="card-subtitle">Defina quantos dias antes do vencimento o financeiro deve destacar entradas e saídas.</p>
                <div class="field">
                    <label for="financeiro-alerta-vencimento">Dias de aviso antes do vencimento</label>
                    <input type="number" min="0" max="365" id="financeiro-alerta-vencimento" name="config[FINANCEIRO_ALERTA_VENCIMENTO_DIAS]" value="<?= htmlspecialchars((string)$financeiroAlertaVencimentoDias, ENT_QUOTES, 'UTF-8'); ?>" required>
                    <p class="field-hint">Use 0 para marcar apenas lançamentos vencidos. Este prazo gera o status "A vencer" nos lançamentos financeiros.</p>
                </div>

                <div class="preview-card" aria-live="polite">
                    <div>
                        <p class="preview-label">Prévia de finança antes de salvar</p>
                        <h3 class="preview-title">Aviso financeiro em <span id="preview-financeiro-alerta"><?= htmlspecialchars((string)$financeiroAlertaVencimentoDias, ENT_QUOTES, 'UTF-8'); ?></span> dias antes do vencimento</h3>
                        <p class="preview-line">Status exibidos nos lançamentos: <strong>Em dia</strong>, <strong>A vencer</strong> ou <strong>Vencido</strong>.</p>
                    </div>
                    <div class="preview-icon" aria-hidden="true">💰</div>
                </div>
            </div>

            <footer class="form-footer">
                <button type="submit" class="btn-primary">Salvar regras</button>
            </footer>
        </form>

        <form class="card tab-panel" data-tab-panel="performance" method="post" action="<?= htmlspecialchars($url('config/save'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="active_tab" value="performance">
            <h2>Desempenho</h2>
            <p class="card-subtitle">Escolha como as médias de produção consideram sábados e domingos de forma independente.</p>

            <div class="field performance-toggle">
                <div>
                    <label>Incluir finais de semana nas médias?</label>
                    <p class="card-subtitle">Marque cada dia para que ele conte nos percentuais exibidos no mapa e no dashboard.</p>
                </div>
                <div class="switch-group">
                    <label class="switch" for="perf-saturday">
                        <input type="hidden" name="config[PERFORMANCE_INCLUDE_SATURDAY]" value="0">
                        <input type="checkbox" id="perf-saturday" name="config[PERFORMANCE_INCLUDE_SATURDAY]" value="1" <?= $performanceIncludeSaturday === '1' ? 'checked' : ''; ?>>
                        <span class="switch-track" aria-hidden="true"></span>
                        <span class="switch-text">Contar sábados</span>
                    </label>
                    <label class="switch" for="perf-sunday">
                        <input type="hidden" name="config[PERFORMANCE_INCLUDE_SUNDAY]" value="0">
                        <input type="checkbox" id="perf-sunday" name="config[PERFORMANCE_INCLUDE_SUNDAY]" value="1" <?= $performanceIncludeSunday === '1' ? 'checked' : ''; ?>>
                        <span class="switch-track" aria-hidden="true"></span>
                        <span class="switch-text">Contar domingos</span>
                    </label>
                </div>
            </div>

            <footer class="form-footer">
                <button type="submit" class="btn-primary">Salvar desempenho</button>
            </footer>
        </form>

        <div class="tab-panel" data-tab-panel="users">
            <div class="user-subtabs" role="tablist" aria-label="Configurações de usuários">
                <button type="button" class="user-subtab active" data-user-tab="create" aria-selected="true">Cadastro</button>
                <button type="button" class="user-subtab" data-user-tab="hierarchy" aria-selected="false">Hierarquia</button>
            </div>

            <div class="user-subpanel active" data-user-panel="create">
                <div class="card">
                    <form
                        method="post"
                        action="<?= htmlspecialchars($url('config/users/create'), ENT_QUOTES, 'UTF-8'); ?>"
                        class="user-form"
                        data-create-action="<?= htmlspecialchars($url('config/users/create'), ENT_QUOTES, 'UTF-8'); ?>"
                        data-update-action="<?= htmlspecialchars($url('config/users/update'), ENT_QUOTES, 'UTF-8'); ?>"
                    >
                        <input type="hidden" name="active_tab" value="users">
                        <input type="hidden" name="id" id="user-id" value="">
                        <h2>Usuários</h2>
                        <p class="card-subtitle">Cadastre usuários internos e escolha a hierarquia que define o acesso aos módulos.</p>

                        <div class="field-grid">
                            <div class="field">
                                <label for="user-name">Nome completo</label>
                                <input id="user-name" name="nome" required placeholder="Ex.: Ana Souza">
                            </div>
                            <div class="field">
                                <label for="user-email">E-mail</label>
                                <input id="user-email" type="email" name="email" required placeholder="email@empresa.com">
                            </div>
                        </div>

                        <div class="field-grid">
                            <div class="field">
                                <label for="user-documento">CPF ou CNPJ</label>
                                <input id="user-documento" name="documento" placeholder="Somente números (opcional)">
                            </div>
                            <div class="field">
                                <label for="user-role">Hierarquia</label>
                                <select id="user-role" name="papel" required>
                                    <?php foreach ($roleLabels as $roleKey => $label): ?>
                                        <option value="<?= htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label for="user-password">Senha (mínimo 6 caracteres)</label>
                            <input id="user-password" type="password" name="senha" required minlength="6" placeholder="Digite uma senha segura">
                        </div>

                        <footer class="form-footer user-actions">
                            <div class="btn-group">
                                <button type="submit" class="btn-primary" id="user-submit">Salvar usuário</button>
                                <button type="button" class="btn ghost" id="user-cancel" style="display:none;">Cancelar edição</button>
                            </div>
                            <p class="field-hint">As permissões refinadas são definidas na seção de Hierarquia abaixo.</p>
                        </footer>
                    </form>
                </div>

                <div class="card user-list-card">
                    <header class="list-header">
                        <div>
                            <h3>Usuários cadastrados</h3>
                            <p class="card-subtitle">Veja a hierarquia atribuída e remova usuários quando necessário.</p>
                        </div>
                    </header>
                    <div class="table-responsive">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Hierarquia</th>
                                    <th class="actions-col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usersList)): ?>
                                    <?php foreach ($usersList as $user): ?>
                                        <tr>
                                            <?php $doc = $user['cpf'] ?? ($user['cnpj'] ?? ''); ?>
                                            <td><?= htmlspecialchars($user['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <span class="role-chip"><?= htmlspecialchars($roleLabels[$user['papel']] ?? $user['papel'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td class="actions-col">
                                                <button
                                                    type="button"
                                                    class="btn ghost user-edit"
                                                    data-id="<?= (int)($user['id'] ?? 0); ?>"
                                                    data-nome="<?= htmlspecialchars($user['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-email="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-papel="<?= htmlspecialchars($user['papel'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-documento="<?= htmlspecialchars(preg_replace('/\D+/', '', (string)$doc), ENT_QUOTES, 'UTF-8'); ?>"
                                                >Editar</button>
                                                <form method="post" action="<?= htmlspecialchars($url('config/users/delete'), ENT_QUOTES, 'UTF-8'); ?>" class="inline-form user-delete-form">
                                                    <input type="hidden" name="active_tab" value="users">
                                                    <input type="hidden" name="id" value="<?= (int)($user['id'] ?? 0); ?>">
                                                    <button type="submit" class="btn-danger ghost">Excluir</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="empty">Nenhum usuário cadastrado até o momento.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="field">
                <label for="allow-negative-stock">Permitir estoque negativo</label>
                <select id="allow-negative-stock" name="config[ALLOW_NEGATIVE_STOCK]">
                    <option value="0" <?= $allowNegativeStock === '0' ? 'selected' : ''; ?>>Não (bloquear execução)</option>
                    <option value="1" <?= $allowNegativeStock === '1' ? 'selected' : ''; ?>>Sim (permitir saldo negativo)</option>
                </select>
                <p class="field-hint">Aplica-se às execuções de receitas e baixas automáticas.</p>
            </div>

            <div class="user-subpanel" data-user-panel="hierarchy">
                <div class="card">
                    <form method="post" action="<?= htmlspecialchars($url('config/hierarchy/save'), ENT_QUOTES, 'UTF-8'); ?>" class="hierarchy-form">
                        <input type="hidden" name="active_tab" value="users">
                        <div class="hierarchy-header">
                            <div>
                                <h2>Hierarquia</h2>
                                <p class="card-subtitle">Marque os módulos que cada nível pode acessar. Admin sempre mantém todos.</p>
                            </div>
                            <small class="field-hint">Ajuste e salve para refletir imediatamente no painel.</small>
                        </div>

                        <div class="role-grid">
                            <?php foreach ($roleLabels as $roleKey => $label): ?>
                                <?php if ($roleKey === 'admin') { continue; } ?>
                                <?php $allowed = $roleHierarchy[$roleKey] ?? []; ?>
                                <div class="role-card">
                                    <header>
                                        <h3><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <span class="role-tag"><?= htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </header>
                                    <div class="module-grid compact">
                                        <?php foreach ($hierarchyModules as $moduleKey => $moduleLabel): ?>
                                            <?php $checked = in_array($moduleKey, $allowed, true) ? 'checked' : ''; ?>
                                            <label class="pill-check">
                                                <input type="checkbox" name="hierarquia[<?= htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8'); ?>][]" value="<?= htmlspecialchars($moduleKey, ENT_QUOTES, 'UTF-8'); ?>" <?= $checked; ?>>
                                                <span class="pill"></span>
                                                <span class="pill-text"><?= htmlspecialchars($moduleLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <footer class="form-footer">
                            <button type="submit" class="btn-primary">Salvar hierarquia</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>

        <div class="card tab-panel" data-tab-panel="admin">
            <h2>Administração</h2>
            <p class="card-subtitle">Centralize informações administrativas e acesso rápido às rotinas de manutenção.</p>
            <div class="field">
                <label>Atalhos administrativos</label>
                <p class="field-hint">Use as outras abas para ajustes de usuários, backups, desempenho e personalização do painel.</p>
            </div>
        </div>

        <form class="card tab-panel" data-tab-panel="backup" method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($url('config/save'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="active_tab" value="backup">
            <h2>Backups</h2>
            <p class="card-subtitle">Configure o backup geral e os módulos que terão cópias dedicadas.</p>

            <div class="field-group backup-card">
                <div class="backup-header">
                    <div>
                        <h3>Backup</h3>
                        <p class="field-hint">Habilite o backup completo ou selecione módulos específicos como Produtos e Clientes.</p>
                    </div>
                    <label class="switch" for="backup-full">
                        <input type="hidden" name="config[BACKUP_FULL_ENABLED]" value="0">
                        <input type="checkbox" id="backup-full" name="config[BACKUP_FULL_ENABLED]" value="1" <?= $backupFull === '1' ? 'checked' : ''; ?>>
                        <span class="switch-track" aria-hidden="true"></span>
                        <span class="switch-text">Backup geral</span>
                    </label>
                </div>

                <div class="field">
                    <span class="field-label">Backup por módulo</span>
                    <div class="module-grid">
                        <?php foreach ($backupModuleOptions as $moduleKey => $moduleLabel): ?>
                            <?php $checked = in_array($moduleKey, $backupModulesSelected, true) ? 'checked' : ''; ?>
                            <label class="pill-check">
                                <input type="checkbox" name="config[BACKUP_MODULES][]" value="<?= htmlspecialchars($moduleKey, ENT_QUOTES, 'UTF-8'); ?>" <?= $checked; ?>>
                                <span class="pill"></span>
                                <span class="pill-text"><?= htmlspecialchars($moduleLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="field-hint">Marque os módulos que devem ter dumps individuais (ex.: Produtos, Clientes, Produção). Se nenhum estiver marcado, só o backup geral será executado.</p>
                </div>

                <div class="field">
                    <label for="backup-dest">Diretório de destino</label>
                    <input id="backup-dest" name="config[BACKUP_DEST_PATH]" value="<?= htmlspecialchars($backupDest, ENT_QUOTES, 'UTF-8'); ?>" placeholder="storage/backups/uploads">
                    <p class="field-hint">Arquivos gerados ou enviados serão salvos nesse caminho relativo. Ex.: <code>storage/backups/uploads</code>.</p>
                    <?php if (!empty($lastBackupFile)): ?>
                        <p class="field-hint">Último backup salvo em: <strong><?= htmlspecialchars($lastBackupFile, ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    <?php endif; ?>
                </div>

                <div class="field backup-upload">
                    <label for="backup-file">Upload de backup</label>
                    <div class="upload-inline">
                        <input type="file" id="backup-file" name="backup_file" accept=".zip,.sql,.gz,.tar,.tar.gz">
                        <button type="submit" name="upload_backup" value="1" class="btn-secondary">Enviar arquivo</button>
                    </div>
                    <p class="field-hint">Envie um dump ou pacote compactado para manter uma cópia restaurável. O arquivo é salvo no diretório configurado acima.</p>
                    <?php if (!empty($lastBackupFile)): ?>
                        <p class="field-meta">Último arquivo enviado: <strong><?= htmlspecialchars($lastBackupFile, ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="form-footer backup-actions">
                <button type="submit" class="btn-secondary" name="run_backup" value="1">Gerar backup agora</button>
                <button type="submit" class="btn-primary">Salvar backups</button>
            </footer>
        </form>

        <form class="card tab-panel" data-tab-panel="appearance" method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($url('config/save'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="active_tab" value="appearance">
            <h2>Personalização Visual</h2>
            <p class="card-subtitle">Ajuste o título, logos e a paleta neutra usada pelo painel.</p>

            <div class="appearance-subtabs" role="tablist" aria-label="Seções de personalização">
                <button type="button" class="appearance-subtab active" data-appearance-tab="geral" aria-selected="true">Geral</button>
                <button type="button" class="appearance-subtab" data-appearance-tab="visual" aria-selected="false">Visual</button>
                <button type="button" class="appearance-subtab" data-appearance-tab="cards" aria-selected="false">Cards</button>
                <button type="button" class="appearance-subtab" data-appearance-tab="portal" aria-selected="false">Portal</button>
            </div>

            <div class="appearance-panels">
                <div class="appearance-panel active" data-appearance-panel="geral">
                    <div class="field">
                        <label for="list-page-size-options">Números disponíveis por página</label>
                        <input id="list-page-size-options" name="config[LIST_PAGE_SIZE_OPTIONS]" value="<?= htmlspecialchars(implode(', ', array_map('intval', $listPageSizeOptions)), ENT_QUOTES, 'UTF-8'); ?>" placeholder="15, 20, 30, 40, 50, 100" data-list-page-options>
                        <p class="field-hint">Digite os números separados por vírgula. Eles aparecem no seletor das listas.</p>
                    </div>
                    <div class="field">
                        <label class="field-label">Padrão por lista</label>
                        <div class="list-page-defaults">
                            <?php
                                $pageDefaultFields = [
                                    'LIST_PAGE_SIZE_DEFAULT' => ['Geral', $listPageSizeDefault],
                                    'LIST_PAGE_SIZE_DEFAULT_CLIENTES' => ['Clientes', $listPageSizeDefaults['clientes'] ?? $listPageSizeDefault],
                                    'LIST_PAGE_SIZE_DEFAULT_PRODUTOS' => ['Produtos', $listPageSizeDefaults['produtos'] ?? $listPageSizeDefault],
                                    'LIST_PAGE_SIZE_DEFAULT_FINANCEIRO_ENTRADA' => ['Financeiro entrada', $listPageSizeDefaults['financeiro_entrada'] ?? $listPageSizeDefault],
                                    'LIST_PAGE_SIZE_DEFAULT_FINANCEIRO_SAIDA' => ['Financeiro saída', $listPageSizeDefaults['financeiro_saida'] ?? $listPageSizeDefault],
                                ];
                            ?>
                            <?php foreach ($pageDefaultFields as $configKey => [$label, $value]): ?>
                                <label class="list-page-default-row">
                                    <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <input type="number" min="1" max="500" name="config[<?= htmlspecialchars($configKey, ENT_QUOTES, 'UTF-8'); ?>]" value="<?= (int)$value; ?>" data-list-page-default>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="field-hint">Cada lista pode abrir com uma quantidade diferente. Ex.: Clientes 30, Produtos 50.</p>
                    </div>
                </div>
                <div class="appearance-panel" data-appearance-panel="visual">

            <div class="field">
                <label for="theme-title">Título exibido no painel</label>
                <input id="theme-title" name="config[THEME_APP_TITLE]" value="<?= htmlspecialchars($themeTitle, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome do sistema">
            </div>
            <div class="field">
                <label for="title-size-input">Tamanho do título (px)</label>
                <div class="title-size-control">
                    <input type="number" id="title-size-input" name="config[THEME_TITLE_SIZE]" min="16" max="64" step="1" value="<?= htmlspecialchars($titleSize, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="range" id="title-size-range" min="16" max="64" step="1" value="<?= htmlspecialchars($titleSize, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Selecionar tamanho do título">
                </div>
                <p class="field-hint">Use a barra ou digite o valor desejado entre 16 e 64 pixels.</p>
            </div>
            <div class="field">
                <span class="field-label">Ajuste do título</span>
                <div class="pos-grid title-offset-grid">
                    <label class="slider-label" for="title-offset-x">Posição X <output id="title-offset-x-val"><?= htmlspecialchars($titleOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                    <input type="range" id="title-offset-x" name="config[THEME_TITLE_OFFSET_X]" min="-80" max="80" value="<?= htmlspecialchars($titleOffsetX, ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="slider-label" for="title-offset-y">Posição Y <output id="title-offset-y-val"><?= htmlspecialchars($titleOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                    <input type="range" id="title-offset-y" name="config[THEME_TITLE_OFFSET_Y]" min="-80" max="80" value="<?= htmlspecialchars($titleOffsetY, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>
            <div class="field">
                <span class="field-label">Cor do título</span>
                <label class="switch" for="title-colorize">
                    <input type="hidden" name="config[THEME_TITLE_COLORIZE]" value="0">
                    <input type="checkbox" id="title-colorize" name="config[THEME_TITLE_COLORIZE]" value="1" <?= $titleColorize === '1' ? 'checked' : ''; ?>>
                    <span class="switch-track" aria-hidden="true"></span>
                    <span class="switch-text">Aplicar a cor da paleta ao título</span>
                </label>
                <p class="field-hint">Quando ativo, o nome do sistema utiliza a cor selecionada na paleta lateral.</p>
            </div>
            <div class="field">
                <span class="field-label">Filtro das logos</span>
                <label class="switch" for="logo-colorize">
                    <input type="hidden" name="config[THEME_LOGO_FILTER]" value="0">
                    <input type="checkbox" id="logo-colorize" name="config[THEME_LOGO_FILTER]" value="1" <?= $logoFilter === '1' ? 'checked' : ''; ?>>
                    <span class="switch-track" aria-hidden="true"></span>
                    <span class="switch-text">Colorir logos com a paleta escolhida</span>
                </label>
                <p class="field-hint">Ative para harmonizar automaticamente as marcas com a cor primária do painel.</p>
            </div>
            <div class="field">
                <span class="field-label">Tema base</span>
                <div class="theme-mode">
                    <label class="mode-option">
                        <input type="radio" name="config[THEME_MODE]" value="light" <?= $themeMode === 'light' ? 'checked' : ''; ?>>
                        <span>Claro</span>
                    </label>
                    <label class="mode-option">
                        <input type="radio" name="config[THEME_MODE]" value="dark" <?= $themeMode === 'dark' ? 'checked' : ''; ?>>
                        <span>Escuro</span>
                    </label>
                </div>
            </div>
            <div class="field">
                <span class="field-label">Paleta de Cor</span>
                <div class="palette">
                    <?php foreach ($palette as $option): ?>
                        <label class="swatch <?= strtolower($themePrimaryHex) === strtolower($option['hex']) ? 'active' : ''; ?>" data-name="<?= htmlspecialchars($option['name'], ENT_QUOTES, 'UTF-8'); ?>" data-hex="<?= htmlspecialchars($option['hex'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="radio" name="palette-choice" value="<?= htmlspecialchars($option['hex'], ENT_QUOTES, 'UTF-8'); ?>" <?= strtolower($themePrimaryHex) === strtolower($option['hex']) ? 'checked' : ''; ?>>
                            <span class="swatch-color" style="--swatch: <?= htmlspecialchars($option['hex'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                            <span class="swatch-meta">
                                <strong><?= htmlspecialchars($option['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <small><?= htmlspecialchars(strtoupper($option['hex']), ENT_QUOTES, 'UTF-8'); ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="field">
                <label for="custom-hex">Cor personalizada</label>
                <div class="custom-color">
                    <input type="color" id="custom-hex-picker" value="<?= htmlspecialchars($themePrimaryHex, ENT_QUOTES, 'UTF-8'); ?>">
                    <input id="custom-hex" name="config[THEME_PRIMARY_HEX]" value="<?= htmlspecialchars(strtoupper($themePrimaryHex), ENT_QUOTES, 'UTF-8'); ?>" placeholder="#5B616F">
                </div>
                <input type="hidden" id="custom-name" name="config[THEME_PRIMARY_NAME]" value="<?= htmlspecialchars($themePrimaryName, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="logo-grid">
                <div class="logo-field">
                    <div class="logo-preview light">
                        <header>Tema claro</header>
                        <div class="preview-body">
                            <img src="<?= htmlspecialchars($logoLightUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Pré-visualização logo tema claro" id="preview-light" data-preview-tint>
                        </div>
                    </div>
                    <label class="logo-label" for="logo-light">Caminho logo tema claro</label>
                    <input id="logo-light" name="config[THEME_LOGO_LIGHT]" value="<?= htmlspecialchars($themeLogoLight, ENT_QUOTES, 'UTF-8'); ?>" placeholder="uploads/branding/logodark.png">
                    <label class="upload-label" for="logo-light-file">
                        <span>Enviar novo arquivo</span>
                        <input type="file" id="logo-light-file" name="logo_light_file" accept="image/*">
                    </label>
                    <div class="logo-tuners">
                        <div class="move-key-label">Chave de movimento</div>
                        <label class="slider-label" for="logo-light-size">Tamanho <output id="logo-light-size-val"><?= htmlspecialchars($logoLightSize, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                        <input type="range" id="logo-light-size" name="config[THEME_LOGO_LIGHT_SIZE]" min="24" max="140" value="<?= htmlspecialchars($logoLightSize, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-light">
                        <div class="pos-grid">
                            <label class="slider-label" for="logo-light-x">Posição X <output id="logo-light-x-val"><?= htmlspecialchars($logoLightOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-light-x" name="config[THEME_LOGO_LIGHT_OFFSET_X]" min="-80" max="80" value="<?= htmlspecialchars($logoLightOffsetX, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-light" data-axis="x">
                            <label class="slider-label" for="logo-light-y">Posição Y <output id="logo-light-y-val"><?= htmlspecialchars($logoLightOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-light-y" name="config[THEME_LOGO_LIGHT_OFFSET_Y]" min="-80" max="80" value="<?= htmlspecialchars($logoLightOffsetY, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-light" data-axis="y">
                        </div>
                    </div>
                </div>
                <div class="logo-field">
                    <div class="logo-preview dark">
                        <header>Tema escuro</header>
                        <div class="preview-body">
                            <img src="<?= htmlspecialchars($logoDarkUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Pré-visualização logo tema escuro" id="preview-dark" data-preview-tint>
                        </div>
                    </div>
                    <label class="logo-label" for="logo-dark">Caminho logo tema escuro</label>
                    <input id="logo-dark" name="config[THEME_LOGO_DARK]" value="<?= htmlspecialchars($themeLogoDark, ENT_QUOTES, 'UTF-8'); ?>" placeholder="uploads/branding/logowhite.png">
                    <label class="upload-label" for="logo-dark-file">
                        <span>Enviar novo arquivo</span>
                        <input type="file" id="logo-dark-file" name="logo_dark_file" accept="image/*">
                    </label>
                    <div class="logo-tuners">
                        <div class="move-key-label">Chave de movimento</div>
                        <label class="slider-label" for="logo-dark-size">Tamanho <output id="logo-dark-size-val"><?= htmlspecialchars($logoDarkSize, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                        <input type="range" id="logo-dark-size" name="config[THEME_LOGO_DARK_SIZE]" min="24" max="140" value="<?= htmlspecialchars($logoDarkSize, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-dark">
                        <div class="pos-grid">
                            <label class="slider-label" for="logo-dark-x">Posição X <output id="logo-dark-x-val"><?= htmlspecialchars($logoDarkOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-dark-x" name="config[THEME_LOGO_DARK_OFFSET_X]" min="-80" max="80" value="<?= htmlspecialchars($logoDarkOffsetX, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-dark" data-axis="x">
                            <label class="slider-label" for="logo-dark-y">Posição Y <output id="logo-dark-y-val"><?= htmlspecialchars($logoDarkOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-dark-y" name="config[THEME_LOGO_DARK_OFFSET_Y]" min="-80" max="80" value="<?= htmlspecialchars($logoDarkOffsetY, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-dark" data-axis="y">
                        </div>
                    </div>
                </div>
                <div class="logo-field">
                    <div class="logo-preview compact-light">
                        <header>Menu recolhido (claro)</header>
                        <div class="preview-body">
                            <img src="<?= htmlspecialchars($logoCompactLightUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Pré-visualização logo compacta clara" id="preview-compact-light" data-preview-tint>
                        </div>
                    </div>
                    <label class="logo-label" for="logo-compact-light">Caminho logo compacta clara</label>
                    <input id="logo-compact-light" name="config[THEME_LOGO_COMPACT_LIGHT]" value="<?= htmlspecialchars($themeLogoCompactLight, ENT_QUOTES, 'UTF-8'); ?>" placeholder="img/logosmall.png">
                    <label class="upload-label" for="logo-compact-light-file">
                        <span>Enviar novo arquivo</span>
                        <input type="file" id="logo-compact-light-file" name="logo_compact_light_file" accept="image/*">
                    </label>
                    <div class="logo-tuners">
                        <div class="move-key-label">Chave de movimento</div>
                        <label class="slider-label" for="logo-compact-light-size">Tamanho <output id="logo-compact-light-size-val"><?= htmlspecialchars($logoCompactLightSize, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                        <input type="range" id="logo-compact-light-size" name="config[THEME_LOGO_COMPACT_LIGHT_SIZE]" min="16" max="120" value="<?= htmlspecialchars($logoCompactLightSize, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-compact-light">
                        <div class="pos-grid">
                            <label class="slider-label" for="logo-compact-light-x">Posição X <output id="logo-compact-light-x-val"><?= htmlspecialchars($logoCompactLightOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-compact-light-x" name="config[THEME_LOGO_COMPACT_LIGHT_OFFSET_X]" min="-80" max="80" value="<?= htmlspecialchars($logoCompactLightOffsetX, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-compact-light" data-axis="x">
                            <label class="slider-label" for="logo-compact-light-y">Posição Y <output id="logo-compact-light-y-val"><?= htmlspecialchars($logoCompactLightOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-compact-light-y" name="config[THEME_LOGO_COMPACT_LIGHT_OFFSET_Y]" min="-80" max="80" value="<?= htmlspecialchars($logoCompactLightOffsetY, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-compact-light" data-axis="y">
                        </div>
                    </div>
                </div>
                <div class="logo-field">
                    <div class="logo-preview compact-dark">
                        <header>Menu recolhido (escuro)</header>
                        <div class="preview-body">
                            <img src="<?= htmlspecialchars($logoCompactDarkUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Pré-visualização logo compacta escura" id="preview-compact-dark" data-preview-tint>
                        </div>
                    </div>
                    <label class="logo-label" for="logo-compact-dark">Caminho logo compacta escura</label>
                    <input id="logo-compact-dark" name="config[THEME_LOGO_COMPACT_DARK]" value="<?= htmlspecialchars($themeLogoCompactDark, ENT_QUOTES, 'UTF-8'); ?>" placeholder="img/logosmall.png">
                    <label class="upload-label" for="logo-compact-dark-file">
                        <span>Enviar novo arquivo</span>
                        <input type="file" id="logo-compact-dark-file" name="logo_compact_dark_file" accept="image/*">
                    </label>
                    <div class="logo-tuners">
                        <div class="move-key-label">Chave de movimento</div>
                        <label class="slider-label" for="logo-compact-dark-size">Tamanho <output id="logo-compact-dark-size-val"><?= htmlspecialchars($logoCompactDarkSize, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                        <input type="range" id="logo-compact-dark-size" name="config[THEME_LOGO_COMPACT_DARK_SIZE]" min="16" max="120" value="<?= htmlspecialchars($logoCompactDarkSize, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-compact-dark">
                        <div class="pos-grid">
                            <label class="slider-label" for="logo-compact-dark-x">Posição X <output id="logo-compact-dark-x-val"><?= htmlspecialchars($logoCompactDarkOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-compact-dark-x" name="config[THEME_LOGO_COMPACT_DARK_OFFSET_X]" min="-80" max="80" value="<?= htmlspecialchars($logoCompactDarkOffsetX, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-compact-dark" data-axis="x">
                            <label class="slider-label" for="logo-compact-dark-y">Posição Y <output id="logo-compact-dark-y-val"><?= htmlspecialchars($logoCompactDarkOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                            <input type="range" id="logo-compact-dark-y" name="config[THEME_LOGO_COMPACT_DARK_OFFSET_Y]" min="-80" max="80" value="<?= htmlspecialchars($logoCompactDarkOffsetY, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-compact-dark" data-axis="y">
                        </div>
                    </div>
                </div>
            </div>

                </div>
                <div class="appearance-panel" data-appearance-panel="cards">
                    <div class="field">
                        <label class="field-label">Ações rápidas no dashboard</label>
                        <p class="field-hint">Selecione quais módulos aparecem no dashboard e qual tecla de atalho será atribuída.</p>
                        <input type="hidden" id="quick-actions-config" value="<?= htmlspecialchars(json_encode($quickActionDefaults ?? []), ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="quick-actions-config">
                            <?php foreach (($quickActionOptions ?? []) as $option): ?>
                                <?php
                                    $key = $option['key'];
                                    $default = $quickActionDefaults[$key] ?? ['enabled' => '1', 'shortcut' => $option['default_shortcut']];
                                ?>
                                <div class="quick-actions-row" data-qa-row="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="quick-actions-toggle">
                                        <label class="switch">
                                            <input type="hidden" name="config[DASH_QUICK_ACTIONS][<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>][enabled]" value="0">
                                            <input type="checkbox" class="acao-rapida-checkbox" name="config[DASH_QUICK_ACTIONS][<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>][enabled]" value="1" data-qa-enabled <?= ($default['enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                            <span class="switch-track" aria-hidden="true"></span>
                                        </label>
                                        <span>
                                            <strong><?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <small><?= htmlspecialchars($option['description'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        </span>
                                    </div>
                                    <label class="quick-actions-shortcut">
                                        <span class="field-hint">Atalho</span>
                                        <select name="config[DASH_QUICK_ACTIONS][<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>][shortcut]" data-qa-shortcut>
                                            <option value="">Sem atalho</option>
                                            <?php foreach (['F1','F2','F3','F4','F5','F6','F7','F8','F9','F10','F11','F12'] as $hotkey): ?>
                                                <option value="<?= $hotkey; ?>" <?= ($default['shortcut'] ?? '') === $hotkey ? 'selected' : ''; ?>><?= $hotkey; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="appearance-panel" data-appearance-panel="portal">
                    <div class="support-card">
                        <h3>Logo do login do portal</h3>
                        <p class="card-subtitle">Configure as logos clara e escura do login, com tamanho e posiÃ§Ã£o independentes.</p>
                        <div class="field">
                            <span class="field-label">Filtro da paleta do cliente</span>
                            <label class="switch" for="portal-login-logo-filter">
                                <input type="hidden" name="config[PORTAL_LOGIN_LOGO_FILTER]" value="0">
                                <input type="checkbox" id="portal-login-logo-filter" name="config[PORTAL_LOGIN_LOGO_FILTER]" value="1" <?= $portalLoginLogoFilter === '1' ? 'checked' : ''; ?>>
                                <span class="switch-track" aria-hidden="true"></span>
                                <span class="switch-text">Colorir no login com a cor escolhida pelo cliente</span>
                            </label>
                            <p class="field-hint">Ligado usa a logo do modo escuro com filtro da paleta do portal. Desligado usa a logo do modo claro.</p>
                        </div>
                        <div class="logo-grid portal-login-logo-grid">
                            <div class="logo-field">
                                <div class="logo-preview light">
                                    <header>Tema claro</header>
                                    <div class="preview-body">
                                        <img src="<?= htmlspecialchars($portalLoginLogoLightUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="PrÃ©-visualizaÃ§Ã£o logo clara do login do portal" id="preview-portal-login-light">
                                    </div>
                                </div>
                                <label class="logo-label" for="portal-login-logo-light">Caminho logo tema claro</label>
                                <input id="portal-login-logo-light" name="config[PORTAL_LOGIN_LOGO_LIGHT]" value="<?= htmlspecialchars($portalLoginLogoLight, ENT_QUOTES, 'UTF-8'); ?>" placeholder="uploads/branding/logodark.png">
                                <label class="upload-label" for="portal-login-logo-light-file">
                                    <span>Enviar novo arquivo</span>
                                    <input type="file" id="portal-login-logo-light-file" name="portal_login_logo_light_file" accept="image/*">
                                </label>
                                <div class="logo-tuners">
                                    <div class="move-key-label">Chave de movimento</div>
                                    <label class="slider-label" for="portal-login-logo-light-size">Tamanho <output id="portal-login-logo-light-size-val"><?= htmlspecialchars($portalLoginLogoLightSize, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                                    <input type="range" id="portal-login-logo-light-size" name="config[PORTAL_LOGIN_LOGO_LIGHT_SIZE]" min="24" max="180" value="<?= htmlspecialchars($portalLoginLogoLightSize, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-portal-login-light">
                                    <div class="pos-grid">
                                        <label class="slider-label" for="portal-login-logo-light-x">Posi&ccedil;&atilde;o X <output id="portal-login-logo-light-x-val"><?= htmlspecialchars($portalLoginLogoLightOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                                        <input type="range" id="portal-login-logo-light-x" name="config[PORTAL_LOGIN_LOGO_LIGHT_OFFSET_X]" min="-120" max="120" value="<?= htmlspecialchars($portalLoginLogoLightOffsetX, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-portal-login-light" data-axis="x">
                                        <label class="slider-label" for="portal-login-logo-light-y">Posi&ccedil;&atilde;o Y <output id="portal-login-logo-light-y-val"><?= htmlspecialchars($portalLoginLogoLightOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                                        <input type="range" id="portal-login-logo-light-y" name="config[PORTAL_LOGIN_LOGO_LIGHT_OFFSET_Y]" min="-120" max="120" value="<?= htmlspecialchars($portalLoginLogoLightOffsetY, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-portal-login-light" data-axis="y">
                                    </div>
                                </div>
                            </div>
                            <div class="logo-field">
                                <div class="logo-preview dark">
                                    <header>Tema escuro</header>
                                    <div class="preview-body">
                                        <img src="<?= htmlspecialchars($portalLoginLogoDarkUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="PrÃ©-visualizaÃ§Ã£o logo escura do login do portal" id="preview-portal-login-dark">
                                    </div>
                                </div>
                                <label class="logo-label" for="portal-login-logo-dark">Caminho logo tema escuro</label>
                                <input id="portal-login-logo-dark" name="config[PORTAL_LOGIN_LOGO_DARK]" value="<?= htmlspecialchars($portalLoginLogoDark, ENT_QUOTES, 'UTF-8'); ?>" placeholder="uploads/branding/logowhite.png">
                                <label class="upload-label" for="portal-login-logo-dark-file">
                                    <span>Enviar novo arquivo</span>
                                    <input type="file" id="portal-login-logo-dark-file" name="portal_login_logo_dark_file" accept="image/*">
                                </label>
                                <div class="logo-tuners">
                                    <div class="move-key-label">Chave de movimento</div>
                                    <label class="slider-label" for="portal-login-logo-dark-size">Tamanho <output id="portal-login-logo-dark-size-val"><?= htmlspecialchars($portalLoginLogoDarkSize, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                                    <input type="range" id="portal-login-logo-dark-size" name="config[PORTAL_LOGIN_LOGO_DARK_SIZE]" min="24" max="180" value="<?= htmlspecialchars($portalLoginLogoDarkSize, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-portal-login-dark">
                                    <div class="pos-grid">
                                        <label class="slider-label" for="portal-login-logo-dark-x">Posi&ccedil;&atilde;o X <output id="portal-login-logo-dark-x-val"><?= htmlspecialchars($portalLoginLogoDarkOffsetX, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                                        <input type="range" id="portal-login-logo-dark-x" name="config[PORTAL_LOGIN_LOGO_DARK_OFFSET_X]" min="-120" max="120" value="<?= htmlspecialchars($portalLoginLogoDarkOffsetX, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-portal-login-dark" data-axis="x">
                                        <label class="slider-label" for="portal-login-logo-dark-y">Posi&ccedil;&atilde;o Y <output id="portal-login-logo-dark-y-val"><?= htmlspecialchars($portalLoginLogoDarkOffsetY, ENT_QUOTES, 'UTF-8'); ?>px</output></label>
                                        <input type="range" id="portal-login-logo-dark-y" name="config[PORTAL_LOGIN_LOGO_DARK_OFFSET_Y]" min="-120" max="120" value="<?= htmlspecialchars($portalLoginLogoDarkOffsetY, ENT_QUOTES, 'UTF-8'); ?>" data-preview="preview-portal-login-dark" data-axis="y">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="support-card">
                <h3>Suporte do portal</h3>
                <p class="card-subtitle">Defina os canais exibidos na aba "Suporte" para seus clientes.</p>
                <div class="support-grid">
                    <div class="field">
                        <label for="support-email">E-mail de atendimento</label>
                        <input id="support-email" name="config[PORTAL_SUPPORT_EMAIL]" type="email" placeholder="suporte@empresa.com" value="<?= htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="field">
                        <label for="support-phone">Telefone</label>
                        <input id="support-phone" name="config[PORTAL_SUPPORT_PHONE]" placeholder="(11) 3000-0000" value="<?= htmlspecialchars($supportTelefone, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="field">
                        <label for="support-whatsapp">WhatsApp</label>
                        <input id="support-whatsapp" name="config[PORTAL_SUPPORT_WHATSAPP]" placeholder="+55 11 90000-0000" value="<?= htmlspecialchars($supportWhatsapp, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="field">
                        <label for="support-hours">Horário de atendimento</label>
                        <input id="support-hours" name="config[PORTAL_SUPPORT_HOURS]" placeholder="Segunda a sexta, 8h às 18h" value="<?= htmlspecialchars($supportHorario, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="support-message">Mensagem complementar</label>
                    <textarea id="support-message" name="config[PORTAL_SUPPORT_MESSAGE]" rows="2" placeholder="Conte conosco para ajustar volumes, lotes ou agendar coletas."><?= htmlspecialchars($supportMensagem, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                    </div>
                </div>
            </div>

            <footer class="form-footer">
                <button type="submit" class="btn-primary">Salvar identidade</button>
            </footer>
        </form>

        <form class="card tab-panel" data-tab-panel="nota" method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($url('config/save'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="active_tab" value="nota">
            <h2>Nota e identificação da fábrica</h2>
            <p class="card-subtitle">Defina os dados exibidos na impressão de pedidos internos.</p>

            <div class="field">
                <label for="note-nome">Nome/Razão social</label>
                <input id="note-nome" name="config[NOTE_FABRICA_NOME]" value="<?= htmlspecialchars($noteNome, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Fantasia ou razão social">
            </div>

            <div class="field">
                <label for="note-doc">Documento</label>
                <input id="note-doc" name="config[NOTE_FABRICA_DOC]" value="<?= htmlspecialchars($noteDoc, ENT_QUOTES, 'UTF-8'); ?>" placeholder="CNPJ ou inscrição estadual">
            </div>

            <div class="field">
                <label for="note-endereco">Endereço</label>
                <input id="note-endereco" name="config[NOTE_FABRICA_ENDERECO]" value="<?= htmlspecialchars($noteEndereco, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Rua, número, bairro, cidade/UF">
            </div>

            <div class="field-grid">
                <label class="field">
                    Contato/telefone
                    <input name="config[NOTE_FABRICA_CONTATO]" value="<?= htmlspecialchars($noteContato, ENT_QUOTES, 'UTF-8'); ?>" placeholder="(11) 3000-0000">
                </label>
                <label class="field">
                    E-mail
                    <input name="config[NOTE_FABRICA_EMAIL]" value="<?= htmlspecialchars($noteEmail, ENT_QUOTES, 'UTF-8'); ?>" placeholder="contato@empresa.com">
                </label>
                <label class="field">
                    Site
                    <input name="config[NOTE_FABRICA_SITE]" value="<?= htmlspecialchars($noteSite, ENT_QUOTES, 'UTF-8'); ?>" placeholder="www.suaempresa.com">
                </label>
            </div>

            <div class="logo-field">
                <div class="logo-preview light">
                    <header>Logo da nota</header>
                    <div class="preview-body">
                        <img src="<?= htmlspecialchars($noteLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Pré-visualização logo nota" id="preview-note" data-preview-tint>
                    </div>
                </div>
                <label class="logo-label" for="note-logo">Caminho logo da nota</label>
                <input id="note-logo" name="config[NOTE_LOGO]" value="<?= htmlspecialchars($noteLogoPath, ENT_QUOTES, 'UTF-8'); ?>" placeholder="img/logo.png">
                <label class="upload-label" for="note-logo-file">
                    <span>Enviar novo arquivo</span>
                    <input type="file" id="note-logo-file" name="note_logo_file" accept="image/*">
                </label>
                <p class="field-hint">Uploads vão para <code>public/uploads/branding/</code>; utilize caminhos relativos como <code>uploads/branding/sua-logo.png</code> na nota.</p>
            </div>

            <div class="field">
                <span class="field-label">Pagamento via PIX</span>
                <label class="switch" for="note-pix-enabled">
                    <input type="hidden" name="config[NOTE_PIX_ENABLED]" value="0">
                    <input type="checkbox" id="note-pix-enabled" name="config[NOTE_PIX_ENABLED]" value="1" <?= $notePixEnabled === '1' ? 'checked' : ''; ?>>
                    <span class="switch-track" aria-hidden="true"></span>
                    <span class="switch-text">Exibir bloco de PIX na nota impressa</span>
                </label>
                <p class="field-hint">Ative para mostrar o QR e a chave PIX de cobrança na emissão da nota.</p>
            </div>

            <div class="field">
                <label for="note-pix-type">Tipo da chave</label>
                <select id="note-pix-type" name="config[NOTE_PIX_KEY_TYPE]">
                    <?php
                    $pixTypes = [
                        'cnpj' => 'CNPJ',
                        'cpf' => 'CPF',
                        'email' => 'E-mail',
                        'telefone' => 'Celular',
                    ];
                    foreach ($pixTypes as $pixKeyTypeValue => $pixKeyTypeLabel):
                        $selected = $notePixType === $pixKeyTypeValue ? 'selected' : '';
                    ?>
                        <option value="<?= $pixKeyTypeValue; ?>" <?= $selected; ?>><?= $pixKeyTypeLabel; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="field-hint">Escolha o formato da chave PIX padrão (CNPJ, CPF, e-mail ou celular).</p>
            </div>

            <div class="field">
                <label for="note-pix-key">Chave PIX padrão</label>
                <input id="note-pix-key" name="config[NOTE_PIX_KEY]" value="<?= htmlspecialchars($notePixKey, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Insira a chave PIX a ser exibida">
                <p class="field-hint">Você ainda pode ajustar a chave ou desativar o PIX ao imprimir cada nota.</p>
            </div>

            <footer class="form-footer">
                <button type="submit" class="btn-primary">Salvar dados da nota</button>
            </footer>
        </form>
    </div>

</section>

<style>
    .config-wrap {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .config-header h1 {
        margin: 0;
        font-size: 1.9rem;
    }
    .config-header p {
        margin: 4px 0 0;
        color: var(--text-muted);
    }
    .config-tabs {
        display: inline-flex;
        gap: 12px;
        background: var(--surface);
        border-radius: 999px;
        padding: 6px;
        border: 1px solid var(--border);
        width: fit-content;
    }
    .tab-button {
        border: none;
        background: transparent;
        border-radius: 999px;
        padding: 10px 20px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .tab-button.active {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 6px 20px -12px var(--primary);
    }
    .rules-subtabs {
        display: inline-flex;
        gap: 8px;
        flex-wrap: wrap;
        width: fit-content;
        padding: 6px;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: var(--surface-muted);
    }
    .rules-subtab {
        border: none;
        border-radius: 999px;
        padding: 9px 16px;
        background: transparent;
        color: var(--text-muted);
        font-weight: 700;
        cursor: pointer;
    }
    .rules-subtab.active {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 6px 20px -12px var(--primary);
    }
    .rules-subpanel {
        display: none;
        flex-direction: column;
        gap: 20px;
    }
    .rules-subpanel.active {
        display: flex;
    }
    .tab-panels {
        display: grid;
    }
    .tab-panel {
        display: none;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 28px;
        gap: 20px;
        box-shadow: 0 18px 40px -28px rgba(15, 23, 42, 0.55);
    }
    .tab-panel.active {
        display: flex;
        flex-direction: column;
    }
    .preview-card {
        margin-top: 8px;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(120deg, rgba(37, 99, 235, 0.08), rgba(37, 99, 235, 0.02));
    }
    .preview-title { margin: 4px 0; }
    .preview-line { margin: 2px 0; color: var(--text-muted); }
    .preview-label { margin: 0; font-weight: 700; color: var(--text-muted); }
    .preview-icon { font-size: 26px; opacity: 0.85; }
    .card-subtitle {
        margin: -6px 0 6px;
        color: var(--text-muted);
        font-size: 0.95rem;
    }
    .user-subtabs {
        display: inline-flex;
        gap: 8px;
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 6px;
        border-radius: 12px;
        box-shadow: 0 12px 24px -20px rgba(15, 23, 42, 0.45);
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .user-subtab {
        border: 1px solid transparent;
        background: var(--surface-alt);
        color: var(--text);
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .user-subtab.active {
        background: linear-gradient(135deg, rgba(var(--primary-rgb),0.12), rgba(var(--primary-rgb),0.06));
        color: var(--primary);
        border-color: rgba(var(--primary-rgb),0.24);
        box-shadow: var(--primary-shadow);
    }
    .user-subpanel { display: none; gap: 12px; }
    .user-subpanel.active { display: block; }
    .appearance-subtabs {
        display: inline-flex;
        gap: 8px;
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 6px;
        border-radius: 12px;
        box-shadow: 0 12px 24px -20px rgba(15, 23, 42, 0.45);
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    .appearance-subtab {
        border: 1px solid transparent;
        background: var(--surface-alt);
        color: var(--text);
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .appearance-subtab.active {
        background: var(--primary);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 10px 24px -18px var(--primary);
    }
    .appearance-panels {
        display: grid;
        gap: 20px;
    }
    .appearance-panel { display: none; gap: 20px; }
    .appearance-panel.active { display: flex; flex-direction: column; }
    .list-page-defaults {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 12px;
    }
    .list-page-default-row {
        display: grid;
        gap: 6px;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px;
        background: var(--surface-alt);
        color: var(--text);
        font-weight: 800;
    }
    .quick-actions-config {
        display: grid;
        gap: 12px;
    }
    .quick-actions-row {
        display: grid;
        grid-template-columns: 1fr 160px;
        gap: 12px;
        align-items: center;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: var(--surface-alt);
    }
    .quick-actions-toggle {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .quick-actions-toggle input {
        margin-top: 4px;
    }
    .quick-actions-toggle strong {
        display: block;
        font-size: 0.98rem;
        color: var(--text);
    }
    .quick-actions-toggle small {
        display: block;
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    .quick-actions-shortcut {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .quick-actions-shortcut select {
        border-radius: 10px;
        border: 1px solid var(--border);
        padding: 8px 10px;
        background: #fff;
        color: var(--text);
    }
    .backup-card {
        border: 1px dashed var(--border);
        border-radius: 14px;
        padding: 14px 16px 6px;
        background: linear-gradient(180deg, rgba(99, 115, 129, 0.04), transparent);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .backup-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .backup-upload .upload-inline {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .backup-upload input[type="file"] {
        max-width: 340px;
    }
    .backup-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .backup-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }
    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        margin-top: 8px;
    }
    .module-grid.compact {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }
    .field-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }
    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }
    .role-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px;
        background: var(--surface);
        box-shadow: 0 4px 18px -16px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .role-card header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .role-card h3 {
        margin: 0;
        font-size: 1rem;
    }
    .role-tag {
        padding: 4px 10px;
        border-radius: 999px;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .user-list-card { margin-top: 12px; }
    .user-table { width: 100%; border-collapse: collapse; }
    .user-table th, .user-table td { text-align: left; padding: 10px; border-bottom: 1px solid var(--border); }
    .user-table th { color: var(--text-muted); font-weight: 700; font-size: 0.95rem; }
    .role-chip { background: rgba(37,99,235,0.08); color: #1f3f7f; border: 1px solid rgba(37,99,235,0.18); padding: 6px 10px; border-radius: 999px; font-weight: 700; font-size: 0.92rem; display: inline-block; }
    .actions-col {
        width: 180px;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .actions-col .inline-form { margin: 0; }
    .actions-col .btn,
    .actions-col .btn-danger,
    .actions-col .btn.ghost,
    .actions-col .btn-danger.ghost {
        width: auto;
    }
    .inline-form { display: inline-block; }
    .btn-danger.ghost { background: transparent; color: #c0392b; border: 1px solid rgba(192,57,43,0.2); padding: 8px 12px; border-radius: 10px; cursor: pointer; transition: all 0.15s ease; }
    .btn-danger.ghost:hover { background: rgba(192,57,43,0.08); }
    .table-responsive { overflow-x: auto; }
    .list-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 4px; }
    .user-table .empty { text-align: center; color: var(--text-muted); }
    .hierarchy-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }
    .performance-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--surface-alt);
    }
    .performance-toggle .switch-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .performance-toggle .field-hint {
        margin: 6px 0 0;
    }
    .pill-check {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        box-shadow: 0 4px 18px -14px rgba(0, 0, 0, 0.35);
        cursor: pointer;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
    }
    .pill-check input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .pill-check .pill {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--primary);
        box-shadow: inset 0 0 0 6px rgba(255, 255, 255, 0.85);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .pill-check input:checked + .pill {
        box-shadow: inset 0 0 0 3px rgba(255, 255, 255, 0.65);
        transform: scale(1.05);
    }
    .pill-text {
        font-weight: 600;
        color: var(--text-body);
    }
    .pill-check:hover {
        border-color: var(--primary);
        box-shadow: 0 10px 30px -18px var(--primary);
    }
    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .range-inline {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .range-box {
        flex: 1;
        min-width: 150px;
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .range-box span {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    .range-box input {
        background: #fff;
    }
    .field-hint {
        margin: 2px 0 0;
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    .upload-hint {
        margin: 12px 0 6px;
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    .upload-hint code {
        background: var(--surface-muted);
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 0.8rem;
    }
    .field label,
    .field-label {
        font-weight: 600;
        color: var(--text-muted);
    }
    .field input,
    .field select {
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 12px 14px;
        background: var(--surface-muted);
        color: var(--text);
    }
    .title-size-control {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .title-size-control input[type="number"] {
        width: 92px;
        min-width: 92px;
        text-align: center;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        font-weight: 600;
    }
    .title-size-control input[type="range"] {
        flex: 1;
        accent-color: var(--primary);
        height: 4px;
        background: linear-gradient(90deg, var(--primary) 0%, rgba(148, 163, 184, 0.35) 100%);
        border-radius: 999px;
        outline: none;
    }
    .title-offset-grid input[type="range"] {
        accent-color: var(--primary);
    }
    .form-footer {
        display: flex;
        justify-content: flex-end;
    }
    .btn-primary {
        background: var(--primary);
        border: none;
        border-radius: 999px;
        color: #fff;
        padding: 12px 22px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: var(--primary-dark);
    }
    .support-card {
        border: 1px dashed var(--border);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: var(--surface-muted);
    }
    .support-card h3 {
        margin: 0;
        font-size: 1.1rem;
        color: var(--text);
    }
    .support-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .support-card textarea {
        min-height: 72px;
        resize: vertical;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 12px 14px;
        background: var(--surface);
        color: var(--text);
    }
    .theme-mode {
        display: inline-flex;
        gap: 12px;
        padding: 8px;
        border-radius: 12px;
        background: var(--surface-muted);
        border: 1px solid var(--border);
    }
    .switch {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        font-weight: 600;
        color: var(--text);
    }
    .switch input[type="checkbox"] {
        display: none;
    }
    .switch-track {
        width: 46px;
        height: 26px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.35);
        position: relative;
        transition: background 0.2s ease;
    }
    .switch-track::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 6px 16px -10px rgba(15, 23, 42, 0.55);
        transition: transform 0.2s ease;
    }
    .theme-dark .switch-track {
        background: rgba(71, 85, 105, 0.42);
    }
    .switch input[type="checkbox"]:checked + .switch-track {
        background: var(--primary);
    }
    .switch input[type="checkbox"]:checked + .switch-track::after {
        transform: translateX(18px);
    }
    .switch-text {
        color: var(--text);
        font-weight: 600;
    }
    .mode-option {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .mode-option input {
        accent-color: var(--primary);
    }
    .palette {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    .swatch {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: var(--surface-muted);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .swatch input {
        display: none;
    }
    .swatch:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px -24px rgba(15, 23, 42, 0.6);
    }
    .swatch.active {
        border-color: var(--primary);
        box-shadow: var(--primary-glow);
    }
    .swatch-color {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--swatch);
        border: 3px solid rgba(255, 255, 255, 0.6);
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.12);
    }
    .swatch-meta {
        display: flex;
        flex-direction: column;
        gap: 2px;
        color: var(--text-muted);
        font-size: 0.92rem;
    }
    .swatch-meta strong {
        color: var(--text);
        font-size: 1rem;
        font-weight: 600;
    }
    .swatch-meta small {
        font-size: 0.78rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .custom-color {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        border: 1px dashed var(--border);
        background: var(--surface-muted);
    }
    .custom-color input[type="color"] {
        width: 48px;
        height: 48px;
        border: none;
        padding: 0;
        background: transparent;
        cursor: pointer;
    }
    .custom-color input[type="text"] {
        flex: 1;
        border-radius: 10px;
        border: 1px solid var(--border);
        padding: 10px 12px;
        background: #fff;
    }
    .logo-preview {
        border-radius: 14px;
        border: 1px solid var(--border);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.05);
    }
    .logo-preview header {
        padding: 10px 14px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.02em;
        background: rgba(148, 163, 184, 0.12);
    }
    .logo-preview .preview-body {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(255, 255, 255, 0.85);
    }
    .logo-preview.light {
        background: linear-gradient(135deg, rgba(255,255,255,0.96), rgba(226,232,240,0.84));
    }
    .logo-preview.dark {
        background: linear-gradient(135deg, rgba(15,23,42,0.92), rgba(51,65,85,0.85));
        color: #f8fafc;
    }
    .logo-preview.dark .preview-body {
        background: rgba(15, 23, 42, 0.32);
    }
    .logo-preview.compact-light {
        background: linear-gradient(135deg, rgba(248,250,252,0.96), rgba(226,232,240,0.84));
        color: #0f172a;
    }
    .logo-preview.compact-light .preview-body {
        background: rgba(255, 255, 255, 0.82);
    }
    .logo-preview.compact-dark {
        background: linear-gradient(135deg, rgba(37,47,63,0.9), rgba(15,23,42,0.86));
        color: #f8fafc;
    }
    .logo-preview.compact-dark .preview-body {
        background: rgba(15, 23, 42, 0.32);
    }
    .logo-preview img {
        max-width: 100%;
        max-height: 96px;
        object-fit: contain;
        filter: drop-shadow(0 12px 22px rgba(15, 23, 42, 0.25));
    }
    .logo-grid {
        display: grid;
        gap: 16px;
    }
    .portal-login-logo-grid {
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }
    @media (min-width: 960px) {
        .logo-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .portal-login-logo-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    .logo-field {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: linear-gradient(145deg, rgba(248, 250, 252, 0.94), rgba(226, 232, 240, 0.72));
        box-shadow: 0 22px 44px -34px rgba(15, 23, 42, 0.55);
    }
    .logo-field input[type="file"] {
        display: none;
    }
    .logo-tuners {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .move-key-label {
        font-weight: 700;
        color: var(--text-title);
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .move-key-label::before {
        content: '\279C';
        display: inline-flex;
        width: 18px;
        height: 18px;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(15, 23, 42, 0.06));
        color: var(--text-title);
        font-size: 0.85rem;
    }
    .logo-tuners input[type="range"] {
        width: 100%;
        accent-color: var(--primary);
    }
    .pos-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px 12px;
        align-items: center;
    }
    .slider-label {
        font-weight: 600;
        color: var(--text-muted);
        display: flex;
        justify-content: space-between;
        gap: 8px;
        font-size: 0.92rem;
    }
    .slider-label output {
        font-weight: 700;
        color: var(--text-title);
    }
    .upload-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 12px;
        border: 1px dashed rgba(148, 163, 184, 0.6);
        padding: 10px 12px;
        background: rgba(148, 163, 184, 0.1);
        color: var(--text);
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }
    .upload-label:hover {
        background: rgba(91, 97, 111, 0.14);
        color: var(--primary);
        border-color: rgba(91, 97, 111, 0.4);
    }
    body.theme-dark .logo-field {
        background: linear-gradient(145deg, rgba(23, 32, 50, 0.92), rgba(13, 20, 36, 0.88));
        border-color: rgba(148, 163, 184, 0.35);
        box-shadow: 0 22px 44px -30px rgba(3, 7, 18, 0.7);
    }
    body.theme-dark .logo-preview header {
        background: rgba(51, 65, 85, 0.55);
        color: #e2e8f0;
    }
    body.theme-dark .logo-preview .preview-body {
        background: rgba(15, 23, 42, 0.35);
    }
    body.theme-dark .upload-label {
        background: rgba(148, 163, 184, 0.18);
        border-color: rgba(148, 163, 184, 0.45);
        color: #e2e8f0;
    }
    body.theme-dark .upload-label:hover {
        background: rgba(148, 163, 184, 0.28);
        color: #fff;
        border-color: rgba(148, 163, 184, 0.6);
    }
</style>

<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute;visibility:hidden;">
    <defs>
        <filter id="previewLogoTintFilter" color-interpolation-filters="sRGB">
            <feColorMatrix id="previewLogoTintMatrix" type="matrix" values="<?= htmlspecialchars($previewMatrixDefault, ENT_QUOTES, 'UTF-8'); ?>"></feColorMatrix>
        </filter>
    </defs>
</svg>

<script>
    (() => {
        const storage = {
            get(key) {
                try {
                    return window.localStorage.getItem(key);
                } catch (error) {
                    return null;
                }
            },
            set(key, value) {
                try {
                    window.localStorage.setItem(key, value);
                } catch (error) {
                    // ignore storage failures (private mode, etc.)
                }
            },
            remove(key) {
                try {
                    window.localStorage.removeItem(key);
                } catch (error) {
                    // ignore removal issues
                }
            },
        };

        const paletteLabels = document.querySelectorAll('.swatch');
        const customHexInput = document.getElementById('custom-hex');
        const customNameInput = document.getElementById('custom-name');
        const colorPicker = document.getElementById('custom-hex-picker');
        const previewLogos = document.querySelectorAll('[data-preview-tint]');
        const themeRadios = document.querySelectorAll('input[name="config[THEME_MODE]"]');
        const basePath = <?= json_encode($basePath ?? ''); ?>;
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');
        const titleColorCheckbox = document.getElementById('title-colorize');
        const logoColorCheckbox = document.getElementById('logo-colorize');
        const titleSizeInput = document.getElementById('title-size-input');
        const titleSizeRange = document.getElementById('title-size-range');
        const titleOffsetX = document.getElementById('title-offset-x');
        const titleOffsetY = document.getElementById('title-offset-y');
        const titleOffsetXVal = document.getElementById('title-offset-x-val');
        const titleOffsetYVal = document.getElementById('title-offset-y-val');
        const alertaInput = document.getElementById('alerta');
        const unitsInput = document.getElementById('metric-units');
        const lowMinInput = document.getElementById('low-min');
        const lowMaxInput = document.getElementById('low-max');
        const skuDigitsInput = document.getElementById('sku-digits');
        const financeiroAlertaInput = document.getElementById('financeiro-alerta-vencimento');
        const previewAlerta = document.getElementById('preview-alerta');
        const previewUnits = document.getElementById('preview-unidades');
        const previewLow = document.getElementById('preview-low');
        const previewSku = document.getElementById('preview-sku');
        const previewFinanceiroAlerta = document.getElementById('preview-financeiro-alerta');
        let currentHex = customHexInput ? customHexInput.value : '#5B616F';
        const storedPreviewActive = storage.get('themePreviewActive') === '1';
        const storedPreviewHex = storage.get('themePreviewHex');
        const storedPreviewName = storage.get('themePreviewName');
        const storedLogoFilterPref = storage.get('themePreviewLogoFilter');
        const storedPreviewTitleOffsetX = storage.get('themePreviewTitleOffsetX');
        const storedPreviewTitleOffsetY = storage.get('themePreviewTitleOffsetY');

        const normaliseHex = (value) => {
            if (!value) {
                return null;
            }
            let hex = value.trim();
            if (!hex.startsWith('#')) {
                hex = `#${hex}`;
            }
            hex = hex.replace(/[^#0-9a-fA-F]/g, '').toUpperCase();
            if (hex.length === 4) {
                hex = `#${hex[1]}${hex[1]}${hex[2]}${hex[2]}${hex[3]}${hex[3]}`;
            }
            if (hex.length !== 7) {
                return null;
            }
            return hex;
        };

        const clampChannel = (value) => Math.max(0, Math.min(255, value));

        const shadeHex = (hex, percent) => {
            const normalised = normaliseHex(hex);
            if (!normalised) {
                return '#5B616F';
            }
            const raw = normalised.slice(1);
            const amount = percent / 100;
            const compute = (channel) => {
                if (percent >= 0) {
                    return clampChannel(channel + (255 - channel) * amount);
                }
                return clampChannel(channel + channel * amount);
            };
            const r = compute(parseInt(raw.slice(0, 2), 16));
            const g = compute(parseInt(raw.slice(2, 4), 16));
            const b = compute(parseInt(raw.slice(4, 6), 16));
            const toHex = (channel) => channel.toString(16).padStart(2, '0');
            return `#${toHex(Math.round(r))}${toHex(Math.round(g))}${toHex(Math.round(b))}`.toUpperCase();
        };

        const hexToRgbChannels = (hex) => {
            const normalised = normaliseHex(hex) || '#5B616F';
            const raw = normalised.slice(1);
            return {
                r: parseInt(raw.slice(0, 2), 16),
                g: parseInt(raw.slice(2, 4), 16),
                b: parseInt(raw.slice(4, 6), 16),
            };
        };

        const setCssVariables = (hex) => {
            const normalised = normaliseHex(hex);
            if (!normalised) {
                return;
            }
            const primary = normalised.toLowerCase();
            const darker = shadeHex(primary, -25).toLowerCase();
            const softer = shadeHex(primary, 45).toLowerCase();
            const { r, g, b } = hexToRgbChannels(primary);
            const root = document.documentElement.style;
            root.setProperty('--primary', primary);
            root.setProperty('--primary-dark', darker);
            root.setProperty('--primary-soft', softer);
            root.setProperty('--primary-rgb', `${r}, ${g}, ${b}`);
            root.setProperty('--primary-shadow', `0 18px 34px -24px rgba(${r}, ${g}, ${b}, 0.62)`);
            root.setProperty('--primary-shadow-strong', `0 22px 42px -26px rgba(${r}, ${g}, ${b}, 0.72)`);
            root.setProperty('--primary-glow', `0 0 0 3px rgba(${r}, ${g}, ${b}, 0.20)`);
        };

        const clampTitleSize = (value) => {
            const parsed = parseInt(value, 10);
            if (Number.isNaN(parsed)) {
                return 24;
            }
            if (parsed < 16) {
                return 16;
            }
            if (parsed > 64) {
                return 64;
            }
            return parsed;
        };

        currentHex = normaliseHex(currentHex) || '#5B616F';
        if (customHexInput) {
            customHexInput.value = currentHex.toUpperCase();
        }
        if (colorPicker) {
            colorPicker.value = currentHex.toLowerCase();
        }
        setCssVariables(currentHex);

        const setTitleSizePreview = (value) => {
            const clamped = clampTitleSize(value);
            const root = document.documentElement;
            root.style.setProperty('--brand-title-size', `${clamped}px`);
            storage.set('themePreviewActive', '1');
            storage.set('themePreviewTitleSize', String(clamped));
        };

        const setTitleOffsetPreview = (axis, value) => {
            const parsed = parseInt(value, 10);
            const clamped = Number.isNaN(parsed) ? 0 : Math.max(-80, Math.min(80, parsed));
            const root = document.documentElement;
            root.style.setProperty(axis === 'y' ? '--brand-title-y' : '--brand-title-x', `${clamped}px`);
            storage.set('themePreviewActive', '1');
            storage.set(axis === 'y' ? 'themePreviewTitleOffsetY' : 'themePreviewTitleOffsetX', String(clamped));
        };

        if (titleSizeInput && titleSizeRange) {
            const initial = clampTitleSize(titleSizeInput.value || titleSizeRange.value);
            titleSizeInput.value = initial;
            titleSizeRange.value = initial;
            setTitleSizePreview(initial);

            titleSizeInput.addEventListener('change', () => {
                const value = clampTitleSize(titleSizeInput.value);
                titleSizeInput.value = value;
                titleSizeRange.value = value;
                setTitleSizePreview(value);
            });

            titleSizeInput.addEventListener('input', () => {
                const value = clampTitleSize(titleSizeInput.value);
                titleSizeRange.value = value;
                setTitleSizePreview(value);
            });

            titleSizeRange.addEventListener('input', () => {
                const value = clampTitleSize(titleSizeRange.value);
                titleSizeInput.value = value;
                setTitleSizePreview(value);
            });
        }

        if (titleOffsetX && titleOffsetXVal) {
            const syncX = () => {
                titleOffsetXVal.textContent = `${titleOffsetX.value}px`;
                setTitleOffsetPreview('x', titleOffsetX.value);
            };
            titleOffsetX.addEventListener('input', syncX);
            syncX();
        }

        if (titleOffsetY && titleOffsetYVal) {
            const syncY = () => {
                titleOffsetYVal.textContent = `${titleOffsetY.value}px`;
                setTitleOffsetPreview('y', titleOffsetY.value);
            };
            titleOffsetY.addEventListener('input', syncY);
            syncY();
        }

        const toRgb = (hex) => {
            const value = (hex || '').replace(/[^0-9a-f]/gi, '');
            if (value.length === 3) {
                const [r, g, b] = value.split('');
                return {
                    r: parseInt(r + r, 16),
                    g: parseInt(g + g, 16),
                    b: parseInt(b + b, 16),
                };
            }
            if (value.length !== 6) {
                return { r: 91, g: 97, b: 111 };
            }
            return {
                r: parseInt(value.slice(0, 2), 16),
                g: parseInt(value.slice(2, 4), 16),
                b: parseInt(value.slice(4, 6), 16),
            };
        };

        const previewMatrixNode = document.getElementById('previewLogoTintMatrix');

        const getSelectedMode = () => {
            const checked = Array.from(themeRadios).find((radio) => radio.checked);
            return checked ? checked.value : 'light';
        };

        const buildPreviewMatrix = (hex) => {
            const normalised = normaliseHex(hex) || '#5B616F';
            const { r, g, b } = toRgb(normalised);
            const rNorm = r / 255;
            const gNorm = g / 255;
            const bNorm = b / 255;
            const row = (factor) => [
                (0.2126 * factor).toFixed(6),
                (0.7152 * factor).toFixed(6),
                (0.0722 * factor).toFixed(6),
                '0',
                '0',
            ];
            const rRow = row(rNorm);
            const gRow = row(gNorm);
            const bRow = row(bNorm);
            const values = [...rRow, ...gRow, ...bRow, '0', '0', '0', '1', '0'].join(' ');
            if (previewMatrixNode) {
                previewMatrixNode.setAttribute('values', values);
            }
            return values;
        };

        const updateRulePreview = () => {
            if (previewAlerta && alertaInput) {
                previewAlerta.textContent = alertaInput.value || '0';
            }
            if (previewUnits && unitsInput) {
                previewUnits.textContent = unitsInput.value || unitsInput.placeholder || '';
            }
            if (previewLow && lowMinInput && lowMaxInput) {
                previewLow.textContent = `${lowMinInput.value || '0'} a ${lowMaxInput.value || '0'}`;
            }
            if (previewSku && skuDigitsInput) {
                previewSku.textContent = `${skuDigitsInput.value || skuDigitsInput.placeholder || '3'} dígitos`;
            }
            if (previewFinanceiroAlerta && financeiroAlertaInput) {
                previewFinanceiroAlerta.textContent = financeiroAlertaInput.value || '0';
            }
        };

        const applyLogoTint = () => {
            const layoutMatrix = document.getElementById('logoTintMatrix');
            const layoutLogos = document.querySelectorAll('.logo-tintable');

            const clearFilters = () => {
                previewLogos.forEach((img) => {
                    img.style.removeProperty('filter');
                    img.style.removeProperty('-webkit-filter');
                });
                layoutLogos.forEach((img) => {
                    img.style.removeProperty('filter');
                    img.style.removeProperty('-webkit-filter');
                });
                document.body.classList.remove('logo-filter-enabled');
            };

            if (!logoColorCheckbox || !logoColorCheckbox.checked) {
                clearFilters();
                return;
            }

            const tintSource = currentHex;
            const matrixValues = buildPreviewMatrix(tintSource);

            previewLogos.forEach((img) => {
                img.style.filter = 'url(#previewLogoTintFilter)';
                img.style.webkitFilter = 'url(#previewLogoTintFilter)';
            });

            if (layoutMatrix) {
                layoutMatrix.setAttribute('values', matrixValues);
            }
            if (layoutLogos.length) {
                document.body.classList.add('logo-filter-enabled');
                layoutLogos.forEach((img) => {
                    img.style.filter = 'url(#logoTintFilter)';
                    img.style.webkitFilter = 'url(#logoTintFilter)';
                });
            }
        };

        const rulesSubButtons = document.querySelectorAll('.rules-subtab');
        const rulesSubPanels = document.querySelectorAll('.rules-subpanel');
        const activateRulesSubtab = (key) => {
            const target = key === 'financa' ? 'financa' : 'producao';
            rulesSubButtons.forEach((button) => {
                const active = button.dataset.rulesTab === target;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            rulesSubPanels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.rulesPanel === target);
            });
            localStorage.setItem('configRulesTab', target);
        };

        const serverTab = <?= json_encode($defaultTab); ?> || '';
        const storedTab = localStorage.getItem('configTab');
        const initialTab = serverTab || storedTab || 'rules';
        activateTab(initialTab);

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                activateTab(button.dataset.tab);
            });
        });

        rulesSubButtons.forEach((button) => {
            button.addEventListener('click', () => activateRulesSubtab(button.dataset.rulesTab));
        });
        activateRulesSubtab(localStorage.getItem('configRulesTab') || 'producao');

        function activateTab(tab) {
            tabButtons.forEach((btn) => {
                const isActive = btn.dataset.tab === tab;
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            tabPanels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.tabPanel === tab);
            });
            localStorage.setItem('configTab', tab);
            document.querySelectorAll('input[name="active_tab"]').forEach((input) => {
                input.value = tab;
            });
        }

        [alertaInput, unitsInput, lowMinInput, lowMaxInput, skuDigitsInput, financeiroAlertaInput].forEach((node) => {
            if (node) {
                node.addEventListener('input', updateRulePreview);
            }
        });
        updateRulePreview();

        const userSubButtons = document.querySelectorAll('.user-subtab');
        const userSubPanels = document.querySelectorAll('.user-subpanel');
        const storedUserSub = localStorage.getItem('configUserTab') || 'create';
        const activateUserSub = (key) => {
            userSubButtons.forEach((btn) => {
                const active = btn.dataset.userTab === key;
                btn.classList.toggle('active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            userSubPanels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.userPanel === key);
            });
            localStorage.setItem('configUserTab', key);
        };
        if (userSubButtons.length) {
            activateUserSub(storedUserSub);
            userSubButtons.forEach((btn) => btn.addEventListener('click', () => activateUserSub(btn.dataset.userTab)));
        }

        document.querySelectorAll('.user-delete-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                const ok = window.confirm('Deseja realmente excluir este usuário?');
                if (!ok) {
                    event.preventDefault();
                }
            });
        });

        const userForm = document.querySelector('.user-form');
        const userId = document.querySelector('#user-id');
        const nameInput = document.querySelector('#user-name');
        const emailInput = document.querySelector('#user-email');
        const docInput = document.querySelector('#user-documento');
        const roleInput = document.querySelector('#user-role');
        const passInput = document.querySelector('#user-password');
        const submitBtn = document.querySelector('#user-submit');
        const cancelBtn = document.querySelector('#user-cancel');
        const createAction = userForm ? userForm.getAttribute('data-create-action') : null;
        const updateAction = userForm ? userForm.getAttribute('data-update-action') : null;

        const resetUserForm = () => {
            if (!userForm) { return; }
            userForm.setAttribute('action', createAction || userForm.getAttribute('action'));
            if (submitBtn) { submitBtn.textContent = 'Salvar usuário'; }
            if (cancelBtn) { cancelBtn.style.display = 'none'; }
            if (userId) { userId.value = ''; }
            if (nameInput) { nameInput.value = ''; }
            if (emailInput) { emailInput.value = ''; }
            if (docInput) { docInput.value = ''; }
            if (passInput) { passInput.value = ''; }
            if (roleInput && roleInput.options.length) { roleInput.selectedIndex = 0; }
        };

        if (cancelBtn) {
            cancelBtn.addEventListener('click', resetUserForm);
        }

        document.querySelectorAll('.user-edit').forEach((button) => {
            button.addEventListener('click', () => {
                if (!userForm) { return; }
                userForm.setAttribute('action', updateAction || userForm.getAttribute('action'));
                if (submitBtn) { submitBtn.textContent = 'Atualizar usuário'; }
                if (cancelBtn) { cancelBtn.style.display = 'inline-flex'; }

                if (userId) { userId.value = button.dataset.id || ''; }
                if (nameInput) { nameInput.value = button.dataset.nome || ''; }
                if (emailInput) { emailInput.value = button.dataset.email || ''; }
                if (docInput) { docInput.value = button.dataset.documento || ''; }
                if (roleInput) { roleInput.value = button.dataset.papel || ''; }
                if (passInput) { passInput.value = ''; }
                nameInput?.focus();
            });
        });

        const resolveAsset = (path) => {
            if (!path) {
                return '';
            }
            if (/^https?:\/\//i.test(path)) {
                return path;
            }
            const base = (basePath || '').replace(/\/$/, '');
            if (path.startsWith('/')) {
                return `${base}${path}` || path;
            }
            const normalised = `${base}/${path}`;
            return normalised.replace(/\/{2,}/g, '/');
        };

        const updatePreview = (hex, name) => {
            const normalised = normaliseHex(hex) || '#5B616F';
            currentHex = normalised;
            if (customHexInput) {
                customHexInput.value = normalised.toUpperCase();
            }
            let resolvedName = name;
            if (customNameInput) {
                const fallbackName = customNameInput.value || 'Cor personalizada';
                resolvedName = name || fallbackName;
                customNameInput.value = resolvedName;
            } else {
                resolvedName = name || 'Cor personalizada';
            }
            if (colorPicker) {
                colorPicker.value = normalised.toLowerCase();
            }
            storage.set('themePreviewActive', '1');
            storage.set('themePreviewHex', normalised);
            storage.set('themePreviewName', resolvedName);
            setCssVariables(normalised);
            applyLogoTint();
        };

        paletteLabels.forEach((item) => {
            item.addEventListener('click', () => {
                paletteLabels.forEach((sw) => sw.classList.remove('active'));
                item.classList.add('active');
                const radio = item.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
                const hex = item.getAttribute('data-hex');
                const name = item.getAttribute('data-name');
                updatePreview(hex, name);
            });
        });

        if (colorPicker) {
            colorPicker.addEventListener('input', (event) => {
                paletteLabels.forEach((sw) => sw.classList.remove('active'));
                updatePreview(event.target.value, 'Cor personalizada');
            });
        }

        if (customHexInput) {
            customHexInput.addEventListener('input', (event) => {
                const value = event.target.value.startsWith('#') ? event.target.value : `#${event.target.value}`;
                if (/^#[0-9a-fA-F]{6}$/.test(value)) {
                    paletteLabels.forEach((sw) => sw.classList.remove('active'));
                    updatePreview(value, 'Cor personalizada');
                }
            });
        }

        const clampNumber = (val, min, max) => Math.min(Math.max(val, min), max);

        const logoTuning = [
            {
                size: '#logo-light-size',
                x: '#logo-light-x',
                y: '#logo-light-y',
                output: '#logo-light-size-val',
                outputX: '#logo-light-x-val',
                outputY: '#logo-light-y-val',
                preview: '#preview-light',
                min: 24,
                max: 140,
                cssSize: '--logo-light-height',
                cssX: '--logo-light-x',
                cssY: '--logo-light-y',
            },
            {
                size: '#logo-dark-size',
                x: '#logo-dark-x',
                y: '#logo-dark-y',
                output: '#logo-dark-size-val',
                outputX: '#logo-dark-x-val',
                outputY: '#logo-dark-y-val',
                preview: '#preview-dark',
                min: 24,
                max: 140,
                cssSize: '--logo-dark-height',
                cssX: '--logo-dark-x',
                cssY: '--logo-dark-y',
            },
            {
                size: '#logo-compact-light-size',
                x: '#logo-compact-light-x',
                y: '#logo-compact-light-y',
                output: '#logo-compact-light-size-val',
                outputX: '#logo-compact-light-x-val',
                outputY: '#logo-compact-light-y-val',
                preview: '#preview-compact-light',
                min: 16,
                max: 120,
                cssSize: '--logo-compact-light-height',
                cssX: '--logo-compact-light-x',
                cssY: '--logo-compact-light-y',
            },
            {
                size: '#logo-compact-dark-size',
                x: '#logo-compact-dark-x',
                y: '#logo-compact-dark-y',
                output: '#logo-compact-dark-size-val',
                outputX: '#logo-compact-dark-x-val',
                outputY: '#logo-compact-dark-y-val',
                preview: '#preview-compact-dark',
                min: 16,
                max: 120,
                cssSize: '--logo-compact-dark-height',
                cssX: '--logo-compact-dark-x',
                cssY: '--logo-compact-dark-y',
            },
            {
                size: '#portal-login-logo-light-size',
                x: '#portal-login-logo-light-x',
                y: '#portal-login-logo-light-y',
                output: '#portal-login-logo-light-size-val',
                outputX: '#portal-login-logo-light-x-val',
                outputY: '#portal-login-logo-light-y-val',
                preview: '#preview-portal-login-light',
                min: 24,
                max: 180,
                offsetMin: -120,
                offsetMax: 120,
            },
            {
                size: '#portal-login-logo-dark-size',
                x: '#portal-login-logo-dark-x',
                y: '#portal-login-logo-dark-y',
                output: '#portal-login-logo-dark-size-val',
                outputX: '#portal-login-logo-dark-x-val',
                outputY: '#portal-login-logo-dark-y-val',
                preview: '#preview-portal-login-dark',
                min: 24,
                max: 180,
                offsetMin: -120,
                offsetMax: 120,
            },
        ];

        const applyLogoSizeAndOffset = ({ size, x, y, output, outputX, outputY, preview, min, max, offsetMin = -80, offsetMax = 80, cssSize, cssX, cssY }) => {
            const sizeInput = document.querySelector(size);
            const offsetX = document.querySelector(x);
            const offsetY = document.querySelector(y);
            const outputEl = document.querySelector(output);
            const outputXEl = outputX ? document.querySelector(outputX) : null;
            const outputYEl = outputY ? document.querySelector(outputY) : null;
            const img = document.querySelector(preview);
            if (!sizeInput || !img) {
                return;
            }
            const sizeVal = clampNumber(parseInt(sizeInput.value || sizeInput.min || min, 10), min, max);
            img.style.maxHeight = `${sizeVal}px`;
            if (outputEl) {
                outputEl.textContent = `${sizeVal}px`;
            }
            const xVal = offsetX ? clampNumber(parseInt(offsetX.value || '0', 10), offsetMin, offsetMax) : 0;
            const yVal = offsetY ? clampNumber(parseInt(offsetY.value || '0', 10), offsetMin, offsetMax) : 0;
            if (outputXEl) {
                outputXEl.textContent = `${xVal}px`;
            }
            if (outputYEl) {
                outputYEl.textContent = `${yVal}px`;
            }
            img.style.transform = `translate(${xVal}px, ${yVal}px)`;

            const root = document.documentElement;
            if (cssSize) {
                root.style.setProperty(cssSize, `${sizeVal}px`);
            }
            if (cssX) {
                root.style.setProperty(cssX, `${xVal}px`);
            }
            if (cssY) {
                root.style.setProperty(cssY, `${yVal}px`);
            }
        };

        logoTuning.forEach((cfg) => {
            ['size', 'x', 'y'].forEach((key) => {
                const node = document.querySelector(cfg[key]);
                if (node) {
                    node.addEventListener('input', () => applyLogoSizeAndOffset(cfg));
                }
            });
            applyLogoSizeAndOffset(cfg);
        });

        if (logoColorCheckbox) {
            logoColorCheckbox.addEventListener('change', () => {
                storage.set('themePreviewActive', '1');
                storage.set('themePreviewLogoFilter', logoColorCheckbox.checked ? '1' : '0');
                applyLogoTint();
            });
        }

        themeRadios.forEach((radio) => {
            radio.addEventListener('change', () => {
                const mode = radio.value.toLowerCase();
                document.body.classList.toggle('theme-dark', mode === 'dark');
                applyLogoTint();
            });
        });

        const logoBindings = [
            { file: '#logo-light-file', text: '#logo-light', preview: '#preview-light' },
            { file: '#logo-dark-file', text: '#logo-dark', preview: '#preview-dark' },
            { file: '#logo-compact-light-file', text: '#logo-compact-light', preview: '#preview-compact-light' },
            { file: '#logo-compact-dark-file', text: '#logo-compact-dark', preview: '#preview-compact-dark' },
            { file: '#portal-login-logo-light-file', text: '#portal-login-logo-light', preview: '#preview-portal-login-light' },
            { file: '#portal-login-logo-dark-file', text: '#portal-login-logo-dark', preview: '#preview-portal-login-dark' },
        ];

        logoBindings.forEach(({ file, text, preview }) => {
            const fileInput = document.querySelector(file);
            const textInput = document.querySelector(text);
            const previewImg = document.querySelector(preview);

            if (fileInput && previewImg) {
                fileInput.addEventListener('change', (event) => {
                    const [fileObj] = event.target.files || [];
                    if (fileObj) {
                        const blobUrl = URL.createObjectURL(fileObj);
                        previewImg.src = blobUrl;
                        applyLogoTint();
                    }
                });
            }

            if (textInput && previewImg) {
                textInput.addEventListener('change', () => {
                    const path = textInput.value.trim();
                    if (path) {
                        previewImg.src = resolveAsset(path);
                        applyLogoTint();
                    }
                });
            }
        });

        if (storedPreviewActive) {
            const previewHex = normaliseHex(storedPreviewHex);
            if (previewHex && previewHex !== normaliseHex(currentHex)) {
                updatePreview(previewHex, storedPreviewName || undefined);
            }
            if (logoColorCheckbox && storedLogoFilterPref !== null) {
                const desiredState = storedLogoFilterPref === '1';
                if (logoColorCheckbox.checked !== desiredState) {
                    logoColorCheckbox.checked = desiredState;
                    applyLogoTint();
                }
            }
            if (storedPreviewTitleOffsetX !== null) {
                setTitleOffsetPreview('x', storedPreviewTitleOffsetX);
                if (titleOffsetX) titleOffsetX.value = storedPreviewTitleOffsetX;
            }
            if (storedPreviewTitleOffsetY !== null) {
                setTitleOffsetPreview('y', storedPreviewTitleOffsetY);
                if (titleOffsetY) titleOffsetY.value = storedPreviewTitleOffsetY;
            }
        }

        applyLogoTint();

        const appearanceTabs = document.querySelectorAll('[data-appearance-tab]');
        const appearancePanels = document.querySelectorAll('[data-appearance-panel]');
        if (appearanceTabs.length && appearancePanels.length) {
            const setAppearanceTab = (target) => {
                appearanceTabs.forEach((button) => {
                    const isActive = button.dataset.appearanceTab === target;
                    button.classList.toggle('active', isActive);
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                appearancePanels.forEach((panel) => {
                    panel.classList.toggle('active', panel.dataset.appearancePanel === target);
                });
                localStorage.setItem('configAppearanceTab', target);
            };
            appearanceTabs.forEach((button) => {
                button.addEventListener('click', () => setAppearanceTab(button.dataset.appearanceTab));
            });
            const storedAppearanceTab = localStorage.getItem('configAppearanceTab');
            setAppearanceTab(storedAppearanceTab || 'geral');
        }

        const listPageOptionsInput = document.querySelector('[data-list-page-options]');
        const listPageDefaults = document.querySelectorAll('[data-list-page-default]');
        const syncDefaultPageOption = () => {
            if (!listPageOptionsInput) {
                return;
            }
            const values = String(listPageOptionsInput.value || '')
                .split(/[,\s;]+/)
                .map((value) => parseInt(value, 10))
                .filter((value) => Number.isFinite(value) && value > 0);
            listPageDefaults.forEach((input) => {
                const value = parseInt(input.value || '0', 10);
                if (Number.isFinite(value) && value > 0) {
                    values.push(value);
                }
            });
            const unique = Array.from(new Set(values)).sort((left, right) => left - right);
            listPageOptionsInput.value = unique.join(', ');
        };
        if (listPageOptionsInput) {
            listPageDefaults.forEach((input) => input.addEventListener('change', syncDefaultPageOption));
            syncDefaultPageOption();
        }

        const quickActionsInput = document.getElementById('quick-actions-config');
        if (quickActionsInput) {
            const rows = document.querySelectorAll('[data-qa-row]');
            const syncQuickActions = () => {
                const payload = {};
                rows.forEach((row) => {
                    const key = row.dataset.qaRow;
                    const enabledInput = row.querySelector('[data-qa-enabled]');
                    const shortcutSelect = row.querySelector('[data-qa-shortcut]');
                    payload[key] = {
                        enabled: enabledInput && enabledInput.checked ? '1' : '0',
                        shortcut: shortcutSelect ? shortcutSelect.value : '',
                    };
                });
                quickActionsInput.value = JSON.stringify(payload);
            };
            rows.forEach((row) => {
                row.querySelectorAll('input, select').forEach((input) => {
                    input.addEventListener('change', syncQuickActions);
                });
            });
            const quickActionsForm = quickActionsInput.closest('form');
            if (quickActionsForm) {
                quickActionsForm.addEventListener('submit', syncQuickActions);
            }
            syncQuickActions();
        }

        const appearanceForm = document.querySelector('form[data-tab-panel="appearance"]');
        if (appearanceForm) {
            appearanceForm.addEventListener('submit', () => {
                storage.remove('themePreviewActive');
                storage.remove('themePreviewHex');
                storage.remove('themePreviewName');
                storage.remove('themePreviewLogoFilter');
                storage.remove('themePreviewTitleOffsetX');
                storage.remove('themePreviewTitleOffsetY');
            });
        }
    })();
</script>
