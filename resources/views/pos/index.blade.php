@extends('layouts.pos')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pages/pos/index.css') }}">
@endsection

@section('content')
<div class="pos-wrapper">
    <!-- Full Width Navbar -->
    <nav class="pos-navbar">
        <div class="pos-navbar-top">
            <div class="pos-navbar-left">
                <div class="pos-navbar-brand d-block d-md-none">
                     <!-- Removed Toggle Button, can put Logo here if needed, or empty -->
                </div>
                <div>
                    <h1 class="pos-welcome">Halo, {{ explode(' ', auth()->user()->name ?? 'Guest')[0] }}</h1>
                    <div class="pos-date" id="currentDate"></div>
                </div>
            </div>
            
            <div class="pos-navbar-center desktop-only">
                <div class="pos-search">
                    <svg class="pos-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="searchProduct" placeholder="Cari produk..." autocomplete="off">
                </div>
            </div>
            
            <div class="pos-navbar-right">
                <!-- Unified Actions (Visible on all devices) -->
                <div class="pos-actions d-flex align-items-center">
                    <!-- Dashboard Button -->
                    <a href="{{ route('dashboard') }}" class="pos-navbar-btn me-2 d-none d-md-inline-flex" title="Dashboard">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </a>

                    <!-- Refresh Button -->
                    <button class="pos-navbar-btn me-2" title="Refresh" onclick="loadProducts()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>

                    <!-- Printer Status Button -->
                    <button class="pos-navbar-btn me-2" id="printerNavBtn" title="Printer: Tidak Terhubung" onclick="handlePrinterNavClick()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span class="printer-status-dot" id="printerStatusIndicator"></span>
                    </button>

                    <!-- Transaction History Button -->
                    <button class="pos-navbar-btn me-2" title="Riwayat Transaksi" onclick="openHistoryModal()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div class="dropdown d-inline-block me-2">
                        <button class="pos-navbar-btn border-0" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="pos-notification-badge d-none" id="navNotificationBadge">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end p-0">
                            <div class="dropdown-head">
                                <span class="sub-title nk-dropdown-title">Notifikasi</span>
                                <a href="#" onclick="event.preventDefault(); markAllRead()">Tandai Semua Dibaca</a>
                            </div>
                            <div class="dropdown-body">
                                <div class="nk-notification" id="notificationDropdownList">
                                    <div class="text-center p-3 text-muted">
                                        <em class="icon ni ni-bell fs-1 opacity-50"></em>
                                        <p class="small mt-1">Tidak ada notifikasi baru</p>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-foot center">
                                <a href="#" onclick="event.preventDefault(); clearNotifications()">Hapus Semua</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Avatar Dropdown -->
                    <div class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="pos-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <span>{{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{ auth()->user()->name ?? 'Guest' }}</span>
                                        <span class="sub-text">{{ auth()->user()->email ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="#" onclick="event.preventDefault(); openSettingsModal()">
                                            <em class="icon ni ni-setting-alt"></em>
                                            <span>Pengaturan POS</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="event.preventDefault(); toggleTheme()">
                                            <em class="icon ni ni-moon"></em>
                                            <span>Mode Gelap</span>
                                        </a>
                                    </li>
                                    <!-- Mobile Dashboard Link -->
                                    <li class="d-md-none">
                                        <a href="{{ route('dashboard') }}">
                                            <em class="icon ni ni-dashboard"></em>
                                            <span>Kembali ke Dashboard</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <em class="icon ni ni-signout"></em>
                                            <span>Keluar</span>
                                        </a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Search Row -->
        <div class="pos-navbar-bottom mobile-only">
            <div class="pos-search">
                <svg class="pos-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchProductMobile" placeholder="Cari produk..." autocomplete="off">
            </div>
        </div>
    </nav>

    <!-- Offline Status Bar -->
    <div class="pos-offline-bar" id="offlineStatusBar" style="display: none;">
        <span class="pos-offline-icon">📡</span>
        <span id="offlineStatusText">Mode Offline</span>
        <span class="pos-pending-badge" id="pendingCount">0</span>
        <button class="pos-sync-btn" onclick="syncPendingTransactions()" title="Sinkronkan Sekarang">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu Sidebar Removed -->

    <!-- Content Area -->
    <div class="pos-content">
        <!-- Left Panel - Products -->
        <div class="pos-products-panel">
            <!-- Category Pills -->
            <div class="pos-categories" id="categoryContainer">
                <button class="pos-category-pill active" data-category="all">Semua Menu</button>
                <!-- Categories will be loaded dynamically -->
            </div>

            <!-- Products Grid -->
            <div class="pos-products-wrapper">
                <div class="pos-products-grid" id="productsGrid">
                    <!-- Products will be loaded dynamically -->
                    <div class="pos-loading">
                        <div class="pos-spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Cart -->
        <div class="pos-cart-panel">
            <!-- Cart Items Header -->
            <div class="pos-cart-items-header">
                <div class="pos-cart-title-wrapper">
                    <div class="pos-cart-icon-wrapper">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h3 class="pos-cart-title">Pesanan Saat Ini</h3>
                </div>
                <div class="pos-cart-count-badge" id="cartCountBadge">0 Item</div>
            </div>

            <!-- Cart Items List -->
            <div class="pos-cart-items" id="cartItems">
                <div class="pos-empty">
                    <div class="pos-empty-icon">🛒</div>
                    <div class="pos-empty-text">Keranjang kosong<br>Tambah produk untuk memulai</div>
                </div>
            </div>



            <!-- Voucher Section -->
            <div class="pos-voucher-section">
                <button class="pos-voucher-btn" onclick="openVoucherModal()" id="voucherBtn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span id="voucherBtnText">Pilih Voucher</span>
                </button>
            </div>

            <!-- Cart Summary -->
            <div class="pos-cart-summary">
                <div class="pos-summary-row">
                    <span class="pos-summary-label">Subtotal</span>
                    <span class="pos-summary-value" id="subtotal">Rp 0</span>
                </div>
                <div class="pos-summary-row" id="discountRow" style="display: none;">
                    <span class="pos-summary-label">Diskon</span>
                    <span class="pos-summary-value pos-discount-value" id="discountAmount">- Rp 0</span>
                </div>
                <div class="pos-summary-row" id="taxRow">
                    <span class="pos-summary-label" id="taxLabel">Pajak (11%)</span>
                    <span class="pos-summary-value" id="taxAmount">Rp 0</span>
                </div>
                <div class="pos-summary-row total">
                    <span class="pos-summary-label">Total Pembayaran</span>
                    <span class="pos-summary-value" id="totalAmount">Rp 0</span>
                </div>
            </div>

            <!-- Cart Actions -->
            <div class="pos-cart-actions">
                <button class="pos-action-btn btn btn-dim btn-outline-danger" onclick="clearCart()">
                    <em class="icon ni ni-trash me-1"></em> Reset
                </button>
                <button class="pos-action-btn pos-btn-order" onclick="openCheckoutModal()">Proses Pesanan</button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal V2 -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembayaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="checkout-summary mb-4">
                    <div class="pos-summary-row flex-column">
                        <span class="pos-summary-label fw-bold">TOTAL YANG HARUS DIBAYAR</span>
                        <span class="pos-summary-value text-primary" id="checkoutTotal" style="font-size: 1.25rem;">Rp 0</span>
                    </div>
                </div>

                <div class="pos-cart-row">
                    <div class="pos-cart-field">
                        <label class="pos-cart-label">Tipe Pesanan</label>
                        <select class="form-control js-select2" id="orderType">
                            <option value="dine_in">Makan di Tempat</option>
                            <option value="take_away">Bawa Pulang</option>
                            <option value="delivery">Pengiriman</option>
                        </select>
                    </div>
                    <div class="pos-cart-field">
                        <label class="pos-cart-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Nama Pelanggan">
                    </div>
                </div>

                <div class="pos-cart-field mt-3">
                    <label class="pos-cart-label">Metode Pembayaran</label>
                    <select class="js-select2" id="paymentMethod">
                        <option value="">Pilih Metode Pembayaran</option>
                        @foreach($payments as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button class="pos-action-btn pos-btn-cancel" onclick="closeCheckoutModal()">Batal</button>
                <button class="pos-action-btn pos-btn-order" onclick="confirmOrder()">Konfirmasi Pesanan</button>
            </div>
        </div>
    </div>
</div>

<!-- Voucher Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Voucher</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <table class="table table-striped table-bordered" id="voucherTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama Promo</th>
                            <th>Min. Order</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengaturan POS</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Printer Settings -->
                <div class="pos-settings-section">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Printer & Struk</h6>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Target Printer</label>
                        <select class="form-control" id="settingPrinterTarget">
                            <option value="cashier">Printer Kasir (Default)</option>
                            <option value="kitchen">Printer Dapur</option>
                            <option value="both">Keduanya</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Ukuran Kertas</label>
                        <select class="form-control" id="settingPaperSize">
                            <option value="58">58mm (Kecil)</option>
                            <option value="80">80mm (Standar)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Cetak Otomatis setelah Bayar</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="settingAutoPrint">
                            <label class="custom-control-label" for="settingAutoPrint"></label>
                        </div>
                    </div>
                </div>
                
                <hr>

                <!-- Bluetooth Printer Settings -->
                <div class="pos-settings-section">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Printer Bluetooth</h6>
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded" style="background: var(--pos-bg-secondary, #f1f5f9);">
                        <div>
                            <div class="small text-muted">Status Printer</div>
                            <div class="fw-bold" id="printerStatusText">Tidak terhubung</div>
                            <div class="small text-muted" id="connectedPrinterName">-</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary" id="btnConnectPrinter" onclick="connectBluetoothPrinter()">
                                <em class="icon ni ni-bluetooth me-1"></em> Hubungkan
                            </button>
                            <button class="btn btn-sm btn-outline-danger" id="btnDisconnectPrinter" onclick="disconnectBluetoothPrinter()" style="display: none;">
                                <em class="icon ni ni-cross me-1"></em> Putuskan
                            </button>
                        </div>
                    </div>

                    <button class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="testPrintThermal()">
                        <em class="icon ni ni-printer me-1"></em> Test Print
                    </button>

                    <button class="btn btn-sm btn-outline-warning w-100" onclick="forgetPrinterDevice()">
                        <em class="icon ni ni-trash me-1"></em> Lupakan Perangkat (Ganti Printer)
                    </button>

                    <small class="text-muted d-block mt-2">
                        <em class="icon ni ni-info me-1"></em>
                        Gunakan browser Chrome/Edge. Printer yang didukung: VSC MP-58C dan printer thermal Bluetooth lainnya.
                    </small>
                </div>

                <hr>

                <!-- QZ Tray Settings -->
                <div class="pos-settings-section">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Integrasi QZ Tray (Desktop)</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Gunakan QZ Tray</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="settingUseQzTray">
                            <label class="custom-control-label" for="settingUseQzTray"></label>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3" id="qzPrinterGroup" style="display: none;">
                        <label class="form-label">Pilih Printer</label>
                        <div class="input-group">
                            <select class="form-control" id="settingQzPrinterName">
                                <option value="">-- Pilih Printer --</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button" onclick="findPrinters()" title="Refresh Printer">
                                <em class="icon ni ni-reload"></em>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">Pastikan aplikasi QZ Tray sudah berjalan.</small>
                    </div>

                    <div id="qzStatus" class="alert alert-light text-center small py-1 mb-0" style="display: none;">
                        Status: <span id="qzStatusText">Menunggu...</span>
                    </div>
                </div>

                <hr>

                <!-- Tax Settings -->
                <div class="pos-settings-section">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Pajak (PPN)</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Aktifkan Pajak</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="settingEnableTax" checked>
                            <label class="custom-control-label" for="settingEnableTax"></label>
                        </div>
                    </div>
                    
                    <div class="form-group mb-2" id="taxRateGroup">
                        <label class="form-label">Persentase Pajak (%)</label>
                        <input type="number" class="form-control" id="settingTaxRate" value="11" min="0" step="0.1">
                    </div>
                </div>

                <hr>

                <!-- UI Settings -->
                <div class="pos-settings-section">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Tampilan & Sistem</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tampilkan Gambar Produk</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="settingShowImages" checked>
                            <label class="custom-control-label" for="settingShowImages"></label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tampilan Produk</span>
                        <div class="btn-group btn-group-sm toggle-view-mode" role="group">
                            <input type="radio" class="btn-check" name="viewMode" id="viewModeGrid" value="grid" checked>
                            <label class="btn btn-outline-primary" for="viewModeGrid"><i class="ni ni-grid-fill me-1"></i> Grid</label>

                            <input type="radio" class="btn-check" name="viewMode" id="viewModeList" value="list">
                            <label class="btn btn-outline-primary" for="viewModeList"><i class="ni ni-list-thumb-fill me-1"></i> List</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Suara Efek (Beep)</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="settingSoundEffect" checked>
                            <label class="custom-control-label" for="settingSoundEffect"></label>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Cache Management -->
                <div class="pos-settings-section">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Cache Offline</h6>
                    
                    <div class="mb-3">
                        <span class="text-muted small" id="storageInfo">Memuat info cache...</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearProductCache()">
                            <em class="icon ni ni-box me-1"></em> Hapus Produk
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearCategoryCache()">
                            <em class="icon ni ni-folder me-1"></em> Hapus Kategori
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSyncHistory()">
                            <em class="icon ni ni-histroy me-1"></em> Hapus History
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="clearAllCache()">
                            <em class="icon ni ni-trash me-1"></em> Hapus Semua Cache
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="pos-action-btn pos-btn-primary w-100" onclick="saveSettings()">Simpan Pengaturan</button>
            </div>
        </div>
    </div>
</div>

<!-- Order Success Modal -->
<div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
        <div class="modal-content" style="border: none; border-radius: 16px;">
            <div class="modal-body text-center" style="padding: 32px 24px 24px; position: relative;">
                <!-- Close Button -->
                <button type="button" class="btn-close" onclick="closeOrderSuccessModal()" 
                    style="position: absolute; top: 14px; right: 14px; z-index: 10; opacity: 0.4;"></button>

                <!-- Success Icon -->
                <div style="margin-bottom: 16px;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                        <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <h5 class="fw-bold" style="font-size: 1.15rem; margin-bottom: 6px;">Pesanan Berhasil!</h5>
                <p class="text-muted" id="successOrderInfo" style="font-size: 0.85rem; margin-bottom: 24px;">-</p>
                
                <!-- Action Buttons -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <button class="btn btn-primary w-100 btn-print-receipt" id="btnPrintReceiptBluetooth" onclick="handlePrintReceiptBluetooth()" disabled>
                        <em class="icon ni ni-printer me-1"></em> Cetak Struk (Bluetooth)
                    </button>
                    <button class="btn btn-outline-secondary w-100" onclick="handlePrintReceiptBrowser()" style="padding: 8px 16px;">
                        <em class="icon ni ni-printer me-1"></em> Cetak via Browser
                    </button>
                    <button class="btn btn-light w-100" onclick="closeOrderSuccessModal()" style="padding: 8px 16px;">
                        <em class="icon ni ni-check me-1"></em> Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Transaksi Hari Ini</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom d-flex align-items-center gap-2">
                    <input type="date" class="form-control form-control-sm" id="historyDateFilter" style="max-width: 200px;">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadTransactionHistory()">
                        <em class="icon ni ni-search"></em>
                    </button>
                </div>
                <div class="history-list" id="historyList" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center p-4 text-muted">
                        <em class="icon ni ni-loader ni-spin fs-3"></em>
                        <p class="small mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.posRoutes = {
        categories: "{{ route('pos.categories') }}",
        products: "{{ route('pos.products') }}",
        vouchers: "{{ route('pos.vouchers') }}",
        sync: "{{ route('pos.sync.transactions') }}",
        store: "{{ route('pos.store') }}",
        receiptData: "{{ url('pos/receipt-data') }}",
        transactions: "{{ route('pos.transactions') }}"
    };
</script>
<script src="{{ asset('js/pos/offline-db.js') }}"></script>
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/10.9.0/jsrsasign-all-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.min.js"></script>
@endsection
@endsection