<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">

<head>
    @include('layouts.partials.header')

    @if(isset($css_library))
        @vite($css_library)
    @endif

    <style type="text/css">
        .nk-sidebar.is-compact:not(:hover) .logo-img-small {
            max-height: 20px;
        }
    </style>
</head>

<body class="nk-body bg-white npc-default pg-error">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle wide-md mx-auto">
                        <div class="nk-block-content nk-error-ld text-center">
                            <img class="nk-error-gfx" src="{{ asset('images/error-404.svg') }}" alt="">
                            <div class="wide-xs mx-auto">
                                <h3 class="nk-error-title">Terlalu Banyak Permintaan!</h3>
                                <p class="nk-error-text">Maaf, Anda telah melakukan terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat dan coba lagi.</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary mt-2">Kembali ke Dashboard</a>
                            </div>
                        </div>
                    </div><!-- .nk-block -->
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="{{ asset('js/bundle.js') }}"></script>
    @vite(['resources/js/scripts.js', 'resources/js/app.js', 'resources/css/app.css'])

</html>
