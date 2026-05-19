<style>
    .cliente-form .uf-flag-field {
        position: relative;
        max-width: 150px;
    }

    .cliente-form .uf-flag-field input[name="estado"] {
        width: 100%;
        padding-right: 52px;
        text-transform: uppercase;
    }

    .cliente-form .uf-flag-badge {
        position: absolute;
        right: 10px;
        bottom: 9px;
        display: none;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 24px;
        border-radius: 6px;
        overflow: hidden;
        background: #eef2f7;
        border: 1px solid #dbe3ee;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .12);
        pointer-events: none;
    }

    .cliente-form .uf-flag-badge img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cliente-form .uf-flag-field.has-flag .uf-flag-badge {
        display: flex;
    }
</style>

<script>
(function () {
    const form = document.querySelector('.cliente-form');
    const estadoInput = form ? form.querySelector('input[name="estado"]') : null;
    const estadoLabel = estadoInput ? estadoInput.closest('label') : null;
    if (!form || !estadoInput || !estadoLabel) {
        return;
    }

    const stateFlagFiles = {
        AC: ['Bandeira do Acre.svg'],
        AL: ['Bandeira de Alagoas.svg'],
        AP: ['Bandeira do Amapá.svg'],
        AM: ['Bandeira do Amazonas.svg'],
        BA: ['Bandeira da Bahia.svg'],
        CE: ['Bandeira do Ceará.svg'],
        DF: ['Bandeira do Distrito Federal.svg'],
        ES: ['Bandeira do Espírito Santo.svg'],
        GO: ['Bandeira de Goiás.svg'],
        MA: ['Bandeira do Maranhão.svg'],
        MT: ['Bandeira de Mato Grosso.svg'],
        MS: ['Bandeira de Mato Grosso do Sul.svg'],
        MG: ['Bandeira de Minas Gerais.svg'],
        PA: ['Bandeira do Pará.svg'],
        PB: ['Bandeira da Paraíba.svg'],
        PR: ['Bandeira do Estado do Paraná.svg', 'Bandeira do Paraná.svg'],
        PE: ['Bandeira de Pernambuco.svg'],
        PI: ['Bandeira do Piauí.svg'],
        RJ: ['Bandeira do estado do Rio de Janeiro.svg'],
        RN: ['Bandeira do Rio Grande do Norte.svg'],
        RS: ['Bandeira do Rio Grande do Sul.svg'],
        RO: ['Bandeira de Rondônia.svg'],
        RR: ['Bandeira de Roraima.svg'],
        SC: ['Bandeira de Santa Catarina.svg'],
        SP: ['Bandeira do estado de São Paulo.svg', 'Bandeira do Estado de São Paulo.svg'],
        SE: ['Bandeira de Sergipe.svg'],
        TO: ['Bandeira do Tocantins.svg'],
    };
    const validUfs = new Set(Object.keys(stateFlagFiles));
    const flagUrl = (fileName) => `https://commons.wikimedia.org/wiki/Special:FilePath/${encodeURIComponent(fileName).replace(/%20/g, '_')}`;

    estadoLabel.classList.add('uf-flag-field');
    let badge = estadoLabel.querySelector('.uf-flag-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'uf-flag-badge';
        estadoLabel.appendChild(badge);
    }

    const loadFlagCandidate = (image, uf, index) => {
        const files = stateFlagFiles[uf] || [];
        const fileName = files[index];
        if (!fileName) {
            estadoLabel.classList.remove('has-flag');
            badge.innerHTML = '';
            return;
        }

        image.onload = () => estadoLabel.classList.add('has-flag');
        image.onerror = () => loadFlagCandidate(image, uf, index + 1);
        image.alt = `Bandeira oficial do estado ${uf}`;
        image.referrerPolicy = 'no-referrer';
        image.decoding = 'async';
        image.src = flagUrl(fileName);
        estadoLabel.classList.add('has-flag');
    };

    const updateFlag = () => {
        const uf = String(estadoInput.value || '').replace(/[^a-z]/gi, '').slice(0, 2).toUpperCase();
        if (estadoInput.value !== uf) {
            estadoInput.value = uf;
        }

        if (!validUfs.has(uf)) {
            badge.innerHTML = '';
            estadoLabel.classList.remove('has-flag');
            return;
        }

        let image = badge.querySelector('img');
        if (!image) {
            badge.innerHTML = '';
            image = document.createElement('img');
            badge.appendChild(image);
        }

        if (image.dataset.uf === uf) {
            estadoLabel.classList.add('has-flag');
            return;
        }

        image.dataset.uf = uf;
        loadFlagCandidate(image, uf, 0);
    };

    window.updateClienteUfFlag = updateFlag;
    estadoInput.addEventListener('input', updateFlag);
    estadoInput.addEventListener('change', updateFlag);
    updateFlag();
})();
</script>
