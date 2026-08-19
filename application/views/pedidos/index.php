<style>
    #panel-lista-pedidos .pedido-review-hovercard { position: relative; display: inline-flex; justify-content: center; }
    #panel-lista-pedidos .pedido-review-hovercard .pedido-status-badge { cursor: help; }
    #panel-lista-pedidos .pedido-review-items { position: absolute; z-index: 120; top: calc(100% + 10px); left: 50%; display: grid; width: min(330px, 80vw); gap: 8px; padding: 14px; border: 1px solid #fecaca; border-radius: 14px; background: #fff; box-shadow: 0 18px 45px rgba(15, 23, 42, .18); color: #0f172a; text-align: left; visibility: hidden; opacity: 0; pointer-events: none; transform: translate(-50%, 5px); transition: opacity .14s ease, transform .14s ease, visibility .14s ease; }
    #panel-lista-pedidos .pedido-review-items::before { content: ''; position: absolute; left: 50%; top: -7px; width: 12px; height: 12px; border-top: 1px solid #fecaca; border-left: 1px solid #fecaca; background: #fff; transform: translateX(-50%) rotate(45deg); }
    #panel-lista-pedidos .pedido-review-items > b { color: #b91c1c; font-size: .78rem; letter-spacing: .06em; text-transform: uppercase; }
    #panel-lista-pedidos .pedido-review-items > span { display: grid; gap: 3px; padding: 9px 10px; border: 1px solid #fee2e2; border-radius: 10px; background: #fff7f7; }
    #panel-lista-pedidos .pedido-review-items strong { color: #b91c1c; }
    #panel-lista-pedidos .pedido-review-items small { color: #64748b; line-height: 1.4; }
    #panel-lista-pedidos .pedido-review-hovercard:hover .pedido-review-items,
    #panel-lista-pedidos .pedido-review-hovercard:focus-within .pedido-review-items { visibility: visible; opacity: 1; transform: translate(-50%, 0); }
    tr[data-pedido-row]:has(.pedido-status-badge[data-status="entregue"]) form[action*="/aprovar"] { display: none !important; }
    .pedido-status-badge[data-status="entregue"] .pedido-status-icon {
        display: inline-block !important;
        width: 22px !important;
        height: 22px !important;
        flex: 0 0 22px !important;
        border-radius: 999px !important;
        background-color: #d1fae5 !important;
        background-position: center !important;
        background-size: 22px auto !important;
        background-repeat: no-repeat !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 22' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cg stroke='%2315803d' stroke-width='1.8'%3E%3Cpath d='M12 4h10v11.5H12z'/%3E%3Cpath d='M22 8h4l4 4v3.5h-2M22 15.5h2'/%3E%3Ccircle cx='15' cy='17' r='1.7'/%3E%3Ccircle cx='26' cy='17' r='1.7'/%3E%3Cpath d='M12 6v7.5'/%3E%3C/g%3E%3Cg fill='%2315803d'%3E%3Crect x='-.05' y='11.85' width='8.45' height='8.45' rx='.9'/%3E%3Crect x='6.92' y='9.52' width='6.76' height='6.76' rx='.75'/%3E%3C/g%3E%3Cg stroke='%23dcfce7' stroke-width='1.1'%3E%3Cpath d='M.7 14.45h6.95M4.18 12.5v7.05'/%3E%3Cpath d='M7.55 11.6h5.5M10.3 10.05v5.45'/%3E%3C/g%3E%3C/svg%3E") !important;
        transform: scaleX(-1);
    }
    .pedido-status-badge[data-status="entregue"] .pedido-status-icon > * { display: none !important; }
</style>
<section class="panel pedidos-tabs-card">
    <div class="pedidos-tabs-header">
        <div>
            <h2>Pedidos</h2>
            <p class="panel-description">Crie novos pedidos e acompanhe os pedidos cadastrados em abas separadas.</p>
        </div>
        <div class="pedidos-tabs" role="tablist" aria-label="Pedidos">
            <button type="button" class="pedidos-tab is-active" id="tab-novo-pedido" role="tab" aria-selected="true" aria-controls="panel-novo-pedido" data-tab-target="novo-pedido">Novo pedido</button>
            <button type="button" class="pedidos-tab" id="tab-lista-pedidos" role="tab" aria-selected="false" aria-controls="panel-lista-pedidos" data-tab-target="lista-pedidos">Lista de pedidos</button>
            <button type="button" class="pedidos-tab" id="tab-saida-dia" role="tab" aria-selected="false" aria-controls="panel-saida-dia" data-tab-target="saida-dia">Saida do dia</button>
        </div>
    </div>

    <div class="pedidos-tab-panel is-active" id="panel-novo-pedido" role="tabpanel" aria-labelledby="tab-novo-pedido" data-tab-panel="novo-pedido">
        <h3>Novo pedido interno</h3>
        <p class="panel-description">Selecione um cliente cadastrado, inclua os itens e gere a nota já formatada.</p>
        <form method="post" action="<?= htmlspecialchars($url('pedidos'), ENT_QUOTES, 'UTF-8'); ?>" class="form-grid" id="pedido-form">
            <label>
                Cliente
                <select name="cliente_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach (($clientes ?? []) as $cliente): ?>
                        <option value="<?= (int)$cliente['id']; ?>"><?= htmlspecialchars($cliente['nome_fantasia'] ?? $cliente['razao_social'] ?? 'Cliente '.$cliente['id'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Observações
                <textarea name="observacoes" rows="2" placeholder="Instruções internas, datas ou referências"></textarea>
            </label>

            <div class="item-table" style="grid-column: 1 / -1;">
                <div class="item-table__header">
                    <div>
                        <strong>Itens do pedido</strong>
                        <p class="panel-description">Use os produtos já cadastrados e informe quantidade e valor unitário.</p>
                    </div>
                    <button type="button" class="button" id="add-item">Adicionar item</button>
                </div>
                <div class="item-table__grid" id="items-grid">
                    <div class="item-row item-row--head">
                        <span>Produto</span>
                        <span>Quantidade</span>
                        <span>Preço unit.</span>
                        <span>Subtotal</span>
                        <span></span>
                    </div>
                </div>
                <div class="item-total">
                    <span>Total</span>
                    <strong id="total-display">R$ 0,00</strong>
                </div>
            </div>

            <div class="form-actions" style="grid-column: 1 / -1;">
                <button class="button button-primary" type="submit">Criar pedido</button>
            </div>
        </form>
    </div>

    <div class="pedidos-tab-panel" id="panel-lista-pedidos" role="tabpanel" aria-labelledby="tab-lista-pedidos" data-tab-panel="lista-pedidos" hidden>
        <h3>Lista de pedidos</h3>
        <div class="pedido-list-toolbar">
            <label class="pedido-list-search">
                <span>Buscar pedidos</span>
                <span class="pedido-list-search-box">
                    <span data-lucide="search" aria-hidden="true"></span>
                    <input type="search" data-pedido-list-search placeholder="Buscar por loja, codigo, data ou valor">
                </span>
            </label>
            <div class="system-list-export" data-system-list-export data-export-table="#panel-lista-pedidos .pedido-list-table" data-export-name="pedidos">
                <button type="button" class="system-list-export__trigger" data-system-export-trigger aria-haspopup="true" aria-expanded="false"><span class="icon" data-lucide="download"></span><span>Exportar</span></button>
                <div class="system-list-export__menu" data-system-export-menu hidden>
                    <button type="button" class="system-list-export__option" data-system-export-format="csv"><span class="icon" data-lucide="file-text"></span><span>CSV</span></button>
                    <button type="button" class="system-list-export__option" data-system-export-format="txt"><span class="icon" data-lucide="file-text"></span><span>TXT</span></button>
                    <button type="button" class="system-list-export__option" data-system-export-format="xls"><span class="icon" data-lucide="file-spreadsheet"></span><span>XLS</span></button>
                </div>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table pedido-list-table">
                <thead>
                    <tr>
                        <th><button type="button" class="pedido-list-sort-head" data-pedido-sort-field="id" data-pedido-sort-type="number">ID <span>&#8645;</span></button></th>
                        <th><button type="button" class="pedido-list-sort-head" data-pedido-sort-field="codigo" data-pedido-sort-type="text">Código <span>&#8645;</span></button></th>
                        <th><button type="button" class="pedido-list-sort-head" data-pedido-sort-field="cliente" data-pedido-sort-type="text">Cliente <span>&#8645;</span></button></th>
                        <th>Status</th>
                        <th><button type="button" class="pedido-list-sort-head" data-pedido-sort-field="total" data-pedido-sort-type="number">Total <span>&#8645;</span></button></th>
                        <th><button type="button" class="pedido-list-sort-head" data-pedido-sort-field="created" data-pedido-sort-type="date">Criado em <span>&#8645;</span></button></th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pedidos): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <?php
                            $status = $pedido['status'] ?? 'novo';
                            $statusClass = in_array($status, ['faturado','entregue'], true) ? 'badge-success' : ($status === 'cancelado' ? 'badge-danger' : 'badge-warning');
                            $statusMeta = [
                                'aprovacao' => ['icon' => 'circle-check', 'label' => 'aprovação'],
                                'producao' => ['icon' => 'factory', 'label' => 'produção'],
                                'separacao' => ['icon' => 'package-check', 'label' => 'separação'],
                                'expedicao' => ['icon' => 'truck', 'label' => 'expedição'],
                                'faturado' => ['icon' => 'badge-check', 'label' => 'faturado'],
                                'entregue' => ['icon' => 'truck', 'label' => 'entregue'],
                                'cancelado' => ['icon' => 'circle-x', 'label' => 'cancelado'],
                            ];
                            $statusInfo = $statusMeta[$status] ?? ['icon' => 'circle-dot', 'label' => $status];
                            $statusMeta['separando'] = ['icon' => 'package-open', 'label' => 'separando'];
                            $statusMeta['separacao'] = ['icon' => 'package-check', 'label' => 'separado'];
                            $statusMeta['revisao'] = ['icon' => 'alert-triangle', 'label' => 'revisão'];
                            $statusInfo = $statusMeta[$status] ?? ['icon' => 'circle-dot', 'label' => $status];
                            $clienteLabel = $clienteMap[(int)($pedido['cliente_id'] ?? 0)]['nome_fantasia']
                                ?? ($clienteMap[(int)($pedido['cliente_id'] ?? 0)]['razao_social'] ?? ($pedido['cliente_id'] ?? '-'));
                            $pedidoCodigo = (string)($pedido['codigo'] ?? '-');
                            $pedidoCreated = (string)($pedido['created_at'] ?? '');
                            $pedidoTotal = (float)($pedido['total'] ?? 0);
                            $pedidoSearch = trim($clienteLabel.' '.$pedidoCodigo.' '.$pedidoCreated.' '.number_format($pedidoTotal, 2, ',', '.').' '.$pedidoTotal);
                            ?>
                            <tr
                                data-pedido-row
                                data-pedido-search="<?= htmlspecialchars($pedidoSearch, ENT_QUOTES, 'UTF-8'); ?>"
                                data-sort-id="<?= (int)$pedido['id']; ?>"
                                data-sort-codigo="<?= htmlspecialchars($pedidoCodigo, ENT_QUOTES, 'UTF-8'); ?>"
                                data-sort-cliente="<?= htmlspecialchars((string)$clienteLabel, ENT_QUOTES, 'UTF-8'); ?>"
                                data-sort-total="<?= htmlspecialchars((string)$pedidoTotal, ENT_QUOTES, 'UTF-8'); ?>"
                                data-sort-created="<?= htmlspecialchars($pedidoCreated, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <td><?= (int)$pedido['id']; ?></td>
                                <td><?= htmlspecialchars($pedidoCodigo, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($clienteLabel, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php $itensRevisao = $status === 'revisao' ? ($revisoesPicking[(int)$pedido['id']] ?? []) : []; ?>
                                    <div class="pedido-review-hovercard">
                                    <span class="badge pedido-status-badge <?= $statusClass; ?>" data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>"<?= $itensRevisao ? ' tabindex="0" aria-describedby="pedido-review-popup-'.((int)$pedido['id']).'"' : ''; ?>>
                                        <?php if ($status === 'revisao'): ?><span class="pedido-status-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.6 2.2 18a2 2 0 0 0 1.8 3h16a2 2 0 0 0 1.8-3L13.7 3.6a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span><?php else: ?><span class="pedido-status-icon" aria-hidden="true"><i data-lucide="<?= htmlspecialchars($statusInfo['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span><?php endif; ?>
                                        <span class="pedido-status-text"><?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </span>
                                    <?php if ($itensRevisao): ?>
                                        <div class="pedido-review-items" id="pedido-review-popup-<?= (int)$pedido['id']; ?>" role="tooltip"><b>Informações da revisão</b><?php foreach ($itensRevisao as $itemRevisao): ?><span><strong>✕ <?= htmlspecialchars((string)($itemRevisao['nome'] ?? 'Produto'), ENT_QUOTES, 'UTF-8'); ?></strong><small>Motivo: <?= ($itemRevisao['picking_motivo'] ?? '') === 'falta_produto' ? 'Falta de produto' : 'Erro interno'; ?><?php if (($itemRevisao['picking_motivo'] ?? '') === 'falta_produto'): ?> · Novo valor: <?= number_format((float)($itemRevisao['picking_novo_valor'] ?? 0), 4, ',', '.'); ?><?php endif; ?><br><?= htmlspecialchars((string)($itemRevisao['picking_observacao'] ?? 'Sem observação'), ENT_QUOTES, 'UTF-8'); ?></small></span><?php endforeach; ?></div>
                                    <?php endif; ?>
                                    </div>
                                </td>
                                <td>R$ <?= number_format($pedidoTotal, 2, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($pedidoCreated, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="action-cell">
                                    <?php $podeAprovar = !in_array($status, ['producao','separando','separacao','revisao','expedicao','faturado','entregue','cancelado'], true); ?>
                                    <div class="action-group pedido-actions">
                                        <div class="pedido-action-buttons">
                                            <?php if ($podeAprovar): ?>
                                                <form method="post" action="<?= htmlspecialchars($url('pedidos/'.((int)$pedido['id']).'/aprovar'), ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return confirm('Deseja aprovar este pedido?');">
                                                    <button type="submit" class="button button-primary">Aprovar</button>
                                                </form>
                                            <?php endif; ?>
                                            <button type="button" class="button pedido-edit-btn" data-pedido="<?= (int)$pedido['id']; ?>" data-cliente="<?= (int)($pedido['cliente_id'] ?? 0); ?>" data-observacoes="<?= htmlspecialchars($pedido['observacoes'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Editar pedido</button>
                                            <button type="button" class="button nota-print-btn" data-pedido="<?= (int)$pedido['id']; ?>">Imprimir nota</button>
                                        </div>
                                        <div class="pix-inline">
                                            <label class="pix-toggle">
                                                <input type="checkbox" class="pix-checkbox" <?= $notePixEnabled ? 'checked' : ''; ?>>
                                                <span class="pix-brand-icon" aria-hidden="true">
                                                    <img src="<?= htmlspecialchars($asset('uploads/branding/pix-logo.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="" onerror="this.closest('.pix-brand-icon').style.display='none'">
                                                </span>
                                                <span>PIX na nota</span>
                                            </label>
                                            <input type="text" class="pix-key" value="<?= htmlspecialchars($notePixKey, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Chave PIX opcional">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr data-pedido-empty>
                            <td colspan="7">Nenhum pedido cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                    <tr data-pedido-no-results hidden>
                        <td colspan="7">Nenhum pedido encontrado para a busca.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php
        $saidaDia = $saidaDia ?? ['data' => date('Y-m-d'), 'itens' => [], 'resumo' => []];
        $saidaResumo = $saidaDia['resumo'] ?? [];
        $saidaItens = $saidaDia['itens'] ?? [];
        $saidaData = $saidaDia['data'] ?? date('Y-m-d');
        $saidaPeriodo = ($saidaDia['periodo'] ?? 'dia') === 'mes' ? 'mes' : 'dia';
        $saidaMes = $saidaDia['mes'] ?? date('Y-m');
        $saidaDataLabel = $saidaPeriodo === 'mes'
            ? date('m/Y', strtotime($saidaMes.'-01'))
            : date('d/m/Y', strtotime($saidaData));
        $saidaLojasSelecionadas = array_map('intval', $saidaDia['lojas'] ?? []);
        $saidaLojasDisponiveis = $saidaLojasDisponiveis ?? [];
        $saidaComFiltroLojas = count($saidaLojasSelecionadas) > 0;
        $saidaLojasLabel = $saidaComFiltroLojas ? count($saidaLojasSelecionadas).' loja(s) selecionada(s)' : 'Todas as lojas';
    ?>
    <div class="pedidos-tab-panel" id="panel-saida-dia" role="tabpanel" aria-labelledby="tab-saida-dia" data-tab-panel="saida-dia" hidden>
        <div class="daily-output-head">
            <div>
                <h3>Gerar saida de vendas</h3>
                <p class="panel-description">Consolide por dia ou por mes todos os produtos vendidos em pedidos internos e pedidos do portal.</p>
            </div>
            <form class="daily-output-filter" method="get" action="<?= htmlspecialchars($url('pedidos'), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="daily-output-filter__top">
                    <label class="daily-output-date" for="saida-periodo">
                        Periodo
                        <select id="saida-periodo" name="saida_periodo" data-output-period-select>
                            <option value="dia" <?= $saidaPeriodo === 'dia' ? 'selected' : ''; ?>>Por dia</option>
                            <option value="mes" <?= $saidaPeriodo === 'mes' ? 'selected' : ''; ?>>Por mes</option>
                        </select>
                    </label>
                    <label class="daily-output-date" for="saida-data" data-output-day-field <?= $saidaPeriodo === 'mes' ? 'hidden' : ''; ?>>
                        Dia da saida
                        <input type="date" id="saida-data" name="saida_data" value="<?= htmlspecialchars($saidaData, ENT_QUOTES, 'UTF-8'); ?>">
                    </label>
                    <label class="daily-output-date" for="saida-mes" data-output-month-field <?= $saidaPeriodo === 'dia' ? 'hidden' : ''; ?>>
                        Mes da saida
                        <input type="month" id="saida-mes" name="saida_mes" value="<?= htmlspecialchars($saidaMes, ENT_QUOTES, 'UTF-8'); ?>">
                    </label>
                    <div class="daily-output-store-filter" data-output-store-filter>
                        <button type="button" class="button daily-output-store-trigger" data-output-store-trigger aria-expanded="false">
                            Filtrar lojas
                            <span><?= htmlspecialchars($saidaLojasLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                        </button>
                        <div class="daily-output-stores" data-output-store-popup hidden>
                            <div class="daily-output-stores__head">
                                <div>
                                    <span>Filtro de lojas</span>
                                    <strong><?= htmlspecialchars($saidaLojasLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>
                                <div class="daily-output-stores__actions">
                                    <button type="button" data-output-store-select-all>Marcar todas</button>
                                    <button type="button" data-output-store-clear>Limpar</button>
                                </div>
                            </div>
                            <div class="daily-output-store-list">
                                <?php if ($saidaLojasDisponiveis): ?>
                                <?php foreach ($saidaLojasDisponiveis as $cliente): ?>
                                    <?php
                                        $lojaId = (int)($cliente['id'] ?? 0);
                                        if ($lojaId <= 0) {
                                            continue;
                                        }
                                        $lojaNome = $cliente['nome_fantasia'] ?? $cliente['razao_social'] ?? ('Loja '.$lojaId);
                                        $lojaDoc = $cliente['cnpj'] ?? $cliente['cpf'] ?? '';
                                        $checked = in_array($lojaId, $saidaLojasSelecionadas, true);
                                    ?>
                                    <label class="daily-output-store-pill">
                                        <input type="checkbox" name="saida_lojas[]" value="<?= $lojaId; ?>" <?= $checked ? 'checked' : ''; ?>>
                                        <span>
                                            <strong><?= htmlspecialchars($lojaNome, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <?php if ($lojaDoc !== ''): ?>
                                                <small><?= htmlspecialchars($lojaDoc, ENT_QUOTES, 'UTF-8'); ?></small>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="daily-output-store-empty">Nenhuma loja com saida neste dia.</div>
                                <?php endif; ?>
                            </div>
                            <p>Sem lojas marcadas, o relatorio mostra todas as lojas com saida no periodo.</p>
                        </div>
                    </div>
                    <button type="submit" class="button button-primary">Gerar relatorio</button>
                    <button type="button" class="button" data-print-daily-output>Imprimir</button>
                    <button type="button" class="button" data-export-daily-output>
                        <span class="icon" data-lucide="file-spreadsheet"></span>
                        Exportar Excel
                    </button>
                </div>
            </form>
        </div>

        <div class="daily-output-report" data-daily-output-report>
            <div class="daily-output-report__title">
                <span>Relatorio de saida <?= $saidaPeriodo === 'mes' ? 'mensal' : 'diaria'; ?> - <?= htmlspecialchars($saidaLojasLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                <strong><?= htmlspecialchars($saidaDataLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="daily-output-summary">
                <div><span>Pedidos</span><strong><?= (int)($saidaResumo['pedidos'] ?? 0); ?></strong></div>
                <div><span>Portal</span><strong><?= (int)($saidaResumo['pedidos_portal'] ?? 0); ?></strong></div>
                <div><span>Internos</span><strong><?= (int)($saidaResumo['pedidos_internos'] ?? 0); ?></strong></div>
                <div><span>Produtos</span><strong><?= (int)($saidaResumo['produtos'] ?? 0); ?></strong></div>
                <div><span>Qtd. total</span><strong><?= number_format((float)($saidaResumo['quantidade'] ?? 0), 2, ',', '.'); ?></strong></div>
                <div><span>Valor total</span><strong>R$ <?= number_format((float)($saidaResumo['valor_total'] ?? 0), 2, ',', '.'); ?></strong></div>
                <div><span>Frete total</span><strong>R$ <?= number_format((float)($saidaResumo['frete_total'] ?? 0), 2, ',', '.'); ?></strong></div>
            </div>

            <div class="daily-output-table-wrap">
                <table class="data-table daily-output-table">
                    <thead>
                        <tr>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="sku" data-output-sort-type="text">SKU <span>⇅</span></button></th>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="produto" data-output-sort-type="text">Produto <span>⇅</span></button></th>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="pedidos" data-output-sort-type="number">Pedidos <span>⇅</span></button></th>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="quantidade" data-output-sort-type="number">Quantidade <span>⇅</span></button></th>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="valor_medio" data-output-sort-type="number">Valor medio <span>⇅</span></button></th>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="valor_total" data-output-sort-type="number">Valor total <span>⇅</span></button></th>
                            <th><button type="button" class="daily-output-sort-head" data-output-sort-field="peso_total" data-output-sort-type="number">Peso total <span>⇅</span></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($saidaItens): ?>
                            <?php foreach ($saidaItens as $item): ?>
                                <tr
                                    data-sort-sku="<?= htmlspecialchars((string)($item['sku'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-sort-produto="<?= htmlspecialchars((string)($item['nome'] ?? 'Produto'), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-sort-pedidos="<?= (int)($item['pedidos'] ?? 0); ?>"
                                    data-sort-quantidade="<?= htmlspecialchars((string)((float)($item['quantidade'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-sort-valor_medio="<?= htmlspecialchars((string)((float)($item['valor_medio'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-sort-valor_total="<?= htmlspecialchars((string)((float)($item['valor_total'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-sort-peso_total="<?= htmlspecialchars((string)((float)($item['peso_total'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"
                                >
                                    <td><?= htmlspecialchars($item['sku'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($item['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= (int)($item['pedidos'] ?? 0); ?></td>
                                    <td><?= number_format((float)($item['quantidade'] ?? 0), 2, ',', '.'); ?> <?= htmlspecialchars($item['unidade'] ?? 'un', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>R$ <?= number_format((float)($item['valor_medio'] ?? 0), 2, ',', '.'); ?></td>
                                    <td>R$ <?= number_format((float)($item['valor_total'] ?? 0), 2, ',', '.'); ?></td>
                                    <td><?= number_format((float)($item['peso_total'] ?? 0), 3, ',', '.'); ?> kg</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Nenhuma saida encontrada para este periodo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    (function() {
        const produtos = <?= json_encode($produtos ?? []); ?>;
        const grid = document.getElementById('items-grid');
        const addBtn = document.getElementById('add-item');
        const totalEl = document.getElementById('total-display');

        const formatCurrency = (value) => {
            return 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');
        };

        const computeTotal = () => {
            let total = 0;
            grid.querySelectorAll('.item-row[data-row]').forEach((row) => {
                const qty = parseFloat(row.querySelector('input[name="item_quantidade[]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name="item_preco[]"]').value) || 0;
                const subtotal = qty * price;
                row.querySelector('.item-subtotal').textContent = formatCurrency(subtotal);
                total += subtotal;
            });
            totalEl.textContent = formatCurrency(total);
        };

        const buildRow = () => {
            const row = document.createElement('div');
            row.className = 'item-row';
            row.dataset.row = '1';
            row.innerHTML = `
                <select name="item_produto[]" required>
                    <option value="">Produto...</option>
                    ${produtos.map(p => `<option value="${p.id}" data-preco="${p.preco ?? 0}">${p.nome} (${p.sku})</option>`).join('')}
                </select>
                <input type="number" min="0" step="0.01" name="item_quantidade[]" value="1">
                <input type="number" min="0" step="0.01" name="item_preco[]" value="0">
                <span class="item-subtotal">R$ 0,00</span>
                <button type="button" class="button button-ghost remove-item" aria-label="Remover">×</button>
            `;
            row.querySelector('select').addEventListener('change', (e) => {
                const preco = parseFloat(e.target.selectedOptions[0]?.dataset?.preco || '0');
                row.querySelector('input[name="item_preco[]"]').value = preco.toFixed(2);
                computeTotal();
            });
            row.querySelectorAll('input').forEach((input) => {
                input.addEventListener('input', computeTotal);
            });
            row.querySelector('.remove-item').addEventListener('click', () => {
                row.remove();
                computeTotal();
            });
            return row;
        };

        addBtn.addEventListener('click', () => {
            grid.appendChild(buildRow());
            computeTotal();
        });

        grid.appendChild(buildRow());
        computeTotal();
    })();
</script>

<?php
    $dailyReportLogoPath = $sidebarLogo['path'] ?? ($theme['logo_light'] ?? 'img/logo.png');
    $dailyReportLogoFilterEnabled = !empty($sidebarLogo['filter_enabled']);
    $dailyReportLogoSize = 53;
    $dailyReportLogoFilterMatrix = (string)($sidebarLogo['filter_matrix'] ?? '');
    $dailyReportLogoFilterSvg = '';
    if ($dailyReportLogoFilterEnabled && $dailyReportLogoFilterMatrix !== '') {
        $dailyReportLogoFilterSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute;visibility:hidden" aria-hidden="true"><defs><filter id="dailyOutputLogoTintFilter" color-interpolation-filters="sRGB"><feColorMatrix type="matrix" values="'
            . htmlspecialchars($dailyReportLogoFilterMatrix, ENT_QUOTES, 'UTF-8')
            . '"></feColorMatrix></filter></defs></svg>';
    }
?>
<script>
    (() => {
        const tabs = document.querySelectorAll('.pedidos-tab');
        const panels = document.querySelectorAll('.pedidos-tab-panel');
        const reportLogo = <?= json_encode($asset($dailyReportLogoPath), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
        const reportLogoSize = <?= (int)$dailyReportLogoSize; ?>;
        const reportLogoFilterCss = <?= json_encode($dailyReportLogoFilterEnabled ? 'url(#dailyOutputLogoTintFilter)' : 'none'); ?>;
        const reportLogoFilterSvg = <?= json_encode($dailyReportLogoFilterSvg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tabTarget;

                tabs.forEach((item) => {
                    const isActive = item === tab;
                    item.classList.toggle('is-active', isActive);
                    item.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach((panel) => {
                    const isActive = panel.dataset.tabPanel === target;
                    panel.classList.toggle('is-active', isActive);
                    panel.hidden = !isActive;
                });
            });
        });

        if (new URLSearchParams(window.location.search).has('saida_data') || new URLSearchParams(window.location.search).has('saida_mes')) {
            document.querySelector('[data-tab-target="saida-dia"]')?.click();
        }

        const periodSelect = document.querySelector('[data-output-period-select]');
        const dayField = document.querySelector('[data-output-day-field]');
        const monthField = document.querySelector('[data-output-month-field]');
        const syncPeriodFields = () => {
            const monthly = periodSelect?.value === 'mes';
            if (dayField) dayField.hidden = monthly;
            if (monthField) monthField.hidden = !monthly;
        };
        periodSelect?.addEventListener('change', syncPeriodFields);
        syncPeriodFields();

        document.querySelector('[data-export-daily-output]')?.addEventListener('click', () => {
            const table = document.querySelector('.daily-output-table');
            if (!table) return;

            const clean = (value) => String(value || '').replace(/\s+/g, ' ').trim();
            const escape = (value) => String(value ?? '').replace(/[&<>]/g, (char) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;'
            }[char]));
            const headers = Array.from(table.querySelectorAll('thead th')).map((cell) =>
                clean(cell.textContent).replace(/[â‡…⇅▲▼]+/g, '').trim()
            );
            const rows = Array.from(table.querySelectorAll('tbody tr'))
                .filter((row) => !row.querySelector('td[colspan]'))
                .map((row) => Array.from(row.children).map((cell) => clean(cell.textContent)));

            const title = clean(document.querySelector('.daily-output-report__title span')?.textContent || 'Relatorio de saida');
            const period = clean(document.querySelector('.daily-output-report__title strong')?.textContent || '');
            const summary = Array.from(document.querySelectorAll('.daily-output-summary > div')).map((item) => [
                clean(item.querySelector('span')?.textContent),
                clean(item.querySelector('strong')?.textContent)
            ]);
            const cells = (values, tag = 'td') => values.map((value) => `<${tag}>${escape(value)}</${tag}>`).join('');
            const summaryRows = summary.map((row) => `<tr>${cells(row)}</tr>`).join('');
            const bodyRows = rows.map((row) => `<tr>${cells(row)}</tr>`).join('');
            const html = `<!doctype html><html><head><meta charset="UTF-8"><style>
                body{font-family:Arial,sans-serif}table{border-collapse:collapse;margin-bottom:18px}
                th{background:#e8eef8;font-weight:700}th,td{border:1px solid #b8c2d1;padding:7px 10px}
                h2{margin-bottom:4px}p{margin-top:0;color:#475569}
            </style></head><body><h2>${escape(title)}</h2><p>${escape(period)}</p>
            <table><tbody>${summaryRows}</tbody></table>
            <table><thead><tr>${cells(headers, 'th')}</tr></thead><tbody>${bodyRows}</tbody></table></body></html>`;
            const blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const reference = periodSelect?.value === 'mes'
                ? document.getElementById('saida-mes')?.value
                : document.getElementById('saida-data')?.value;
            const link = Object.assign(document.createElement('a'), {
                href: url,
                download: `saida-vendas-${reference || new Date().toISOString().slice(0, 10)}.xls`
            });
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        });

        document.querySelector('[data-print-daily-output]')?.addEventListener('click', () => {
            const report = document.querySelector('[data-daily-output-report]');
            if (!report) {
                window.print();
                return;
            }

            const printWindow = window.open('', '_blank', 'width=1100,height=800');
            if (!printWindow) {
                window.print();
                return;
            }

            const lt = String.fromCharCode(60);
            printWindow.document.write(`${lt}!doctype html>
                ${lt}html lang="pt-BR">
                ${lt}head>
                    ${lt}meta charset="utf-8">
                    ${lt}title>Relatorio de saida${lt}/title>
                    ${lt}style>
                        @page { margin: 12mm; }
                        * { box-sizing: border-box; }
                        body {
                            margin: 0;
                            color: #0f172a;
                            background: #fff;
                            font-family: Arial, Helvetica, sans-serif;
                        }
                        .daily-output-print-brand {
                            display: flex;
                            align-items: center;
                            gap: 14px;
                            margin-bottom: 18px;
                            padding-bottom: 12px;
                            border-bottom: 1px solid #e2e8f0;
                        }
                        .daily-output-print-brand img {
                            display: block;
                            width: auto;
                            max-width: 190px;
                            height: ${reportLogoSize}px;
                            object-fit: contain;
                            filter: ${reportLogoFilterCss};
                        }
                        .daily-output-print-brand strong {
                            color: #0f172a;
                            font-size: 20px;
                        }
                        .daily-output-report {
                            display: block;
                            width: 100%;
                            border: 0;
                            padding: 0;
                            background: #fff;
                            box-shadow: none;
                        }
                        .daily-output-report__title {
                            display: flex;
                            justify-content: space-between;
                            gap: 16px;
                            align-items: flex-start;
                            margin-bottom: 14px;
                            color: #475569;
                            font-weight: 800;
                        }
                        .daily-output-report__title strong {
                            color: #0f172a;
                            font-size: 18px;
                        }
                        .daily-output-summary {
                            display: grid;
                            grid-template-columns: repeat(3, 1fr);
                            gap: 8px;
                            margin-bottom: 16px;
                        }
                        .daily-output-summary div {
                            border: 1px solid #dbe3ee;
                            border-radius: 8px;
                            padding: 8px;
                            break-inside: avoid;
                        }
                        .daily-output-summary span {
                            display: block;
                            color: #64748b;
                            font-size: 10px;
                            font-weight: 800;
                            text-transform: uppercase;
                        }
                        .daily-output-summary strong {
                            display: block;
                            margin-top: 4px;
                            color: #0f172a;
                            font-size: 14px;
                        }
                        .daily-output-table-wrap {
                            overflow: visible;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            table-layout: auto;
                            font-size: 11px;
                        }
                        thead {
                            display: table-header-group;
                        }
                        tfoot {
                            display: table-footer-group;
                        }
                        tr {
                            break-inside: avoid;
                            page-break-inside: avoid;
                        }
                        th,
                        td {
                            border-bottom: 1px solid #e2e8f0;
                            padding: 7px 6px;
                            text-align: left;
                            white-space: normal;
                            word-break: break-word;
                        }
                        th {
                            color: #475569;
                            font-size: 10px;
                            text-transform: uppercase;
                        }
                        .daily-output-sort-head {
                            border: 0;
                            padding: 0;
                            color: inherit;
                            background: transparent;
                            font: inherit;
                            text-transform: inherit;
                            letter-spacing: inherit;
                        }
                        .daily-output-sort-head span {
                            display: none;
                        }
                ${lt}/style>
                ${lt}/head>
                ${lt}body>
                    ${reportLogoFilterSvg}
                    ${lt}header class="daily-output-print-brand">
                        ${lt}img src="${reportLogo}" alt="Logo">
                        ${lt}strong>Relatório de saída${lt}/strong>
                    ${lt}/header>
                    ${report.outerHTML}
                ${lt}/body>
                ${lt}/html>`);
            printWindow.document.close();
            printWindow.focus();
            const printReport = () => {
                printWindow.print();
                setTimeout(() => printWindow.close(), 500);
            };
            if (printWindow.document.readyState === 'complete') {
                setTimeout(printReport, 100);
            } else {
                printWindow.addEventListener('load', printReport, { once: true });
            }
        });

        const normalizePedidoSearch = (value) => String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('pt-BR');
        const pedidoRows = Array.from(document.querySelectorAll('[data-pedido-row]'));
        const pedidoSearch = document.querySelector('[data-pedido-list-search]');
        const pedidoNoResults = document.querySelector('[data-pedido-no-results]');
        const updatePedidoSearch = () => {
            const term = normalizePedidoSearch(pedidoSearch?.value || '');
            let visible = 0;
            pedidoRows.forEach((row) => {
                const match = !term || normalizePedidoSearch(row.dataset.pedidoSearch || '').includes(term);
                row.hidden = !match;
                if (match) {
                    visible += 1;
                }
            });
            if (pedidoNoResults) {
                pedidoNoResults.hidden = visible > 0 || pedidoRows.length === 0;
            }
        };
        pedidoSearch?.addEventListener('input', updatePedidoSearch);
        updatePedidoSearch();

        const pedidoSortState = { field: '', direction: '' };
        const pedidoSortButtons = document.querySelectorAll('[data-pedido-sort-field]');
        const pedidoTableBody = document.querySelector('.pedido-list-table tbody');
        const updatePedidoSortButtons = () => {
            pedidoSortButtons.forEach((button) => {
                const isActive = pedidoSortState.field === button.dataset.pedidoSortField && pedidoSortState.direction;
                const indicator = button.querySelector('span');
                button.dataset.direction = isActive ? pedidoSortState.direction : '';
                button.setAttribute('aria-sort', isActive ? (pedidoSortState.direction === 'asc' ? 'ascending' : 'descending') : 'none');
                if (indicator) {
                    indicator.textContent = isActive ? (pedidoSortState.direction === 'asc' ? '▲' : '▼') : '⇅';
                    indicator.classList.toggle('active', Boolean(isActive));
                }
            });
        };
        const pedidoSortValue = (row, field, type) => {
            const value = row.getAttribute(`data-sort-${field}`) || '';
            if (type === 'number') {
                return Number.parseFloat(value) || 0;
            }
            if (type === 'date') {
                const parsed = Date.parse(value.replace(' ', 'T'));
                return Number.isNaN(parsed) ? 0 : parsed;
            }
            return normalizePedidoSearch(value);
        };
        pedidoSortButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (!pedidoTableBody) {
                    return;
                }
                const field = button.dataset.pedidoSortField || '';
                const type = button.dataset.pedidoSortType || 'text';
                if (pedidoSortState.field !== field || !pedidoSortState.direction) {
                    pedidoSortState.field = field;
                    pedidoSortState.direction = 'asc';
                } else if (pedidoSortState.direction === 'asc') {
                    pedidoSortState.direction = 'desc';
                } else {
                    pedidoSortState.field = '';
                    pedidoSortState.direction = '';
                }

                const rows = Array.from(pedidoTableBody.querySelectorAll('tr[data-pedido-row]'));
                const orderedRows = pedidoSortState.direction
                    ? [...rows].sort((left, right) => {
                        const leftValue = pedidoSortValue(left, field, type);
                        const rightValue = pedidoSortValue(right, field, type);
                        const result = type === 'number' || type === 'date'
                            ? leftValue - rightValue
                            : String(leftValue).localeCompare(String(rightValue), 'pt-BR', { numeric: true, sensitivity: 'base' });
                        return pedidoSortState.direction === 'desc' ? -result : result;
                    })
                    : [...rows].sort((left, right) => {
                        const createdResult = pedidoSortValue(right, 'created', 'date') - pedidoSortValue(left, 'created', 'date');
                        return createdResult || pedidoSortValue(right, 'id', 'number') - pedidoSortValue(left, 'id', 'number');
                    });
                orderedRows.forEach((row) => pedidoTableBody.appendChild(row));
                if (pedidoNoResults) {
                    pedidoTableBody.appendChild(pedidoNoResults);
                }
                updatePedidoSortButtons();
                updatePedidoSearch();
            });
        });
        updatePedidoSortButtons();

        const outputSortState = { field: '', direction: '' };
        const outputSortButtons = document.querySelectorAll('[data-output-sort-field]');
        const outputTableBody = document.querySelector('.daily-output-table tbody');
        const updateOutputSortButtons = () => {
            outputSortButtons.forEach((button) => {
                const isActive = outputSortState.field === button.dataset.outputSortField && outputSortState.direction;
                const indicator = button.querySelector('span');
                button.dataset.direction = isActive ? outputSortState.direction : '';
                button.setAttribute('aria-sort', isActive ? (outputSortState.direction === 'asc' ? 'ascending' : 'descending') : 'none');
                if (indicator) {
                    indicator.textContent = isActive ? (outputSortState.direction === 'asc' ? '▲' : '▼') : '⇅';
                    indicator.classList.toggle('active', Boolean(isActive));
                }
            });
        };
        const outputSortValue = (row, field, type) => {
            const value = row.getAttribute(`data-sort-${field}`) || '';
            if (type === 'number') {
                return Number.parseFloat(value) || 0;
            }
            return value.toLocaleLowerCase('pt-BR');
        };
        outputSortButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (!outputTableBody) {
                    return;
                }
                const field = button.dataset.outputSortField || '';
                const type = button.dataset.outputSortType || 'text';
                if (outputSortState.field !== field || !outputSortState.direction) {
                    outputSortState.field = field;
                    outputSortState.direction = 'asc';
                } else if (outputSortState.direction === 'asc') {
                    outputSortState.direction = 'desc';
                } else {
                    outputSortState.field = '';
                    outputSortState.direction = '';
                }

                const rows = Array.from(outputTableBody.querySelectorAll('tr[data-sort-sku]'));
                const orderedRows = outputSortState.direction
                    ? [...rows].sort((left, right) => {
                        const leftValue = outputSortValue(left, field, type);
                        const rightValue = outputSortValue(right, field, type);
                        const result = type === 'number'
                            ? leftValue - rightValue
                            : String(leftValue).localeCompare(String(rightValue), 'pt-BR', { numeric: true, sensitivity: 'base' });
                        return outputSortState.direction === 'desc' ? -result : result;
                    })
                    : [...rows].sort((left, right) => {
                        const productResult = String(outputSortValue(left, 'produto', 'text'))
                            .localeCompare(String(outputSortValue(right, 'produto', 'text')), 'pt-BR', { numeric: true, sensitivity: 'base' });
                        return productResult || String(outputSortValue(left, 'sku', 'text'))
                            .localeCompare(String(outputSortValue(right, 'sku', 'text')), 'pt-BR', { numeric: true, sensitivity: 'base' });
                    });
                orderedRows.forEach((row) => outputTableBody.appendChild(row));
                updateOutputSortButtons();
            });
        });
        updateOutputSortButtons();

        const storeChecks = document.querySelectorAll('.daily-output-store-pill input[type="checkbox"]');
        const storeFilter = document.querySelector('[data-output-store-filter]');
        const storeTrigger = document.querySelector('[data-output-store-trigger]');
        const storePopup = document.querySelector('[data-output-store-popup]');
        const closeStorePopup = () => {
            if (!storePopup || !storeTrigger) {
                return;
            }
            storePopup.hidden = true;
            storeTrigger.setAttribute('aria-expanded', 'false');
        };
        storeTrigger?.addEventListener('click', () => {
            if (!storePopup) {
                return;
            }
            const isOpen = !storePopup.hidden;
            storePopup.hidden = isOpen;
            storeTrigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });
        document.addEventListener('click', (event) => {
            if (storeFilter && !storeFilter.contains(event.target)) {
                closeStorePopup();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeStorePopup();
            }
        });
        document.querySelector('[data-output-store-select-all]')?.addEventListener('click', () => {
            storeChecks.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });
        document.querySelector('[data-output-store-clear]')?.addEventListener('click', () => {
            storeChecks.forEach((checkbox) => {
                checkbox.checked = false;
            });
        });

        document.querySelectorAll('.pedido-edit-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const novoPedidoTab = document.querySelector('[data-tab-target="novo-pedido"]');
                const clienteSelect = document.querySelector('#pedido-form select[name="cliente_id"]');
                const observacoes = document.querySelector('#pedido-form textarea[name="observacoes"]');

                novoPedidoTab?.click();
                if (clienteSelect && button.dataset.cliente) {
                    clienteSelect.value = button.dataset.cliente;
                }
                if (observacoes) {
                    observacoes.value = button.dataset.observacoes || '';
                }
                document.getElementById('pedido-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    })();
</script>

<script>
    (() => {
        const baseNota = <?= json_encode($url('pedidos'), JSON_UNESCAPED_SLASHES); ?>;
        const defaultPixEnabled = <?= $notePixEnabled ? 'true' : 'false'; ?>;
        const defaultPixKey = <?= json_encode($notePixKey ?? '', JSON_UNESCAPED_SLASHES); ?>;

        document.querySelectorAll('.nota-print-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const row = btn.closest('tr');
                const checkbox = row.querySelector('.pix-checkbox');
                const keyInput = row.querySelector('.pix-key');
                const pedidoId = btn.dataset.pedido;

                const enabled = checkbox ? checkbox.checked : defaultPixEnabled;
                const key = keyInput ? keyInput.value.trim() : defaultPixKey;

                const params = new URLSearchParams();
                params.set('pix', enabled ? '1' : '0');
                if (enabled && key) {
                    params.set('pix_key', key);
                }

                const finalUrl = `${baseNota.replace(/\/$/, '')}/${pedidoId}/nota${params.toString() ? '?' + params.toString() : ''}`;
                window.open(finalUrl, '_blank');
            });
        });
    })();
</script>

<style>
    .pedidos-tabs-card {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .pedidos-tabs-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .pedidos-tabs-header h2,
    .pedidos-tab-panel h3 {
        margin-top: 0;
    }

    .pedidos-tabs {
        display: inline-flex;
        gap: 0.35rem;
        padding: 0.3rem;
        border-radius: 999px;
        background: var(--surface-muted, #f3f4f6);
    }

    .pedidos-tab {
        border: 0;
        border-radius: 999px;
        padding: 0.55rem 1rem;
        color: var(--text-muted);
        background: transparent;
        font-weight: 700;
        cursor: pointer;
    }

    .pedidos-tab.is-active {
        color: #fff;
        background: var(--primary, #dc2626);
        box-shadow: 0 8px 18px rgba(220, 38, 38, 0.18);
    }

    .pedidos-tab-panel[hidden] {
        display: none !important;
    }

    .pedido-list-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin: -0.2rem 0 0.85rem;
    }
    .pedido-list-toolbar .system-list-export {
        flex: 0 0 auto;
        margin-left: auto;
    }
    @media (max-width: 680px) {
        .pedido-list-toolbar {
            align-items: stretch;
            flex-direction: column;
        }
        .pedido-list-toolbar .system-list-export {
            align-self: flex-end;
        }
    }

    .pedido-list-search {
        display: grid;
        gap: 0.35rem;
        width: min(100%, calc(36ch + 72px));
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 800;
    }

    .pedido-list-search > span:first-child {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }

    .pedido-list-search-box {
        display: flex;
        align-items: center;
        gap: 0.62rem;
        min-height: 42px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 999px;
        padding: 0.55rem 1.05rem;
        background: var(--surface, #fff);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .pedido-list-search-box [data-lucide] {
        width: 19px;
        height: 19px;
        flex: 0 0 auto;
        color: var(--text-muted, #64748b);
        stroke-width: 2.2;
    }

    .pedido-list-search input {
        width: 100%;
        min-width: 0;
        border: 0;
        padding: 0;
        color: var(--text, #111827);
        background: transparent;
        outline: 0;
        font-weight: 800;
        font-size: 0.82rem;
    }

    .pedido-list-sort-head {
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
        border: 0;
        padding: 0;
        color: inherit;
        background: transparent;
        font: inherit;
        font-weight: 800;
        text-transform: inherit;
        letter-spacing: inherit;
        cursor: pointer;
    }

    .pedido-list-sort-head > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 21px;
        height: 21px;
        border-radius: 999px;
        background: var(--surface-muted, #eef2f7);
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        line-height: 1;
        transition: color 0.2s ease, background 0.2s ease;
    }

    .pedido-list-sort-head:hover > span,
    .pedido-list-sort-head > span.active {
        color: var(--primary, #dc2626);
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #fff);
    }

    .daily-output-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .daily-output-filter {
        display: grid;
        gap: 0.65rem;
        min-width: min(100%, 620px);
    }

    .daily-output-filter__top {
        display: flex;
        align-items: end;
        gap: 0.65rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .daily-output-date {
        display: grid;
        gap: 0.35rem;
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 800;
    }

    .daily-output-date input {
        min-height: 40px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 999px;
        padding: 0.5rem 0.8rem;
        color: var(--text, #111827);
        background: var(--surface, #fff);
        font-weight: 800;
    }

    .daily-output-store-filter {
        position: relative;
    }

    .daily-output-store-trigger {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 40px;
        white-space: nowrap;
    }

    .daily-output-store-trigger span {
        border-radius: 999px;
        padding: 0.18rem 0.48rem;
        color: var(--primary, #dc2626);
        background: color-mix(in srgb, var(--primary, #dc2626) 10%, transparent);
        font-size: 0.72rem;
        font-weight: 850;
    }

    .daily-output-stores {
        position: absolute;
        z-index: 20;
        top: calc(100% + 0.5rem);
        left: 0;
        right: auto;
        width: max-content;
        min-width: 300px;
        max-width: min(92vw, 380px);
        display: grid;
        gap: 0.5rem;
        border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 14%, var(--border, #e5e7eb));
        border-radius: 16px;
        padding: 0.65rem;
        background: color-mix(in srgb, var(--primary, #dc2626) 4%, var(--surface, #fff));
        box-shadow: 0 24px 55px rgba(15, 23, 42, 0.18);
    }

    .daily-output-stores[hidden] {
        display: none !important;
    }

    .daily-output-stores__head {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .daily-output-stores__head span {
        display: block;
        color: var(--text-muted, #64748b);
        font-size: 0.72rem;
        font-weight: 850;
        text-transform: uppercase;
    }

    .daily-output-stores__head strong {
        display: block;
        margin-top: 0.2rem;
        color: var(--text, #111827);
        font-size: 0.92rem;
    }

    .daily-output-stores__actions {
        display: inline-flex;
        gap: 0.45rem;
        align-items: center;
    }

    .daily-output-stores__actions button {
        border: 0;
        border-radius: 999px;
        padding: 0.35rem 0.62rem;
        color: var(--primary, #dc2626);
        background: color-mix(in srgb, var(--primary, #dc2626) 10%, transparent);
        font-size: 0.76rem;
        font-weight: 850;
        cursor: pointer;
    }

    .daily-output-store-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.12rem;
        max-height: 230px;
        overflow: auto;
        padding-right: 0.1rem;
    }

    .daily-output-store-pill {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 0;
        border-radius: 10px;
        padding: 0.38rem 0.45rem;
        background: transparent;
        cursor: pointer;
    }

    .daily-output-store-pill:hover {
        background: color-mix(in srgb, var(--primary, #dc2626) 7%, transparent);
    }

    .daily-output-store-pill input {
        width: 15px;
        height: 15px;
        accent-color: var(--primary, #dc2626);
        flex: 0 0 auto;
    }

    .daily-output-store-pill span {
        display: grid;
        min-width: 0;
        gap: 0.1rem;
    }

    .daily-output-store-pill strong,
    .daily-output-store-pill small {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .daily-output-store-pill strong {
        color: var(--text, #111827);
        font-size: 0.82rem;
        font-weight: 850;
    }

    .daily-output-store-pill small,
    .daily-output-stores p,
    .daily-output-store-empty {
        color: var(--text-muted, #64748b);
        font-size: 0.74rem;
        font-weight: 700;
    }

    .daily-output-store-empty {
        border: 1px dashed var(--border, #e5e7eb);
        border-radius: 14px;
        padding: 0.8rem;
        text-align: center;
        background: var(--surface, #fff);
    }

    .daily-output-stores p {
        margin: 0;
    }

    .daily-output-report {
        display: grid;
        gap: 1rem;
        border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 16%, var(--border, #e5e7eb));
        border-radius: 18px;
        padding: 1rem;
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #dc2626) 5%, var(--surface, #fff)), var(--surface, #fff));
    }

    .daily-output-report__title {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        color: var(--text-muted, #64748b);
        font-weight: 850;
    }

    .daily-output-report__title strong {
        color: var(--primary, #dc2626);
        font-size: 1.2rem;
    }

    .daily-output-summary {
        display: grid;
        grid-template-columns: repeat(6, minmax(120px, 1fr));
        gap: 0.7rem;
    }

    .daily-output-summary div {
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 14px;
        padding: 0.85rem;
        background: var(--surface, #fff);
    }

    .daily-output-summary span {
        display: block;
        color: var(--text-muted, #64748b);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .daily-output-summary strong {
        display: block;
        margin-top: 0.32rem;
        color: var(--text, #111827);
        font-size: 1.05rem;
    }

    .daily-output-table-wrap {
        overflow-x: auto;
    }

    .daily-output-table th,
    .daily-output-table td {
        white-space: nowrap;
    }

    .daily-output-table th {
        font-size: calc(0.85rem - 3px);
    }

    .daily-output-table td {
        font-size: calc(1rem - 3px);
    }

    .daily-output-sort-head {
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
        border: 0;
        padding: 0;
        color: inherit;
        background: transparent;
        font: inherit;
        font-weight: 800;
        text-transform: inherit;
        letter-spacing: inherit;
        cursor: pointer;
    }

    .daily-output-sort-head > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 21px;
        height: 21px;
        border-radius: 999px;
        background: var(--surface-muted, #eef2f7);
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        line-height: 1;
        transition: color 0.2s ease, background 0.2s ease;
    }

    .daily-output-sort-head:hover > span,
    .daily-output-sort-head > span.active {
        color: var(--primary, #dc2626);
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #fff);
    }

    .pedido-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.55rem;
    }

    .pedido-action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .pedido-action-buttons form {
        margin: 0;
    }

    .pix-inline {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .pix-inline .pix-key {
        min-width: 180px;
    }
    .pix-toggle {
        display: inline-flex;
        gap: 6px;
        align-items: center;
        font-size: 0.9rem;
    }
    .pix-brand-icon {
        display: block;
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        overflow: hidden;
    }
    .pix-brand-icon img {
        display: block;
        width: 54px;
        max-width: none;
        height: 18px;
        object-fit: contain;
        object-position: left center;
    }
    .nota-print-btn {
        align-self: center;
    }

    @media (max-width: 720px) {
        .pedidos-tabs-header {
            align-items: stretch;
        }

        .pedidos-tabs {
            width: 100%;
            border-radius: 12px;
            display: grid;
            grid-template-columns: 1fr;
        }

        .pedidos-tab {
            flex: 1;
            border-radius: 10px;
        }

        .daily-output-head,
        .daily-output-filter,
        .daily-output-filter__top {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .daily-output-filter .button,
        .daily-output-date input {
            width: 100%;
        }

        .daily-output-store-filter,
        .daily-output-store-trigger {
            width: 100%;
        }

        .daily-output-store-trigger {
            justify-content: center;
        }

        .daily-output-stores {
            left: 0;
            right: auto;
            width: 100%;
        }

        .daily-output-store-list {
            grid-template-columns: 1fr;
            max-height: 240px;
        }

        .daily-output-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media print {
        @page {
            margin: 12mm;
        }

        html,
        body {
            height: auto !important;
            overflow: visible !important;
            background: #fff !important;
        }

        body * {
            visibility: hidden !important;
        }

        .daily-output-report,
        .daily-output-report * {
            visibility: visible !important;
        }

        .daily-output-report {
            position: absolute;
            inset: 0 auto auto 0;
            width: 100% !important;
            border: 0;
            box-shadow: none;
            background: #fff;
        }

        .daily-output-table-wrap {
            overflow: visible !important;
        }

        .daily-output-table {
            width: 100% !important;
            table-layout: auto;
            font-size: 11px;
        }

        .daily-output-table thead {
            display: table-header-group;
        }

        .daily-output-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .daily-output-table th,
        .daily-output-table td {
            white-space: normal !important;
            word-break: break-word;
        }

        .daily-output-sort-head {
            border: 0 !important;
            padding: 0 !important;
            color: inherit !important;
            background: transparent !important;
            box-shadow: none !important;
            font: inherit !important;
            text-transform: inherit !important;
            letter-spacing: inherit !important;
        }

        .daily-output-sort-head > span {
            display: none !important;
        }
    }
</style>
<script>
(() => {
    const params = new URLSearchParams(window.location.search);
    const auditPedidoId = Number(params.get('pedido_id') || 0);
    if (auditPedidoId <= 0 || params.get('audit_focus') !== 'pedido') return;
    const revealPedido = () => {
        const button = document.querySelector(`.pedido-edit-btn[data-pedido="${auditPedidoId}"]`);
        const row = button?.closest('[data-pedido-row]');
        if (!row) return false;
        row.hidden = false;
        row.style.display = '';
        row.setAttribute('data-audit-pedido-target', '');
        return true;
    };
    if (!revealPedido()) {
        const observer = new MutationObserver(() => { if (revealPedido()) observer.disconnect(); });
        observer.observe(document.body, {childList:true,subtree:true});
        window.setTimeout(() => observer.disconnect(), 8000);
    }
})();
</script>
