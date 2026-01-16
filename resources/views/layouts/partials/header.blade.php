<meta charset="utf-8">
<meta name="author" content="Jari POS">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Jari Pos adalah platform Point of Sales (POS) yang memudahkan pengelolaan transaksi dan operasional bisnis Anda secara efisien dan terintegrasi.">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- PWA Meta Tags -->
<meta name="theme-color" content="#6576ff">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Jari POS">
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="Jari POS">
<meta name="msapplication-TileColor" content="#6576ff">
<meta name="msapplication-TileImage" content="{{ asset('images/pwa/icon-144x144.png') }}">

<!-- Fav Icon  -->
<link rel="shortcut icon" href="{{ asset('images/brand-logo.svg') }}">

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Apple Touch Icons -->
<link rel="apple-touch-icon" href="{{ asset('images/pwa/icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/pwa/icon-72x72.png') }}">
<link rel="apple-touch-icon" sizes="96x96" href="{{ asset('images/pwa/icon-96x96.png') }}">
<link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/pwa/icon-128x128.png') }}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/pwa/icon-144x144.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/pwa/icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/pwa/icon-192x192.png') }}">
<link rel="apple-touch-icon" sizes="384x384" href="{{ asset('images/pwa/icon-384x384.png') }}">
<link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/pwa/icon-512x512.png') }}">

<!-- Page Title  -->
<title>{{ isset($title) ? $title : 'Jari POS' }}</title>

<!-- StyleSheets  -->
@vite(['resources/css/dashlite.css', 'resources/css/theme.css'])

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registered: ', registration.scope);
                })
                .catch(function(error) {
                    console.log('ServiceWorker registration failed: ', error);
                });
        });
    }
</script>