<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - {{ config('app.name') }}</title>
    
    {{-- Google Fonts - Inter (dapat diganti ke Poppins jika diperlukan) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/dashlite.css', 'resources/css/theme.css', 'resources/css/app.css', 'resources/js/app.js'])
    
    @if(isset($css_library))
        @vite($css_library)
    @endif

    @vite(['resources/css/pages/pos/index.css', 'resources/js/pages/pos/thermal-printer.js', 'resources/js/pages/pos/index.js'])
</head>
<body>
    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('js/bundle.js') }}"></script>
    @vite(['resources/js/scripts.js'])
    
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Theme Toggle Function
        function toggleTheme() {
            const html = document.documentElement;
            const toggle = document.querySelector('.pos-toggle-switch');
            const currentTheme = html.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                html.removeAttribute('data-theme');
                toggle?.classList.remove('active');
                localStorage.setItem('pos-theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                toggle?.classList.add('active');
                localStorage.setItem('pos-theme', 'dark');
            }
        }

        // Initialize theme from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('pos-theme');
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.querySelector('.pos-toggle-switch')?.classList.add('active');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
