<?php
$unidades = $unidades ?? ['kg', 'un', 'cx'];
$produtosLancamento = $produtosLancamento ?? $produtos ?? [];
$listPageSizeOptions = array_values(array_map('intval', $listPageSizeOptions ?? [15, 20, 30, 40, 50, 100]));
$listPageSizeDefault = (int)($listPageSizeDefault ?? 15);
?>

<style>
    .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .tab-button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 8px 14px; font-weight: 700; background: var(--surface-alt, #f8fafc); cursor: pointer; }
    .tab-button .icon { width: 16px; height: 16px; flex: 0 0 16px; }
    .tab-button.active { background: var(--primary, #4b5563); color: #fff; border-color: transparent; }
    .tab-button[data-tab="historico-preco"] { display: none; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    #product-create-form .product-primary-field__hint {
        display: block;
        min-height: 2.4em;
        line-height: 1.2;
    }
    #product-create-form .product-primary-field__hint.is-placeholder {
        visibility: hidden;
        user-select: none;
    }
    #product-create-form .product-sku-sequence-hint {
        font-size: calc(1em - 5.3px);
    }
    @media (max-width: 760px) {
        #product-create-form .product-primary-field__hint {
            min-height: 0;
        }
        #product-create-form .product-primary-field__hint.is-placeholder {
            display: none;
        }
    }
    .history-list { display: grid; gap: 10px; margin-top: 12px; }
    .history-item { border: 1px solid var(--border, #e5e7eb); border-radius: 16px; background: var(--surface, #fff); padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .history-title { font-weight: 700; }
    .history-meta { font-size: 0.82rem; color: var(--text-muted, #64748b); font-weight: 600; }
    .history-actions { display: inline-flex; gap: 8px; }
    .history-filter { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px; }
    .history-filter input,
    .history-filter select {
        border: 1px solid var(--border-muted, #e5e7eb);
        border-radius: 10px;
        padding: 8px 10px;
        background: var(--surface, #fff);
        color: var(--text, #111827);
    }
    .print-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        padding: 0;
        border-radius: 10px;
        border: 1px solid var(--border, #e5e7eb);
        background: #fff;
        color: var(--text, #111827);
        font-size: 0;
        font-weight: 700;
        text-decoration: none;
        transition: border-color 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
    }
    .print-link:hover {
        border-color: var(--primary, #4b5563);
        color: var(--primary, #4b5563);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
        transform: translateY(-1px);
    }
    .print-actions { display: inline-flex; align-items: center; gap: 8px; }
    .print-link svg { width: 19px; height: 19px; }
    .print-link::after {
        content: attr(data-tooltip);
        position: absolute;
        right: 0;
        bottom: calc(100% + 8px);
        z-index: 30;
        width: max-content;
        max-width: 220px;
        padding: 7px 9px;
        border-radius: 8px;
        background: #111827;
        color: #fff;
        font-size: 0.76rem;
        font-weight: 700;
        line-height: 1.2;
        pointer-events: none;
        opacity: 0;
        transform: translateY(4px);
        transition: opacity 140ms ease, transform 140ms ease;
    }
    .print-link:hover::after,
    .print-link:focus-visible::after { opacity: 1; transform: translateY(0); }
    .print-link--simple .print-link__icon-wrap { position: relative; display: inline-flex; }
    .print-link--simple .print-link__badge {
        position: absolute;
        right: -7px;
        bottom: -6px;
        display: grid;
        place-items: center;
        width: 13px;
        height: 13px;
        border-radius: 4px;
        background: var(--primary, #4b5563);
        color: #fff;
        border: 2px solid #fff;
    }
    .print-link--simple .print-link__badge svg { width: 8px; height: 8px; stroke-width: 2.4; }
    .list-pagination { display: grid; grid-template-columns: minmax(120px, 1fr) auto minmax(220px, 1fr); align-items: center; gap: 12px; margin-top: 14px; color: var(--text-muted, #64748b); font-size: 0.85rem; font-weight: 800; }
    .list-pagination-pages, .list-pagination-size { display: inline-flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .list-pagination-pages { justify-content: center; }
    .list-pagination-size { justify-content: flex-end; }
    .list-page-button, .list-page-input { min-width: 38px; min-height: 38px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 850; text-align: center; }
    .list-page-button { padding: 7px 12px; cursor: pointer; }
    .list-page-button.active { border-color: var(--primary, #4f46e5); background: var(--primary, #4f46e5); color: #fff; }
    .list-page-input { width: 58px; padding: 7px 10px; }
    .list-page-size-select { min-height: 38px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 7px 34px 7px 14px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 850; }
    @media (max-width: 760px) {
        .list-pagination { grid-template-columns: 1fr; }
        .list-pagination-pages, .list-pagination-size { justify-content: flex-start; }
    }
    .mp-only { display: none; }
    .mp-only.active {
        display: grid;
        grid-template-columns: 0.9fr 0.9fr 1.3fr 1fr;
        column-gap: 20px;
        row-gap: 22px;
        align-items: start;
    }
    .mp-only .mp-only__header { grid-column: 1 / -1; margin-top: 8px; }
    .mp-only label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
        margin: 0;
        font-weight: 600;
    }
    .mp-only input,
    .mp-only select {
        width: 100%;
        min-width: 0;
        padding: 10px 12px;
        border-radius: 12px;
        min-height: 42px;
        font-size: 0.95rem;
        box-sizing: border-box;
    }
    .mp-only .span-4 { grid-column: 1 / -1; }
    @media (max-width: 900px) {
        .mp-only.active { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .mp-only .span-2,
        .mp-only .span-4 { grid-column: 1 / -1; }
    }
    @media (max-width: 560px) {
        .mp-only.active { grid-template-columns: 1fr; }
    }
</style>

<div class="tabs" role="tablist">
    <button type="button" class="tab-button active" data-tab="cadastro" aria-selected="true">
        <span class="icon" data-lucide="package-plus" aria-hidden="true"></span>
        <span>Cadastro</span>
    </button>
    <button type="button" class="tab-button" data-tab="lista" aria-selected="false">
        <span class="icon" data-lucide="list" aria-hidden="true"></span>
        <span>Lista</span>
    </button>
    <button type="button" class="tab-button" data-tab="lancamento" aria-selected="false">
        <span class="icon" data-lucide="package-open" aria-hidden="true"></span>
        <span>Lançamento</span>
    </button>
    <button type="button" class="tab-button" data-tab="historico-preco" aria-selected="false">
        <span class="icon" data-lucide="history" aria-hidden="true"></span>
        <span>Histórico de preço</span>
    </button>
</div>

<section class="panel tab-panel active" data-tab-panel="cadastro">
    <h2>Novo produto</h2>
    <p class="panel-description">Cadastre produtos acabados (PA) e matérias-primas (MP) com SKU único.</p>
    <form method="post" action="<?= htmlspecialchars($url('produtos'), ENT_QUOTES, 'UTF-8'); ?>" class="form-grid" id="product-create-form">
        <label class="product-primary-field">
            SKU <small class="muted product-primary-field__hint">Gerado automaticamente conforme o tipo</small>
            <input type="text" name="sku" readonly data-next-pa="<?= htmlspecialchars($nextSkuPa ?? 'A-001', ENT_QUOTES, 'UTF-8'); ?>" data-next-mp="<?= htmlspecialchars($nextSkuMp ?? 'MP-001', ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars($nextSkuPa ?? 'A-001', ENT_QUOTES, 'UTF-8'); ?>" placeholder="A-001">
            <small class="muted product-sku-sequence-hint">Sequência com <?= (int)($skuDigits ?? 3); ?> dígitos (configurável em Configurações &gt; Regras).</small>
        </label>
        <label class="product-primary-field">
            Nome
            <small class="muted product-primary-field__hint is-placeholder" aria-hidden="true">Alinhamento</small>
            <input type="text" name="nome" required>
        </label>
        <label class="product-primary-field">
            Tipo
            <small class="muted product-primary-field__hint is-placeholder" aria-hidden="true">Alinhamento</small>
            <select name="tipo">
                <option value="PA">Produto acabado (PA)</option>
                <option value="MP">Matéria-prima (MP)</option>
            </select>
        </label>
        <label class="product-primary-field">
            Categoria
            <small class="muted product-primary-field__hint is-placeholder" aria-hidden="true">Alinhamento</small>
            <input type="text" name="categoria">
        </label>
        <label>
            Unidade
            <select name="unidade" required>
                <?php foreach ($unidades as $unidade): ?>
                    <option value="<?= htmlspecialchars($unidade, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($unidade, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Peso líquido
            <input type="number" step="0.001" name="peso_liquido">
        </label>
        <label>
            Validade ( dias )
            <input type="number" name="shelf_life_dias" min="0">
        </label>
        <label>
            Preço padrão
            <input type="number" step="0.01" name="preco">
        </label>
        <div class="mp-only" id="mp-recebimento">
            <div class="mp-only__header">
                <strong>Recebimento inicial</strong>
                <p class="panel-description" style="margin: 4px 0 0;">Registre o primeiro recebimento do produto no fornecedor para já aparecer no lançamento.</p>
            </div>
            <label>
                Quantidade de compra
                <input type="number" step="0.001" min="0" name="recebimento_quantidade" placeholder="0">
            </label>
            <label>
                Unidade da compra
                <select name="recebimento_unidade">
                    <?php foreach ($unidades as $unidade): ?>
                        <option value="<?= htmlspecialchars($unidade, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($unidade, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="field-fornecedor">
                Fornecedor
                <input type="text" name="recebimento_fornecedor" placeholder="Fornecedor">
            </label>
            <label>
                Data de entrega
                <input type="date" name="recebimento_data_entrega">
            </label>
            <label class="field-lote">
                Lote
                <input type="text" name="recebimento_lote" placeholder="Lote">
            </label>
            <label>
                Data de fabricação
                <input type="date" name="recebimento_data_fabricacao">
            </label>
            <label>
                Data de vencimento
                <input type="date" name="recebimento_data_vencimento">
            </label>
            <label>
                Preço de compra
                <input type="number" step="0.01" name="recebimento_preco" placeholder="0,00">
            </label>
        </div>
        <div class="form-actions" style="grid-column: 1 / -1; justify-content: flex-end;">
            <button class="button button-primary" type="submit">Salvar produto</button>
        </div>
    </form>
</section>

<section class="panel tab-panel" data-tab-panel="lista">
    <div class="panel-header row-between">
        <div>
            <h2>Catálogo interno</h2>
            <p class="panel-description">Produtos acabados (PA) e matérias-primas (MP) disponíveis para pedidos e produção.</p>
        </div>
        <div class="panel-actions">
            <?php
                $printFilters = array_filter([
                    'q' => $filters['term'] ?? null,
                    'categoria' => $filters['categoria'] ?? null,
                    'tipo' => $filters['tipo'] ?? null,
                ], fn($v) => $v !== null && $v !== '');
                $printUrl = $url('produtos/print' . ($printFilters ? '?' . http_build_query($printFilters) : ''));
                $simplifiedPrintFilters = array_merge($printFilters, ['modo' => 'simplificado']);
                $simplifiedPrintUrl = $url('produtos/print?' . http_build_query($simplifiedPrintFilters));
            ?>
            <div class="print-actions" aria-label="Opcoes de impressao">
            <a class="print-link" data-print-mode="completo" data-tooltip="Imprimir relatorio completo" title="Imprimir relatorio completo" aria-label="Imprimir relatorio completo" href="<?= htmlspecialchars($printUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 9V3h12v6" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <path d="M6 14h12v7H6z" />
                </svg>
                Imprimir relatório
            </a>
            <a class="print-link print-link--simple" data-print-mode="simplificado" data-tooltip="Imprimir relatorio simplificado" title="Imprimir relatorio simplificado" aria-label="Imprimir relatorio simplificado" href="<?= htmlspecialchars($simplifiedPrintUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                <span class="print-link__icon-wrap">
                    <span data-lucide="printer" aria-hidden="true"></span>
                    <span class="print-link__badge"><span data-lucide="list-checks" aria-hidden="true"></span></span>
                </span>
            </a>
            </div>
        </div>
    </div>

    <form method="get" class="filter-grid" id="catalog-filters" data-endpoint="<?= htmlspecialchars($url('produtos'), ENT_QUOTES, 'UTF-8'); ?>" data-print="<?= htmlspecialchars($url('produtos/print'), ENT_QUOTES, 'UTF-8'); ?>">
        <label>
            Busca geral
            <input type="text" name="q" value="<?= htmlspecialchars($filters['term'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="SKU, nome ou categoria">
        </label>
        <label>
            Categoria
            <select name="categoria">
                <option value="">Todas</option>
                <?php foreach (($categorias ?? []) as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>" <?= isset($filters['categoria']) && $filters['categoria'] === $categoria ? 'selected' : ''; ?>><?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Tipo
            <select name="tipo">
                <option value="">Todos</option>
                <option value="PA" <?= (isset($filters['tipo']) && $filters['tipo'] === 'PA') ? 'selected' : ''; ?>>Produto acabado (PA)</option>
                <option value="MP" <?= (isset($filters['tipo']) && $filters['tipo'] === 'MP') ? 'selected' : ''; ?>>Matéria-prima (MP)</option>
            </select>
        </label>
        <div class="filter-actions">
            <button class="button button-primary" type="submit">Aplicar filtros</button>
            <a class="button" href="<?= htmlspecialchars($url('produtos'), ENT_QUOTES, 'UTF-8'); ?>">Limpar</a>
        </div>
    </form>

    <div class="catalog-table">
        <div class="catalog-meta">Resultados: <?= count($produtos); ?> itens</div>
        <?php
            $activeSort = $filters['sort'] ?? 'created_at';
            $activeDir = strtolower($filters['dir'] ?? 'desc');
            $activeDir = in_array($activeDir, ['asc', 'desc'], true) ? $activeDir : 'desc';
            $queryBase = [];
            if (!empty($filters['term'])) {
                $queryBase['q'] = $filters['term'];
            }
            if (!empty($filters['categoria'])) {
                $queryBase['categoria'] = $filters['categoria'];
            }
            if (!empty($filters['tipo'])) {
                $queryBase['tipo'] = $filters['tipo'];
            }
            $sortLink = function (string $column, string $label, string $labelHtml = '') use ($activeSort, $activeDir, $queryBase, $url) {
                $qs = $queryBase;
                $qs['sort'] = $column;
                $qs['dir'] = ($activeSort === $column && $activeDir === 'asc') ? 'desc' : 'asc';
                $indicator = $activeSort === $column ? ($activeDir === 'asc' ? '▲' : '▼') : '⇅';
                $href = $url('produtos' . ($qs ? '?' . http_build_query($qs) : ''));
                $labelMarkup = $labelHtml !== '' ? $labelHtml : htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

                return '<a class="sort-link" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" data-sort="' . htmlspecialchars($column, ENT_QUOTES, 'UTF-8') . '" data-next="' . htmlspecialchars($qs['dir'], ENT_QUOTES, 'UTF-8') . '" data-active="' . htmlspecialchars($activeSort, ENT_QUOTES, 'UTF-8') . '" data-direction="' . htmlspecialchars($activeDir, ENT_QUOTES, 'UTF-8') . '">' .
                    '<span class="sort-label">' . $labelMarkup . '</span>' .
                    '<span class="sort-indicator' . ($activeSort === $column ? ' active' : '') . '">' . $indicator . '</span>' .
                '</a>';
            };
        ?>
        <style>
            .sort-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                color: inherit;
                text-decoration: none;
                font-weight: 600;
            }
            .sort-link:hover .sort-indicator {
                opacity: 1;
            }
            .sort-indicator {
                font-size: 0.85rem;
                opacity: 0.65;
                padding: 2px 5px;
                border-radius: 8px;
                background: rgba(0,0,0,0.06);
                color: inherit;
                transition: all 0.2s ease;
            }
            .sort-indicator.active {
                background: rgba(86, 97, 255, 0.12);
                color: inherit;
                opacity: 1;
            }
            #catalog-table th { white-space: nowrap; font-size: calc(0.85rem - 3px); }
            #catalog-table td { font-size: calc(1rem - 3px); }
            #catalog-table .sort-label--stacked { display: block; margin-top: 1px; line-height: 1.05; }
            .product-actions { display: flex; flex-wrap: nowrap; gap: 5px; align-items: center; white-space: nowrap; }
            .product-actions form { flex: 0 0 auto; margin: 0; }
            .product-actions .product-status-form { order: 1; }
            .product-actions .js-edit-product { order: 2; }
            .product-actions .product-delete-form { order: 3; }
            .product-actions .js-product-info { order: 4; }
            .product-icon-btn {
                box-sizing: border-box;
                position: relative;
                display: inline-flex;
                flex: 0 0 34px;
                align-items: center;
                justify-content: center;
                width: 34px;
                min-width: 34px;
                max-width: 34px;
                height: 34px;
                min-height: 34px;
                max-height: 34px;
                padding: 0;
                border: 0;
                border-radius: 10px;
                background: var(--surface-muted, #f3f4f6);
                color: var(--text, #020617);
                cursor: pointer;
            }
            .product-icon-btn svg { width: 14px; height: 14px; stroke-width: 2.25; }
            .product-icon-btn--info {
                flex: 0 0 24px;
                width: 24px;
                min-width: 24px;
                max-width: 24px;
                height: 24px;
                min-height: 24px;
                max-height: 24px;
                border-radius: 8px;
                color: var(--primary, #dc2626);
                background: color-mix(in srgb, var(--primary, #dc2626) 10%, #fff);
                border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 28%, transparent);
            }
            .product-icon-btn--info svg { width: 10px; height: 10px; }
            .product-icon-btn--danger { color: #dc2626; background: #fff1f2; }
            .product-status-switch {
                position: relative;
                display: inline-flex;
                align-items: center;
                justify-content: flex-start;
                gap: 0;
                width: 42px;
                min-width: 42px;
                height: 20px;
                padding: 2px;
                border: 1px solid #cbd5e1;
                border-radius: 999px;
                background: #f1f5f9;
                color: #64748b;
                font-size: 0;
                font-weight: 850;
                cursor: pointer;
                transition: background 160ms ease, border-color 160ms ease, color 160ms ease, box-shadow 160ms ease;
            }
            .product-status-switch__icon {
                display: grid;
                place-items: center;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 2px 7px rgba(15, 23, 42, 0.12);
            }
            .product-status-switch__icon svg { width: 8px; height: 8px; stroke-width: 2.5; }
            .product-status-switch > span:last-child { display: none; }
            .product-status-switch.is-active {
                justify-content: flex-end;
                padding: 2px;
                border-color: #86efac;
                background: #ecfdf5;
                color: #15803d;
            }
            .product-status-switch.is-active .product-status-switch__icon {
                background: #16a34a;
                color: #fff;
            }
            .product-status-switch:hover {
                box-shadow: 0 7px 18px rgba(15, 23, 42, 0.12);
            }
            .product-icon-btn::after {
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
            .product-icon-btn:hover::after,
            .product-icon-btn:focus-visible::after {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
            .product-actions .danger { color: #dc2626; border-color: #fecdd3; background: #fff1f2; }
            .product-status { display: inline-flex; margin-top: 4px; padding: 3px 8px; border-radius: 999px; background: #f1f5f9; color: #64748b; font-size: 0.72rem; font-weight: 800; }
            .product-status.is-active { background: #ecfdf5; color: #047857; }
            .product-info-modal.hidden { display: none !important; }
            .product-info-modal {
                position: fixed;
                inset: 0;
                z-index: 1200;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: rgba(15, 23, 42, 0.46);
                backdrop-filter: blur(3px);
            }
            .product-info-modal__dialog {
                width: min(560px, 100%);
                border: 1px solid var(--border, #e5e7eb);
                border-radius: 22px;
                background: var(--surface, #fff);
                box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
                overflow: hidden;
            }
            .product-info-modal__backdrop { position: absolute; inset: 0; }
            .product-info-modal__head {
                position: relative;
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 20px 22px 16px;
                border-bottom: 1px solid var(--border, #e5e7eb);
            }
            .product-info-modal__head h3 { margin: 3px 0 0; color: var(--text, #0f172a); }
            .product-info-modal__icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                border-radius: 13px;
                color: var(--primary, #dc2626);
                background: color-mix(in srgb, var(--primary, #dc2626) 10%, #fff);
                flex: 0 0 38px;
            }
            .product-info-modal__icon svg { width: 18px; height: 18px; stroke-width: 2.4; }
            .product-info-modal__close {
                margin-left: auto;
                width: 34px;
                height: 34px;
                border: 0;
                border-radius: 11px;
                background: var(--surface-muted, #f3f4f6);
                color: var(--text, #0f172a);
                font-size: 1.25rem;
                font-weight: 800;
                cursor: pointer;
            }
            .product-info-modal__body { position: relative; display: grid; gap: 16px; padding: 18px 22px 22px; }
            .product-info-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }
            .product-info-grid div,
            .product-price-row {
                border: 1px solid var(--border, #e5e7eb);
                border-radius: 14px;
                padding: 11px 12px;
                background: var(--surface-muted, #f8fafc);
            }
            .product-info-grid span,
            .product-price-row span {
                display: block;
                color: var(--text-muted, #64748b);
                font-size: 0.7rem;
                font-weight: 850;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }
            .product-info-grid strong,
            .product-price-row strong {
                display: block;
                margin-top: 5px;
                color: var(--text, #0f172a);
                font-size: 0.92rem;
            }
            .product-price-row small {
                display: block;
                margin-top: 4px;
                color: var(--text-muted, #64748b);
                font-size: 0.72rem;
                font-weight: 700;
            }
            .product-info-modal__body h4 { margin: 0 0 9px; color: var(--text, #0f172a); }
            .product-price-history { display: grid; gap: 8px; }
            .product-price-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .product-price-empty {
                border: 1px dashed var(--border, #e5e7eb);
                border-radius: 14px;
                padding: 14px;
                color: var(--text-muted, #64748b);
                font-weight: 750;
                text-align: center;
            }
            @media (max-width: 620px) {
                .product-info-grid { grid-template-columns: 1fr; }
                .product-price-row { align-items: flex-start; flex-direction: column; }
            }
        </style>
        <div style="overflow-x: auto;">
            <table class="data-table" id="catalog-table" data-empty="Nenhum produto encontrado para os filtros selecionados.">
                <thead>
                    <tr>
                        <th><?= $sortLink('id', 'ID'); ?></th>
                        <th><?= $sortLink('sku', 'SKU'); ?></th>
                        <th><?= $sortLink('nome', 'Nome'); ?></th>
                        <th><?= $sortLink('categoria', 'Categoria'); ?></th>
                        <th><?= $sortLink('tipo', 'Tipo'); ?></th>
                        <th><?= $sortLink('unidade', 'Unidade'); ?></th>
                        <th><?= $sortLink('preco', 'Preço'); ?></th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produtos): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?= (int)$produto['id']; ?></td>
                                <td><?= htmlspecialchars($produto['sku'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php $isActive = (int)($produto['is_ativo'] ?? 1) === 1; ?>
                                    <span class="product-status <?= $isActive ? 'is-active' : ''; ?>"><?= $isActive ? 'Ativo' : 'Inativo'; ?></span>
                                </td>
                                <td><?= htmlspecialchars($produto['categoria'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge <?= $produto['tipo'] === 'PA' ? 'badge-success' : 'badge-warning'; ?>"><?= htmlspecialchars($produto['tipo'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?= htmlspecialchars($produto['unidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>R$ <?= number_format((float)($produto['preco'] ?? 0), 2, ',', '.'); ?></td>
                                <td>
                                    <?php $payload = json_encode($produto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
                                    <div class="product-actions">
                                        <button type="button" class="product-icon-btn product-icon-btn--info js-product-info" data-product-id="<?= (int)$produto['id']; ?>" data-tooltip="Informações" aria-label="Ver informações do produto">
                                            <span data-lucide="info" aria-hidden="true"></span>
                                        </button>
                                        <button type="button" class="product-icon-btn js-edit-product" data-product="<?= htmlspecialchars($payload, ENT_QUOTES, 'UTF-8'); ?>" data-tooltip="Editar" aria-label="Editar produto">
                                            <span data-lucide="pencil" aria-hidden="true"></span>
                                        </button>
                                        <form class="product-status-form" method="post" action="<?= htmlspecialchars($url('produtos/'.(int)$produto['id'].'/'.($isActive ? 'desativar' : 'ativar')), ENT_QUOTES, 'UTF-8'); ?>" <?= $isActive ? 'onsubmit="return confirm(\'Desativar este produto? Ele deixara de aparecer em novos pedidos e relatorios.\');"' : ''; ?>>
                                            <button type="submit" class="product-status-switch <?= $isActive ? 'is-active' : ''; ?>" title="<?= $isActive ? 'Desativar produto' : 'Ativar produto'; ?>" aria-label="<?= $isActive ? 'Desativar produto' : 'Ativar produto'; ?>">
                                                <span class="product-status-switch__icon"><span data-lucide="<?= $isActive ? 'power' : 'power-off'; ?>" aria-hidden="true"></span></span>
                                                <span><?= $isActive ? 'Ativo' : 'Inativo'; ?></span>
                                            </button>
                                        </form>
                                        <form class="product-delete-form" method="post" action="<?= htmlspecialchars($url('produtos/'.(int)$produto['id'].'/excluir'), ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return confirm('Excluir permanentemente este produto? Todos os registros vinculados a ele tambem serao apagados. Esta acao nao pode ser desfeita.');">
                                            <button type="submit" class="product-icon-btn product-icon-btn--danger" data-tooltip="Excluir permanentemente" aria-label="Excluir produto permanentemente">
                                                <span data-lucide="trash-2" aria-hidden="true"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Nenhum produto encontrado para os filtros selecionados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="list-pagination" data-list-pagination="produtos" aria-label="Paginação da lista de produtos"></div>
    </div>
</section>

<section class="panel tab-panel" data-tab-panel="lancamento">
    <div class="panel-header row-between">
        <div>
            <h2>Lançamento</h2>
            <p class="panel-description">Selecione um produto cadastrado e atualize fornecedor, lote e datas quando novas entregas chegarem.</p>
        </div>
        <div class="panel-actions">
            <div class="pill soft">Registro de entrega</div>
        </div>
    </div>

    <form class="form-grid" method="post" action="<?= htmlspecialchars($url('produtos?tab=lancamento'), ENT_QUOTES, 'UTF-8'); ?>" style="margin-top: 12px;">
        <label>
            Produto
            <select name="lancamento_produto">
                <option value="">Selecione</option>
                <?php foreach (($produtosLancamento ?? []) as $produto): ?>
                    <?php $ultimo = $lancamentosPorProduto[(int)$produto['id']] ?? null; ?>
                    <option
                        value="<?= (int)$produto['id']; ?>"
                        data-preco="<?= htmlspecialchars($ultimo['preco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-fornecedor="<?= htmlspecialchars($ultimo['fornecedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-data-entrega="<?= htmlspecialchars($ultimo['data_entrega'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-lote="<?= htmlspecialchars($ultimo['lote'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-data-fabricacao="<?= htmlspecialchars($ultimo['data_fabricacao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-data-vencimento="<?= htmlspecialchars($ultimo['data_vencimento'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    ><?= htmlspecialchars($produto['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Preço
            <input type="number" step="0.01" name="lancamento_preco" placeholder="0,00">
        </label>
        <label>
            Fornecedor
            <input type="text" name="lancamento_fornecedor" placeholder="Fornecedor">
        </label>
        <label>
            Data de entrega
            <input type="date" name="lancamento_data_entrega">
        </label>
        <label>
            Lote
            <input type="text" name="lancamento_lote" placeholder="Lote">
        </label>
        <label>
            Data de fabricação
            <input type="date" name="lancamento_data_fabricacao">
        </label>
        <label>
            Data de vencimento
            <input type="date" name="lancamento_data_vencimento">
        </label>
        <div class="form-actions" style="grid-column: 1 / -1;">
            <button class="button button-primary" type="submit">Salvar lançamento</button>
        </div>
    </form>

    <div class="catalog-table" style="margin-top: 16px;">
        <div class="catalog-meta">Histórico de preço e fornecedor</div>
        <?php
            $formatDate = static function (?string $date): string {
                if (!$date) {
                    return '—';
                }
                $timestamp = strtotime($date);
                return $timestamp ? date('d/m/Y', $timestamp) : '—';
            };
        ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Fornecedor</th>
                        <th>Entrega</th>
                        <th>Lote</th>
                        <th>Fabricação</th>
                        <th>Vencimento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lancamentos)): ?>
                        <?php foreach ($lancamentos as $lancamento): ?>
                            <tr>
                                <td><?= htmlspecialchars($lancamento['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>R$ <?= number_format((float)($lancamento['preco'] ?? 0), 2, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($lancamento['fornecedor'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $formatDate($lancamento['data_entrega'] ?? null); ?></td>
                                <td><?= htmlspecialchars($lancamento['lote'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $formatDate($lancamento['data_fabricacao'] ?? null); ?></td>
                                <td><?= $formatDate($lancamento['data_vencimento'] ?? null); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nenhum lançamento registrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="catalog-table" style="margin-top: 16px;">
        <div class="catalog-meta">Histórico por produto</div>
        <?php
            $historicoPorProduto = [];
            foreach (($historicoLancamentos ?? []) as $item) {
                $historicoPorProduto[(int)($item['produto_id'] ?? 0)][] = $item;
            }
        ?>
        <div class="history-filter">
            <input type="search" id="history-search" placeholder="Buscar produto">
            <select id="history-filter">
                <option value="ALL">Todos</option>
                <option value="COM">Com histórico</option>
                <option value="SEM">Sem histórico</option>
            </select>
        </div>
        <div class="history-list" id="history-list">
            <?php foreach (($produtosLancamento ?? []) as $produto): ?>
                <?php
                    $pid = (int)($produto['id'] ?? 0);
                    $history = $historicoPorProduto[$pid] ?? [];
                    $ultimoLancamento = $lancamentosPorProduto[$pid] ?? null;
                    $ultimoValor = $ultimoLancamento ? (float)($ultimoLancamento['preco'] ?? 0) : null;
                ?>
                <div class="history-item" data-name="<?= htmlspecialchars(strtolower($produto['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" data-count="<?= count($history); ?>">
                    <div>
                        <div class="history-title"><?= htmlspecialchars($produto['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="history-meta">
                            <?= count($history); ?> lançamento(s)
                            <span>• Último valor: <?= $ultimoValor !== null ? 'R$ '.number_format($ultimoValor, 2, ',', '.') : '—'; ?></span>
                        </div>
                    </div>
                    <div class="history-actions">
                        <button type="button" class="button small js-history-open" data-history-target="historico-preco" data-product="<?= $pid; ?>">
                            Histórico de preço
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="panel tab-panel" data-tab-panel="historico-preco">
    <div class="panel-header row-between">
        <div>
            <div class="history-title" id="history-product-title">Produto</div>
            <h2>Histórico de preço</h2>
            <p class="panel-description">Selecione um produto na lista para ver o histórico completo.</p>
        </div>
        <div class="panel-actions">
            <button type="button" class="button ghost" id="history-back">Voltar para lançamento</button>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Fornecedor</th>
                    <th>Entrega</th>
                    <th>Lote</th>
                    <th>Fabricação</th>
                    <th>Vencimento</th>
                </tr>
            </thead>
            <tbody id="history-table-body">
                <tr>
                    <td colspan="7">Selecione um produto para visualizar o histórico.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
        $historyPayload = [];
        foreach ($historicoPorProduto as $produtoId => $items) {
            $historyPayload[$produtoId] = array_map(static function ($item) {
                return [
                    'preco' => (float)($item['preco'] ?? 0),
                    'fornecedor' => $item['fornecedor'] ?? null,
                    'data_entrega' => $item['data_entrega'] ?? null,
                    'lote' => $item['lote'] ?? null,
                    'data_fabricacao' => $item['data_fabricacao'] ?? null,
                    'data_vencimento' => $item['data_vencimento'] ?? null,
                    'created_at' => $item['created_at'] ?? null,
                    'nome' => $item['nome'] ?? null,
                ];
            }, $items);
        }
        $produtoInfoPayload = [];
        foreach (($produtos ?? []) as $produto) {
            $pid = (int)($produto['id'] ?? 0);
            if ($pid <= 0) {
                continue;
            }
            $produtoInfoPayload[$pid] = [
                'id' => $pid,
                'sku' => $produto['sku'] ?? '',
                'nome' => $produto['nome'] ?? '',
                'shelf_life_dias' => (int)($produto['shelf_life_dias'] ?? 0),
                'created_at' => $produto['created_at'] ?? '',
                'updated_at' => $produtoAtualizacoes[$pid] ?? '',
                'prices' => array_slice($historyPayload[$pid] ?? [], 0, 6),
            ];
        }
    ?>
    <script>
        window.historyPorProduto = <?= json_encode($historyPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        window.productInfoMap = <?= json_encode($produtoInfoPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>
</section>
<div id="product-info-modal" class="product-info-modal hidden" aria-hidden="true">
    <div class="product-info-modal__backdrop" data-product-info-close></div>
    <div class="product-info-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="product-info-title">
        <div class="product-info-modal__head">
            <span class="product-info-modal__icon"><span data-lucide="info" aria-hidden="true"></span></span>
            <div>
                <p class="muted" style="margin:0; font-size:0.78rem;">Informações do produto</p>
                <h3 id="product-info-title">Produto</h3>
            </div>
            <button type="button" class="product-info-modal__close" data-product-info-close aria-label="Fechar">×</button>
        </div>
        <div class="product-info-modal__body">
            <div class="product-info-grid">
                <div>
                    <span>Criação</span>
                    <strong data-product-info-created>—</strong>
                </div>
                <div>
                    <span>Última modificação</span>
                    <strong data-product-info-updated>—</strong>
                </div>
                <div>
                    <span>Validade do produto</span>
                    <strong data-product-info-validity>—</strong>
                </div>
            </div>
            <div>
                <h4>Últimas 6 atualizações de preço</h4>
                <div class="product-price-history" data-product-info-prices></div>
            </div>
        </div>
    </div>
</div>
<div id="product-edit-modal" class="modal hidden" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog">
        <div class="modal-head">
            <div>
                <p class="muted" style="margin:0; font-size:0.85rem;">Editar produto</p>
                <h3 style="margin:4px 0 0;">Atualize os dados do catálogo</h3>
            </div>
            <button type="button" class="button ghost" id="edit-close">×</button>
        </div>
        <form id="product-edit-form" class="form-grid" data-endpoint="<?= htmlspecialchars($url('produtos'), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="id" value="">
            <label>
                SKU <small class="muted">Gerado automaticamente</small>
                <input type="text" name="sku" readonly>
            </label>
            <label>
                Nome
                <input type="text" name="nome" required>
            </label>
            <label>
                Tipo
                <select name="tipo">
                    <option value="PA">Produto acabado (PA)</option>
                    <option value="MP">Matéria-prima (MP)</option>
                </select>
            </label>
            <label>
                Categoria
                <input type="text" name="categoria">
            </label>
        <label>
            Unidade
            <select name="unidade" required>
                <?php foreach ($unidades as $unidade): ?>
                    <option value="<?= htmlspecialchars($unidade, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($unidade, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Peso líquido
            <input type="number" step="0.001" name="peso_liquido">
        </label>
        <label>
            Validade ( dias )
            <input type="number" name="shelf_life_dias" min="0">
        </label>
            <label>
                Preço padrão
                <input type="number" step="0.01" name="preco">
            </label>
            <div class="form-actions" style="grid-column: 1 / -1;">
                <button type="submit" class="button button-primary">Salvar alterações</button>
                <button type="button" class="button ghost" id="edit-cancel">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal.hidden { display: none; }
    .modal { position: fixed; inset: 0; display: grid; place-items: center; z-index: 1200; }
    .modal-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(2px); }
    .modal-dialog { position: relative; background: var(--surface-elevated, #fff); color: var(--text-primary, #0f172a); padding: 20px; border-radius: 16px; width: min(820px, 92vw); box-shadow: 0 20px 60px rgba(15, 23, 42, 0.18); max-height: 90vh; overflow-y: auto; }
    .modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .modal .form-grid label { font-weight: 600; }
    .modal .form-actions { display: flex; gap: 10px; align-items: center; }
</style>

<script>
    (() => {
        const form = document.getElementById('catalog-filters');
        const table = document.getElementById('catalog-table');
        const tbody = table?.querySelector('tbody');
        const resultMeta = document.querySelector('.catalog-meta');
        const sortLinks = Array.from(document.querySelectorAll('.sort-link'));
        const printButtons = Array.from(document.querySelectorAll('.panel-actions .print-link'));
        const createForm = document.getElementById('product-create-form');
        const editModal = document.getElementById('product-edit-modal');
        const editForm = document.getElementById('product-edit-form');
        const editClose = document.getElementById('edit-close');
        const editCancel = document.getElementById('edit-cancel');
        const mpRecebimento = document.getElementById('mp-recebimento');
        const tabButtons = Array.from(document.querySelectorAll('.tab-button'));
        const tabPanels = Array.from(document.querySelectorAll('.tab-panel'));
        const lancamentoSelect = document.querySelector('select[name="lancamento_produto"]');
        const historySearch = document.getElementById('history-search');
        const historyFilter = document.getElementById('history-filter');
        const historyList = document.getElementById('history-list');
        const historyTableBody = document.getElementById('history-table-body');
        const historyProductTitle = document.getElementById('history-product-title');
        const historyBack = document.getElementById('history-back');
        const historyButtons = Array.from(document.querySelectorAll('.js-history-open'));
        const paginationTarget = document.querySelector('[data-list-pagination="produtos"]');
        const pageSizeOptions = <?= json_encode($listPageSizeOptions); ?>;
        const pageSizeDefault = <?= json_encode($listPageSizeDefault); ?>;
        const lancamentoFields = {
            preco: document.querySelector('input[name="lancamento_preco"]'),
            fornecedor: document.querySelector('input[name="lancamento_fornecedor"]'),
            dataEntrega: document.querySelector('input[name="lancamento_data_entrega"]'),
            lote: document.querySelector('input[name="lancamento_lote"]'),
            dataFabricacao: document.querySelector('input[name="lancamento_data_fabricacao"]'),
            dataVencimento: document.querySelector('input[name="lancamento_data_vencimento"]'),
        };

        if (!form || !tbody || !table) {
            return;
        }

        const state = {
            sort: '<?= htmlspecialchars($activeSort, ENT_QUOTES, 'UTF-8'); ?>',
            dir: '<?= htmlspecialchars($activeDir, ENT_QUOTES, 'UTF-8'); ?>',
            q: form.querySelector('input[name="q"]').value,
            categoria: form.querySelector('select[name="categoria"]').value,
            tipo: form.querySelector('select[name="tipo"]').value,
        };
        const paginationState = {
            page: 1,
            perPage: pageSizeOptions.includes(pageSizeDefault) ? pageSizeDefault : (pageSizeOptions[0] || 15),
        };
        let currentCatalogItems = null;

        const endpoint = form.dataset.endpoint;
        const printBase = form.dataset.print;

        const buildQuery = (params) => {
            const entries = Object.entries(params).filter(([, v]) => v !== null && v !== '' && v !== undefined);
            return new URLSearchParams(entries).toString();
        };

        const formatPrice = (val) => {
            const num = Number(val || 0);
            return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };
        const escapeHtml = (value = '') => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        const formatDateTime = (value) => {
            if (!value) return 'Não registrado';
            const normalized = String(value).replace(' ', 'T');
            const date = new Date(normalized);
            if (Number.isNaN(date.getTime())) return String(value);
            return date.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        };
        const formatDateOnly = (value) => {
            if (!value) return 'Sem data';
            const date = new Date(`${value}T00:00:00`);
            if (Number.isNaN(date.getTime())) return String(value);
            return date.toLocaleDateString('pt-BR');
        };

        const parseProductData = (raw = '') => {
            try {
                return JSON.parse(raw);
            } catch (_) {
                try {
                    return JSON.parse(decodeURIComponent(raw));
                } catch (e) {
                    return {};
                }
            }
        };

        const serializeProduct = (obj) => encodeURIComponent(JSON.stringify(obj));

        const applyAutoSku = () => {
            const skuField = createForm?.querySelector('input[name="sku"]');
            const tipoField = createForm?.querySelector('select[name="tipo"]');
            if (!skuField || !tipoField) return;
            const nextPa = skuField.dataset.nextPa || 'A-001';
            const nextMp = skuField.dataset.nextMp || 'MP-001';
            skuField.value = tipoField.value === 'MP' ? nextMp : nextPa;
        };

        window.addEventListener('app:live-saved', (event) => {
            const detail = event.detail || {};
            if (detail.form === createForm) {
                const skuField = createForm?.querySelector('input[name="sku"]');
                createForm.reset();
                if (skuField) {
                    if (detail.payload?.next_sku_pa) {
                        skuField.dataset.nextPa = detail.payload.next_sku_pa;
                    }
                    if (detail.payload?.next_sku_mp) {
                        skuField.dataset.nextMp = detail.payload.next_sku_mp;
                    }
                }
                applyAutoSku();
                toggleMpRecebimento();
                createForm.querySelectorAll('input, select, textarea').forEach((field) => {
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                });
                createForm.querySelector('input[name="nome"]')?.focus();
                return;
            }

            if (!/\/produtos\/\d+\/excluir(?:\/|$|\?)/i.test(String(detail.action || ''))) {
                return;
            }
            const skuField = createForm?.querySelector('input[name="sku"]');
            if (!skuField) {
                return;
            }
            if (detail.payload?.next_sku_pa) {
                skuField.dataset.nextPa = detail.payload.next_sku_pa;
            }
            if (detail.payload?.next_sku_mp) {
                skuField.dataset.nextMp = detail.payload.next_sku_mp;
            }
            applyAutoSku();
        });

        const toggleMpRecebimento = () => {
            const tipoField = createForm?.querySelector('select[name="tipo"]');
            if (!mpRecebimento || !tipoField) return;
            mpRecebimento.classList.toggle('active', tipoField.value === 'MP');
        };

        if (createForm) {
            const tipoField = createForm.querySelector('select[name="tipo"]');
            tipoField?.addEventListener('change', () => {
                applyAutoSku();
                toggleMpRecebimento();
            });
            applyAutoSku();
            toggleMpRecebimento();
        }

        const updatePrintLink = () => {
            if (!printButtons.length) return;
            printButtons.forEach((button) => {
                const params = new URLSearchParams(buildQuery({ q: state.q, categoria: state.categoria, tipo: state.tipo }));
                if (button.dataset.printMode === 'simplificado') {
                    params.set('modo', 'simplificado');
                }
                const qs = params.toString();
                button.setAttribute('href', printBase + (qs ? '?' + qs : ''));
            });
        };

        const renderRows = (items) => {
            if (!items || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8">${table.dataset.empty}</td></tr>`;
                return;
            }

            tbody.innerHTML = items.map((item) => {
                const badgeClass = item.tipo === 'PA' ? 'badge-success' : 'badge-warning';
                const preco = formatPrice(item.preco);
                const categoria = item.categoria && item.categoria !== '' ? item.categoria : '—';
                const encoded = serializeProduct(item);
                const itemId = Number(item.id || 0);
                const isActive = Number(item.is_ativo ?? 1) === 1;
                window.productInfoMap = window.productInfoMap || {};
                if (!window.productInfoMap[itemId]) {
                    window.productInfoMap[itemId] = {
                        id: itemId,
                        sku: item.sku || '',
                        nome: item.nome || '',
                        shelf_life_dias: Number(item.shelf_life_dias || 0),
                        created_at: item.created_at || '',
                        updated_at: '',
                        prices: (window.historyPorProduto && window.historyPorProduto[itemId] ? window.historyPorProduto[itemId] : []).slice(0, 6),
                    };
                }

                return `
                    <tr>
                        <td>${itemId}</td>
                        <td>${item.sku ?? ''}</td>
                        <td>${item.nome ?? ''}</td>
                        <td>${categoria}</td>
                        <td><span class="badge ${badgeClass}">${item.tipo}</span></td>
                        <td>${item.unidade ?? ''}</td>
                        <td>R$ ${preco}</td>
                        <td>
                            <div class="product-actions">
                                <button type="button" class="product-icon-btn product-icon-btn--info js-product-info" data-product-id="${itemId}" data-tooltip="Informações" aria-label="Ver informações do produto">
                                    <span data-lucide="info" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="product-icon-btn js-edit-product" data-product="${encoded}" data-tooltip="Editar" aria-label="Editar produto">
                                    <span data-lucide="pencil" aria-hidden="true"></span>
                                </button>
                                <form class="product-status-form" method="post" action="${endpoint.replace(/\/$/, '')}/${itemId}/${isActive ? 'desativar' : 'ativar'}" ${isActive ? `onsubmit="return confirm('Desativar este produto? Ele deixara de aparecer em novos pedidos e relatorios.');"` : ''}>
                                    <button type="submit" class="product-status-switch ${isActive ? 'is-active' : ''}" title="${isActive ? 'Desativar produto' : 'Ativar produto'}" aria-label="${isActive ? 'Desativar produto' : 'Ativar produto'}">
                                        <span class="product-status-switch__icon"><span data-lucide="${isActive ? 'power' : 'power-off'}" aria-hidden="true"></span></span>
                                        <span>${isActive ? 'Ativo' : 'Inativo'}</span>
                                    </button>
                                </form>
                                <form class="product-delete-form" method="post" action="${endpoint.replace(/\/$/, '')}/${itemId}/excluir" onsubmit="return confirm('Excluir permanentemente este produto? Todos os registros vinculados a ele tambem serao apagados. Esta acao nao pode ser desfeita.');">
                                    <button type="submit" class="product-icon-btn product-icon-btn--danger" data-tooltip="Excluir permanentemente" aria-label="Excluir produto permanentemente">
                                        <span data-lucide="trash-2" aria-hidden="true"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            if (window.lucide) {
                window.lucide.createIcons({ elements: tbody.querySelectorAll('[data-lucide]') });
            }
        };

        const pageWindow = (currentPage, totalPages) => {
            const pages = [];
            const add = (page) => {
                if (page >= 1 && page <= totalPages && !pages.includes(page)) {
                    pages.push(page);
                }
            };
            add(1);
            for (let page = currentPage - 2; page <= currentPage + 2; page += 1) {
                add(page);
            }
            add(totalPages);
            pages.sort((left, right) => left - right);
            return pages.reduce((acc, page, index) => {
                if (index > 0 && page - pages[index - 1] > 1) {
                    acc.push('...');
                }
                acc.push(page);
                return acc;
            }, []);
        };

        const renderPagination = (totalRows) => {
            if (!paginationTarget) {
                return;
            }
            const totalPages = Math.max(1, Math.ceil(totalRows / paginationState.perPage));
            paginationState.page = Math.min(Math.max(1, paginationState.page), totalPages);
            const start = totalRows ? (paginationState.page - 1) * paginationState.perPage + 1 : 0;
            const end = Math.min(totalRows, paginationState.page * paginationState.perPage);
            const buttons = pageWindow(paginationState.page, totalPages).map((page) => {
                if (page === '...') {
                    return '<span class="list-page-ellipsis">...</span>';
                }
                const active = page === paginationState.page;
                return `<button type="button" class="list-page-button ${active ? 'active' : ''}" data-product-page="${page}" aria-current="${active ? 'page' : 'false'}">${page}</button>`;
            }).join('');
            const pageSizeChoices = Array.from(new Set([...pageSizeOptions, paginationState.perPage])).sort((left, right) => left - right);
            const options = pageSizeChoices.map((size) => `<option value="${size}" ${size === paginationState.perPage ? 'selected' : ''}>${size}</option>`).join('');
            paginationTarget.innerHTML = `<span>${start}-${end} de ${totalRows}</span>
                <div class="list-pagination-pages">${buttons}<label>Ir para <input class="list-page-input" type="number" min="1" max="${totalPages}" value="${paginationState.page}" data-product-page-input></label></div>
                <label class="list-pagination-size"><span>Mostrar</span><select class="list-page-size-select" data-product-page-size>${options}</select><input class="list-page-input" type="number" min="1" max="500" value="${paginationState.perPage}" data-product-page-size-manual aria-label="Quantidade manual por página"><span>por página</span></label>`;
        };

        const renderProductPage = () => {
            if (currentCatalogItems) {
                const totalPages = Math.max(1, Math.ceil(currentCatalogItems.length / paginationState.perPage));
                paginationState.page = Math.min(Math.max(1, paginationState.page), totalPages);
                const startIndex = (paginationState.page - 1) * paginationState.perPage;
                renderRows(currentCatalogItems.slice(startIndex, startIndex + paginationState.perPage));
                renderPagination(currentCatalogItems.length);
                return;
            }
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const totalRows = rows.length && !rows[0].querySelector('td[colspan]') ? rows.length : 0;
            const totalPages = Math.max(1, Math.ceil(totalRows / paginationState.perPage));
            paginationState.page = Math.min(Math.max(1, paginationState.page), totalPages);
            const startIndex = (paginationState.page - 1) * paginationState.perPage;
            const endIndex = startIndex + paginationState.perPage;
            rows.forEach((row, index) => {
                row.hidden = totalRows > 0 && (index < startIndex || index >= endIndex);
            });
            renderPagination(totalRows);
        };

        const refreshSortIndicators = () => {
            sortLinks.forEach((link) => {
                const col = link.dataset.sort;
                const indicator = link.querySelector('.sort-indicator');
                const isActive = state.sort === col;
                const nextDir = isActive && state.dir === 'asc' ? 'desc' : 'asc';
                link.dataset.next = nextDir;
                link.dataset.active = state.sort;
                link.dataset.direction = state.dir;

                if (indicator) {
                    indicator.textContent = isActive ? (state.dir === 'asc' ? '▲' : '▼') : '⇅';
                    indicator.classList.toggle('active', isActive);
                }
            });
        };

        const updateResultsLabel = (count) => {
            if (!resultMeta) return;
            const plural = count === 1 ? 'item' : 'itens';
            resultMeta.textContent = `Resultados: ${count} ${plural}`;
        };

        const fetchCatalog = () => {
            const qs = buildQuery({
                q: state.q,
                categoria: state.categoria,
                tipo: state.tipo,
                sort: state.sort,
                dir: state.dir,
            });
            const url = endpoint + (qs ? '?' + qs : '');
            form.classList.add('is-loading');
            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then((res) => res.json())
                .then((payload) => {
                    const items = payload.data || [];
                    currentCatalogItems = items;
                    paginationState.page = 1;
                    renderProductPage();
                    updateResultsLabel(items.length);
                    refreshSortIndicators();
                    updatePrintLink();
                })
                .catch(() => {
                    // fallback: keep existing table content
                })
                .finally(() => form.classList.remove('is-loading'));
        };

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            state.q = form.querySelector('input[name="q"]').value.trim();
            state.categoria = form.querySelector('select[name="categoria"]').value;
            state.tipo = form.querySelector('select[name="tipo"]').value;
            paginationState.page = 1;
            fetchCatalog();
        });

        form.querySelectorAll('input, select').forEach((el) => {
            el.addEventListener('change', () => {
                state.q = form.querySelector('input[name="q"]').value.trim();
                state.categoria = form.querySelector('select[name="categoria"]').value;
                state.tipo = form.querySelector('select[name="tipo"]').value;
            });
        });

        sortLinks.forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const col = link.dataset.sort;
                const next = link.dataset.next || 'asc';
                state.sort = col;
                state.dir = next;
                paginationState.page = 1;
                fetchCatalog();
            });
        });

        paginationTarget?.addEventListener('click', (event) => {
            const button = event.target instanceof Element ? event.target.closest('[data-product-page]') : null;
            if (!button) {
                return;
            }
            paginationState.page = Number(button.dataset.productPage || 1);
            renderProductPage();
        });

        paginationTarget?.addEventListener('change', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement || target instanceof HTMLSelectElement)) {
                return;
            }
            if (target.matches('[data-product-page-size]')) {
                paginationState.perPage = Number(target.value) || paginationState.perPage;
                paginationState.page = 1;
                renderProductPage();
            }
            if (target.matches('[data-product-page-size-manual]')) {
                paginationState.perPage = Math.min(500, Math.max(1, Number(target.value) || paginationState.perPage));
                paginationState.page = 1;
                renderProductPage();
            }
            if (target.matches('[data-product-page-input]')) {
                paginationState.page = Number(target.value) || 1;
                renderProductPage();
            }
        });

        paginationTarget?.addEventListener('keydown', (event) => {
            const target = event.target;
            if (event.key !== 'Enter' || !(target instanceof HTMLInputElement) || !target.matches('[data-product-page-input], [data-product-page-size-manual]')) {
                return;
            }
            event.preventDefault();
            if (target.matches('[data-product-page-size-manual]')) {
                paginationState.perPage = Math.min(500, Math.max(1, Number(target.value) || paginationState.perPage));
                paginationState.page = 1;
                renderProductPage();
                return;
            }
            paginationState.page = Number(target.value) || 1;
            renderProductPage();
        });

        renderProductPage();

        const closeModal = () => {
            if (!editModal) return;
            editModal.classList.add('hidden');
            editModal.setAttribute('aria-hidden', 'true');
        };

        const openModal = () => {
            if (!editModal) return;
            editModal.classList.remove('hidden');
            editModal.setAttribute('aria-hidden', 'false');
        };

        const fillEditForm = (product) => {
            if (!editForm || !product) return;
            editForm.querySelector('input[name="id"]').value = product.id || '';
            editForm.querySelector('input[name="sku"]').value = product.sku || '';
            editForm.querySelector('input[name="nome"]').value = product.nome || '';
            editForm.querySelector('select[name="tipo"]').value = product.tipo || 'PA';
            editForm.querySelector('input[name="categoria"]').value = product.categoria || '';
            const unidadeSelect = editForm.querySelector('select[name="unidade"]');
            if (unidadeSelect) {
                let hasUnit = false;
                Array.from(unidadeSelect.options).forEach((opt) => {
                    if (opt.value === product.unidade) {
                        hasUnit = true;
                    }
                });
                if (!hasUnit && product.unidade) {
                    const extra = document.createElement('option');
                    extra.value = product.unidade;
                    extra.textContent = product.unidade;
                    unidadeSelect.appendChild(extra);
                }
                unidadeSelect.value = product.unidade || unidadeSelect.value;
            }
            editForm.querySelector('input[name="peso_liquido"]').value = product.peso_liquido || '';
            editForm.querySelector('input[name="shelf_life_dias"]').value = product.shelf_life_dias || '';
            editForm.querySelector('input[name="preco"]').value = product.preco || '';
        };

        const infoModal = document.getElementById('product-info-modal');
        const infoTitle = infoModal?.querySelector('#product-info-title');
        const infoCreated = infoModal?.querySelector('[data-product-info-created]');
        const infoUpdated = infoModal?.querySelector('[data-product-info-updated]');
        const infoValidity = infoModal?.querySelector('[data-product-info-validity]');
        const infoPrices = infoModal?.querySelector('[data-product-info-prices]');
        const closeInfoModal = () => {
            if (!infoModal) return;
            infoModal.classList.add('hidden');
            infoModal.setAttribute('aria-hidden', 'true');
        };
        const openInfoModal = (productId) => {
            if (!infoModal) return;
            const info = (window.productInfoMap || {})[productId] || {};
            if (infoTitle) {
                const sku = info.sku ? `${info.sku} - ` : '';
                infoTitle.textContent = `${sku}${info.nome || 'Produto'}`;
            }
            if (infoCreated) {
                infoCreated.textContent = formatDateTime(info.created_at);
            }
            if (infoUpdated) {
                infoUpdated.textContent = info.updated_at ? formatDateTime(info.updated_at) : 'Sem alteração registrada';
            }
            if (infoValidity) {
                const validityDays = Number(info.shelf_life_dias || 0);
                infoValidity.textContent = `${validityDays} ${validityDays === 1 ? 'dia' : 'dias'}`;
            }
            const prices = Array.isArray(info.prices) ? info.prices.slice(0, 6) : [];
            if (infoPrices) {
                infoPrices.innerHTML = prices.length
                    ? prices.map((item) => {
                        const mainDate = item.created_at || item.data_entrega || item.data_fabricacao || item.data_vencimento || '';
                        const detailDates = [
                            item.data_entrega ? `Entrega: ${formatDateOnly(item.data_entrega)}` : '',
                            item.data_fabricacao ? `Fabricação: ${formatDateOnly(item.data_fabricacao)}` : '',
                            item.data_vencimento ? `Vencimento: ${formatDateOnly(item.data_vencimento)}` : '',
                        ].filter(Boolean).join(' · ');
                        return `<div class="product-price-row">
                            <div>
                                <span>${escapeHtml(formatDateTime(mainDate))}</span>
                                <strong>R$ ${escapeHtml(formatPrice(item.preco))}</strong>
                                ${detailDates ? `<small>${escapeHtml(detailDates)}</small>` : ''}
                            </div>
                            ${item.fornecedor ? `<strong>${escapeHtml(item.fornecedor)}</strong>` : ''}
                        </div>`;
                    }).join('')
                    : '<div class="product-price-empty">Nenhuma atualização de preço registrada.</div>';
            }
            infoModal.classList.remove('hidden');
            infoModal.setAttribute('aria-hidden', 'false');
            if (window.lucide) {
                window.lucide.createIcons({ elements: infoModal.querySelectorAll('[data-lucide]') });
            }
        };

        table.addEventListener('click', (e) => {
            const infoBtn = e.target.closest('.js-product-info');
            if (infoBtn) {
                e.preventDefault();
                openInfoModal(Number(infoBtn.dataset.productId || 0));
                return;
            }
            const btn = e.target.closest('.js-edit-product');
            if (!btn) return;
            e.preventDefault();
            const product = parseProductData(btn.dataset.product || '');
            fillEditForm(product);
            openModal();
        });

        infoModal?.querySelectorAll('[data-product-info-close]').forEach((el) => {
            el.addEventListener('click', (ev) => {
                ev.preventDefault();
                closeInfoModal();
            });
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeInfoModal();
            }
        });

        [editClose, editCancel, editModal?.querySelector('.modal-backdrop')].forEach((el) => {
            if (!el) return;
            el.addEventListener('click', (ev) => {
                ev.preventDefault();
                closeModal();
            });
        });

        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const id = editForm.querySelector('input[name="id"]').value;
                const actionBase = editForm.dataset.endpoint || '';
                const url = `${actionBase}/${id}/update`;
                const formData = new FormData(editForm);
                fetch(url, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                })
                    .then((res) => res.json())
                    .then(() => {
                        closeModal();
                        fetchCatalog();
                    })
                    .catch(() => {
                        closeModal();
                    });
            });
        }

        updatePrintLink();
        refreshSortIndicators();

        if (tabButtons.length && tabPanels.length) {
            const activateTab = (tab) => {
                tabButtons.forEach((btn) => {
                    const isActive = btn.dataset.tab === tab;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                tabPanels.forEach((panel) => {
                    panel.classList.toggle('active', panel.dataset.tabPanel === tab);
                });
            };

            tabButtons.forEach((btn) => {
                btn.addEventListener('click', () => activateTab(btn.dataset.tab));
            });

            const params = new URLSearchParams(window.location.search);
            const initialTab = params.get('tab');
            if (initialTab) {
                activateTab(initialTab);
            }
        }

        const renderHistoryTable = (productId, productName = '') => {
            const data = window.historyPorProduto?.[productId] || [];
            if (historyProductTitle) {
                historyProductTitle.textContent = productName || 'Produto';
            }
            if (!historyTableBody) return;
            if (!data.length) {
                historyTableBody.innerHTML = '<tr><td colspan="7">Nenhum lançamento registrado para este produto.</td></tr>';
                return;
            }
            const formatDate = (value) => {
                if (!value) return '—';
                const parts = value.split('-');
                if (parts.length !== 3) return '—';
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            };
            const formatPrice = (value) => {
                return Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };
            historyTableBody.innerHTML = data.map((item) => `
                <tr>
                    <td>${item.nome ?? ''}</td>
                    <td>R$ ${formatPrice(item.preco)}</td>
                    <td>${item.fornecedor ?? '—'}</td>
                    <td>${formatDate(item.data_entrega)}</td>
                    <td>${item.lote ?? '—'}</td>
                    <td>${formatDate(item.data_fabricacao)}</td>
                    <td>${formatDate(item.data_vencimento)}</td>
                </tr>
            `).join('');
        };

        if (historyButtons.length) {
            historyButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const productId = btn.dataset.product;
                    const productName = btn.closest('.history-item')?.querySelector('.history-title')?.textContent?.trim() || '';
                    if (productId) {
                        renderHistoryTable(productId, productName);
                    }
                    const targetTab = btn.dataset.historyTarget;
                    if (targetTab) {
                        const targetButton = tabButtons.find((tab) => tab.dataset.tab === targetTab);
                        targetButton?.click();
                    }
                });
            });
        }

        const applyHistoryFilters = () => {
            if (!historyList) return;
            const term = (historySearch?.value || '').toLowerCase().trim();
            const filter = historyFilter?.value || 'ALL';
            const items = Array.from(historyList.querySelectorAll('.history-item'));
            items.forEach((item) => {
                const name = item.dataset.name || '';
                const count = Number(item.dataset.count || 0);
                const matchesSearch = term === '' || name.includes(term);
                const matchesFilter = filter === 'ALL'
                    || (filter === 'COM' && count > 0)
                    || (filter === 'SEM' && count === 0);
                item.style.display = matchesSearch && matchesFilter ? '' : 'none';
            });
        };

        historySearch?.addEventListener('input', applyHistoryFilters);
        historyFilter?.addEventListener('change', applyHistoryFilters);
        applyHistoryFilters();

        historyBack?.addEventListener('click', () => {
            const targetButton = tabButtons.find((tab) => tab.dataset.tab === 'lancamento');
            targetButton?.click();
        });

        if (lancamentoSelect) {
            const preencherLancamento = () => {
                const option = lancamentoSelect.options[lancamentoSelect.selectedIndex];
                if (!option || !option.value) {
                    Object.values(lancamentoFields).forEach((field) => {
                        if (field) field.value = '';
                    });
                    return;
                }

                if (lancamentoFields.preco) {
                    lancamentoFields.preco.value = option.dataset.preco || '';
                }
                if (lancamentoFields.fornecedor) {
                    lancamentoFields.fornecedor.value = option.dataset.fornecedor || '';
                }
                if (lancamentoFields.dataEntrega) {
                    lancamentoFields.dataEntrega.value = option.dataset.dataEntrega || '';
                }
                if (lancamentoFields.lote) {
                    lancamentoFields.lote.value = option.dataset.lote || '';
                }
                if (lancamentoFields.dataFabricacao) {
                    lancamentoFields.dataFabricacao.value = option.dataset.dataFabricacao || '';
                }
                if (lancamentoFields.dataVencimento) {
                    lancamentoFields.dataVencimento.value = option.dataset.dataVencimento || '';
                }
            };

            lancamentoSelect.addEventListener('change', preencherLancamento);
            preencherLancamento();
        }
    })();
</script>
