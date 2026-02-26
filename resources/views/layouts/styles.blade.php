<!-- Icons -->
<!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
<link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">
<!-- END Icons -->

<!-- Stylesheets -->
<!-- OneUI framework -->
<link rel="stylesheet" id="css-main" href="{{ asset('assets/css/oneui.min.css') }}">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

<!-- Load and set color theme + dark mode preference (blocking script to prevent flashing) -->
<script src="{{ asset('assets/js/setTheme.js') }}"></script>

@livewireStyles

<style>
    .lw-loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255,255,255,0.75);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lw-loading-box {
        background: #fff;
        padding: 24px 32px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,.15);
        text-align: center;
    }
</style>