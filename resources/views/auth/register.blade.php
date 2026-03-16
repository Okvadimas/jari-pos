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
                                    <div class="col-xxl-8 col-xl-7 order-2 order-lg-1 p-3 ps-lg-3 rounded-3 d-none d-xl-block">
                                        @if($sliders && $sliders->isNotEmpty())
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
                                    
                                    <div class="col-xxl-4 col-xl-5 order-1 order-lg-2 bg-white">
                                        <div class="d-flex align-items-center justify-content-center h-100 p-4 p-xl-5">
                                            <div class="w-100 mx-auto" style="max-width: 600px;">
                                                
                                                <!-- Logo for Mobile Only -->
                                                <div class="brand-logo mb-4 text-center d-lg-none">
                                                    <a href="/" class="logo-link">
                                                        <img class="logo-light logo-img" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                        <img class="logo-dark logo-img" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo-dark">
                                                    </a>
                                                </div>

                                                <div class="nk-block-head text-center mb-4">
                                                    <div class="nk-block-head-content">
                                                        <div class="brand-logo mb-4 d-none d-lg-block">
                                                            <a href="/" class="logo-link">
                                                                <img class="logo-light logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                                <img class="logo-dark logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo-dark">
                                                            </a>
                                                        </div>
                                                        <h3 class="nk-block-title fw-bold text-primary mb-2" style="font-size: 28px;">Daftar Akun Baru</h3>
                                                        <div class="nk-block-des text-soft">
                                                            <p>Buat akun <strong class="text-dark fw-bold">Jari POS</strong> dan siapkan data bisnis Anda.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-bordered rounded-4 border-light shadow-sm">
                                                    <div class="card-inner p-4 p-md-5">
                                                        <form id="form-data">
                                                            <!-- Custom Steps Indicator -->
                                                            <ul class="nav nav-tabs nav-tabs-s1 justify-content-between flex-nowrap mb-4 pb-3 w-100 mx-0" role="tablist" style="border-bottom: 1px solid #e5e9f2; gap: 0.25rem;">
                                                                <li class="nav-item flex-grow-1" style="width: 50%;">
                                                                    <a class="nav-link fw-bold active text-primary text-center px-1 w-100 d-flex justify-content-center align-items-center responsive-tab-text" data-bs-toggle="tab" href="#step-1" role="tab" style="padding-bottom: 12px; border-bottom: 2px solid var(--app-primary); white-space: nowrap;">
                                                                        <em class="icon ni ni-user me-1 text-primary"></em> <span>Data Pribadi</span>
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item flex-grow-1" style="width: 50%;">
                                                                    <a class="nav-link fw-bold disabled text-secondary text-center px-1 w-100 d-flex justify-content-center align-items-center responsive-tab-text" data-bs-toggle="tab" href="#step-2" role="tab" id="tab-step-2" style="padding-bottom: 12px; border-bottom: 2px solid transparent; white-space: nowrap;">
                                                                        <em class="icon ni ni-building me-1 text-secondary"></em> <span>Data Perusahaan</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            

                                                            <div class="tab-content pt-1">
                                                                <!-- Step 1: Data Pribadi -->
                                                                <div class="tab-pane active" id="step-1" role="tabpanel">
                                                                    <div class="row g-4 pb-4">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <div class="form-icon form-icon-right"><em class="icon ni ni-user"></em></div>
                                                                                    <input type="text" class="form-control form-control-lg custom-form-control rounded-3" id="name" name="name" placeholder="Nama lengkap Anda" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <div class="form-icon form-icon-right"><em class="icon ni ni-at"></em></div>
                                                                                    <input type="text" class="form-control form-control-lg custom-form-control rounded-3" id="username" name="username" placeholder="admin" required pattern="^[a-z0-9]+$" title="Username hanya boleh huruf kecil dan angka, tanpa spasi" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '')">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <div class="form-icon form-icon-right"><em class="icon ni ni-mail"></em></div>
                                                                                    <input type="email" class="form-control form-control-lg custom-form-control rounded-3" id="email" name="email" placeholder="Alamat email aktif" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="password">Kata Sandi <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                                                    </a>
                                                                                    <input type="password" class="form-control form-control-lg custom-form-control rounded-3" name="password" id="password" placeholder="•••••" required minlength="8" pattern="^(?=.*[A-Z])(?=.*[0-9]).{8,}$" title="Minimal 8 karakter, harus ada huruf besar dan angka">
                                                                                </div>
                                                                                <div class="form-note mt-2 text-soft fs-12px fst-italic"><em class="icon ni ni-info-fill text-primary"></em> Min. 8 karakter (huruf besar & angka)</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-group mt-5">
                                                                        <button type="button" class="btn btn-lg btn-primary btn-block rounded-pill shadow-sm d-flex justify-content-center align-items-center w-100" id="btn-next-step">
                                                                            <span class="fw-bold">Selanjutnya</span>
                                                                            <em class="icon ni ni-arrow-right"></em>
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <div class="form-note-s2 text-center pt-4 mt-2" style="border-top: 1px solid #e5e9f2;">
                                                                        Sudah punya akun? <a href="{{ route('login') }}" class="link link-primary fw-bold">Masuk disini</a>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Step 2: Data Perusahaan -->
                                                                <div class="tab-pane" id="step-2" role="tabpanel">
                                                                    <div class="row g-4 pb-4">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="company_name">Nama Perusahaan <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <div class="form-icon form-icon-right"><em class="icon ni ni-building"></em></div>
                                                                                    <input type="text" class="form-control form-control-lg custom-form-control rounded-3" id="company_name" name="company_name" placeholder="Nama bisnis/toko">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="business_category">Kategori Usaha <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <select class="form-control form-control-lg custom-form-control rounded-3" id="business_category" name="business_category">
                                                                                        <option value="" disabled selected>Pilih Kategori</option>
                                                                                        <option value="retail">Retail</option>
                                                                                        <option value="restoran">Restoran</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="company_email">Email Bisnis <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <div class="form-icon form-icon-right"><em class="icon ni ni-mail"></em></div>
                                                                                    <input type="email" class="form-control form-control-lg custom-form-control rounded-3" id="company_email" name="company_email" placeholder="Email untuk notifikasi">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="company_phone">Nomor Telepon</label>
                                                                                <div class="form-control-wrap">
                                                                                    <div class="form-icon form-icon-right"><em class="icon ni ni-call"></em></div>
                                                                                    <input type="text" class="form-control form-control-lg custom-form-control rounded-3" id="company_phone" name="company_phone" placeholder="Kontak bisnis aktif">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="company_address">Alamat Lengkap</label>
                                                                                <div class="form-control-wrap">
                                                                                    <textarea class="form-control form-control-lg custom-form-control rounded-3" id="company_address" name="company_address" placeholder="Detail alamat lokasi usaha" rows="2"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="row align-items-center pt-4 mt-2" style="border-top: 1px solid #e5e9f2;">
                                                                        <div class="col-12 col-sm-5 order-2 order-sm-1 mt-3 mt-sm-0">
                                                                            <div class="d-flex flex-column flex-sm-row justify-content-sm-start">
                                                                                <button type="button" class="btn btn-lg btn-light mb-0 d-flex justify-content-center px-4 rounded-pill w-100" id="btn-prev-step" style="background:#f5f6fa; border:none; color:#526484;">
                                                                                    <em class="icon ni ni-arrow-left"></em> <span class="fw-bold">Kembali</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-7 order-1 order-sm-2">
                                                                            <div class="d-flex flex-column flex-sm-row justify-content-sm-end">
                                                                                <button type="submit" class="btn btn-lg btn-primary mb-0 d-flex justify-content-center px-4 rounded-pill shadow-sm w-100" id="btn-submit">
                                                                                    <em class="icon ni ni-check-circle fw-bold"></em> <span class="fw-bold">Daftar Sekarang</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
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
