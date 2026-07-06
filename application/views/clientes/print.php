<?php
$formatDocumento = function (?string $value, string $type): string {
    if (!$value) {
        return '-';
    }

    $digits = preg_replace('/\D+/', '', $value);
    if ($type === 'cpf' && strlen($digits) === 11) {
        return sprintf('%s.%s.%s-%s', substr($digits, 0, 3), substr($digits, 3, 3), substr($digits, 6, 3), substr($digits, 9));
    }

    if ($type === 'cnpj' && strlen($digits) === 14) {
        return sprintf('%s.%s.%s/%s-%s', substr($digits, 0, 2), substr($digits, 2, 3), substr($digits, 5, 3), substr($digits, 8, 4), substr($digits, 12));
    }

    return $value ?: '-';
};

$formatCep = function (?string $value): string {
    if (!$value) {
        return '-';
    }

    $digits = preg_replace('/\D+/', '', $value);
    if (strlen($digits) === 8) {
        return substr($digits, 0, 5) . '-' . substr($digits, 5);
    }

    return $value;
};

$formatDateTime = static function (?string $value): string {
    if (!$value) {
        return '-';
    }

    try {
        return (new DateTime($value))->format('d/m/Y H:i');
    } catch (Throwable) {
        return $value;
    }
};

$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i');
$logo = $asset($sidebarLogo['path'] ?? ($theme['logo_light'] ?? 'img/logo.png'));
$totalClientes = count($clientes ?? []);
?>
<style>
    .clients-print-table {
        width: 100%;
        table-layout: fixed;
        font-size: 12px;
        line-height: 1.35;
    }

    .clients-print-table col:nth-child(1) { width: 4%; }
    .clients-print-table col:nth-child(2) { width: 19%; }
    .clients-print-table col:nth-child(3) { width: 17%; }
    .clients-print-table col:nth-child(4) { width: 23%; }
    .clients-print-table col:nth-child(5) { width: 29%; }
    .clients-print-table col:nth-child(6) { width: 8%; }

    .clients-print-table th,
    .clients-print-table td {
        padding: 8px 7px;
        vertical-align: top;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .clients-print-table th {
        letter-spacing: 0.04em;
    }

    .clients-print-table thead {
        display: table-header-group;
    }

    .clients-print-table tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .clients-print-cell {
        display: grid;
        gap: 3px;
        min-width: 0;
    }

    .clients-print-cell strong {
        font-size: 12.5px;
        line-height: 1.3;
    }

    .clients-print-line {
        min-width: 0;
    }

    .clients-print-label {
        color: var(--muted);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .clients-print-date {
        white-space: normal;
    }

    @page {
        size: A4 landscape;
        margin: 7mm;
    }

    @media print {
        .print-header {
            margin-bottom: 12px;
        }

        .print-header img {
            height: 42px;
        }

        .print-header h1 {
            font-size: 20px;
        }

        .clients-print-table {
            margin-bottom: 0;
            font-size: 9.5px;
            line-height: 1.25;
        }

        .clients-print-table th,
        .clients-print-table td {
            padding: 5px 4px;
        }

        .clients-print-table th {
            font-size: 8.5px;
        }

        .clients-print-table .clients-print-cell {
            gap: 2px;
        }

        .clients-print-table .clients-print-cell strong {
            font-size: 10px;
        }

        .clients-print-table .clients-print-label {
            font-size: 7.5px;
        }
    }
</style>
<header class="print-header">
    <div class="print-brand">
        <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?>">
        <div>
            <h1>Clientes cadastrados</h1>
            <p class="print-meta">Atualizado em <?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="print-meta"><strong><?= $totalClientes; ?></strong> <?= $totalClientes === 1 ? 'cliente cadastrado' : 'clientes cadastrados'; ?></p>
        </div>
    </div>
    <button class="print-button" type="button" onclick="window.print()">Imprimir</button>
</header>

<?php if (!empty($clientes)): ?>
    <table class="clients-print-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Documentos</th>
            <th>Contato</th>
            <th>Endereço</th>
            <th>Cadastro</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= (int) $cliente['id']; ?></td>
                <td>
                    <div class="clients-print-cell">
                        <strong><?= htmlspecialchars($cliente['razao_social'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="clients-print-line">
                            <span class="clients-print-label">Fantasia:</span>
                            <?= htmlspecialchars($cliente['nome_fantasia'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                </td>
                <td>
                    <div class="clients-print-cell">
                        <span class="clients-print-line"><span class="clients-print-label">CNPJ:</span> <?= htmlspecialchars($formatDocumento($cliente['cnpj'] ?? null, 'cnpj'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="clients-print-line"><span class="clients-print-label">CPF:</span> <?= htmlspecialchars($formatDocumento($cliente['cpf'] ?? null, 'cpf'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="clients-print-line"><span class="clients-print-label">IE:</span> <?= htmlspecialchars($cliente['ie'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </td>
                <td>
                    <div class="clients-print-cell">
                        <span class="clients-print-line"><span class="clients-print-label">E-mail:</span> <?= htmlspecialchars($cliente['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="clients-print-line"><span class="clients-print-label">Telefone:</span> <?= htmlspecialchars($cliente['telefone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="clients-print-line"><span class="clients-print-label">Tipo:</span> <?= htmlspecialchars(ucfirst((string)($cliente['contato_tipo'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="clients-print-line"><span class="clients-print-label">WhatsApp:</span> <?= htmlspecialchars($cliente['whatsapp'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </td>
                <td>
                    <div class="clients-print-cell">
                        <span class="clients-print-line">
                            <span class="clients-print-label">Logradouro:</span>
                            <?= htmlspecialchars(trim(($cliente['endereco'] ?? '') . ' ' . ($cliente['numero'] ?? '')) ?: '-', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                        <span class="clients-print-line"><span class="clients-print-label">Bairro:</span> <?= htmlspecialchars($cliente['bairro'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="clients-print-line">
                            <span class="clients-print-label">Cidade/UF:</span>
                            <?= htmlspecialchars(trim(($cliente['cidade'] ?? '') . ' / ' . ($cliente['estado'] ?? ''), ' /') ?: '-', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                        <span class="clients-print-line"><span class="clients-print-label">CEP:</span> <?= htmlspecialchars($formatCep($cliente['cep'] ?? null), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </td>
                <td class="clients-print-date"><?= htmlspecialchars($formatDateTime($cliente['created_at'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-data">Nenhum cliente cadastrado até o momento.</div>
<?php endif; ?>
