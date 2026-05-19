<style>
  .pedido-edit-summary {
    display: grid;
    grid-template-columns: minmax(260px, 1fr) minmax(220px, 320px);
    gap: 16px;
    align-items: stretch;
    margin-top: 18px;
  }

  .pedido-edit-frete-card,
  .pedido-edit-summary .item-total {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
  }

  .pedido-edit-frete-card {
    padding: 16px;
  }

  .pedido-edit-frete-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 12px;
  }

  .pedido-edit-frete-title i {
    color: var(--accent, #dc2626);
  }

  .pedido-edit-frete-row {
    display: grid;
    grid-template-columns: auto minmax(160px, 1fr);
    gap: 14px;
    align-items: end;
  }

  .pedido-edit-frete-value label {
    display: block;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 6px;
  }

  .pedido-edit-frete-value input {
    width: 100%;
    min-height: 46px;
    border: 1px solid #dbe3ee;
    border-radius: 14px;
    padding: 0 14px;
    font-weight: 800;
    color: #0f172a;
    background: #f8fafc;
  }

  .pedido-edit-frete-toggle {
    display: inline-flex;
    align-items: center;
    min-width: 86px;
    height: 42px;
    border-radius: 999px;
    padding: 4px;
    background: #e5e7eb;
    cursor: pointer;
    user-select: none;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, .08);
  }

  .pedido-edit-frete-toggle input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .pedido-edit-frete-track {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    height: 100%;
    border-radius: inherit;
    padding: 0 12px;
    color: #64748b;
    font-size: 13px;
    font-weight: 900;
    transition: background .2s ease, color .2s ease;
  }

  .pedido-edit-frete-track::after {
    content: '';
    position: absolute;
    top: 4px;
    left: 4px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .18);
    transition: transform .2s ease;
  }

  .pedido-edit-frete-track::before {
    content: 'Não';
    padding-left: 30px;
  }

  .pedido-edit-frete-toggle input:checked + .pedido-edit-frete-track {
    justify-content: flex-start;
    background: var(--accent, #dc2626);
    color: #fff;
  }

  .pedido-edit-frete-toggle input:checked + .pedido-edit-frete-track::after {
    transform: translateX(42px);
  }

  .pedido-edit-frete-toggle input:checked + .pedido-edit-frete-track::before {
    content: 'Sim';
    padding-left: 6px;
  }

  .pedido-edit-summary .item-total {
    margin-top: 0;
    padding: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .pedido-edit-summary .item-total span {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 800;
    color: #64748b;
  }

  .pedido-edit-summary .item-total span::before {
    content: '$';
    display: grid;
    place-items: center;
    width: 30px;
    height: 30px;
    border-radius: 10px;
    background: rgba(220, 38, 38, .1);
    color: var(--accent, #dc2626);
    font-weight: 900;
  }

  .pedido-edit-summary .item-total strong {
    font-size: 24px;
    color: #0f172a;
  }

  @media (max-width: 760px) {
    .pedido-edit-summary,
    .pedido-edit-frete-row {
      grid-template-columns: 1fr;
    }
  }
</style>
<script>
(function () {
  const basePedidos = <?= json_encode(rtrim($url('pedidos'), '/'), JSON_UNESCAPED_SLASHES); ?>;
  const moneyFormatter = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

  function parseNumber(value) {
    return Number(String(value || '0').replace(/\./g, '').replace(',', '.')) || 0;
  }

  function setupEditFreight() {
    const form = document.getElementById('pedido-edit-form');
    const grid = document.getElementById('pedido-edit-grid');
    const totalCard = document.querySelector('.pedido-edit-items .item-total');
    const totalEl = document.getElementById('pedido-edit-total');
    if (!form || !grid || !totalCard || !totalEl || totalCard.dataset.freightReady === '1') {
      return;
    }

    totalCard.dataset.freightReady = '1';
    totalCard.querySelector('span').textContent = 'Valor total';

    const summary = document.createElement('div');
    summary.className = 'pedido-edit-summary';

    const freightCard = document.createElement('div');
    freightCard.className = 'pedido-edit-frete-card';
    freightCard.innerHTML = `
      <div class="pedido-edit-frete-title"><i data-lucide="truck"></i><span>Frete</span></div>
      <div class="pedido-edit-frete-row">
        <label class="pedido-edit-frete-toggle" title="Ativar frete">
          <input type="checkbox" name="frete_ativo" value="1" id="pedido-edit-frete-ativo">
          <span class="pedido-edit-frete-track"></span>
        </label>
        <div class="pedido-edit-frete-value">
          <label for="pedido-edit-frete-valor">Valor do frete</label>
          <input type="number" min="0" step="0.01" name="frete_valor" id="pedido-edit-frete-valor" value="0.00" inputmode="decimal">
        </div>
      </div>
    `;

    totalCard.parentNode.insertBefore(summary, totalCard);
    summary.appendChild(freightCard);
    summary.appendChild(totalCard);

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }

    const freightToggle = document.getElementById('pedido-edit-frete-ativo');
    const freightInput = document.getElementById('pedido-edit-frete-valor');

    function getItemsTotal() {
      let total = 0;
      grid.querySelectorAll('.item-row[data-row]').forEach((row) => {
        const qtd = parseNumber(row.querySelector('[data-field="quantidade"]')?.value);
        const preco = parseNumber(row.querySelector('[data-field="preco"]')?.value);
        total += qtd * preco;
      });
      return total;
    }

    function updateFreightState() {
      const enabled = freightToggle.checked;
      freightInput.disabled = !enabled;
      freightInput.style.opacity = enabled ? '1' : '.55';
      if (!enabled) {
        freightInput.value = '0.00';
      }
    }

    function updateTotal() {
      updateFreightState();
      const freight = freightToggle.checked ? parseNumber(freightInput.value) : 0;
      totalEl.textContent = moneyFormatter.format(getItemsTotal() + freight);
    }

    function setFreight(value) {
      const freight = Number(value || 0);
      freightToggle.checked = freight > 0;
      freightInput.value = freight.toFixed(2);
      updateTotal();
    }

    function loadFreight(pedidoId) {
      if (!pedidoId) {
        setFreight(0);
        return;
      }
      fetch(`${basePedidos}/${pedidoId}/editar`, { headers: { 'Accept': 'application/json' } })
        .then((response) => response.ok ? response.json() : Promise.reject(response))
        .then((payload) => setFreight(payload?.pedido?.frete_valor || 0))
        .catch(() => setFreight(0));
    }

    document.addEventListener('click', (event) => {
      const button = event.target instanceof Element ? event.target.closest('.pedido-edit-btn') : null;
      if (!button) {
        return;
      }
      setTimeout(() => loadFreight(button.dataset.pedido), 180);
    });

    grid.addEventListener('input', updateTotal);
    grid.addEventListener('change', updateTotal);
    grid.addEventListener('click', () => setTimeout(updateTotal, 0));
    freightToggle.addEventListener('change', updateTotal);
    freightInput.addEventListener('input', updateTotal);
    form.addEventListener('submit', updateTotal, true);

    new MutationObserver(() => setTimeout(updateTotal, 0)).observe(grid, { childList: true, subtree: true });
    updateTotal();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupEditFreight);
  } else {
    setupEditFreight();
  }
})();
</script>
