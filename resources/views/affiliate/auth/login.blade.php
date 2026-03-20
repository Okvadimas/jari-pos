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
                        
                        <!-- Carousel / Split Layout Start -->
                        <div class="card border-0 rounded-0 min-vh-100 overflow-hidden">
                            <div class="card-inner card-inner-lg p-0 min-vh-100">
                                <div class="row g-0 min-vh-100">
                                    <div class="col-xxl-8 col-xl-7 d-none d-xl-block p-3 ps-lg-3 rounded-3">
                                        <div class="d-flex flex-column justify-content-center align-items-center h-100 w-100 p-5 text-center empty-state-bg position-relative overflow-hidden rounded-5" style="background: linear-gradient(135deg, #0971fe 0%, #1c335a 100%);">
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-overlay-pattern" style="opacity: 0.1;"></div>
                                            
                                            <div class="position-relative z-index-1">
                                                <img src="{{ asset('images/brand-full-logo-side.png') }}" alt="Jari POS" class="mb-5 logo-filter-white" style="height: 60px;">
                                                <h1 class="text-white fw-bolder mb-4">Panel Kemitraan Affiliator</h1>
                                                <p class="text-white lead mx-auto" style="max-width: 600px;">
                                                    Bergabunglah dengan ekosistem Jari POS dan mulai hasilkan pendapatan dari setiap referensi bisnis yang Anda bawa. Pantau komisi dan performa Anda secara real-time.
                                                </p>
                                                <div class="mt-5">
                                                    <span class="badge badge-soft-white rounded-pill px-4 py-2 fw-bold text-uppercase">Affiliate Program</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-xl-5 bg-white">
                                        <div class="d-flex align-items-center justify-content-center h-100 p-4 p-xl-5">
                                           <div class="w-100 max-w-400px mx-auto">
                                                
                                                <div class="brand-logo mb-4 text-center d-lg-none">
                                                    <a href="/" class="logo-link">
                                                        <img class="logo-dark logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                    </a>
                                                </div>

                                                <div class="nk-block-head text-center mb-4">
                                                    <div class="nk-block-head-content">
                                                        <div class="brand-logo mb-4 d-none d-lg-block">
                                                            <a href="/" class="logo-link">
                                                                <img class="logo-dark logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                            </a>
                                                        </div>
                                                        <h3 class="nk-block-title fw-bold text-primary mb-2" style="font-size: 28px;">Login Affiliator</h3>
                                                        <div class="nk-block-des text-soft">
                                                            <p>Masuk ke dashboard kemitraan <strong class="text-dark fw-bold">Jari POS</strong> Anda.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-bordered rounded-4 border-light shadow-sm">
                                                    <div class="card-inner p-4 p-md-5">
                                                        @if ($errors->any())
                                                            <div class="alert alert-danger alert-icon alert-dismissible">
                                                                <em class="icon ni ni-cross-circle"></em> 
                                                                {{ $errors->first() }}
                                                                <button class="close" data-bs-dismiss="alert"></button>
                                                            </div>
                                                        @endif

                                                        <form action="{{ route('affiliate.login') }}" method="POST" class="form-validate is-alter" id="form-login">
                                                            @csrf
                                                            <div class="form-group">
                                                                <div class="form-label-group">
                                                                    <label class="form-label" for="email">Email Affiliator</label>
                                                                </div>
                                                                <div class="form-control-wrap">
                                                                    <input type="email" class="form-control form-control-lg bg-lighter rounded-3" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email terdaftar" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="form-label-group">
                                                                    <label class="form-label" for="password">Kata Sandi</label>
                                                                </div>
                                                                <div class="form-control-wrap">
                                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                                    </a>
                                                                    <input type="password" class="form-control form-control-lg bg-lighter rounded-3" name="password" id="password" placeholder="Masukkan kata sandi" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-group mt-5">
                                                                <button type="submit" class="btn btn-lg btn-primary btn-block rounded-pill shadow-sm d-flex justify-content-center align-items-center">
                                                                    <span class="fw-bold">Masuk Sekarang</span>
                                                                    <em class="icon ni ni-arrow-right ms-2"></em>
                                                                </button>
                                                            </div>
                                                        </form>
                                                        
                                                        <div class="form-note-s2 text-center pt-4 mt-2" style="border-top: 1px solid #e5e9f2;">
                                                            Belum punya akun mitra? <a href="{{ route('affiliate.register') }}" class="link link-primary fw-bold">Daftar Affiliator</a>
                                                        </div>

                                                        <div class="form-note-s2 text-center mt-2">
                                                            Pemilik Bisnis? <a href="{{ route('login') }}" class="link link-primary fw-bold">Masuk ke Dashboard POS</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Carousel / Split Layout End -->
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
    <script src="{{ asset('js/scripts.js') }}"></script>
</html>
