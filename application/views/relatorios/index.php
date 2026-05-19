<section class="panel">
    <header class="panel-header">
        <div>
            <h1>Biblioteca de relatórios</h1>
            <p class="panel-description">Escolha o formato ideal para compartilhar indicadores de pedidos, estoque e finanças.</p>
        </div>
    </header>
    <div class="report-grid">
        <?php foreach ($links as $link): ?>
            <article class="report-card">
                <div class="report-card-icon" data-lucide="<?= htmlspecialchars($link['icon'], ENT_QUOTES, 'UTF-8'); ?>"></div>
                <div class="report-card-body">
                    <h2><?= htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?= htmlspecialchars($link['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <a class="report-card-action" href="<?= htmlspecialchars($url($link['href']), ENT_QUOTES, 'UTF-8'); ?>" <?= $link['target'] ? 'target="'.htmlspecialchars($link['target'], ENT_QUOTES, 'UTF-8').'" rel="noopener"' : ''; ?>>
                    <span>Gerar</span>
                    <i data-lucide="arrow-up-right"></i>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<style>
    .panel-header > div h1 {
        font-size: 1.5rem;
        margin: 0;
    }
    .panel-header > div .panel-description {
        margin-top: 6px;
    }
    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
        margin-top: 24px;
    }
    .report-card {
        position: relative;
        display: grid;
        grid-template-columns: auto 1fr;
        grid-template-rows: auto auto;
        gap: 12px 16px;
        padding: 20px;
        border-radius: 18px;
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 26px 48px -38px rgba(15, 23, 42, 0.28);
    }
    .report-card-icon {
        grid-row: span 2;
        align-self: flex-start;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: var(--primary-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }
    .report-card-icon svg {
        width: 22px;
        height: 22px;
    }
    .report-card-body h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text);
    }
    .report-card-body p {
        margin: 6px 0 0;
        font-size: 0.82rem;
        color: var(--text-muted);
        line-height: 1.4;
    }
    .report-card-action {
        grid-column: 1 / -1;
        justify-self: flex-end;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--primary);
        background: rgba(91, 97, 111, 0.08);
        border: 1px solid rgba(91, 97, 111, 0.16);
        transition: background 0.2s ease, color 0.2s ease;
    }
    .report-card-action:hover {
        background: var(--primary);
        color: #fff;
    }
    .report-card-action svg {
        width: 16px;
        height: 16px;
    }
</style>
