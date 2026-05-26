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
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pedidos): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <?php
                            $status = $pedido['status'] ?? 'novo';
                            $statusClass = $status === 'faturado' ? 'badge-success' : ($status === 'cancelado' ? 'badge-danger' : 'badge-warning');
                            $clienteLabel = $clienteMap[(int)($pedido['cliente_id'] ?? 0)]['nome_fantasia']
                                ?? ($clienteMap[(int)($pedido['cliente_id'] ?? 0)]['razao_social'] ?? ($pedido['cliente_id'] ?? '-'));
                            ?>
                            <tr>
                                <td><?= (int)$pedido['id']; ?></td>
                                <td><?= htmlspecialchars($pedido['codigo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($clienteLabel, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td>R$ <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($pedido['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="action-cell">
                                    <?php $podeAprovar = !in_array($status, ['producao','separacao','expedicao','faturado','cancelado'], true); ?>
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
                                                <span>PIX na nota</span>
                                            </label>
                                            <input type="text" class="pix-key" value="<?= htmlspecialchars($notePixKey, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Chave PIX opcional">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nenhum pedido cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
        $saidaDia = $saidaDia ?? ['data' => date('Y-m-d'), 'itens' => [], 'resumo' => []];
        $saidaResumo = $saidaDia['resumo'] ?? [];
        $saidaItens = $saidaDia['itens'] ?? [];
        $saidaData = $saidaDia['data'] ?? date('Y-m-d');
        $saidaDataLabel = date('d/m/Y', strtotime($saidaData));
        $saidaLojasSelecionadas = array_map('intval', $saidaDia['lojas'] ?? []);
        $saidaLojasDisponiveis = $saidaLojasDisponiveis ?? [];
        $saidaComFiltroLojas = count($saidaLojasSelecionadas) > 0;
        $saidaLojasLabel = $saidaComFiltroLojas ? count($saidaLojasSelecionadas).' loja(s) selecionada(s)' : 'Todas as lojas';
    ?>
    <div class="pedidos-tab-panel" id="panel-saida-dia" role="tabpanel" aria-labelledby="tab-saida-dia" data-tab-panel="saida-dia" hidden>
        <div class="daily-output-head">
            <div>
                <h3>Gerar saida do dia</h3>
                <p class="panel-description">Selecione uma data para consolidar todos os produtos vendidos em pedidos internos e pedidos do portal.</p>
            </div>
            <form class="daily-output-filter" method="get" action="<?= htmlspecialchars($url('pedidos'), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="daily-output-filter__top">
                    <label class="daily-output-date" for="saida-data">
                        Dia da saida
                        <input type="date" id="saida-data" name="saida_data" value="<?= htmlspecialchars($saidaData, ENT_QUOTES, 'UTF-8'); ?>">
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
                            <p>Sem lojas marcadas, o relatorio mostra todas as lojas com saida neste dia.</p>
                        </div>
                    </div>
                    <button type="submit" class="button button-primary">Gerar relatorio</button>
                    <button type="button" class="button" data-print-daily-output>Imprimir</button>
                </div>
            </form>
        </div>

        <div class="daily-output-report" data-daily-output-report>
            <div class="daily-output-report__title">
                <span>Relatorio de saida - <?= htmlspecialchars($saidaLojasLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                <strong><?= htmlspecialchars($saidaDataLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="daily-output-summary">
                <div><span>Pedidos</span><strong><?= (int)($saidaResumo['pedidos'] ?? 0); ?></strong></div>
                <div><span>Portal</span><strong><?= (int)($saidaResumo['pedidos_portal'] ?? 0); ?></strong></div>
                <div><span>Internos</span><strong><?= (int)($saidaResumo['pedidos_internos'] ?? 0); ?></strong></div>
                <div><span>Produtos</span><strong><?= (int)($saidaResumo['produtos'] ?? 0); ?></strong></div>
                <div><span>Qtd. total</span><strong><?= number_format((float)($saidaResumo['quantidade'] ?? 0), 2, ',', '.'); ?></strong></div>
                <div><span>Valor total</span><strong>R$ <?= number_format((float)($saidaResumo['valor_total'] ?? 0), 2, ',', '.'); ?></strong></div>
            </div>

            <div class="daily-output-table-wrap">
                <table class="data-table daily-output-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Produto</th>
                            <th>Pedidos</th>
                            <th>Quantidade</th>
                            <th>Valor medio</th>
                            <th>Valor total</th>
                            <th>Peso total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($saidaItens): ?>
                            <?php foreach ($saidaItens as $item): ?>
                                <tr>
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
                                <td colspan="7">Nenhuma saida encontrada para este dia.</td>
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

<script>
    (() => {
        const tabs = document.querySelectorAll('.pedidos-tab');
        const panels = document.querySelectorAll('.pedidos-tab-panel');

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

        if (new URLSearchParams(window.location.search).has('saida_data')) {
            document.querySelector('[data-tab-target="saida-dia"]')?.click();
        }

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
                    ${lt}/style>
                ${lt}/head>
                ${lt}body>${report.outerHTML}${lt}/body>
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
    }
</style>
