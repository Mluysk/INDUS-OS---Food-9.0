<?php
include __DIR__.'/index_profile_pill_width_patch.php';
?>
<style>
    html body .portal-settings-city-state-row {
        display: grid !important;
        grid-template-columns: minmax(260px, 34%) calc(6ch + 74px) !important;
        gap: 1rem !important;
        align-items: stretch !important;
        width: 100% !important;
        max-width: 100% !important;
        justify-content: start !important;
        margin: 0 0 1rem !important;
    }

    html body .portal-settings-city-state-row .portal-settings-field {
        margin: 0 !important;
    }

    html body .portal-settings-city-state-row .portal-settings-field:has(#portal-full-cidade) {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        flex: 0 0 auto !important;
    }

    html body .portal-settings-city-state-row .portal-settings-field:has(#portal-full-cidade) input,
    html body .portal-settings-city-state-row #portal-full-cidade {
        width: 100% !important;
        max-width: 100% !important;
    }

    html body .portal-settings-field:has(#portal-full-estado) {
        width: calc(6ch + 74px) !important;
        min-width: calc(6ch + 74px) !important;
        max-width: calc(6ch + 74px) !important;
        flex: 0 0 calc(6ch + 74px) !important;
    }

    html body .portal-settings-field:has(#portal-full-estado) label {
        white-space: nowrap !important;
    }

    html body #portal-full-estado,
    html body .portal-settings-field:has(#portal-full-estado) input {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        padding-right: 3.15rem !important;
        font-variant-numeric: tabular-nums !important;
        text-transform: uppercase !important;
    }

    html body .portal-settings-field:has(#portal-full-estado) img,
    html body .portal-settings-field:has(#portal-full-estado) [class*="flag"],
    html body .portal-settings-field:has(#portal-full-estado) [data-state-flag] {
        width: 34px !important;
        height: 24px !important;
        min-width: 34px !important;
        max-width: 34px !important;
        object-fit: cover !important;
        flex: 0 0 34px !important;
        border-radius: 6px !important;
    }

    @media (max-width: 640px) {
        html body .portal-settings-city-state-row {
            grid-template-columns: minmax(0, 1fr) calc(6ch + 74px) !important;
        }

        html body .portal-settings-field:has(#portal-full-estado) {
            width: calc(6ch + 74px) !important;
            min-width: calc(6ch + 74px) !important;
            max-width: calc(6ch + 74px) !important;
            flex-basis: calc(6ch + 74px) !important;
        }
    }
</style>
<script>
(function () {
    const moveStateBesideCity = () => {
        const cityField = document.getElementById('portal-full-cidade')?.closest('.portal-settings-field');
        const stateField = document.getElementById('portal-full-estado')?.closest('.portal-settings-field');
        const section = cityField?.closest('[data-settings-section="cadastro"]');
        if (!section || !cityField || !stateField) return;

        let row = section.querySelector('.portal-settings-city-state-row');
        if (!row) {
            row = document.createElement('div');
            row.className = 'portal-settings-city-state-row';
            cityField.insertAdjacentElement('beforebegin', row);
        }

        row.appendChild(cityField);
        row.appendChild(stateField);
    };

    const boot = () => {
        moveStateBesideCity();
        window.setTimeout(moveStateBesideCity, 500);
        window.setTimeout(moveStateBesideCity, 1500);
        window.setTimeout(moveStateBesideCity, 2300);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
</script>
