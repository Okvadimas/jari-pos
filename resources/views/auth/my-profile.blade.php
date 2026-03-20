@extends('layouts.base')

@section('content')
    <!-- content @s -->
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-aside-wrap">
                                <div class="card-inner">
                                    <div class="nk-block-head">
                                        <div class="nk-block-between">
                                            <div class="nk-block-head-content">
                                                <h4 class="nk-block-title">Informasi Akun</h4>
                                                <div class="nk-block-des">
                                                    <p>Informasi dasar, seperti nama dan alamat, yang Anda gunakan di Jari POS.</p>
                                                </div>
                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block">
                                        <div class="nk-data data-list">
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">Nama Lengkap</span>
                                                    <span class="data-value" id="data-name">{{ $user->name }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-user text-primary" style="font-size: 1.25rem;"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">Username</span>
                                                    <span class="data-value" id="data-username">{{ $user->username }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-user-alt text-primary" style="font-size: 1.25rem;"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">Email</span>
                                                    <span class="data-value" id="data-email">{{ $user->email }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more disable"><em class="icon ni ni-mail text-secondary" style="font-size: 1.25rem;"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">Nomor Handphone</span>
                                                    <span class="data-value" id="data-phone">{{ $user->phone ?? '-' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-call text-primary" style="font-size: 1.25rem;"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">Tanggal Lahir</span>
                                                    <span class="data-value" id="data-birth-date">{{ $user->birth_date ? date('d M, Y', strtotime($user->birth_date)) : '-' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-calendar text-primary" style="font-size: 1.25rem;"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">Alamat</span>
                                                    <span class="data-value" id="data-address">{{ $user->address ?? '-' }}</span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-map-pin text-primary" style="font-size: 1.25rem;"></em></span></div>
                                            </div><!-- data-item -->
                                        </div><!-- data-list -->
                                    </div><!-- .nk-block -->

                                    <!-- Paket Langganan Section @s -->
                                    <div class="nk-block-head nk-block-head-sm mt-5">
                                        <div class="nk-block-between">
                                            <div class="nk-block-head-content">
                                                <h5 class="nk-block-title">Paket Langganan</h5>
                                                <div class="nk-block-des">
                                                    <p>Kelola paket langganan Anda saat ini dan jelajahi pilihan paket lainnya untuk fitur yang lebih lengkap.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block">
                                        <div class="row g-4">
                                            <!-- Paket Kelingking (Current) -->
                                            <div class="col-md-6">
                                                <div class="card card-bordered text-center h-100 pb-0 {{ $user->role_id == 2 ? 'border-success shadow-sm' : '' }}">
                                                    <div class="card-inner d-flex flex-column h-100">
                                                        <div class="mx-auto mb-3">
                                                            @if( $user->role_id == 2)
                                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-12px"><em class="icon ni ni-check-circle me-1"></em>Paket Saat Ini</span>
                                                            @else
                                                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill fs-12px">Paket Dasar</span>
                                                            @endif
                                                        </div>
                                                        <h4 class="title mb-1">Kelingking</h4>
                                                        <span class="sub-text mb-4">Paket Dasar (Gratis)</span>
                                                        <div class="text-start p-3 bg-lighter rounded mb-auto">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-flex align-items-center mb-2"><em class="icon ni ni-check-circle text-success me-2 fs-5"></em> <span>Fitur Dasar Kasir</span></li>
                                                                <li class="d-flex align-items-center mb-2"><em class="icon ni ni-check-circle text-success me-2 fs-5"></em> <span>Laporan Penjualan (Harian)</span></li>
                                                                <li class="d-flex align-items-center mb-2"><em class="icon ni ni-check-circle text-success me-2 fs-5"></em> <span>Manajemen Menu Dasar</span></li>
                                                                <li class="d-flex align-items-center text-muted"><em class="icon ni ni-cross-circle text-light me-2 fs-5"></em> <span>Fitur Lanjutan Tidak Tersedia</span></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Paket Jempol (Pro) -->
                                            <div class="col-md-6">
                                                <div class="card card-bordered text-center h-100 pb-0 shadow-sm {{ $user->role_id == 3 ? 'border-success' : 'border-primary' }}">
                                                    <div class="card-inner d-flex flex-column h-100">
                                                        <div class="mx-auto mb-3">
                                                            @if($user->role_id == 3)
                                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-12px shadow-sm"><em class="icon ni ni-check-circle me-1"></em>Paket Saat Ini</span>
                                                            @else
                                                                <span class="badge bg-primary px-3 py-2 rounded-pill fs-12px shadow-sm">Rekomendasi</span>
                                                            @endif
                                                        </div>
                                                        <h4 class="title text-primary mb-1">Jempol <em class="icon ni ni-star-fill text-warning fs-14px"></em></h4>
                                                        <span class="sub-text mb-4">Paket Profesional</span>
                                                        <div class="text-start p-3 bg-lighter rounded mb-4">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-flex align-items-center mb-2"><em class="icon ni ni-check-circle-fill text-primary me-2 fs-5"></em> <span class="fw-medium">Semua Fitur Kelingking</span></li>
                                                                <li class="d-flex align-items-center mb-2"><em class="icon ni ni-check-circle-fill text-primary me-2 fs-5"></em> <span class="fw-medium">Laporan Lengkap & Analisa</span></li>
                                                                <li class="d-flex align-items-center mb-2"><em class="icon ni ni-check-circle-fill text-primary me-2 fs-5"></em> <span class="fw-medium">Manajemen Inventori Lanjut</span></li>
                                                                <li class="d-flex align-items-center"><em class="icon ni ni-check-circle-fill text-primary me-2 fs-5"></em> <span class="fw-medium">Dukungan Pelanggan Prioritas</span></li>
                                                            </ul>
                                                        </div>
                                                        @if($user->role_id != 1)
                                                            <div class="mt-auto">
                                                                @if($user->role_id == 3)
                                                                    <button type="button" class="btn btn-outline-success d-block w-100 shadow-sm" disabled>Paket Jempol Anda Aktif </button>
                                                                @else
                                                                    <button type="button" class="btn btn-primary d-block w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#upgradeModal"><em class="icon ni ni-arrow-up-right me-1"></em> Upgrade ke Jempol</button>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block -->
                                    <!-- Paket Langganan Section @e -->

                                </div>
                                <div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
                                    <div class="card-inner-group" data-simplebar>
                                        <div class="card-inner">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    @if($user->profile_picture)
                                                        <img src="{{ asset($user->profile_picture) }}" id="profile-picture" alt="Avatar" class="h-100 w-100" style="object-fit: cover;">
                                                    @else
                                                        <span id="profile-picture-text">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                    @endif
                                                </div>
                                                <div class="user-info">
                                                    <span class="lead-text" id="profile-name">{{ $user->name }}</span>
                                                    <span class="sub-text" id="profile-email">{{ $user->email }}</span>
                                                </div>
                                                <div class="user-action">
                                                    <div class="dropdown">
                                                        <a class="btn btn-icon btn-trigger me-n2" data-bs-toggle="dropdown" href="#"><em class="icon ni ni-more-v"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#updatePhotoModal"><em class="icon ni ni-camera-fill"></em><span>Ganti Foto Profil</span></a></li>
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#profile-edit"><em class="icon ni ni-edit-fill"></em><span>Edit Informasi Akun</span></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- .user-card -->
                                            <div class="mt-4 pt-4 border-top">
                                                @php $completeness = $user->profile_completeness ?? 50; @endphp
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fs-14px text-soft">Kelengkapan Profil</span>
                                                    <span class="fs-14px fw-bold {{ $completeness == 100 ? 'text-success' : 'text-warning' }}">{{ $completeness }}%</span>
                                                </div>
                                                <div class="progress progress-lg bg-light border">
                                                    <div class="progress-bar {{ $completeness == 100 ? 'bg-success' : 'bg-warning' }}" data-progress="{{ $completeness }}" style="width: {{ $completeness }}%;"></div>
                                                </div>
                                                @if($completeness < 100)
                                                    <div class="fs-12px text-warning mt-2"><em class="icon ni ni-alert-circle"></em> Lengkapi semua data profil agar mencapai 100%.</div>
                                                @else
                                                    <div class="fs-12px text-success mt-2"><em class="icon ni ni-check-circle"></em> Profil Anda sudah lengkap.</div>
                                                @endif
                                            </div>
                                        </div><!-- .card-inner -->
                                        <div class="card-inner">
                                            <div class="user-account-info py-0">
                                                <h6 class="overline-title-alt">Terakhir Masuk</h6>
                                                <p>{{ $user->last_login ? date('d M, Y H:i', strtotime($user->last_login)) : '-' }}</p>
                                                
                                                <h6 class="overline-title-alt mt-4">Paket Saat Ini</h6>
                                                <p><span class="badge badge-sm bg-primary">{{ $user->role->name ?? 'Kelingking' }}</span></p>
                                                
                                                <h6 class="overline-title-alt">Bergabung Pada</h6>
                                                <p>{{ $user->created_at ? $user->created_at->format('d M, Y') : '-' }}</p>

                                                @if($user->company)
                                                <h6 class="overline-title-alt">Perusahaan</h6>
                                                <p>{{ $user->company->name }}</p>
                                                @endif
                                            </div>
                                        </div><!-- .card-inner -->
                                        <div class="card-inner p-0">
                                            
                                        </div><!-- .card-inner -->
                                    </div><!-- .card-inner-group -->
                                </div><!-- card-aside -->
                            </div><!-- .card-aside-wrap -->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>
    <!-- content @e -->

    <!-- Modal Upgrade Package -->
    <div class="modal fade" tabindex="-1" id="upgradeModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Upgrade ke Paket Jempol</h5>
                </div>
                <div class="modal-body">
                    
                    <!-- Step 1: Pilih Durasi & Invoice -->
                    <div id="step-1-duration">
                        <p class="text-soft">Pilih durasi berlangganan yang sesuai dengan kebutuhan bisnis Anda.</p>
                        
                        <div class="row g-4 mt-1">
                            <!-- Kiri: Pilihan Durasi -->
                            <div class="col-md-7">
                                <h6 class="title mb-3">Pilih Durasi</h6>
                                <div class="row g-3">
                                    @if($jempolPackage && $jempolPackage->prices)
                                        @foreach($jempolPackage->prices as $index => $price)
                                        <div class="col-sm-6">
                                            <div class="custom-control custom-control-sm custom-radio custom-control-pro form-control-wrap border rounded px-3 py-2 w-100">
                                                <input type="radio" class="custom-control-input package-duration-radio" 
                                                       name="duration_months" 
                                                       id="duration-{{ $price->duration_months }}" 
                                                       value="{{ $price->duration_months }}" 
                                                       data-price="{{ $price->price }}"
                                                       {{ $index === 0 ? 'checked' : '' }}>
                                                <label class="custom-control-label fw-bold d-block w-100" for="duration-{{ $price->duration_months }}">
                                                    {{ $price->duration_months }} Bulan
                                                    <span class="d-block fw-normal text-muted mt-1">Rp {{ number_format($price->price, 0, ',', '.') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" id="selected_package_id" value="{{ $jempolPackage->id ?? '' }}">

                                <h6 class="title mb-2 mt-4">Kode Promo / Voucher</h6>
                                <p class="text-soft fs-13px mb-2">Anda bisa memasukkan maksimal 2 kode diskon secara berurutan (Kupon & Afiliasi).</p>
                                <div class="form-group mb-1">
                                    <div class="form-control-wrap position-relative">
                                        <input type="text" class="form-control text-uppercase" id="voucher_code" placeholder="Cth: DISKON20">
                                        <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 h-100" id="btn-apply-voucher" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Apply</button>
                                    </div>
                                    <div id="voucher-message" class="fs-12px mt-1"></div>
                                </div>
                                
                                <!-- Placeholder untuk menampilkan kode yang aktif -->
                                <div id="active-vouchers-container" class="mt-2" style="display:none;">
                                    <span class="badge badge-dim bg-success" id="badge-voucher" style="display:none;"></span>
                                    <span class="badge badge-dim bg-info" id="badge-affiliate" style="display:none;"></span>
                                </div>
                            </div>

                            <!-- Kanan: Rincian Kalkulasi -->
                            <div class="col-md-5">
                                <div class="card h-100">
                                    <div class="card-inner d-flex flex-column justify-content-between h-100 pb-1 pt-1 px-1 px-md-3">

                                        <!-- ATAS -->
                                        <div>
                                            <h6 class="title mb-3">Rincian Pembayaran</h6>

                                            <div class="d-flex justify-content-between text-soft mb-2">
                                                <span>Subtotal</span>
                                                <span id="label-subtotal" class="fw-medium text-dark">Rp 0</span>
                                            </div>

                                            <div class="d-flex justify-content-between text-soft mb-2" id="row-discount" style="display: none;">
                                                <span>Diskon Voucher</span>
                                                <span id="label-discount" class="text-danger">-Rp 0</span>
                                            </div>

                                            <div class="d-flex justify-content-between text-soft mb-3" id="row-affiliate" style="display: none;">
                                                <span>Diskon Afiliasi</span>
                                                <span id="label-affiliate" class="text-danger">-Rp 0</span>
                                            </div>
                                        </div>

                                        <!-- BAWAH -->
                                        <div>
                                            <div class="border-top border-light mb-3"></div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Total Bayar</span>
                                                <span id="label-grandtotal" class="fs-4 fw-bold text-primary">Rp 0</span>
                                            </div>

                                            <button type="button" class="btn btn-primary w-100 mt-4 d-flex justify-content-center" id="btn-next-step">
                                                Lanjutkan Pembayaran <em class="icon ni ni-arrow-right ms-1"></em>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pembayaran -->
                    <div id="step-2-payment" style="display: none;">                        
                        <div class="alert alert-pro alert-primary border-primary d-flex justify-content-between">
                            <div class="alert-text">
                                <h6 class="text-dark mb-2">Segera Selesaikan Pembayaran Anda</h6>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="text-soft me-1">Transfer tepat</span>
                                    <span class="fs-5 fw-bold text-dark copy-grandtotal me-2">Rp 0</span>
                                </div>
                                <div class="text-soft fs-13px">sesuai nominal di atas untuk memudahkan pengecekan otomatis.</div>
                            </div>
                            <button type="button" class="btn btn-icon btn-xl px-2 btn-outline-primary border-0" id="btn-copy-nominal" title="Copy Nominal"><em class="icon ni ni-copy fs-18px"></em></button>
                        </div>

                        <ul class="nav nav-tabs mt-3 mb-3 justify-content-center" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-bca" role="tab" aria-controls="tab-bca" aria-selected="true"><em class="icon ni ni-building me-1"></em> Transfer BCA</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-qris" role="tab" aria-controls="tab-qris" aria-selected="false"><em class="icon ni ni-qr me-1"></em> QRIS</a>
                            </li>
                        </ul>
                        <div class="tab-content border rounded p-3 mb-4">
                            <div class="tab-pane active" id="tab-bca">
                                <div class="d-flex flex-column justify-content-center align-items-center text-center py-4">
                                    <div class="mb-3">
                                        <img src="{{ asset('public/assets/images/bca-logo.png') }}" alt="BCA" style="height: 40px;" onerror="this.style.display='none'">
                                        <em class="icon ni ni-building text-primary bca-icon-fallback" style="font-size: 3.5rem;"></em>
                                    </div>
                                    <h5 class="text-primary mb-2">Bank BCA</h5>
                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                        <h2 class="fw-bold mb-0 me-2" id="rek-bca">123 456 7890</h2>
                                        <button class="btn btn-icon btn-sm btn-outline-light" onclick="navigator.clipboard.writeText('1234567890')"><em class="icon ni ni-copy"></em></button>
                                    </div>
                                    <span class="sub-text fs-15px">a/n Jari POS Dummy</span>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-qris">
                                <div class="row align-items-center justify-content-center py-2">
                                    <div class="col-sm-auto text-center mb-3 mb-sm-0">
                                        <img src="{{ asset('QRIS-ONLY.jpg') }}" alt="QRIS" class="img-fluid border rounded shadow-sm p-2" style="width: 220px; height: 220px; object-fit: contain;">
                                    </div>
                                    <div class="col-sm px-sm-4 mt-2">
                                        <div class="text-soft fs-13px mb-3 text-center text-sm-start">
                                            Scan QR diatas melalui aplikasi m-banking atau e-wallet (GoPay, OVO, Dana, LinkAja, ShopeePay).<br>
                                            Pastikan pembayaran ke <strong class="text-dark">Jari POS Official</strong>.
                                        </div>
                                        <a href="{{ asset('QRIS.jpg') }}" download="QRIS_Jari_POS.jpg" class="btn btn-sm btn-outline-primary w-100 justify-content-center">
                                            <em class="icon ni ni-download"></em><span>Download QR Image</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-success btn-lg" id="btn-checkout" style="min-width: 250px;">
                                <em class="icon ni ni-whatsapp" style="font-size: 1.25rem;"></em><span>Konfirmasi via WhatsApp</span>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-lg mt-2 mt-md-0" id="btn-prev-step"><em class="icon ni ni-arrow-left"></em> Kembali</button>
                            <div class="mt-2 text-soft fs-12px">Klik tombol "Konfirmasi" di atas setelah Anda melakukan transfer.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profile -->
    <div class="modal fade" tabindex="-1" role="dialog" id="profile-edit">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Informasi Akun</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#" id="form-update-profile">
                    <div class="modal-body">
                        <div class="row gy-2">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="full-name">Nama Lengkap</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="full-name" name="name" value="{{ $user->name }}" placeholder="Masukkan nama Anda" required>
                                    </div>
                                </div>
                            </div>
                            <!-- Phone -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="phone-no">Nomor Handphone</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="phone-no" name="phone" value="{{ $user->phone }}" placeholder="Contoh: 08123456789">
                                    </div>
                                </div>
                            </div>
                            <!-- Birth Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="birth-date">Tanggal Lahir</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control date-picker" data-date-format="dd/mm/yyyy" id="birth-date" name="birth_date" value="{{ $user->birth_date ? date('d/m/Y', strtotime($user->birth_date)) : '' }}">
                                    </div>
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="address">Alamat</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="address" name="address" placeholder="Detail alamat" rows="3">{{ $user->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn-save-profile">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Update Photo -->
    <div class="modal fade" tabindex="-1" role="dialog" id="updatePhotoModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ganti Foto Profil</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-md">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-3 w-100 text-center" style="max-height: 350px;">
                            @if($user->profile_picture)
                                <img id="image-to-crop" src="{{ asset($user->profile_picture) }}" style="max-width: 100%; display: block; margin: 0 auto;">
                            @else
                                <div id="avatar-placeholder" class="user-avatar bg-primary mx-auto mb-2" style="width: 150px; height: 150px; font-size: 3rem;">
                                    <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                                <img id="image-to-crop" src="" style="max-width: 100%; display: none; margin: 0 auto;">
                            @endif
                        </div>
                        <div class="form-group w-100">
                            <label class="form-label">Pilih Gambar Baru</label>
                            <div class="form-control-wrap">
                                <div class="form-file">
                                    <input type="file" class="form-file-input" id="upload-avatar" accept="image/png, image/jpeg, image/jpg">
                                    <label class="form-file-label" for="upload-avatar">Pilih File</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-crop-upload">Terapkan Foto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Cropperjs dynamically -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <!-- Load moment js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
@endsection

