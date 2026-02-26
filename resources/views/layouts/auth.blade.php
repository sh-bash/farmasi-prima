<!doctype html>
<html lang="en" class="remember-theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name') }} - Login</title>

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">

    <!-- OneUI CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/oneui.min.css') }}">

    <script src="{{ asset('assets/js/setTheme.js') }}"></script>

    @livewireStyles
</head>

<body>

<div id="page-container">
    <main id="main-container">
        <div class="bg-image" style="background-image: url('{{ asset('assets/media/photos/photo14@2x.jpg') }}');">
            <div class="hero-static d-flex align-items-center bg-primary-dark-op">
                <div class="content">

                    {{-- INI TEMPAT ISI LIVEWIRE --}}
                    {{ $slot }}

                </div>
            </div>
        </div>
    </main>
</div>

<!-- JS -->
<script src="{{ asset('assets/js/oneui.app.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>

@livewireScripts

</body>
</html>