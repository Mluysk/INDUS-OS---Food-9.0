<section class="panel cliente-detail">
    <div class="detail-header">
        <div>
            <h2><?= htmlspecialchars($cliente['razao_social'] ?? $cliente['nome_fantasia'] ?? 'Cliente', ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if (!empty($cliente['email'])): ?>
                <p class="panel-description">Contato principal: <?= htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>
        <a class="button" href="<?= htmlspecialchars($url('clientes'), ENT_QUOTES, 'UTF-8'); ?>">Voltar para a lista</a>
    </div>

    <dl class="detail-grid">
        <div><dt>ID</dt><dd><?= (int) $cliente['id']; ?></dd></div>
        <div><dt>Razão social</dt><dd><?= htmlspecialchars($cliente['razao_social'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Nome fantasia</dt><dd><?= htmlspecialchars($cliente['nome_fantasia'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>CNPJ</dt><dd><?= htmlspecialchars($cliente['cnpj'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>CPF</dt><dd><?= htmlspecialchars($cliente['cpf'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Contato</dt><dd><?= htmlspecialchars($cliente['telefone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Tipo contato</dt><dd><?= htmlspecialchars(ucfirst((string)($cliente['contato_tipo'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>WhatsApp</dt><dd><?= htmlspecialchars($cliente['whatsapp'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>CEP</dt><dd><?= htmlspecialchars($cliente['cep'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Endereço</dt><dd><?= htmlspecialchars($cliente['endereco'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Número</dt><dd><?= htmlspecialchars($cliente['numero'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Bairro</dt><dd><?= htmlspecialchars($cliente['bairro'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Cidade</dt><dd><?= htmlspecialchars($cliente['cidade'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Estado</dt><dd><?= htmlspecialchars($cliente['estado'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
        <div><dt>Criado em</dt><dd><?= htmlspecialchars($cliente['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd></div>
    </dl>
</section>

<style>
    .cliente-detail .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .cliente-detail .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.2rem 1.6rem;
        margin-top: 1.5rem;
    }

    .cliente-detail dt {
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
    }

    .cliente-detail dd {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-strong);
    }
</style>
