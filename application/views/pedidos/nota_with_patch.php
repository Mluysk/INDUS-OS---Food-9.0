<?php
$__freteValor = max(0.0, (float)($pedido['frete_valor'] ?? 0));
$__itensSubtotal = array_reduce($itens ?? [], static function ($carry, $item) {
    $quantidade = (float)($item['quantidade'] ?? 0);
    $preco = (float)($item['preco_unit'] ?? 0);
    return $carry + ($quantidade * $preco);
}, 0.0);
$__formatMoney = static fn ($value) => 'R$ ' . number_format((float)$value, 2, ',', '.');
include __DIR__.'/nota.php';

if ($__freteValor > 0):
?>
<style>
  .nota-frete-row td {
    background: #fff7ed;
    font-weight: 800;
  }

  .nota-frete-row td:nth-child(2) {
    color: #0f172a;
  }
</style>
<script>
(function () {
  const freightMoney = <?= json_encode($__formatMoney($__freteValor), JSON_UNESCAPED_UNICODE); ?>;
  const itemsSubtotal = <?= json_encode($__formatMoney($__itensSubtotal), JSON_UNESCAPED_UNICODE); ?>;
  const tbody = document.querySelector('.nota-table tbody');

  if (tbody && !tbody.querySelector('.nota-frete-row')) {
    const row = document.createElement('tr');
    row.className = 'nota-frete-row';
    row.innerHTML = `
      <td>FRETE</td>
      <td>Frete</td>
      <td>1,00</td>
      <td>-</td>
      <td>${freightMoney}</td>
      <td>${freightMoney}</td>
    `;
    tbody.appendChild(row);
  }

  const totalRows = document.querySelectorAll('.total-box .total-row');
  const subtotalValue = totalRows[0]?.querySelector('span:last-child');
  if (subtotalValue) {
    subtotalValue.textContent = itemsSubtotal;
  }
})();
</script>
<?php endif; ?>
