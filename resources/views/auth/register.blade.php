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
                        <!-- Centered Form Wizard Layout Start -->
                        <div class="nk-block nk-block-middle px-3 px-md-4 pt-4 pt-md-5">
                            <div class="brand-logo pb-4 text-center">
                                <a href="/" class="logo-link">
                                    <img class="logo-light logo-img logo-img-lg" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo">
                                    <img class="logo-dark logo-img logo-img-lg" src="{{ asset('images/brand-full-logo-side.png') }}" srcset="{{ asset('images/brand-full-logo-side.png') }}" alt="logo-dark">
                                </a>
                            </div>
                            
                            <div class="row justify-content-center">
                                <div class="col-md-11 col-lg-9 col-xl-8">
                                    <div class="card card-bordered rounded-4">
                                        <div class="card-inner card-inner-lg">
                                            <div class="nk-block-head">
                                        <div class="nk-block-head-content text-center">
                                            <h4 class="nk-block-title">Daftar Akun Baru</h4>
                                            <div class="nk-block-des">
                                                <p>Buat akun Jari POS dan siapkan data bisnis Anda.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <form id="form-data">
                                        <!-- Custom Steps Indicator -->
                                        <ul class="nav nav-tabs nav-tabs-s1 justify-content-center mb-4" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#step-1" role="tab">
                                                    <em class="icon ni ni-user me-1"></em> Data Pribadi
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link disabled" data-bs-toggle="tab" href="#step-2" role="tab" id="tab-step-2">
                                                    <em class="icon ni ni-building me-1"></em> Data Perusahaan
                                                </a>
                                            </li>
                                        </ul>
                                        
                                        <div class="tab-content pt-2">
                                            <!-- Step 1: Data Pribadi -->
                                            <div class="tab-pane active" id="step-1" role="tabpanel">
                                                <div class="row g-4 pb-5">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-icon form-icon-right"><em class="icon ni ni-user"></em></div>
                                                                <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Nama lengkap" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-icon form-icon-right"><em class="icon ni ni-at"></em></div>
                                                                <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Username" required pattern="^[a-z0-9]+$" title="Username hanya boleh huruf kecil dan angka, tanpa spasi" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '')">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-icon form-icon-right"><em class="icon ni ni-mail"></em></div>
                                                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email" required>
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
                                                                <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Kata sandi" required minlength="8" pattern="^(?=.*[A-Z])(?=.*[0-9]).{8,}$" title="Minimal 8 karakter, harus ada huruf besar dan angka">
                                                            </div>
                                                            <div class="form-note mt-1 text-soft fs-12px"><em class="icon ni ni-info"></em> Min. 8 karakter (huruf besar & angka)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row align-items-center pt-md-5 pt-4 border-top">
                                                    <div class="col-12 col-sm-6 text-center text-sm-start order-2 order-sm-1">
                                                        <p>Sudah punya akun? <a href="{{ route('login') }}" class="link link-primary d-inline-block mt-1 mt-sm-0">Masuk</a></p>
                                                    </div>
                                                    <div class="col-12 col-sm-6 order-1 order-sm-2 mb-1">
                                                        <div class="d-flex flex-column flex-sm-row justify-content-sm-end">
                                                            <button type="button" class="btn btn-lg btn-primary mb-0 d-flex justify-content-center" id="btn-next-step">Selanjutnya <em class="icon ni ni-arrow-right ms-2"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Step 2: Data Perusahaan -->
                                            <div class="tab-pane" id="step-2" role="tabpanel">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="company_name">Nama Perusahaan <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-icon form-icon-right"><em class="icon ni ni-building"></em></div>
                                                                <input type="text" class="form-control form-control-lg" id="company_name" name="company_name" placeholder="Nama perusahaan">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="business_category">Kategori Usaha <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-control form-control-lg" id="business_category" name="business_category">
                                                                    <option value="" disabled selected>Pilih Kategori</option>
                                                                    <option value="retail">Retail</option>
                                                                    <option value="restoran">Restoran</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="company_email">Email Perusahaan <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-icon form-icon-right"><em class="icon ni ni-mail"></em></div>
                                                                <input type="email" class="form-control form-control-lg" id="company_email" name="company_email" placeholder="Email perusahaan">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="company_phone">Telepon Perusahaan</label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-icon form-icon-right"><em class="icon ni ni-call"></em></div>
                                                                <input type="text" class="form-control form-control-lg" id="company_phone" name="company_phone" placeholder="Nomor telepon">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="company_address">Alamat Perusahaan</label>
                                                            <div class="form-control-wrap">
                                                                <textarea class="form-control form-control-lg" id="company_address" name="company_address" placeholder="Alamat lengkap perusahaan" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row align-items-center mt-5 pt-4 border-top gy-3">
                                                    <div class="col-12 col-sm-6 order-2 order-sm-1">
                                                        <div class="d-flex flex-column flex-sm-row justify-content-sm-start">
                                                            <button type="button" class="btn btn-lg btn-outline-light mb-0 d-flex justify-content-center" id="btn-prev-step"><em class="icon ni ni-arrow-left me-2"></em> Kembali</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 order-1 order-sm-2">
                                                        <div class="d-flex flex-column flex-sm-row justify-content-sm-end">
                                                            <button type="submit" class="btn btn-lg btn-primary mb-0 d-flex justify-content-center" id="btn-submit">Daftar Sekarang</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Simple Script for Tab Navigation -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const btnNext = document.getElementById('btn-next-step');
                                const btnPrev = document.getElementById('btn-prev-step');
                                const tabStep2 = document.getElementById('tab-step-2');
                                const tabStep1 = document.querySelector('a[href="#step-1"]');
                                
                                // Form inputs to validate before next step
                                const step1Inputs = ['name', 'username', 'email', 'password'];
                                
                                btnNext.addEventListener('click', function() {
                                    let isValid = true;
                                    step1Inputs.forEach(id => {
                                        const input = document.getElementById(id);
                                        if(!input.checkValidity()) {
                                            input.reportValidity();
                                            isValid = false;
                                        }
                                    });
                                    
                                    if(isValid) {
                                        // Enable step 2 tab and switch
                                        tabStep2.classList.remove('disabled');
                                        let tab = new bootstrap.Tab(tabStep2);
                                        tab.show();
                                    }
                                });
                                
                                btnPrev.addEventListener('click', function() {
                                    let tab = new bootstrap.Tab(tabStep1);
                                    tab.show();
                                });
                            });
                        </script>
                        <!-- Centered Form Wizard Layout End -->
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
