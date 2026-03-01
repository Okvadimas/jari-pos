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
                    <div class="nk-block nk-block-middle p-0 m-0">
                        <div class="card border-0 rounded-0 min-vh-100 overflow-hidden">
                            <div class="card-inner card-inner-lg p-0 min-vh-100">
                                <div class="d-flex align-items-center justify-content-center min-vh-100 p-4 p-xl-5">
                                    <div class="w-100 max-w-500px mx-auto text-center">
                                        <div class="brand-logo mb-5">
                                            <a href="/" class="logo-link">
                                                <img class="logo-img" src="{{ asset('images/brand-full-logo-side.png') }}" alt="logo" style="height: 40px;">
                                            </a>
                                        </div>
                                        
                                        <div class="nk-block-head mb-4">
                                            <div class="verify-icon-wrap mb-4">
                                                <em class="icon ni ni-mail-fill verify-icon"></em>
                                            </div>
                                            <h3 class="nk-block-title fw-bold">Verifikasi Email Anda</h3>
                                            <div class="nk-block-des text-soft mt-3">
                                                <p>Kami telah mengirimkan email verifikasi ke alamat email yang Anda daftarkan. Silakan cek inbox (dan folder spam) Anda untuk menyelesaikan proses registrasi.</p>
                                            </div>
                                        </div>

                                        <div class="card card-bordered shadow-sm">
                                            <div class="card-inner">
                                                <p class="text-soft mb-3">Tidak menerima email verifikasi?</p>
                                                <form id="resend-form">
                                                    <div class="form-group">
                                                        <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Masukkan email Anda" required>
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <button type="submit" class="btn btn-primary btn-block" id="btn-resend">
                                                            <em class="icon ni ni-send"></em>
                                                            <span>Kirim Ulang Email Verifikasi</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="form-note-s2 text-center pt-4">
                                            <a href="{{ route('login') }}"><em class="icon ni ni-arrow-left"></em> Kembali ke Login</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    @if ($css)
        @vite($css)
    @endif

    @if ($js)
        @vite($js)
    @endif
</html>
