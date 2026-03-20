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
                                                <h1 class="text-white fw-bolder mb-4">Mari Tumbuh Bersama</h1>
                                                <p class="text-white lead mx-auto" style="max-width: 600px;">
                                                    Daftarkan diri Anda sebagai mitra Affiliator Jari POS. Dapatkan bagi hasil yang kompetitif dan bantu bisnis di Indonesia bertransformasi ke arah digital.
                                                </p>
                                                <div class="mt-5">
                                                    <span class="badge badge-pill badge-outline-light px-4 py-2 fw-bold text-uppercase">Partner Jari POS</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xxl-4 col-xl-5 bg-white">
                                        <div class="d-flex align-items-center justify-content-center h-100 p-4 p-xl-5">
                                            <div class="w-100 mx-auto" style="max-width: 500px;">
                                                
                                                <div class="brand-logo mb-4 text-center d-lg-none">
                                                    <a href="/" class="logo-link">
                                                        <img class="logo-dark logo-img" src="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                    </a>
                                                </div>

                                                <div class="nk-block-head text-center mb-4">
                                                    <div class="nk-block-head-content">
                                                        <div class="brand-logo mb-4 d-none d-lg-block">
                                                            <a href="/" class="logo-link">
                                                                <img class="logo-dark logo-img logo-img-login" src="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                                            </a>
                                                        </div>
                                                        <h3 class="nk-block-title fw-bold text-primary mb-2" style="font-size: 28px;">Daftar Affiliator</h3>
                                                        <div class="nk-block-des text-soft">
                                                            <p>Buat akun kemitraan Anda dan mulai hasilkan cuan.</p>
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

                                                        <form action="{{ route('affiliate.register') }}" method="POST" id="form-register">
                                                            @csrf
                                                            <!-- Custom Steps Indicator -->
                                                            <ul class="nav nav-tabs nav-tabs-s1 justify-content-between flex-nowrap mb-4 pb-3" role="tablist">
                                                                <li class="nav-item flex-grow-1">
                                                                    <a class="nav-link fw-bold active text-primary text-center" data-bs-toggle="tab" href="#step-1" id="tab-step-1" role="tab">
                                                                        <em class="icon ni ni-user-fill"></em> <span>Profil</span>
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item flex-grow-1">
                                                                    <a class="nav-link fw-bold disabled text-soft text-center" data-bs-toggle="tab" href="#step-2" id="tab-step-2" role="tab">
                                                                        <em class="icon ni ni-wallet-fill"></em> <span>Rekening</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            

                                                            <div class="tab-content pt-1">
                                                                <!-- Step 1: Data Pribadi -->
                                                                <div class="tab-pane active" id="step-1" role="tabpanel">
                                                                    <div class="row g-3">
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="text" class="form-control form-control-lg rounded-3" id="name" name="name" value="{{ old('name') }}" placeholder="Nama Anda" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="email">Email Aktif <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="email" class="form-control form-control-lg rounded-3" id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="phone">Nomor WhatsApp <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="text" class="form-control form-control-lg rounded-3" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxx" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="password">Kata Sandi <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                                                    </a>
                                                                                    <input type="password" class="form-control form-control-lg rounded-3" name="password" id="password" placeholder="Min. 8 karakter" required minlength="8">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="password" class="form-control form-control-lg rounded-3" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-group mt-4 pt-2">
                                                                        <button type="button" class="btn btn-lg btn-primary btn-block rounded-pill py-2 shadow-sm" id="btn-next">
                                                                            <span class="fw-bold">Selanjutnya</span>
                                                                            <em class="icon ni ni-arrow-right ms-2"></em>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Step 2: Data Rekening -->
                                                                <div class="tab-pane" id="step-2" role="tabpanel">
                                                                    <div class="alert alert-fill alert-info alert-icon mb-4 fs-12px">
                                                                        <em class="icon ni ni-info-fill"></em> 
                                                                        Data ini digunakan untuk pencairan komisi bulanan Anda.
                                                                    </div>
                                                                    <div class="row g-3">
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="text" class="form-control form-control-lg rounded-3" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" placeholder="Contoh: BCA / Mandiri / BRI">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="bank_account_number">Nomor Rekening <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="text" class="form-control form-control-lg rounded-3" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="Digit angka rekening">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="bank_account_name">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                                                                <div class="form-control-wrap">
                                                                                    <input type="text" class="form-control form-control-lg rounded-3" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}" placeholder="Harus sesuai dengan Buku Tabungan">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="row g-3 mt-4 pt-2">
                                                                        <div class="col-4">
                                                                            <button type="button" class="btn btn-lg btn-outline-light btn-block rounded-pill py-2" id="btn-prev">
                                                                                <em class="icon ni ni-arrow-left"></em>
                                                                            </button>
                                                                        </div>
                                                                        <div class="col-8">
                                                                            <button type="submit" class="btn btn-lg btn-success btn-block rounded-pill py-2 shadow-sm">
                                                                                <span class="fw-bold">Selesaikan Pendaftaran</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-note-s2 text-center pt-4 mt-2">
                                                    Sudah punya akun mitra? <a href="{{ route('affiliate.login') }}" class="link link-primary fw-bold">Masuk disini</a>
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
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="{{ asset('js/bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnNext = document.getElementById('btn-next');
            const btnPrev = document.getElementById('btn-prev');
            const tab1 = document.getElementById('tab-step-1');
            const tab2 = document.getElementById('tab-step-2');
            
            btnNext.addEventListener('click', function() {
                // Mock validation: all fields on step 1 must be filled
                const inputs = document.getElementById('step-1').querySelectorAll('input[required]');
                let valid = true;
                inputs.forEach(input => {
                    if(!input.value) {
                        input.classList.add('error');
                        valid = false;
                    } else {
                        input.classList.remove('error');
                    }
                });

                if(valid) {
                    tab2.classList.remove('disabled');
                    const tabTrigger = new bootstrap.Tab(tab2);
                    tabTrigger.show();
                    tab1.classList.remove('active', 'text-primary');
                    tab1.classList.add('text-soft');
                    tab2.classList.add('active', 'text-primary');
                }
            });

            btnPrev.addEventListener('click', function() {
                const tabTrigger = new bootstrap.Tab(tab1);
                tabTrigger.show();
                tab2.classList.add('disabled');
                tab2.classList.remove('active', 'text-primary');
                tab1.classList.add('active', 'text-primary');
                tab1.classList.remove('text-soft');
            });
        });
    </script>
</body>
</html>
