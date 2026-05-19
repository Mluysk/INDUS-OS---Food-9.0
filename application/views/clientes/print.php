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

$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i');
$logo = $asset($theme['mode'] === 'dark' ? ($theme['logo_dark'] ?? 'img/logodark.png') : ($theme['logo_light'] ?? 'img/logo.png'));
?>
<header class="print-header">
    <div class="print-brand">
        <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?>">
        <div>
            <h1>Clientes cadastrados</h1>
            <p class="print-meta">Atualizado em <?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
    <button class="print-button" type="button" onclick="window.print()">Imprimir</button>
</header>

<?php if (!empty($clientes)): ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Razão social</th>
            <th>Nome fantasia</th>
            <th>CNPJ</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Contato</th>
            <th>Tipo</th>
            <th>WhatsApp</th>
            <th>Endereço</th>
            <th>Cidade / UF</th>
            <th>CEP</th>
            <th>Criado em</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= (int) $cliente['id']; ?></td>
                <td><?= htmlspecialchars($cliente['razao_social'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($cliente['nome_fantasia'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($formatDocumento($cliente['cnpj'] ?? null, 'cnpj'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($formatDocumento($cliente['cpf'] ?? null, 'cpf'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($cliente['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($cliente['telefone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars(ucfirst((string)($cliente['contato_tipo'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($cliente['whatsapp'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?= htmlspecialchars(trim(($cliente['endereco'] ?? '') . ' ' . ($cliente['numero'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($cliente['bairro'])): ?>
                        <br><small>Bairro: <?= htmlspecialchars($cliente['bairro'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars(trim(($cliente['cidade'] ?? '') . ' / ' . ($cliente['estado'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($formatCep($cliente['cep'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($cliente['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-data">Nenhum cliente cadastrado até o momento.</div>
<?php endif; ?>
