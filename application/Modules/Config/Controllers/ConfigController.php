<?php
namespace App\Modules\Config\Controllers;

use App\Core\Controller;
use App\Repositories\UsuariosRepository;
use App\Services\BackupService;
use App\Services\ConfigService;

class ConfigController extends Controller
{
    private ConfigService $config;
    private BackupService $backup;
    private UsuariosRepository $usuarios;

    private const DEFAULT_HEX = '#5B616F';

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        ConfigService $config,
        BackupService $backup,
        UsuariosRepository $usuarios
    ) {
        parent::__construct($container, $auth, $audit);
        $this->config = $config;
        $this->backup = $backup;
        $this->usuarios = $usuarios;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin']);
        $requestPath = trim((string)parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
        $isAuditModule = preg_match('~(^|/)auditoria$~', $requestPath) === 1;
        if ($isAuditModule) {
            $_GET['tab'] = 'audit';
        }
        $stmt = $this->config->getConnection()->query('SELECT * FROM configuracoes');
        $configs = $stmt->fetchAll();

        $roles = $this->roleLabels();
        $moduleOptions = $this->hierarchyModules();

        $rawHierarchy = $this->config->get('RBAC_MODULES', '');
        $decoded = json_decode($rawHierarchy, true);
        $hierarchy = is_array($decoded) ? $decoded : [];
        $defaultHierarchy = $this->defaultHierarchy($moduleOptions);
        foreach ($roles as $key => $_label) {
            $saved = $hierarchy[$key] ?? null;
            if (!is_array($saved)) {
                $hierarchy[$key] = $defaultHierarchy[$key];
                continue;
            }
            $hierarchy[$key] = array_values(array_intersect(array_keys($moduleOptions), array_map('strval', $saved)));
            if (!$hierarchy[$key]) {
                $hierarchy[$key] = $defaultHierarchy[$key];
            }
        }

        $defaultTab = $_GET['tab'] ?? '';
        $validTabs = ['rules', 'performance', 'backup', 'appearance', 'nota', 'users', 'audit'];
        if (!in_array($defaultTab, $validTabs, true)) {
            $defaultTab = '';
        }

        if ($this->wantsJson()) {
            $this->json($configs);
            return;
        }

        $compactFallback = $this->config->get('THEME_LOGO_COMPACT', 'img/logosmall.png') ?: 'img/logosmall.png';
        $themeDefaults = [
            'THEME_APP_TITLE' => $this->config->get('THEME_APP_TITLE', 'IndusOS Food') ?: 'IndusOS Food',
            'THEME_APP_TAGLINE' => $this->config->get('THEME_APP_TAGLINE', 'Sistema de Controle Industrial (SCI)') ?: 'Sistema de Controle Industrial (SCI)',
            'THEME_TITLE_SIZE' => $this->config->get('THEME_TITLE_SIZE', '24') ?: '24',
            'THEME_TITLE_OFFSET_X' => $this->config->get('THEME_TITLE_OFFSET_X', '0') ?: '0',
            'THEME_TITLE_OFFSET_Y' => $this->config->get('THEME_TITLE_OFFSET_Y', '0') ?: '0',
            'THEME_MODE' => strtolower($this->config->get('THEME_MODE', 'light') ?: 'light'),
            'THEME_PRIMARY_NAME' => $this->config->get('THEME_PRIMARY_NAME', 'Grafite Suave') ?: 'Grafite Suave',
            'THEME_PRIMARY_HEX' => $this->config->get('THEME_PRIMARY_HEX', '#5b616f') ?: '#5b616f',
            'THEME_LOGO_LIGHT' => $this->config->get('THEME_LOGO_LIGHT', 'uploads/branding/logodark.png') ?: 'uploads/branding/logodark.png',
            'THEME_LOGO_DARK' => $this->config->get('THEME_LOGO_DARK', 'uploads/branding/logowhite.png') ?: 'uploads/branding/logowhite.png',
            'THEME_LOGO_COMPACT_LIGHT' => $this->config->get('THEME_LOGO_COMPACT_LIGHT', $compactFallback) ?: $compactFallback,
            'THEME_LOGO_COMPACT_DARK' => $this->config->get('THEME_LOGO_COMPACT_DARK', $compactFallback) ?: $compactFallback,
            'THEME_LOGO_COMPACT' => $compactFallback,
            'THEME_LOGO_FOOTER' => $this->config->get('THEME_LOGO_FOOTER', 'uploads/branding/logo.png') ?: 'uploads/branding/logo.png',
            'THEME_LOGO_LIGHT_SIZE' => $this->config->get('THEME_LOGO_LIGHT_SIZE', '50') ?: '50',
            'THEME_LOGO_DARK_SIZE' => $this->config->get('THEME_LOGO_DARK_SIZE', '50') ?: '50',
            'THEME_LOGO_COMPACT_LIGHT_SIZE' => $this->config->get('THEME_LOGO_COMPACT_LIGHT_SIZE', '40') ?: '40',
            'THEME_LOGO_COMPACT_DARK_SIZE' => $this->config->get('THEME_LOGO_COMPACT_DARK_SIZE', '40') ?: '40',
            'THEME_LOGO_LIGHT_OFFSET_X' => $this->config->get('THEME_LOGO_LIGHT_OFFSET_X', '0') ?: '0',
            'THEME_LOGO_LIGHT_OFFSET_Y' => $this->config->get('THEME_LOGO_LIGHT_OFFSET_Y', '0') ?: '0',
            'THEME_LOGO_DARK_OFFSET_X' => $this->config->get('THEME_LOGO_DARK_OFFSET_X', '0') ?: '0',
            'THEME_LOGO_DARK_OFFSET_Y' => $this->config->get('THEME_LOGO_DARK_OFFSET_Y', '0') ?: '0',
            'THEME_LOGO_COMPACT_LIGHT_OFFSET_X' => $this->config->get('THEME_LOGO_COMPACT_LIGHT_OFFSET_X', '0') ?: '0',
            'THEME_LOGO_COMPACT_LIGHT_OFFSET_Y' => $this->config->get('THEME_LOGO_COMPACT_LIGHT_OFFSET_Y', '0') ?: '0',
            'THEME_LOGO_COMPACT_DARK_OFFSET_X' => $this->config->get('THEME_LOGO_COMPACT_DARK_OFFSET_X', '0') ?: '0',
            'THEME_LOGO_COMPACT_DARK_OFFSET_Y' => $this->config->get('THEME_LOGO_COMPACT_DARK_OFFSET_Y', '0') ?: '0',
            'THEME_TITLE_COLORIZE' => $this->config->get('THEME_TITLE_COLORIZE', '0') === '1' ? '1' : '0',
            'THEME_LOGO_FILTER' => $this->config->get('THEME_LOGO_FILTER', '0') === '1' ? '1' : '0',
        ];

        $notaDefaults = [
            'NOTE_FABRICA_NOME' => $this->config->get('NOTE_FABRICA_NOME', $themeDefaults['THEME_APP_TITLE']) ?: $themeDefaults['THEME_APP_TITLE'],
            'NOTE_FABRICA_DOC' => $this->config->get('NOTE_FABRICA_DOC', 'CNPJ 00.000.000/0000-00') ?: 'CNPJ 00.000.000/0000-00',
            'NOTE_FABRICA_ENDERECO' => $this->config->get('NOTE_FABRICA_ENDERECO', 'Rua Exemplo, 123 - Bairro - Cidade/UF') ?: 'Rua Exemplo, 123 - Bairro - Cidade/UF',
            'NOTE_FABRICA_CONTATO' => $this->config->get('NOTE_FABRICA_CONTATO', '(11) 3000-0000') ?: '(11) 3000-0000',
            'NOTE_FABRICA_EMAIL' => $this->config->get('NOTE_FABRICA_EMAIL', 'contato@indusos.com.br') ?: 'contato@indusos.com.br',
            'NOTE_FABRICA_SITE' => $this->config->get('NOTE_FABRICA_SITE', 'indusos.com.br') ?: 'indusos.com.br',
            'NOTE_LOGO' => $this->config->get('NOTE_LOGO', $compactFallback) ?: $compactFallback,
            'NOTE_PIX_ENABLED' => $this->config->get('NOTE_PIX_ENABLED', '0') === '1' ? '1' : '0',
            'NOTE_PIX_KEY' => $this->config->get('NOTE_PIX_KEY', '') ?: '',
            'NOTE_PIX_KEY_TYPE' => $this->config->get('NOTE_PIX_KEY_TYPE', 'cnpj') ?: 'cnpj',
        ];

        $portalSupport = [
            'email' => $this->config->get('PORTAL_SUPPORT_EMAIL', 'suporte@indusos.com.br') ?: 'suporte@indusos.com.br',
            'telefone' => $this->config->get('PORTAL_SUPPORT_PHONE', '(11) 3000-0000') ?: '(11) 3000-0000',
            'whatsapp' => $this->config->get('PORTAL_SUPPORT_WHATSAPP', '+55 11 99999-9999') ?: '+55 11 99999-9999',
            'horario' => $this->config->get('PORTAL_SUPPORT_HOURS', 'Segunda a sexta, 8h às 18h') ?: 'Segunda a sexta, 8h às 18h',
            'mensagem' => $this->config->get('PORTAL_SUPPORT_MESSAGE', 'Fale conosco sempre que precisar ajustar volumes, lotes ou agendar coletas.') ?: 'Fale conosco sempre que precisar ajustar volumes, lotes ou agendar coletas.',
        ];

        $quickActionOptions = [
            [
                'key' => 'clientes',
                'label' => 'Clientes',
                'description' => 'Cadastre e acompanhe contas e acessos.',
                'href' => 'clientes',
                'icon' => 'users',
                'default_shortcut' => 'F1',
            ],
            [
                'key' => 'produtos',
                'label' => 'Produtos',
                'description' => 'Atualize catálogo, BOM e fichas técnicas.',
                'href' => 'produtos',
                'icon' => 'package',
                'default_shortcut' => 'F2',
            ],
            [
                'key' => 'pedidos',
                'label' => 'Pedidos',
                'description' => 'Gerencie vendas e aprovações do portal.',
                'href' => 'pedidos',
                'icon' => 'file-text',
                'default_shortcut' => 'F3',
            ],
            [
                'key' => 'producao',
                'label' => 'Ordens de Produção',
                'description' => 'Planeje, inicie e finalize lotes de fabricação.',
                'href' => 'producao/op',
                'icon' => 'factory',
                'default_shortcut' => 'F4',
            ],
            [
                'key' => 'estoque',
                'label' => 'Estoque',
                'description' => 'Confira saldos, reservas e validade de lotes.',
                'href' => 'estoque/posicao',
                'icon' => 'boxes',
                'default_shortcut' => 'F5',
            ],
            [
                'key' => 'expedicao',
                'label' => 'Expedição',
                'description' => 'Monte picking FEFO e gere romaneios.',
                'href' => 'expedicao',
                'icon' => 'truck',
                'default_shortcut' => 'F6',
            ],
            [
                'key' => 'financeiro',
                'label' => 'Financeiro',
                'description' => 'Controle contas a pagar e receber.',
                'href' => 'financeiro/titulos',
                'icon' => 'wallet',
                'default_shortcut' => 'F7',
            ],
        ];

        $rawQuickActions = $this->config->get('DASH_QUICK_ACTIONS', '');
        $decodedQuickActions = json_decode((string)$rawQuickActions, true);
        $quickActionState = is_array($decodedQuickActions) ? $decodedQuickActions : [];
        $quickActionDefaults = [];
        foreach ($quickActionOptions as $option) {
            $key = $option['key'];
            $saved = $quickActionState[$key] ?? [];
            $quickActionDefaults[$key] = [
                'enabled' => isset($saved['enabled']) ? (string)$saved['enabled'] : '1',
                'shortcut' => isset($saved['shortcut']) ? (string)$saved['shortcut'] : $option['default_shortcut'],
            ];
        }

        $metricUnitsRaw = $this->config->get('METRIC_UNITS', 'kg, un, cx') ?: 'kg, un, cx';
        $metricUnits = array_values(array_filter(array_map('trim', explode(',', $metricUnitsRaw)), static fn($u) => $u !== ''));
        if (empty($metricUnits)) {
            $metricUnits = ['kg', 'un', 'cx'];
        }

        $skuDigitsRaw = $this->config->get('SKU_DIGITS', '3') ?: '3';
        $skuDigits = (int)$skuDigitsRaw;
        if ($skuDigits < 2) {
            $skuDigits = 2;
        } elseif ($skuDigits > 8) {
            $skuDigits = 8;
        }

        $estoqueBaixoMin = (int)($this->config->get('ESTOQUE_BAIXO_MIN', '4') ?: '4');
        $estoqueBaixoMax = (int)($this->config->get('ESTOQUE_BAIXO_MAX', '8') ?: '8');
        if ($estoqueBaixoMin < 0) {
            $estoqueBaixoMin = 0;
        }
        if ($estoqueBaixoMax < $estoqueBaixoMin) {
            $estoqueBaixoMax = $estoqueBaixoMin;
        }
        $allowNegativeStock = $this->config->get('ALLOW_NEGATIVE_STOCK', '0') === '1' ? '1' : '0';
        $financeiroAlertaVencimentoDias = (int)($this->config->get('FINANCEIRO_ALERTA_VENCIMENTO_DIAS', '7') ?: '7');
        if ($financeiroAlertaVencimentoDias < 0) {
            $financeiroAlertaVencimentoDias = 0;
        } elseif ($financeiroAlertaVencimentoDias > 365) {
            $financeiroAlertaVencimentoDias = 365;
        }
        $listPageSizeAvailable = [15, 20, 30, 40, 50, 100];
        $listPageSizeRaw = $this->config->get('LIST_PAGE_SIZE_OPTIONS', '15,20,30,40,50,100') ?: '15,20,30,40,50,100';
        $listPageSizeOptions = array_values(array_intersect($listPageSizeAvailable, array_map('intval', explode(',', $listPageSizeRaw))));
        if (!$listPageSizeOptions) {
            $listPageSizeOptions = $listPageSizeAvailable;
        }
        $listPageSizeDefault = (int)($this->config->get('LIST_PAGE_SIZE_DEFAULT', '15') ?: '15');
        if (!in_array($listPageSizeDefault, $listPageSizeOptions, true)) {
            $listPageSizeDefault = $listPageSizeOptions[0];
        }

        $backupFull = $this->config->get('BACKUP_FULL_ENABLED', '1') === '1' ? '1' : '0';
        $backupModulesRaw = $this->config->get('BACKUP_MODULES', implode(', ', array_keys($this->backupModuleOptions())));
        $backupModulesSelected = array_values(array_filter(array_map('trim', explode(',', $backupModulesRaw)), static fn($m) => $m !== ''));
        $lastBackupFile = $this->config->get('BACKUP_LAST_FILE', '');
        $backupDest = trim((string)($this->config->get('BACKUP_DEST_PATH', 'storage/backups/uploads') ?: 'storage/backups/uploads'));

        $usersList = $this->usuarios->all();

        $weekendLegacy = $this->config->get('PERFORMANCE_INCLUDE_WEEKENDS', '1') === '1' ? '1' : '0';
        $performanceIncludeSaturday = $this->config->get('PERFORMANCE_INCLUDE_SATURDAY');
        $performanceIncludeSunday = $this->config->get('PERFORMANCE_INCLUDE_SUNDAY');
        $performanceIncludeSaturday = ($performanceIncludeSaturday ?? $weekendLegacy) === '1' ? '1' : '0';
        $performanceIncludeSunday = ($performanceIncludeSunday ?? $weekendLegacy) === '1' ? '1' : '0';

        $this->render('config/index', [
            'configs' => $configs,
            'alertaPadrao' => $this->config->get('VALIDADE_ALERTA_DIAS', '15'),
            'baixaImediata' => $this->config->get('PEDIDO_BAIXA_NO_CRIAR', 'false'),
            'themeDefaults' => $themeDefaults,
            'portalSupport' => $portalSupport,
            'metricUnits' => $metricUnits,
            'metricUnitsRaw' => $metricUnitsRaw,
            'noteDefaults' => $notaDefaults,
            'skuDigits' => $skuDigits,
            'estoqueBaixoMin' => $estoqueBaixoMin,
            'estoqueBaixoMax' => $estoqueBaixoMax,
            'backupFull' => $backupFull,
            'backupModulesRaw' => $backupModulesRaw,
            'backupModulesSelected' => $backupModulesSelected,
            'backupModuleOptions' => $this->backupModuleOptions(),
            'lastBackupFile' => $lastBackupFile,
            'backupDest' => $backupDest,
            'performanceIncludeSaturday' => $performanceIncludeSaturday,
            'performanceIncludeSunday' => $performanceIncludeSunday,
            'allowNegativeStock' => $allowNegativeStock,
            'financeiroAlertaVencimentoDias' => $financeiroAlertaVencimentoDias,
            'listPageSizeAvailable' => $listPageSizeAvailable,
            'listPageSizeOptions' => $listPageSizeOptions,
            'listPageSizeDefault' => $listPageSizeDefault,
            'roleLabels' => $roles,
            'hierarchyModules' => $moduleOptions,
            'roleHierarchy' => $hierarchy,
            'usersList' => $usersList,
            'defaultTab' => $defaultTab,
            'quickActionOptions' => $quickActionOptions,
            'quickActionDefaults' => $quickActionDefaults,
        ], [
            'title' => $isAuditModule ? 'Auditoria' : 'Configurações',
            'activeMenu' => $isAuditModule ? 'auditoria' : 'config',
            'breadcrumbs' => ['Administração', $isAuditModule ? 'Auditoria' : 'Configurações'],
        ]);
    }

    public function save(): void
    {
        $this->auth->ensureRole(['admin']);
        $payload = json_decode(file_get_contents('php://input'), true);
        if (is_array($payload)) {
            $data = $payload;
        } elseif (isset($_POST['config']) && is_array($_POST['config'])) {
            $data = $_POST['config'];
        } else {
            $data = $_POST;
        }

        $runBackup = isset($_POST['run_backup']) && !$this->wantsJson();

        $errors = [];
        $currentBackupFile = $this->config->get('BACKUP_LAST_FILE', '');

        if (!$this->wantsJson()) {
            $uploadMap = [
                'THEME_LOGO_LIGHT' => 'logo_light_file',
                'THEME_LOGO_DARK' => 'logo_dark_file',
                'THEME_LOGO_COMPACT_LIGHT' => 'logo_compact_light_file',
                'THEME_LOGO_COMPACT_DARK' => 'logo_compact_dark_file',
                'THEME_LOGO_FOOTER' => 'logo_footer_file',
                'NOTE_LOGO' => 'note_logo_file',
            ];

            $uploadDir = dirname(__DIR__, 4).'/public/uploads/branding';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                $errors[] = 'Não foi possível preparar o diretório de uploads.';
            }

            foreach ($uploadMap as $configKey => $inputName) {
                if (!isset($_FILES[$inputName]) || !is_array($_FILES[$inputName])) {
                    continue;
                }

                $file = $_FILES[$inputName];
                if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                    $errors[] = sprintf('Falha ao enviar o arquivo para %s.', $configKey);
                    continue;
                }

                $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
                if (!in_array($extension, $allowed, true)) {
                    $errors[] = 'Formatos permitidos: PNG, JPG, SVG ou WEBP.';
                    continue;
                }

                if (!isset($uploadDir) || !is_dir($uploadDir)) {
                    $errors[] = 'Diretório de upload indisponível.';
                    continue;
                }

                $basename = sprintf('%s-%s-%s.%s',
                    strtolower($configKey),
                    date('YmdHis'),
                    bin2hex(random_bytes(4)),
                    $extension
                );

                $target = rtrim($uploadDir, '/').'/'.$basename;
                if (!move_uploaded_file($file['tmp_name'] ?? '', $target)) {
                    $errors[] = sprintf('Não foi possível salvar o arquivo de %s.', $configKey);
                    continue;
                }

                $relativePath = 'uploads/branding/'.$basename;
                $data[$configKey] = $relativePath;
            }

            $cleanDest = trim((string)($data['BACKUP_DEST_PATH'] ?? $this->config->get('BACKUP_DEST_PATH', 'storage/backups/uploads')));
            $cleanDest = str_replace('..', '', $cleanDest);
            $cleanDest = ltrim($cleanDest, '/');
            if ($cleanDest === '') {
                $cleanDest = 'storage/backups/uploads';
            }
            $data['BACKUP_DEST_PATH'] = $cleanDest;

            $backupUploadDir = dirname(__DIR__, 4).'/'.$cleanDest;
            if (!is_dir($backupUploadDir) && !mkdir($backupUploadDir, 0775, true) && !is_dir($backupUploadDir)) {
                $errors[] = 'Não foi possível preparar o diretório de backups.';
            }
            if (isset($_FILES['backup_file']) && is_array($_FILES['backup_file']) && ($_FILES['backup_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $original = (string)($_FILES['backup_file']['name'] ?? '');
                $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $original);
                $ext = pathinfo($safeName, PATHINFO_EXTENSION) ?: 'zip';
                $targetName = sprintf('backup_%s.%s', date('Ymd_His'), $ext);
                $target = rtrim($backupUploadDir, '/').'/'.$targetName;
                if (move_uploaded_file($_FILES['backup_file']['tmp_name'] ?? '', $target)) {
                    $data['BACKUP_LAST_FILE'] = $cleanDest.'/'.$targetName;
                } else {
                    $errors[] = 'Falha ao salvar o arquivo de backup enviado.';
                }
            }
        }

        $data['THEME_TITLE_COLORIZE'] = isset($data['THEME_TITLE_COLORIZE']) && $data['THEME_TITLE_COLORIZE'] === '1' ? '1' : '0';
        $data['THEME_LOGO_FILTER'] = isset($data['THEME_LOGO_FILTER']) && $data['THEME_LOGO_FILTER'] === '1' ? '1' : '0';

        if (isset($data['THEME_TITLE_SIZE'])) {
            $size = (int)$data['THEME_TITLE_SIZE'];
            if ($size < 16) {
                $size = 16;
            } elseif ($size > 64) {
                $size = 64;
            }
            $data['THEME_TITLE_SIZE'] = (string)$size;
        }

        $logoSizeKeys = [
            'THEME_LOGO_LIGHT_SIZE' => ['min' => 24, 'max' => 140],
            'THEME_LOGO_DARK_SIZE' => ['min' => 24, 'max' => 140],
            'THEME_LOGO_COMPACT_LIGHT_SIZE' => ['min' => 16, 'max' => 120],
            'THEME_LOGO_COMPACT_DARK_SIZE' => ['min' => 16, 'max' => 120],
        ];

        foreach ($logoSizeKeys as $key => $bounds) {
            if (!isset($data[$key])) {
                continue;
            }
            $val = (int)$data[$key];
            $val = max($bounds['min'], min($bounds['max'], $val));
            $data[$key] = (string)$val;
        }

        $logoOffsetKeys = [
            'THEME_LOGO_LIGHT_OFFSET_X',
            'THEME_LOGO_LIGHT_OFFSET_Y',
            'THEME_LOGO_DARK_OFFSET_X',
            'THEME_LOGO_DARK_OFFSET_Y',
            'THEME_LOGO_COMPACT_LIGHT_OFFSET_X',
            'THEME_LOGO_COMPACT_LIGHT_OFFSET_Y',
            'THEME_LOGO_COMPACT_DARK_OFFSET_X',
            'THEME_LOGO_COMPACT_DARK_OFFSET_Y',
        ];

        foreach ($logoOffsetKeys as $key) {
            if (!isset($data[$key])) {
                continue;
            }
            $val = (int)$data[$key];
            $val = max(-80, min(80, $val));
            $data[$key] = (string)$val;
        }

        if (isset($data['THEME_PRIMARY_HEX'])) {
            $data['THEME_PRIMARY_HEX'] = $this->normaliseHex($data['THEME_PRIMARY_HEX']);
        }

        if (isset($data['THEME_PRIMARY_NAME'])) {
            $name = trim((string)$data['THEME_PRIMARY_NAME']);
            $data['THEME_PRIMARY_NAME'] = $name !== '' ? $name : 'Cor personalizada';
        }

        if (isset($data['THEME_MODE'])) {
            $mode = strtolower(trim((string)$data['THEME_MODE']));
            $data['THEME_MODE'] = in_array($mode, ['dark', 'light'], true) ? $mode : 'light';
        }

        $logoKeys = [
            'THEME_LOGO_LIGHT',
            'THEME_LOGO_DARK',
            'THEME_LOGO_COMPACT_LIGHT',
            'THEME_LOGO_COMPACT_DARK',
            'THEME_LOGO_FOOTER',
            'NOTE_LOGO',
        ];

        foreach ($logoKeys as $logoKey) {
            if (isset($data[$logoKey])) {
                $data[$logoKey] = trim((string)$data[$logoKey]);
            }
        }

        $supportKeys = [
            'PORTAL_SUPPORT_EMAIL',
            'PORTAL_SUPPORT_PHONE',
            'PORTAL_SUPPORT_WHATSAPP',
            'PORTAL_SUPPORT_HOURS',
            'PORTAL_SUPPORT_MESSAGE',
        ];

        foreach ($supportKeys as $supportKey) {
            if (isset($data[$supportKey])) {
                $data[$supportKey] = trim((string)$data[$supportKey]);
            }
        }

        $notaKeys = [
            'NOTE_FABRICA_NOME',
            'NOTE_FABRICA_DOC',
            'NOTE_FABRICA_ENDERECO',
            'NOTE_FABRICA_CONTATO',
            'NOTE_FABRICA_EMAIL',
            'NOTE_FABRICA_SITE',
            'NOTE_PIX_KEY',
            'NOTE_PIX_KEY_TYPE',
        ];

        $data['NOTE_PIX_ENABLED'] = isset($data['NOTE_PIX_ENABLED']) && $data['NOTE_PIX_ENABLED'] === '1' ? '1' : '0';

        if (isset($data['NOTE_PIX_KEY_TYPE'])) {
            $pixType = strtolower(trim((string)$data['NOTE_PIX_KEY_TYPE']));
            $allowedTypes = ['cnpj', 'cpf', 'email', 'telefone'];
            $data['NOTE_PIX_KEY_TYPE'] = in_array($pixType, $allowedTypes, true) ? $pixType : 'cnpj';
        }

        foreach ($notaKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = trim((string)$data[$key]);
            }
        }

        if (isset($data['METRIC_UNITS'])) {
            $units = array_values(array_filter(array_map('trim', explode(',', (string)$data['METRIC_UNITS'])), static fn($u) => $u !== ''));
            $data['METRIC_UNITS'] = $units ? implode(', ', $units) : 'kg, un, cx';
        }

        if (isset($data['ESTOQUE_BAIXO_MIN']) || isset($data['ESTOQUE_BAIXO_MAX'])) {
            $lowMin = isset($data['ESTOQUE_BAIXO_MIN']) ? (int)$data['ESTOQUE_BAIXO_MIN'] : 4;
            $lowMax = isset($data['ESTOQUE_BAIXO_MAX']) ? (int)$data['ESTOQUE_BAIXO_MAX'] : 8;
            $lowMin = max(0, $lowMin);
            if ($lowMax < $lowMin) {
                $lowMax = $lowMin;
            }
            $data['ESTOQUE_BAIXO_MIN'] = (string)$lowMin;
            $data['ESTOQUE_BAIXO_MAX'] = (string)$lowMax;
        }

        if (isset($data['SKU_DIGITS'])) {
            $digits = (int)$data['SKU_DIGITS'];
            if ($digits < 2) {
                $digits = 2;
            } elseif ($digits > 8) {
                $digits = 8;
            }
            $data['SKU_DIGITS'] = (string)$digits;
        }

        if (isset($data['FINANCEIRO_ALERTA_VENCIMENTO_DIAS'])) {
            $financeDays = (int)$data['FINANCEIRO_ALERTA_VENCIMENTO_DIAS'];
            if ($financeDays < 0) {
                $financeDays = 0;
            } elseif ($financeDays > 365) {
                $financeDays = 365;
            }
            $data['FINANCEIRO_ALERTA_VENCIMENTO_DIAS'] = (string)$financeDays;
        }

        $listPageSizeAvailable = [15, 20, 30, 40, 50, 100];
        if (isset($data['LIST_PAGE_SIZE_OPTIONS']) || isset($data['LIST_PAGE_SIZE_DEFAULT'])) {
            $selectedRaw = $data['LIST_PAGE_SIZE_OPTIONS'] ?? [];
            if (!is_array($selectedRaw)) {
                $selectedRaw = explode(',', (string)$selectedRaw);
            }
            $selected = array_values(array_intersect($listPageSizeAvailable, array_map('intval', $selectedRaw)));
            $defaultPageSize = (int)($data['LIST_PAGE_SIZE_DEFAULT'] ?? 15);
            if (!in_array($defaultPageSize, $listPageSizeAvailable, true)) {
                $defaultPageSize = 15;
            }
            if (!$selected) {
                $selected = [$defaultPageSize];
            }
            if (!in_array($defaultPageSize, $selected, true)) {
                $selected[] = $defaultPageSize;
            }
            sort($selected);
            $data['LIST_PAGE_SIZE_OPTIONS'] = implode(',', $selected);
            $data['LIST_PAGE_SIZE_DEFAULT'] = (string)$defaultPageSize;
        }

        $includeSaturday = isset($data['PERFORMANCE_INCLUDE_SATURDAY']) && $data['PERFORMANCE_INCLUDE_SATURDAY'] === '1' ? '1' : '0';
        $includeSunday = isset($data['PERFORMANCE_INCLUDE_SUNDAY']) && $data['PERFORMANCE_INCLUDE_SUNDAY'] === '1' ? '1' : '0';

        $data['PERFORMANCE_INCLUDE_SATURDAY'] = $includeSaturday;
        $data['PERFORMANCE_INCLUDE_SUNDAY'] = $includeSunday;
        // Mantém a chave legada para compatibilidade com instalações antigas
        $data['PERFORMANCE_INCLUDE_WEEKENDS'] = ($includeSaturday === '1' && $includeSunday === '1') ? '1' : '0';

        $data['BACKUP_FULL_ENABLED'] = isset($data['BACKUP_FULL_ENABLED']) && $data['BACKUP_FULL_ENABLED'] === '1' ? '1' : '0';
        $backupSelection = [];
        if (isset($data['BACKUP_MODULES']) && is_array($data['BACKUP_MODULES'])) {
            $allowed = array_keys($this->backupModuleOptions());
            $selected = array_values(array_intersect($allowed, array_map('trim', $data['BACKUP_MODULES'])));
            $backupSelection = $selected;
            $data['BACKUP_MODULES'] = implode(', ', $selected);
        } else {
            $data['BACKUP_MODULES'] = '';
        }

        if (isset($data['DASH_QUICK_ACTIONS']) && is_array($data['DASH_QUICK_ACTIONS'])) {
            $allowedShortcuts = array_merge([''], array_map(static fn($num) => 'F'.$num, range(1, 12)));
            $quickActionsPayload = [];
            foreach ($data['DASH_QUICK_ACTIONS'] as $key => $state) {
                if (!is_array($state)) {
                    continue;
                }
                $enabled = isset($state['enabled']) && (string)$state['enabled'] === '1' ? '1' : '0';
                $shortcutRaw = strtoupper(trim((string)($state['shortcut'] ?? '')));
                $shortcut = in_array($shortcutRaw, $allowedShortcuts, true) ? $shortcutRaw : '';
                $quickActionsPayload[(string)$key] = [
                    'enabled' => $enabled,
                    'shortcut' => $shortcut,
                ];
            }
            $data['DASH_QUICK_ACTIONS'] = json_encode($quickActionsPayload, JSON_UNESCAPED_SLASHES);
        }

        $backupDestRaw = trim((string)($data['BACKUP_DEST_PATH'] ?? $this->config->get('BACKUP_DEST_PATH', 'storage/backups/uploads')));
        $backupDestRaw = str_replace('..', '', $backupDestRaw);
        $backupDestRaw = ltrim($backupDestRaw, '/');
        if ($backupDestRaw === '') {
            $backupDestRaw = 'storage/backups/uploads';
        }
        $data['BACKUP_DEST_PATH'] = $backupDestRaw;

        if (!isset($data['BACKUP_LAST_FILE']) || $data['BACKUP_LAST_FILE'] === '') {
            $data['BACKUP_LAST_FILE'] = $currentBackupFile;
        }

        foreach ($data as $chave => $valor) {
            $this->config->set($chave, (string)$valor);
        }

        $backupMessage = '';
        if ($runBackup && empty($errors)) {
            try {
                $result = $this->backup->run(
                    $data['BACKUP_FULL_ENABLED'] === '1',
                    $backupSelection,
                    $data['BACKUP_DEST_PATH'] ?? 'storage/backups/uploads'
                );
                $backupMessage = ' Backup salvo em '.$result['relative'];
                $this->config->set('BACKUP_LAST_FILE', $result['relative']);
            } catch (\Throwable $exception) {
                $errors[] = 'Erro ao executar backup: '.$exception->getMessage();
            }
        }

        if ($this->wantsJson()) {
            $status = $errors ? 400 : 200;
            http_response_code($status);
            $this->json([
                'message' => $errors ? implode(' ', $errors) : 'Configurações atualizadas'.$backupMessage,
            ]);
            return;
        }

        if ($errors) {
            $this->setFlash('error', implode(' ', $errors));
        } else {
            $this->setFlash('success', 'Parâmetros salvos com sucesso.'.$backupMessage);
        }

        $activeTab = (string)($_POST['active_tab'] ?? '');
        $validTabs = ['rules', 'performance', 'backup', 'appearance', 'nota', 'users'];
        if (!in_array($activeTab, $validTabs, true)) {
            $activeTab = '';
        }
        $redirect = '/config';
        if ($activeTab !== '') {
            $redirect .= '?tab='.$activeTab;
        }

        $this->redirect($redirect);
    }

    public function createUser(): void
    {
        $this->auth->ensureRole(['admin']);

        $nome = trim((string)($_POST['nome'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $documento = preg_replace('/\D+/', '', (string)($_POST['documento'] ?? ''));
        $papel = trim((string)($_POST['papel'] ?? 'gestor'));
        $senha = (string)($_POST['senha'] ?? '');

        $errors = [];
        $roles = array_keys($this->roleLabels());

        if ($nome === '') {
            $errors[] = 'Informe o nome do usuário.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }
        if (!in_array($papel, $roles, true)) {
            $errors[] = 'Selecione uma hierarquia válida.';
        }
        if (strlen($senha) < 6) {
            $errors[] = 'A senha precisa ter ao menos 6 caracteres.';
        }

        if ($email !== '' && $this->usuarios->findByEmail($email)) {
            $errors[] = 'Já existe um usuário com este e-mail.';
        }

        if ($errors) {
            if ($this->wantsJson()) {
                http_response_code(400);
                $this->json(['message' => implode(' ', $errors)]);
                return;
            }
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect('/config?tab=users');
            return;
        }

        $cpf = null;
        $cnpj = null;
        if ($documento !== '') {
            if (strlen($documento) === 11) {
                $cpf = $documento;
            } elseif (strlen($documento) === 14) {
                $cnpj = $documento;
            }
        }

        $userId = $this->usuarios->create([
            'nome' => $nome,
            'email' => $email,
            'cpf' => $cpf,
            'cnpj' => $cnpj,
            'senha_hash' => password_hash($senha, PASSWORD_ARGON2ID),
            'papel' => $papel,
            'is_ativo' => 1,
        ]);

        $this->audit->log($this->auth->id(), 'create', 'usuarios', (int)$userId, ['papel' => $papel]);

        if ($this->wantsJson()) {
            $this->json(['message' => 'Usuário criado com sucesso.', 'id' => $userId]);
            return;
        }

        $this->setFlash('success', 'Usuário cadastrado com sucesso.');
        $this->redirect('/config?tab=users');
    }

    public function updateUser(): void
    {
        $this->auth->ensureRole(['admin']);

        $id = (int)($_POST['id'] ?? 0);
        $nome = trim((string)($_POST['nome'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $documento = preg_replace('/\D+/', '', (string)($_POST['documento'] ?? ''));
        $papel = trim((string)($_POST['papel'] ?? 'gestor'));
        $senha = (string)($_POST['senha'] ?? '');

        $errors = [];
        $roles = array_keys($this->roleLabels());

        if ($id <= 0 || !($existing = $this->usuarios->find($id))) {
            $errors[] = 'Usuário inválido.';
        }
        if ($nome === '') {
            $errors[] = 'Informe o nome do usuário.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }
        if (!in_array($papel, $roles, true)) {
            $errors[] = 'Selecione uma hierarquia válida.';
        }
        if ($senha !== '' && strlen($senha) < 6) {
            $errors[] = 'A senha precisa ter ao menos 6 caracteres.';
        }

        if ($email !== '' && $this->usuarios->findByEmailExcept($email, $id)) {
            $errors[] = 'Já existe um usuário com este e-mail.';
        }

        if ($errors) {
            if ($this->wantsJson()) {
                http_response_code(400);
                $this->json(['message' => implode(' ', $errors)]);
                return;
            }
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect('/config?tab=users');
            return;
        }

        $cpf = null;
        $cnpj = null;
        if ($documento !== '') {
            if (strlen($documento) === 11) {
                $cpf = $documento;
            } elseif (strlen($documento) === 14) {
                $cnpj = $documento;
            }
        }

        $payload = [
            'nome' => $nome,
            'email' => $email,
            'cpf' => $cpf,
            'cnpj' => $cnpj,
            'papel' => $papel,
        ];

        if ($senha !== '') {
            $payload['senha_hash'] = password_hash($senha, PASSWORD_ARGON2ID);
        }

        $this->usuarios->update($id, $payload);
        $this->audit->log($this->auth->id(), 'update', 'usuarios', $id, ['papel' => $papel]);

        if ($this->wantsJson()) {
            $this->json(['message' => 'Usuário atualizado com sucesso.', 'id' => $id]);
            return;
        }

        $this->setFlash('success', 'Usuário atualizado com sucesso.');
        $this->redirect('/config?tab=users');
    }

    public function saveHierarchy(): void
    {
        $this->auth->ensureRole(['admin']);
        $roles = $this->roleLabels();
        $modules = $this->hierarchyModules();
        $defaults = $this->defaultHierarchy($modules);

        $payload = $_POST['hierarquia'] ?? [];
        $result = [];
        foreach ($roles as $key => $_label) {
            if ($key === 'admin') {
                $result[$key] = array_keys($modules);
                continue;
            }

            $selected = $payload[$key] ?? [];
            if (!is_array($selected)) {
                $selected = [];
            }

            $choices = array_values(array_intersect(array_keys($modules), array_map('strval', $selected)));
            if (!$choices && isset($defaults[$key])) {
                $choices = $defaults[$key];
            }

            $result[$key] = $choices;
        }

        $this->config->set('RBAC_MODULES', json_encode($result, JSON_UNESCAPED_SLASHES));

        if ($this->wantsJson()) {
            $this->json(['message' => 'Hierarquia atualizada.']);
            return;
        }

        $this->setFlash('success', 'Hierarquia salva com sucesso.');
        $this->redirect('/config?tab=users');
    }

    public function deleteUser(): void
    {
        $this->auth->ensureRole(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            if ($this->wantsJson()) {
                http_response_code(400);
                $this->json(['message' => 'Usuário inválido.']);
                return;
            }
            $this->setFlash('error', 'Usuário inválido.');
            $this->redirect('/config?tab=users');
            return;
        }

        $this->usuarios->deleteById($id);
        $this->audit->log($this->auth->id(), 'delete', 'usuarios', $id, []);

        if ($this->wantsJson()) {
            $this->json(['message' => 'Usuário removido.']);
            return;
        }

        $this->setFlash('success', 'Usuário removido com sucesso.');
        $this->redirect('/config?tab=users');
    }

    private function normaliseHex(?string $value): string
    {
        if (!is_string($value)) {
            return self::DEFAULT_HEX;
        }

        $hex = strtoupper(preg_replace('/[^#0-9A-F]/', '', '#'.ltrim(trim($value), '#')));

        if (strlen($hex) === 4) {
            $hex = sprintf('#%1$s%1$s%2$s%2$s%3$s%3$s', $hex[1], $hex[2], $hex[3]);
        }

        if (strlen($hex) !== 7) {
            return self::DEFAULT_HEX;
        }

        return $hex;
    }

    /**
     * Lista de módulos disponíveis para backup granular.
     *
     * @return array<string, string>
     */
    private function backupModuleOptions(): array
    {
        return [
            'auth' => 'Autenticação',
            'clientes' => 'Clientes',
            'produtos' => 'Produtos',
            'producao' => 'Produção',
            'estoque' => 'Estoque',
            'expedicao' => 'Expedição',
            'financeiro' => 'Financeiro',
            'relatorios' => 'Relatórios',
            'config' => 'Configurações',
            'portal' => 'Portal do cliente',
        ];
    }

    /**
     * Módulos exibidos na hierarquia de usuários.
     *
     * @return array<string,string>
     */
    private function hierarchyModules(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'clientes' => 'Clientes',
            'produtos' => 'Produtos',
            'pedidos' => 'Pedidos',
            'producao' => 'Produção',
            'estoque' => 'Estoque',
            'expedicao' => 'Expedição',
            'financeiro' => 'Financeiro',
            'relatorios' => 'Relatórios',
            'mapa' => 'Mapa',
            'desempenho' => 'Desempenho',
            'meta' => 'Metas',
            'receitas' => 'Receitas',
            'auditoria' => 'Auditoria',
            'config' => 'Configuração',
            'portal' => 'Portal do cliente',
        ];
    }

    /**
     * Labels de papéis para formularios.
     *
     * @return array<string,string>
     */
    private function roleLabels(): array
    {
        return [
            'admin' => 'Administrador',
            'gestor' => 'Gestor',
            'producao' => 'Produção',
            'expedicao' => 'Expedição',
            'financeiro' => 'Financeiro',
            'qualidade' => 'Qualidade',
        ];
    }

    /**
     * Hierarquia padrão por papel.
     *
     * @param array<string,string> $modules
     * @return array<string,array>
     */
    private function defaultHierarchy(array $modules): array
    {
        $all = array_keys($modules);
        return [
            'admin' => $all,
            'gestor' => ['dashboard', 'clientes', 'produtos', 'pedidos', 'receitas', 'producao', 'estoque', 'expedicao', 'financeiro', 'relatorios', 'mapa', 'desempenho', 'meta', 'config'],
            'producao' => ['dashboard', 'receitas', 'producao', 'mapa'],
            'expedicao' => ['dashboard', 'expedicao', 'pedidos', 'estoque'],
            'financeiro' => ['dashboard', 'financeiro', 'relatorios'],
            'qualidade' => ['dashboard', 'producao', 'estoque', 'mapa', 'desempenho', 'relatorios'],
        ];
    }
}
