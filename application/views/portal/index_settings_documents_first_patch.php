<?php
include __DIR__.'/index_settings_first_line_wide_patch.php';
?>
<style>
    html body .portal-settings-first-line {
        grid-template-columns: minmax(0, 2.15fr) minmax(0, 2.15fr) minmax(190px, 0.72fr) minmax(215px, 0.72fr) !important;
        gap: 1rem !important;
    }

    html body .portal-settings-document-card {
        display: grid;
        gap: 0.85rem;
        width: 100%;
        max-width: 100%;
        margin: 0 0 1rem;
        padding: 1rem;
        border: 1px solid rgba(15, 23, 42, 0.075);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.72);
        box-sizing: border-box;
    }

    html body .portal-settings-document-card .portal-settings-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(280px, 100%), 1fr)) !important;
    }

    [data-theme="dark"] body .portal-settings-document-card {
        border-color: rgba(148, 163, 184, 0.14);
        background: rgba(30, 41, 59, 0.56);
    }

    @media (max-width: 1280px) {
        html body .portal-settings-first-line {
            grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.7fr) minmax(200px, 0.9fr) minmax(215px, 0.85fr) !important;
        }
    }

    @media (max-width: 1080px) {
        html body .portal-settings-first-line {
            grid-template-columns: repeat(2, minmax(260px, 1fr)) !important;
        }
    }

    @media (max-width: 700px) {
        html body .portal-settings-first-line {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<script>
(function () {
    const moveDocumentsFirst = () => {
        const section = document.querySelector('[data-settings-section="cadastro"]');
        const personalTitle = section?.querySelector('.portal-settings-section-title');
        const docToggle = section?.querySelector('.portal-settings-doc-toggle');
        const docField = section?.querySelector('[data-settings-doc-field]');
        const docGrid = docField?.closest('.portal-settings-grid');
        const hint = section?.querySelector('[data-settings-cnpj-hint]');
        if (!section || !personalTitle || !docToggle || !docGrid || !hint) return;

        personalTitle.childNodes.forEach((node) => {
            if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                node.textContent = ' Informação';
            }
        });

        let card = section.querySelector('.portal-settings-document-card');
        if (!card) {
            card = document.createElement('div');
            card.className = 'portal-settings-document-card';
            personalTitle.insertAdjacentElement('beforebegin', card);
        }
        card.appendChild(docToggle);
        card.appendChild(docGrid);
        card.appendChild(hint);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            moveDocumentsFirst();
            window.setTimeout(moveDocumentsFirst, 800);
            window.setTimeout(moveDocumentsFirst, 1400);
        }, { once: true });
    } else {
        moveDocumentsFirst();
        window.setTimeout(moveDocumentsFirst, 800);
        window.setTimeout(moveDocumentsFirst, 1400);
    }
})();
</script>
