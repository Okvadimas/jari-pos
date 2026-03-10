<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">

<head>
    @include('layouts.partials.header')
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content p-0">
                    <div class="nk-block nk-block-middle nk-auth-body wide-xs">
                        <div class="lock-screen-container">
                            <!-- Lock Icon Animation -->
                            <div class="lock-icon-wrapper">
                                <div class="lock-icon-circle">
                                    <em class="icon ni ni-lock-alt lock-icon"></em>
                                </div>
                                <div class="lock-pulse"></div>
                            </div>

                            <!-- User Info -->
                            <div class="lock-user-info text-center mt-4">
                                <div class="user-avatar user-avatar-lg bg-primary mx-auto mb-3">
                                    <span class="text-uppercase">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <h4 class="fw-bold mb-1">{{ Auth::user()->name }}</h4>
                                <p class="text-soft mb-0">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Unlock Form -->
                            <div class="lock-form-wrapper mt-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-inner card-inner-lg">
                                        <div class="nk-block-head text-center mb-3">
                                            <h5 class="nk-block-title fw-bold">Layar Terkunci</h5>
                                            <div class="nk-block-des text-soft">
                                                <p>Masukkan kata sandi Anda untuk membuka kunci layar.</p>
                                            </div>
                                        </div>
                                        <form class="form-validate" id="form-unlock">
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="password">Kata Sandi</label>
                                                </div>
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Masukkan kata sandi Anda" required autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group mt-4">
                                                <button type="submit" class="btn btn-lg btn-primary btn-block" id="btn-unlock">
                                                    <em class="icon ni ni-unlock me-1"></em> Buka Kunci
                                                </button>
                                            </div>
                                        </form>
                                        <div class="text-center mt-4">
                                            <a href="{{ route('logout') }}" class="link link-primary fw-bold">
                                                <em class="icon ni ni-signout me-1"></em> Masuk dengan akun lain
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Clock -->
                            <div class="lock-clock text-center mt-4">
                                <div id="lock-time" class="lock-time"></div>
                                <div id="lock-date" class="lock-date text-soft"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    <!-- JavaScript -->
    <script src="{{ asset('js/bundle.js') }}"></script>
    @vite(['resources/js/scripts.js', 'resources/js/app.js', 'resources/css/app.css'])

    @if ($css)
        @vite($css)
    @endif

    @if ($js)
        @vite($js)
    @endif
</html>
