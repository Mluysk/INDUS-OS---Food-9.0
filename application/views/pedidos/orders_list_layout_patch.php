<?php
if (isset($_GET['delete_pedido'])) {
    $pedidoIdToDelete = (int)$_GET['delete_pedido'];
    $loggedRole = strtolower((string)($user['papel'] ?? $user['role'] ?? $_SESSION['user_role'] ?? ''));

    if (!in_array($loggedRole, ['admin', 'administrator', 'gestor', 'financeiro', 'expedicao'], true)) {
        $this->setFlash('error', 'Você não tem permissão para excluir pedidos.');
        $this->redirect('/pedidos');
    }

    if ($pedidoIdToDelete <= 0) {
        $this->setFlash('error', 'Pedido inválido para exclusão.');
        $this->redirect('/pedidos');
    }

    try {
        $pedidosRepo = $this->container->get(\App\Repositories\PedidosRepository::class);
        $itensRepo = $this->container->get(\App\Repositories\PedidoItensRepository::class);
        $pedidoAtual = $pedidosRepo->find($pedidoIdToDelete);

        if (!$pedidoAtual) {
            $this->setFlash('error', 'Pedido não encontrado.');
            $this->redirect('/pedidos');
        }

        $db = $pedidosRepo->getConnection();
        $db->beginTransaction();
        $itensRepo->deleteByPedido($pedidoIdToDelete);
        $pedidosRepo->delete($pedidoIdToDelete);
        $db->commit();

        $this->audit->log((int)($user['id'] ?? 0), 'delete', 'pedidos', $pedidoIdToDelete, $pedidoAtual);
        $this->setFlash('success', 'Pedido excluído com sucesso.');
    } catch (\Throwable $exception) {
        if (isset($db) && $db instanceof \PDO && $db->inTransaction()) {
            $db->rollBack();
        }
        $this->setFlash('error', 'Não foi possível excluir o pedido agora.');
    }

    $this->redirect('/pedidos');
}

if (!function_exists('pedido_device_label')) {
    function pedido_device_label(?string $userAgent): string
    {
        $agent = strtolower(trim((string)$userAgent));
        if ($agent === '' || in_array($agent, ['não registrado', 'nao registrado', '-'], true)) {
            return 'Web';
        }
        if (in_array($agent, ['web', 'desktop'], true)) {
            return 'Web';
        }
        if (in_array($agent, ['mobile', 'celular', 'tablet'], true)) {
            return 'Mobile';
        }

        foreach (['android', 'iphone', 'ipad', 'ipod', 'mobile', 'windows phone', 'blackberry', 'opera mini', 'tablet'] as $token) {
            if (strpos($agent, $token) !== false) {
                return 'Mobile';
            }
        }

        return 'Web';
    }
}

$pedidoInfoMap = [];
try {
    $pedidoIds = array_values(array_filter(array_map(static function ($pedido) {
        return (int)($pedido['id'] ?? 0);
    }, $pedidos ?? [])));

    if ($pedidoIds) {
        $configService = $this->container->get(\App\Services\ConfigService::class);
        $auditDb = $configService->getConnection();
        $placeholders = implode(',', array_fill(0, count($pedidoIds), '?'));
        $stmt = $auditDb->prepare("SELECT al.entidade_id, al.usuario_id, al.created_at, al.ip, al.detalhes, u.nome AS usuario_nome
            FROM audit_logs al
            LEFT JOIN usuarios u ON u.id = al.usuario_id
            WHERE al.entidade = 'pedidos'
              AND al.acao = 'create'
              AND al.entidade_id IN ($placeholders)
            ORDER BY al.id DESC");
        $stmt->execute($pedidoIds);

        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [] as $log) {
            $pedidoId = (int)($log['entidade_id'] ?? 0);
            if ($pedidoId <= 0 || isset($pedidoInfoMap[$pedidoId])) {
                continue;
            }

            $detalhes = json_decode((string)($log['detalhes'] ?? ''), true);
            if (!is_array($detalhes)) {
                $detalhes = [];
            }

            $usuarioNome = trim((string)($log['usuario_nome'] ?? ''));
            if ($usuarioNome === '' && !empty($log['usuario_id'])) {
                $usuarioNome = 'Usuário #' . (int)$log['usuario_id'];
            }

            $pedidoInfoMap[$pedidoId] = [
                'usuario' => $usuarioNome !== '' ? $usuarioNome : 'Não registrado',
                'horario' => (string)($log['created_at'] ?? ''),
                'dispositivo' => pedido_device_label((string)($detalhes['dispositivo'] ?? $detalhes['user_agent'] ?? '')),
                'localidade' => (string)($detalhes['localidade'] ?? $log['ip'] ?? 'Não registrada'),
            ];
        }
    }
} catch (\Throwable $exception) {
    $pedidoInfoMap = $pedidoInfoMap ?? [];
}

foreach (($pedidos ?? []) as $pedido) {
    $pedidoId = (int)($pedido['id'] ?? 0);
    if ($pedidoId > 0 && !isset($pedidoInfoMap[$pedidoId])) {
        $pedidoInfoMap[$pedidoId] = [
            'usuario' => 'Não registrado',
            'horario' => (string)($pedido['created_at'] ?? 'Não registrado'),
            'dispositivo' => pedido_device_label($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'localidade' => 'Não registrada',
        ];
    }
}
?>

<style>
    #panel-lista-pedidos > div[style*="overflow-x"] {
        overflow-x: visible !important;
    }

    #panel-lista-pedidos .data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #panel-lista-pedidos .data-table thead th {
        padding: 15px 12px;
        background: var(--surface-muted, #f8fafc);
        color: var(--text-muted, #64748b);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #panel-lista-pedidos .data-table thead th:first-child {
        border-top-left-radius: 14px;
    }

    #panel-lista-pedidos .data-table thead th:last-child {
        border-top-right-radius: 14px;
    }

    #panel-lista-pedidos .data-table tbody td {
        padding: 15px 12px;
        border-bottom: 1px solid var(--border, #e5e7eb);
        vertical-align: middle;
        font-size: 0.92rem;
        line-height: 1.25;
    }

    #panel-lista-pedidos .data-table th:nth-child(1),
    #panel-lista-pedidos .data-table td:nth-child(1) {
        width: 48px;
        text-align: center;
    }

    #panel-lista-pedidos .data-table th:nth-child(2),
    #panel-lista-pedidos .data-table td:nth-child(2) {
        width: 76px;
    }

    #panel-lista-pedidos .data-table th:nth-child(3),
    #panel-lista-pedidos .data-table td:nth-child(3) {
        width: 160px;
        overflow-wrap: anywhere;
    }

    #panel-lista-pedidos .data-table th:nth-child(4),
    #panel-lista-pedidos .data-table td:nth-child(4) {
        width: 116px;
    }

    #panel-lista-pedidos .data-table th:nth-child(5),
    #panel-lista-pedidos .data-table td:nth-child(5) {
        width: 116px;
        white-space: nowrap;
    }

    #panel-lista-pedidos .data-table th:nth-child(6),
    #panel-lista-pedidos .data-table td:nth-child(6) {
        width: 178px;
        white-space: nowrap;
    }

    #panel-lista-pedidos .data-table th:nth-child(7),
    #panel-lista-pedidos .data-table td:nth-child(7) {
        width: 330px;
    }

    #panel-lista-pedidos .data-table .badge {
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent !important;
        color: inherit !important;
        box-shadow: none;
        font: inherit;
        text-transform: none;
        letter-spacing: 0;
    }

    #panel-lista-pedidos .pedido-status-badge {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-width: 74px;
        color: var(--text, #020617) !important;
        text-align: center;
        line-height: 1.05;
    }

    #panel-lista-pedidos .pedido-status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        color: var(--primary, #dc2626);
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #fff);
    }

    #panel-lista-pedidos .pedido-status-icon svg,
    #panel-lista-pedidos .pedido-status-icon[data-lucide] {
        width: 15px;
        height: 15px;
        stroke-width: 2.4;
    }

    #panel-lista-pedidos .pedido-status-badge[data-status="producao"] .pedido-status-icon,
    #panel-lista-pedidos .pedido-status-badge[data-status="separacao"] .pedido-status-icon,
    #panel-lista-pedidos .pedido-status-badge[data-status="expedicao"] .pedido-status-icon {
        color: #2563eb;
        background: #dbeafe;
    }

    #panel-lista-pedidos .pedido-status-badge[data-status="faturado"] .pedido-status-icon {
        color: #059669;
        background: #d1fae5;
    }

    #panel-lista-pedidos .pedido-status-badge[data-status="cancelado"] .pedido-status-icon {
        color: #dc2626;
        background: #fee2e2;
    }

    #panel-lista-pedidos .pedido-status-text {
        display: block;
        color: var(--text, #020617);
        font-size: 0.82rem;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
    }

    #panel-lista-pedidos .pedido-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        max-width: 100%;
    }

    #panel-lista-pedidos .pedido-action-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        max-width: 100%;
    }

    #panel-lista-pedidos .pedido-action-buttons form {
        display: contents;
    }

    #panel-lista-pedidos .pedido-action-buttons .button {
        min-width: 94px;
        min-height: 40px;
        padding: 0 14px;
        justify-content: center;
        border-radius: 13px;
        white-space: nowrap;
        font-size: 0.86rem;
    }

    #panel-lista-pedidos .pedido-icon-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        min-width: 40px !important;
        height: 40px;
        min-height: 40px !important;
        padding: 0 !important;
        border: 0;
        border-radius: 13px;
        background: var(--surface-muted, #f3f4f6);
        color: var(--text, #020617);
        cursor: pointer;
    }

    #panel-lista-pedidos .pedido-icon-btn svg {
        width: 18px;
        height: 18px;
        stroke-width: 2.25;
    }

    #panel-lista-pedidos .pedido-icon-btn--danger {
        color: #dc2626;
        background: #fff1f2;
    }

    #panel-lista-pedidos .pedido-icon-btn::after,
    #panel-lista-pedidos .pedido-info-btn::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 50%;
        bottom: calc(100% + 8px);
        transform: translateX(-50%) translateY(4px);
        padding: 7px 10px;
        border-radius: 8px;
        background: #111827;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.16s ease, transform 0.16s ease;
        z-index: 40;
    }

    #panel-lista-pedidos .pedido-icon-btn:hover::after,
    #panel-lista-pedidos .pedido-icon-btn:focus-visible::after,
    #panel-lista-pedidos .pedido-info-btn:hover::after,
    #panel-lista-pedidos .pedido-info-btn:focus-visible::after {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    #panel-lista-pedidos .pedido-icon-btn:hover,
    #panel-lista-pedidos .pedido-info-btn:hover {
        filter: brightness(0.98);
    }

    #panel-lista-pedidos .pedido-info-btn {
        position: relative;
        display: inline-flex;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        width: 36px;
        min-width: 36px;
        height: 36px;
        min-height: 36px;
        padding: 0;
        border: 1px solid #fca5a5;
        border-radius: 10px;
        background: #fff5f5;
        color: #ef2929;
        cursor: pointer;
        transition: background-color 0.16s ease, border-color 0.16s ease;
    }

    #panel-lista-pedidos .pedido-info-btn svg {
        width: 16px;
        height: 16px;
        stroke-width: 2;
    }

    #panel-lista-pedidos .pedido-info-btn:hover {
        border-color: #f87171;
        background: #fee2e2;
    }

    #panel-lista-pedidos .pix-inline {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
        padding-left: 2px;
        max-width: 100%;
    }

    #panel-lista-pedidos .pix-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        white-space: nowrap;
        font-size: 0.82rem;
        font-weight: 500;
    }

    #panel-lista-pedidos .pix-toggle input {
        width: 14px;
        height: 14px;
        margin: 0;
    }

    #panel-lista-pedidos .pix-key {
        width: 176px;
        min-width: 0;
        height: 28px;
        padding: 3px 7px;
        border: 1px solid var(--border, #9ca3af);
        border-radius: 0;
        background: #fff;
        font-size: 0.82rem;
    }

    .pedido-info-modal[hidden] {
        display: none !important;
    }

    .pedido-info-modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, 0.42);
        backdrop-filter: blur(3px);
    }

    .pedido-info-dialog {
        width: min(520px, 100%);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 24px;
        background: var(--surface, #fff);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }

    .pedido-info-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 22px 24px 16px;
    }

    .pedido-info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #fff);
        color: var(--primary, #dc2626);
        flex: 0 0 42px;
    }

    .pedido-info-icon svg {
        width: 20px;
        height: 20px;
    }

    .pedido-info-title {
        margin: 0;
        color: var(--text, #020617);
        font-size: 1.22rem;
        font-weight: 800;
    }

    .pedido-info-subtitle {
        margin: 2px 0 0;
        color: var(--text-muted, #64748b);
        font-size: 0.9rem;
        font-weight: 600;
    }

    .pedido-info-body {
        display: grid;
        gap: 12px;
        padding: 0 24px 22px;
    }

    .pedido-info-item {
        display: grid;
        gap: 4px;
        padding: 13px 14px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 14px;
        background: var(--surface-muted, #f8fafc);
    }

    .pedido-info-label {
        color: var(--text-muted, #64748b);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .pedido-info-value {
        color: var(--text, #020617);
        font-size: 0.94rem;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .pedido-info-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 24px 22px;
        border-top: 1px solid var(--border, #e5e7eb);
    }

    .pedido-info-audit {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted, #64748b);
        font-size: 0.88rem;
        font-weight: 700;
        flex-wrap: wrap;
    }

    .pedido-info-audit a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 9px 14px;
        border-radius: 13px;
        background: var(--primary, #2563eb);
        color: #fff;
        font-size: 0.86rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: var(--primary-shadow, 0 16px 28px -22px rgba(37, 99, 235, 0.65));
    }

    .pedido-info-close {
        min-width: 94px;
        min-height: 40px;
        border: 0;
        border-radius: 13px;
        background: var(--surface-muted, #f3f4f6);
        color: var(--text, #020617);
        font-weight: 800;
        cursor: pointer;
    }

    @media (max-width: 1180px) {
        #panel-lista-pedidos > div[style*="overflow-x"] {
            overflow-x: auto !important;
        }

        #panel-lista-pedidos .data-table {
            min-width: 1024px;
        }
    }
</style>

<script>
(function () {
    const panel = document.getElementById('panel-lista-pedidos');
    if (!panel) {
        return;
    }

    const pedidoInfo = window.pedidoInfoMap = <?= json_encode($pedidoInfoMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?> || {};
    const tbody = panel.querySelector('.data-table tbody');
    if (!tbody) {
        return;
    }

    const getId = (row) => {
        const value = row.children[0]?.textContent || '';
        const parsed = parseInt(value.replace(/\D+/g, ''), 10);
        return Number.isFinite(parsed) ? parsed : -1;
    };

    const iconButton = (button, icon, tooltip, extraClass = '') => {
        if (!button || button.dataset.iconified === '1') {
            return;
        }
        button.dataset.iconified = '1';
        button.classList.add('pedido-icon-btn');
        if (extraClass) {
            button.classList.add(extraClass);
        }
        button.setAttribute('data-tooltip', tooltip);
        button.setAttribute('aria-label', tooltip);
        button.setAttribute('title', tooltip);
        button.innerHTML = `<span data-lucide="${icon}" aria-hidden="true"></span>`;
    };

    const ensureInfoModal = () => {
        let modal = document.querySelector('.pedido-info-modal');
        if (modal) {
            return modal;
        }

        modal = document.createElement('div');
        modal.className = 'pedido-info-modal';
        modal.hidden = true;
        modal.innerHTML = `
            <div class="pedido-info-dialog" role="dialog" aria-modal="true" aria-labelledby="pedido-info-title">
                <div class="pedido-info-header">
                    <span class="pedido-info-icon"><span data-lucide="info" aria-hidden="true"></span></span>
                    <div>
                        <h3 class="pedido-info-title" id="pedido-info-title">Informações do pedido</h3>
                        <p class="pedido-info-subtitle" data-info-subtitle>Detalhes de criação</p>
                    </div>
                </div>
                <div class="pedido-info-body">
                    <div class="pedido-info-item"><span class="pedido-info-label">Usuário</span><span class="pedido-info-value" data-info="usuario"></span></div>
                    <div class="pedido-info-item"><span class="pedido-info-label">Horário</span><span class="pedido-info-value" data-info="horario"></span></div>
                    <div class="pedido-info-item"><span class="pedido-info-label">Dispositivo</span><span class="pedido-info-value" data-info="dispositivo"></span></div>
                    <div class="pedido-info-item"><span class="pedido-info-label">Localidade</span><span class="pedido-info-value" data-info="localidade"></span></div>
                </div>
                <div class="pedido-info-footer">
                    <div class="pedido-info-audit">
                        <span>Para ver mais informações:</span>
                        <a href="<?= htmlspecialchars($url('auditoria'), ENT_QUOTES, 'UTF-8'); ?>">Ver auditoria</a>
                    </div>
                    <button type="button" class="pedido-info-close">Fechar</button>
                </div>
            </div>`;
        document.body.appendChild(modal);

        const close = () => {
            modal.hidden = true;
        };
        modal.querySelector('.pedido-info-close')?.addEventListener('click', close);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                close();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                close();
            }
        });

        if (window.lucide) {
            window.lucide.createIcons({ elements: modal.querySelectorAll('[data-lucide]') });
        }

        return modal;
    };

    const formatInfoValue = (value) => {
        const text = String(value || '').trim();
        return text || 'Não registrado';
    };

    const formatDeviceValue = (value) => {
        const text = String(value || '').trim();
        const lower = text.toLowerCase();
        if (!lower || lower === 'não registrado' || lower === 'nao registrado' || lower === '-') {
            return 'Web';
        }
        if (lower === 'web' || lower === 'desktop') {
            return 'Web';
        }
        if (lower === 'mobile' || lower === 'celular' || lower === 'tablet') {
            return 'Mobile';
        }
        return /android|iphone|ipad|ipod|mobile|windows phone|blackberry|opera mini|tablet/.test(lower) ? 'Mobile' : 'Web';
    };

    const openInfoModal = (pedidoId) => {
        const modal = ensureInfoModal();
        const info = window.pedidoInfoMap?.[String(pedidoId)] || window.pedidoInfoMap?.[pedidoId] || pedidoInfo[String(pedidoId)] || pedidoInfo[pedidoId] || {};
        const subtitle = modal.querySelector('[data-info-subtitle]');
        if (subtitle) {
            subtitle.textContent = `Pedido #${pedidoId}`;
        }
        ['usuario', 'horario', 'dispositivo', 'localidade'].forEach((key) => {
            const target = modal.querySelector(`[data-info="${key}"]`);
            if (target) {
                target.textContent = key === 'dispositivo' ? formatDeviceValue(info[key]) : formatInfoValue(info[key]);
            }
        });
        modal.hidden = false;
    };
    window.openPedidoInfoModal = openInfoModal;

    const addInfoButton = (row) => {
        const group = row.querySelector('.pedido-action-buttons');
        if (!group || group.querySelector('.pedido-info-btn')) {
            return;
        }

        const pedidoId = getId(row);
        if (pedidoId <= 0) {
            return;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'pedido-info-btn';
        button.dataset.tooltip = 'Informações';
        button.setAttribute('aria-label', 'Informações');
        button.setAttribute('title', 'Informações');
        button.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 10.5v5"></path><circle cx="12" cy="7.5" r="0.75" fill="currentColor" stroke="none"></circle></svg>';
        button.addEventListener('click', () => openInfoModal(pedidoId));
        group.appendChild(button);
    };

    const addDeleteButton = (row) => {
        const group = row.querySelector('.pedido-action-buttons');
        const edit = row.querySelector('.pedido-edit-btn');
        if (!group || !edit || group.querySelector('.pedido-delete-link')) {
            return;
        }

        const pedidoId = getId(row);
        if (!pedidoId) {
            return;
        }

        const href = new URL(window.location.href);
        href.searchParams.set('delete_pedido', String(pedidoId));

        const link = document.createElement('a');
        link.href = href.toString();
        link.className = 'button pedido-icon-btn pedido-icon-btn--danger pedido-delete-link';
        link.dataset.tooltip = 'Excluir';
        link.setAttribute('aria-label', 'Excluir');
        link.setAttribute('title', 'Excluir');
        link.innerHTML = '<span data-lucide="trash-2" aria-hidden="true"></span>';
        link.addEventListener('click', (event) => {
            if (!window.confirm('Tem certeza que deseja excluir este pedido?')) {
                event.preventDefault();
            }
        });
        group.appendChild(link);
    };

    const rows = Array.from(tbody.querySelectorAll('tr')).filter((row) => row.children.length > 1);
    rows
        .sort((a, b) => getId(b) - getId(a))
        .forEach((row) => {
            tbody.appendChild(row);
            iconButton(row.querySelector('.pedido-edit-btn'), 'pencil', 'Editar');
            iconButton(row.querySelector('.nota-print-btn'), 'printer', 'Imprimir');
            addDeleteButton(row);
            addInfoButton(row);
        });

    if (window.lucide) {
        window.lucide.createIcons({ elements: panel.querySelectorAll('[data-lucide]') });
    }
})();
</script>
