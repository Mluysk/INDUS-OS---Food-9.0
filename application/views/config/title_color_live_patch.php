<script>
(function () {
    const bindTitleColorLive = () => {
        const toggle = document.getElementById('title-colorize');
        if (!toggle || toggle.dataset.titleColorLiveReady === '1') {
            return;
        }

        toggle.dataset.titleColorLiveReady = '1';
        const sync = () => {
            document.querySelectorAll('.brand-title').forEach((title) => {
                title.classList.toggle('colorized', toggle.checked);
                if (toggle.checked) {
                    title.style.color = 'var(--primary)';
                } else {
                    title.style.removeProperty('color');
                }
            });
        };

        toggle.addEventListener('change', sync);
        document.querySelectorAll('.swatch, #custom-hex-picker, #custom-hex').forEach((control) => {
            control.addEventListener('click', sync);
            control.addEventListener('input', sync);
            control.addEventListener('change', sync);
        });
        sync();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindTitleColorLive, { once: true });
    } else {
        bindTitleColorLive();
    }
})();
</script>
