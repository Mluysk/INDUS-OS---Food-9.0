<?php
include __DIR__.'/index_header_patch.php';
?>
<style>
    html body .portal-app__tag {
        min-height: calc(1em + 0.6px) !important;
        padding-top: calc(0.35em + 0.3px) !important;
        padding-bottom: calc(0.35em + 0.3px) !important;
        font-size: 1.11rem !important;
        line-height: 1 !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    @media (max-width: 720px) {
        html body .portal-app__tag {
            font-size: 1rem !important;
        }
    }
</style>
