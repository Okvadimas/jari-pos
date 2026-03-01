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
                                    <div class="col-lg-8 order-2 order-lg-1 p-3 ps-lg-3 rounded-3">
                                        @if($sliders->isNotEmpty())
                                        <div id="auth-carousel" class="carousel slide h-100 rounded-4 overflow-hidden" data-bs-ride="carousel" style="min-height: 300px;">
                                            <div class="carousel-indicators">
                                                @foreach($sliders as $key => $slider)
                                                <button type="button" data-bs-target="#auth-carousel" data-bs-slide-to="{{ $key }}" class="{{ $key === 0 ? 'active' : '' }}" aria-current="{{ $key === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $key + 1 }}"></button>
                                                @endforeach
                                            </div>
                                            <div class="carousel-inner h-100">
                                                @foreach($sliders as $key => $slider)
                                                <div class="carousel-item h-100 {{ $key === 0 ? 'active' : '' }}">
                                                    <div class="h-100 w-100 position-relative" style="background-image: url('{{ asset($slider->image) }}'); background-size: cover; background-position: center;">
                                                        <div class="slider-overlay">
                                                            @if($slider->title)
                                                            <div class="slider-caption container">
                                                                <h2 class="display-4 fw-bold mb-3">{{ $slider->title }}</h2>
                                                                <p class="lead text-white-75 mb-0">{{ $slider->description }}</p>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                        </div>
                                        @else
                                        <div class="d-flex flex-column justify-content-center align-items-center h-100 w-100 p-5 text-center empty-state-bg position-relative overflow-hidden rounded-5">
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-overlay-pattern"></div>
                                            
                                            <div class="position-relative z-index-1">
                                                <img src="{{ asset('images/brand-full-logo-side.png') }}" alt="Jari POS" class="mb-5 logo-filter-white" style="height: 60px;">
                                                <h1 class="text-white fw-bolder responsive-heading mb-4">Selamat Datang di Jari POS</h1>
                                                <p class="text-white-80 lead responsive-text mx-auto" style="max-width: 600px;">
                                                    Kelola bisnis Anda secara efisien dengan sistem POS canggih kami. Lacak pesanan, kelola inventaris, dan kembangkan bisnis Anda.
                                                </p>
                                                <div class="mt-5">
                                                    <span class="badge badge-soft-white rounded-pill px-4 py-2 fw-bold text-uppercase">Sistem v1.0</span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-4 order-1 order-lg-2">
                                        <div class="d-flex align-items-center justify-content-center h-100 p-4 p-xl-5">
                                           <div class="w-100 max-w-400px mx-auto">
                                                <div class="nk-block-head text-center mb-4">
                                                    <div class="nk-block-head-content">
                                                        <div class="brand-logo mb-5">
                                                            <a href="/" class="logo-link">
                                                                <img class="logo-light logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                                <img class="logo-dark logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo-dark">
                                                            </a>
                                                        </div>
                                                        <h3 class="nk-block-title fw-bold">Daftar Akun</h3>
                                                        <div class="nk-block-des text-soft">
                                                            <p>Buat akun baru untuk mengakses Panel Admin Jari POS.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form class="form-validate is-alter" id="form-data">
                                                    <h6 class="text-primary mb-3"><em class="icon ni ni-user"></em> Data Pribadi</h6>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="name">Nama Lengkap</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Masukkan nama lengkap Anda" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="username">Username</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Masukkan username Anda" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="email">Email</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Masukkan email Anda" required>
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
                                                            <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Masukkan kata sandi Anda" required>
                                                        </div>
                                                    </div>

                                                    <hr class="my-4">
                                                    <h6 class="text-primary mb-3"><em class="icon ni ni-building"></em> Data Perusahaan</h6>

                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="company_name">Nama Perusahaan</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control form-control-lg" id="company_name" name="company_name" placeholder="Masukkan nama perusahaan" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="business_category">Kategori Usaha</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select form-control form-control-lg" id="business_category" name="business_category" required>
                                                                <option value="" disabled selected>Pilih Kategori Usaha</option>
                                                                <option value="retail">Retail</option>
                                                                <option value="restoran">Restoran</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="company_email">Email Perusahaan</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <input type="email" class="form-control form-control-lg" id="company_email" name="company_email" placeholder="Masukkan email perusahaan" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="company_phone">Telepon Perusahaan</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control form-control-lg" id="company_phone" name="company_phone" placeholder="Masukkan nomor telepon perusahaan">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label" for="company_address">Alamat Perusahaan</label>
                                                        </div>
                                                        <div class="form-control-wrap">
                                                            <textarea class="form-control" id="company_address" name="company_address" placeholder="Masukkan alamat perusahaan" rows="2"></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mt-5">
                                                        <button type="submit" class="btn btn-lg btn-primary btn-block" id="btn-submit">Daftar</button>
                                                    </div>
                                                </form>
                                                <div class="form-note-s2 text-center pt-4"> Sudah punya akun? <a href="{{ route('login') }}">Masuk disini</a>
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
    
    <!-- Initialize NioApp before DashLite scripts -->
    <script>var NioApp = window.NioApp || {};</script>
    
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
