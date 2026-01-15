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

<body class="nk-body bg-lighter npc-default has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">

            <!-- sidebar @s -->
            @include('layouts.partials.sidebar')
            <!-- sidebar @e -->

            <!-- wrap @s -->
            <div class="nk-wrap ">

                <!-- main header @s -->
                <div class="nk-header nk-header-fixed bg-primary is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon text-light" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="/dashboard" class="logo-link">
                                    <img class="logo-light logo-img" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png 2x') }}" alt="logo">
                                    <img class="logo-dark logo-img" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png 2x') }}" alt="logo-dark">
                                </a>
                            </div><!-- .nk-header-brand -->
                            <!-- .nk-header-news -->
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle me-n1" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>

                                                <div class="user-info d-none d-xl-block">
                                                    <div class="user-status user-status-unverified text-white fw-lighter">
                                                        ({{ ucwords(str_replace('-', ' ', session('role'))) }})</div>
                                                    <div class="user-name dropdown-indicator text-white fw-bold">{{ Auth::user()->name }}</div>
                                                </div>

                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span class="text-uppercase">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->name }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="/change-password"><em class="icon ni ni-lock"></em><span>Change Password</span></a></li>
                                                    <li hidden><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="/logout"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
                <!-- main header @e -->

                <!-- content @s -->
                @yield('content')
                <!-- content @e -->

                <!-- footer @s -->
                @include('layouts.partials.footer')
                <!-- footer @e -->

            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    <!-- JavaScript -->
    <script src="{{ asset('js/bundle.js') }}"></script>
    @vite(['resources/js/scripts.js', 'resources/js/app.js', 'resources/css/app.css'])

    @if (isset($css))
        @vite($css)
    @endif

    @if (isset($js))
        @vite($js)
    @endif

    @if(isset($js_library))
        @vite($js_library)
    @endif

</body>
