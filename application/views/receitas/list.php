<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 16px; }
.panel-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.panel-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn.primary { background: var(--primary, #4b5563); color: #fff; }
.btn.ghost { background: transparent; border: 1px solid var(--border, #e5e7eb); color: inherit; }
.table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 0.92rem; }
.data-table th, .data-table td { border: 1px solid rgba(148,163,184,0.4); padding: 8px 10px; text-align: left; }
.data-table th { text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.08em; background: rgba(15,23,42,0.08); }
.badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
.badge-success { background: rgba(16,185,129,0.18); color: #0f766e; }
.badge-muted { background: rgba(148,163,184,0.18); color: #475569; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-top: 12px; }
.filter-grid label { font-size: 0.82rem; color: var(--muted, #6b7280); display: flex; flex-direction: column; gap: 6px; }
.filter-grid input, .filter-grid select { border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 8px 10px; background: var(--surface-alt, #f8fafc); }
</style>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Receitas (BOM)</h2>
            <p class="panel-description">Cadastre e execute receitas com baixa automática de estoque.</p>
        </div>
        <div class="panel-actions">
            <a class="btn primary" href="<?= htmlspecialchars($url('receitas/nova'), ENT_QUOTES, 'UTF-8'); ?>">Nova receita</a>
            <a class="btn ghost" href="<?= htmlspecialchars($url('produtos'), ENT_QUOTES, 'UTF-8'); ?>">Voltar para produtos</a>
        </div>
    </div>

    <form class="filter-grid" method="get" action="<?= htmlspecialchars($url('receitas'), ENT_QUOTES, 'UTF-8'); ?>">
        <label>
            Buscar
            <input type="text" name="q" value="<?= htmlspecialchars($filters['term'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome ou SKU">
        </label>
        <label>
            Status
            <select name="active">
                <option value="">Todos</option>
                <option value="1" <?= ($filters['active'] ?? '') === '1' ? 'selected' : ''; ?>>Ativo</option>
                <option value="0" <?= ($filters['active'] ?? '') === '0' ? 'selected' : ''; ?>>Inativo</option>
            </select>
        </label>
        <label style="align-self:flex-end;">
            <button class="btn primary" type="submit">Filtrar</button>
        </label>
    </form>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>SKU</th>
                    <th>Rendimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recipes)): ?>
                    <?php foreach ($recipes as $recipe): ?>
                        <tr>
                            <td><?= (int)$recipe['id']; ?></td>
                            <td><?= htmlspecialchars($recipe['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($recipe['sku'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= number_format((float)($recipe['yield_qty'] ?? 0), 3, ',', '.'); ?> <?= htmlspecialchars($recipe['yield_unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ((int)($recipe['active'] ?? 1) === 1): ?>
                                    <span class="badge badge-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id']), ENT_QUOTES, 'UTF-8'); ?>">Detalhar</a>
                                <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/editar'), ENT_QUOTES, 'UTF-8'); ?>">Editar</a>
                                <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/executar'), ENT_QUOTES, 'UTF-8'); ?>">Executar</a>
                                <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/runs'), ENT_QUOTES, 'UTF-8'); ?>">Histórico</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nenhuma receita encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
