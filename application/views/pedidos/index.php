<section class="panel pedidos-tabs-card">
    <div class="pedidos-tabs-header">
        <div>
            <h2>Pedidos</h2>
            <p class="panel-description">Crie novos pedidos e acompanhe os pedidos cadastrados em abas separadas.</p>
        </div>
        <div class="pedidos-tabs" role="tablist" aria-label="Pedidos">
            <button type="button" class="pedidos-tab is-active" id="tab-novo-pedido" role="tab" aria-selected="true" aria-controls="panel-novo-pedido" data-tab-target="novo-pedido">Novo pedido</button>
            <button type="button" class="pedidos-tab" id="tab-lista-pedidos" role="tab" aria-selected="false" aria-controls="panel-lista-pedidos" data-tab-target="lista-pedidos">Lista de pedidos</button>
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
        }

        .pedidos-tab {
            flex: 1;
            border-radius: 10px;
        }
    }
</style>
