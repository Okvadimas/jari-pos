@extends('layouts.pos')

@section('content')
<div class="pos-wrapper">
    <!-- Full Width Navbar -->
    <nav class="pos-navbar">
        <div class="pos-navbar-top">
            <div class="pos-navbar-left">
                <button class="pos-mobile-menu-btn" onclick="toggleMobileMenu()">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
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
                <!-- Desktop Buttons -->
                <div class="pos-desktop-actions">
                    <div class="pos-theme-toggle">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                        </svg>
                        <div class="pos-toggle-switch" onclick="toggleTheme()"></div>
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </div>
                    
                    <button class="pos-navbar-btn" title="Refresh" onclick="loadProducts()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div class="dropdown d-inline-block">
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
                    
                    <button class="pos-navbar-btn" title="Pengaturan" onclick="openSettingsModal()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
                
                <div class="pos-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
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

    <!-- Mobile Menu Sidebar/Drawer -->
    <div class="pos-mobile-menu-overlay" id="mobileMenuOverlay" onclick="toggleMobileMenu()"></div>
    <div class="pos-mobile-menu" id="mobileMenu">
        <div class="pos-mobile-menu-header">
            <h3>Menu</h3>
            <button class="pos-close-btn" onclick="toggleMobileMenu()">&times;</button>
        </div>
        <div class="pos-mobile-menu-items">
            <!-- Content will be cloned from desktop actions using JS or just static duplication -->
             <div class="pos-theme-toggle-mobile" onclick="toggleTheme()">
                <span>Dark Mode</span>
                <div class="pos-toggle-switch"></div>
            </div>
            <a href="#" class="pos-mobile-item" onclick="loadProducts(); toggleMobileMenu()">
                Connect / Refresh
            </a>
            <a href="#" class="pos-mobile-item">Notifications</a>
            <a href="#" class="pos-mobile-item">Settings</a>
            <div class="pos-mobile-divider"></div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="pos-mobile-item text-danger">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

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
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu Kredit/Debit</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
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
                            <label class="custom-control-label" for="settingAutoPrint"></label>
                        </div>
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
            </div>
            <div class="modal-footer">
                <button class="pos-action-btn pos-btn-primary w-100" onclick="saveSettings()">Simpan Pengaturan</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.posRoutes = {
        categories: "{{ route('pos.categories') }}",
        products: "{{ route('pos.products') }}",
        vouchers: "{{ route('pos.vouchers') }}"
    };
</script>
@endsection