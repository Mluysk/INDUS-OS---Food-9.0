<style>
    .cliente-form .address-grid {
        grid-template-columns: minmax(132px, 0.7fr) minmax(280px, 2.9fr) minmax(96px, 0.55fr) minmax(258px, 1.36fr);
        align-items: end;
        width: 100%;
        max-width: 100%;
    }

    .cliente-form .address-grid > label,
    .cliente-form .address-grid input {
        min-width: 0;
        max-width: 100%;
    }

    .cliente-form .address-field-cep {
        grid-column: 1;
        grid-row: 1;
    }

    .cliente-form .address-field-street {
        grid-column: 2;
        grid-row: 1;
    }

    .cliente-form .address-field-number {
        grid-column: 3;
        grid-row: 1;
    }

    .cliente-form .address-field-neighborhood {
        grid-column: 4;
        grid-row: 1;
    }

    .cliente-form .address-field-city {
        grid-column: 1 / span 2;
        grid-row: 2;
        min-width: 0;
    }

    .cliente-form .address-field-state {
        grid-column: 3;
        grid-row: 2;
        min-width: 0;
        max-width: 128px;
    }

    .cliente-form .address-field-cep input[name="cep"] {
        width: 100%;
        max-width: 132px;
    }

    .cliente-form .address-field-street input[name="endereco"],
    .cliente-form .address-field-city input[name="cidade"],
    .cliente-form .address-field-state input[name="estado"] {
        width: 100%;
    }

    @media (max-width: 1180px) {
        .cliente-form .address-grid {
            grid-template-columns: minmax(132px, 0.72fr) minmax(260px, 1.9fr) minmax(96px, 0.58fr);
        }

        .cliente-form .address-field-cep,
        .cliente-form .address-field-street,
        .cliente-form .address-field-number,
        .cliente-form .address-field-neighborhood,
        .cliente-form .address-field-city,
        .cliente-form .address-field-state {
            grid-column: auto;
            grid-row: auto;
        }

        .cliente-form .address-field-street,
        .cliente-form .address-field-neighborhood,
        .cliente-form .address-field-city {
            grid-column: span 2;
        }

        .cliente-form .address-field-state {
            max-width: none;
        }
    }

    @media (max-width: 720px) {
        .cliente-form .address-grid {
            grid-template-columns: 1fr;
        }

        .cliente-form .address-field-cep,
        .cliente-form .address-field-street,
        .cliente-form .address-field-number,
        .cliente-form .address-field-neighborhood,
        .cliente-form .address-field-city,
        .cliente-form .address-field-state {
            grid-column: auto;
            grid-row: auto;
            max-width: none;
        }
    }
</style>
