<?php
include __DIR__.'/index_menu_clean_patch.php';
?>
<script>
(function () {
    const cartPlusIcon = `
        <span class="portal-app__link-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M6.1 5.25 5.74 3.7A1.5 1.5 0 0 0 4.28 2.55H2.75a.75.75 0 0 0 0 1.5h1.53l2.34 10.08A2.5 2.5 0 0 0 9.06 16h7.22a2.5 2.5 0 0 0 2.38-1.72l1.47-4.49A1.5 1.5 0 0 0 18.7 7.8H7.02l-.24-1.05H18a.75.75 0 0 0 0-1.5H6.1Zm3.15 14.7a1.45 1.45 0 1 0 0-2.9 1.45 1.45 0 0 0 0 2.9Zm7.5 0a1.45 1.45 0 1 0 0-2.9 1.45 1.45 0 0 0 0 2.9ZM14.25 9.25a.75.75 0 0 1 .75.75v1.25h1.25a.75.75 0 0 1 0 1.5H15V14a.75.75 0 0 1-1.5 0v-1.25h-1.25a.75.75 0 0 1 0-1.5h1.25V10a.75.75 0 0 1 .75-.75Z" />
            </svg>
        </span>`;

    const run = () => {
        const button = document.querySelector('.portal-app__header-links [data-portal-jump="novo-pedido"]');
        if (!button) return;
        const currentIcon = button.querySelector('.portal-app__link-icon');
        if (currentIcon) {
            currentIcon.outerHTML = cartPlusIcon;
        } else {
            button.insertAdjacentHTML('afterbegin', cartPlusIcon);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
