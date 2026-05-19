<?php
include __DIR__.'/index_settings_first_line_patch.php';
?>
<style>
    html body .portal-settings-first-line {
        grid-template-columns: minmax(360px, 1.65fr) minmax(360px, 1.65fr) minmax(245px, 0.82fr) minmax(250px, 0.78fr) !important;
        gap: 1rem !important;
        justify-content: stretch !important;
    }

    html body .portal-settings-first-line .portal-settings-field input {
        width: 100% !important;
    }

    @media (max-width: 1380px) {
        html body .portal-settings-first-line {
            grid-template-columns: minmax(310px, 1.45fr) minmax(310px, 1.45fr) minmax(220px, 0.86fr) minmax(230px, 0.8fr) !important;
        }
    }

    @media (max-width: 1180px) {
        html body .portal-settings-first-line {
            grid-template-columns: repeat(2, minmax(280px, 1fr)) !important;
        }
    }

    @media (max-width: 700px) {
        html body .portal-settings-first-line {
            grid-template-columns: 1fr !important;
        }
    }
</style>
