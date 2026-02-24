/**
 * POS Page JavaScript
 */

console.log('POS Page JavaScript loaded');

$(document).ready(function() {
    // Initialize OfflineDB
    initOfflineMode();
    
    // Initialize
    setCurrentDate();
    loadCategories();
    loadProducts();

    // Background Sync for Offline Mode
    if (navigator.onLine) {
        setTimeout(syncOfflineData, 2000);
    }
    
    // Feature: Notifications & Settings
    checkNotifications();
    loadSettings();

    // Auto-reconnect Bluetooth printer
    initBluetoothPrinter();
    
    // Connection Listener
    window.addEventListener('online',  handleConnectionChange);
    window.addEventListener('offline', handleConnectionChange);
    
    // Search with debounce
    let searchTimeout;
    $('#searchProduct, #searchProductMobile').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadProducts();
        }, 300);
    });

    // Mobile Cart Toggle
    $('.pos-cart-items-header').on('click', function() {
        if ($(window).width() <= 768) {
            $('.pos-cart-panel').toggleClass('expanded');
        }
    });

    // Close mobile cart when clicking outside (on product panel)
    $('.pos-products-panel').on('click', function() {
        if ($(window).width() <= 768 && $('.pos-cart-panel').hasClass('expanded')) {
            $('.pos-cart-panel').removeClass('expanded');
        }
    });

    // Infinite Scroll Listener
    $('.pos-products-wrapper').on('scroll', function() {
        const wrapper = $(this);
        if (wrapper.scrollTop() + wrapper.innerHeight() >= wrapper[0].scrollHeight - 50) {
            if (!isLoading && currentPage < lastPage) {
                loadProducts(true);
            }
        }
    });
});

// ============================================
// State Management
// ============================================
let cart = [];
let categories = [];
let selectedVoucher = null;
let notifications = [];
let settings = {
    printerTarget: 'cashier',
    paperSize: '80',
    autoPrint: false,
    showImages: true,
    viewMode: 'grid', // 'grid' or 'list'
    soundEffect: true,
    enableTax: true,
    taxRate: 11,
    useQzTray: false,
    qzPrinterName: '',
    useBluetoothPrinter: true
};

// Pagination State
let currentPage = 1;
let lastPage = 1;
let isLoading = false;

// Offline State
let isOffline = !navigator.onLine;
let pendingTransactionsCount = 0;

// Import OfflineDB (loaded via script tag or bundled)
// Ensure OfflineDB is available globally

// ============================================
// Utility Functions
// ===========================================
function setCurrentDate() {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const today = new Date().toLocaleDateString('id-ID', options);
    $('#currentDate').text(today);
}

// ============================================
// Category Functions
// ============================================
async function loadCategories() {
    // Try offline fallback if not online
    if (!navigator.onLine && typeof OfflineDB !== 'undefined') {
        try {
            const cached = await OfflineDB.getCachedCategories();
            if (cached.length > 0) {
                console.log('[Offline] Loading categories from cache');
                categories = cached;
                renderCategories();
                return;
            }
        } catch (e) {
            console.error('[Offline] Failed to load cached categories:', e);
        }
    }
    
    $.ajax({
        url: window.posRoutes.categories,
        type: 'GET',
        success: async function(response) {
            if (response.status) {
                categories = response.data;
                renderCategories();
                
                // Cache for offline use
                if (typeof OfflineDB !== 'undefined') {
                    await OfflineDB.cacheCategories(response.data);
                }
            }
        },
        error: async function(xhr, status, error) {
            console.error('Error loading categories:', error);
            // Try cache on error
            if (typeof OfflineDB !== 'undefined') {
                const cached = await OfflineDB.getCachedCategories();
                if (cached.length > 0) {
                    categories = cached;
                    renderCategories();
                }
            }
        }
    });
}

function renderCategories() {
    // Icon mapping based on category name keywords
    const getIcon = (name) => {
        const n = name.toLowerCase();
        if (n.includes('food') || n.includes('makan')) return '🍔';
        if (n.includes('drink') || n.includes('minum')) return '🥤';
        if (n.includes('snack') || n.includes('cemil')) return '🍟';
        if (n.includes('coffee') || n.includes('kopi')) return '☕';
        if (n.includes('dessert')) return '🍰';
        return '🍽️';
    };

    let html = `<button class="pos-category-pill active" data-category="all">🍽️ Semua Menu</button>`;
    
    categories.forEach(cat => {
        const icon = getIcon(cat.name);
        html += `<button class="pos-category-pill" data-category="${cat.id}">${icon} ${cat.name}</button>`;
    });
    
    $('#categoryContainer').html(html);
    
    // Bind click events
    $('.pos-category-pill').on('click', function() {
        $('.pos-category-pill').removeClass('active');
        $(this).addClass('active');
        loadProducts();
    });
}

// ============================================
// Offline Sync Functions (Background Sync Get All Products) For Prepare Offline Mode
// ============================================
async function syncOfflineData() {
    console.log('[Sync] Starting background sync...');
    if (typeof OfflineDB === 'undefined') return;

    try {
        const response = await $.ajax({
            url: window.posRoutes.products,
            type: 'GET',
            data: { all_items: 1 }
        });

        if (response.status && response.data) {
            // Extract products array - handle paginated or direct array response
            let products = response.data;
            if (!Array.isArray(products) && products.data) {
                products = products.data;
            }
            if (!Array.isArray(products)) {
                products = Object.values(products);
            }
            await OfflineDB.cacheProducts(products);
            console.log('[Sync] All products cached successfully (' + products.length + ' items)');
        }
    } catch (error) {
        console.error('[Sync] Failed to sync offline data:', error);
    }
}

// ============================================
// Product Functions
// ============================================
async function loadProducts(isAppend = false) {
    if (isLoading) return;
    
    if (!isAppend) {
        currentPage = 1;
        $('#productsGrid').html('<div class="pos-loading"><div class="pos-spinner"></div></div>');
    }

    const categoryId = $('.pos-category-pill.active').data('category');
    const search = $('#searchProduct').val();
    
    // Lock loading
    isLoading = true;

    // Try offline fallback if not online
    if (!navigator.onLine && typeof OfflineDB !== 'undefined') {
        try {
            // Get ALL cached products (filtered by category/search if possible)
            let cached = await OfflineDB.getCachedProducts(categoryId, search);
            
            if (cached.length > 0) {
                console.log('[Offline] Loading products from cache. Total:', cached.length);
                
                // Manual Pagination Logic
                const itemsPerPage = 20;
                lastPage = Math.ceil(cached.length / itemsPerPage);
                
                // Valid page check
                if (currentPage > lastPage) {
                    isLoading = false;
                    return; 
                }

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const pagedProducts = cached.slice(startIndex, endIndex);

                renderProducts(pagedProducts, isAppend); // Offline renders same way
                
                // Increment page for next scroll
                currentPage++;
                
                isLoading = false;
                return;
            }
        } catch (e) {
            console.error('[Offline] Failed to load cached products:', e);
        }
        $('#productsGrid').html(getEmptyStateHTML('🛒', 'Tidak ada data produk offline', 'Silakan koneksikan internet untuk memuat data'));
        isLoading = false;
        return;
    }
    
    $.ajax({
        url: window.posRoutes.products,
        type: 'GET',
        data: {
            category_id: categoryId,
            search: search,
            page: currentPage
        },
        success: async function(response) {
            console.log('Products response:', response);
            if (response.status) {
                // Determine if response.data is pagination object or direct array
                let products = [];
                if (response.data.data) {
                    products = response.data.data;
                    lastPage = response.data.last_page;
                    // Only cache first page or handle offline pagination logic later
                } else {
                    products = response.data;
                    lastPage = 1; // Assume single page if no pagination data
                }

                renderProducts(products, isAppend);
                checkLowStockProducts(products);
                
                // Cache for offline use
                // logic moved to background sync (syncOfflineData) to avoid partial caching issues
                // But we can still update individual counts or small batches if needed, 
                // but simpler to rely on background sync for consistency.
                
                // Increment page for next load
                currentPage++;
            } else {
                if (!isAppend) $('#productsGrid').html(getEmptyStateHTML('⚠️', 'Gagal memuat produk'));
            }
            isLoading = false;
        },
        error: async function(xhr, status, error) {
            console.error('Error loading products:', error);
            // Try cache on error (only for first page)
            if (!isAppend && typeof OfflineDB !== 'undefined') {
                const cached = await OfflineDB.getCachedProducts(categoryId, search);
                if (cached.length > 0) {
                    renderProductsWithOfflineStock(cached);
                    isLoading = false;
                    return;
                }
            }
            if (!isAppend) $('#productsGrid').html(getEmptyStateHTML('❌', `Gagal memuat produk<br><small>${error}</small>`));
            isLoading = false;
        }
    });
}

function renderProducts(products, isAppend = false) {
    if ((!products || products.length === 0) && !isAppend) {
        $('#productsGrid').html(getEmptyStateHTML('🛒', 'Produk tidak ditemukan', 'Coba sesuaikan pencarian atau kategori'));
        return;
    }
    
    // Ensure cache init
    if (!window.productsCache) window.productsCache = {};

    // Store products in global cache
    products.forEach(p => {
        window.productsCache[p.id] = {
            id: p.id,
            name: p.name,
            price_display: p.price_display || 0,
            image: p.image || null,
            variants: p.variants || [],
            stock: p.stock !== undefined ? p.stock : 15 // Mock stock if not present
        };
    });
    
    let html = '';
    products.forEach(product => {
        const price = product.price_display || 0;
        const inCart = cart.find(item => item.id === product.id);
        const productName = product.name || '';
        const imageSrc = product.image ? product.image : '/images/product-sample.png';
        const stock = product.stock !== undefined ? product.stock : 15; // Mock stock
        
        // Stock Logic
        let stockBadge = '';
        if (stock < 5) {
            stockBadge = `<div class="pos-stock-badge">Sisa ${stock}</div>`;
        }

        html += `
            <div class="pos-product-card" data-product-id="${product.id}">
                ${stockBadge}   
                <img src="${imageSrc}" class="pos-product-image" alt="${productName}">
                <div class="pos-product-info">
                    <h4 class="pos-product-name">${productName}</h4>
                    <div class="pos-product-price">${formatRupiah(price)}</div>
                    <button class="pos-btn-add ${inCart ? 'pos-btn-add-outline' : 'pos-btn-add-primary'}"
                            onclick="addToCartById(${product.id})">
                        ${inCart ? `Di Keranjang (${inCart.qty})` : '+ Tambah'}
                    </button>
                </div>
            </div>
        `;
    });
    
    if (isAppend) {
        $('#productsGrid').append(html);
    } else {
        $('#productsGrid').html(html);
    }
}

// Helper for Empty State
function getEmptyStateHTML(icon, title, subtitle = '') {
    return `
        <div class="pos-empty" style="grid-column: 1/-1;">
            <div class="pos-empty-icon" style="font-size: 3rem; margin-bottom: 0;">${icon}</div>
            <div class="pos-empty-title">${title}</div>
            ${subtitle ? `<div class="pos-empty-text">${subtitle}</div>` : ''}
        </div>
    `;
}

// ============================================
// Cart Functions
// ============================================
function addToCartById(productId) {
    const product = window.productsCache ? window.productsCache[productId] : null;
    if (!product) {
        console.error('Product not found in cache:', productId);
        return;
    }
    addToCart(product);
}

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.push({
            id: product.id,
            variant_id: product.variants && product.variants.length > 0 ? product.variants[0].id : null,
            name: product.name,
            price: product.price_display || 0,
            image: product.image || null,
            qty: 1
        });
    }
    
    renderCart();
    updateProductButtons();
    calculateTotal();
}

function updateProductButtons() {
    // Update all add to cart buttons without re-rendering entire grid
    $('#productsGrid .pos-product-card').each(function() {
        const productId = $(this).data('product-id');
        const inCart = cart.find(item => item.id === productId);
        const btn = $(this).find('.pos-btn-add');
        
        if (inCart) {
            btn.removeClass('pos-btn-add-primary').addClass('pos-btn-add-outline');
            btn.html(`Di Keranjang (${inCart.qty})`);
        } else {
            btn.removeClass('pos-btn-add-outline').addClass('pos-btn-add-primary');
            btn.html('+ Tambah');
        }
    });
}

function removeFromCart(productId) {
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        if (existingItem.qty > 1) {
            existingItem.qty--;
        } else {
            cart = cart.filter(item => item.id !== productId);
        }
    }
    
    renderCart();
    updateProductButtons();
    calculateTotal();
}

function updateQty(productId, action) {
    if (action === 'add') {
        const item = cart.find(i => i.id === productId);
        if (item) item.qty++;
    } else {
        removeFromCart(productId);
    }
    renderCart();
    updateProductButtons();
    calculateTotal();
}

function deleteFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    renderCart();
    updateProductButtons();
    calculateTotal();
}

function getCurrentProducts() {
    let products = [];
    $('#productsGrid .pos-product-card').each(function() {
        const id = $(this).data('product-id');
        const name = $(this).find('.pos-product-name').text();
        const priceText = $(this).find('.pos-product-price').text();
        products.push({
            id: id,
            name: name,
            price_display: parseInt(priceText) || 0
        });
    });
    return products;
}

function renderCart() {
    const totalItems = cart.length;
    $('#cartCountBadge').text(`${totalItems} Item`);

    if (cart.length === 0) {
        $('#cartItems').html(`
            <div class="pos-empty">
                <div class="pos-empty-icon">🛒</div>
                <div class="pos-empty-text">Keranjang kosong<br>Tambah produk untuk memulai</div>
            </div>
        `);
        return;
    }
    
    let html = '';
    cart.forEach(item => {
        const originalPrice = item.originalPrice || item.price;
        const hasDiscount = originalPrice > item.price;
        
        html += `
            <div class="pos-cart-item">
                <div class="pos-cart-item-top">
                    ${item.image 
                        ? `<img src="${item.image}" class="pos-cart-item-image" alt="${item.name}">`
                        : `<div class="pos-cart-item-image" style="background: linear-gradient(135deg, #e5e7eb, #d1d5db); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <svg width="20" height="20" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>`
                    }
                    <div class="pos-cart-item-info">
                        <h4 class="pos-cart-item-name">${item.name}</h4>
                        <div class="pos-cart-item-price">
                            <span class="pos-cart-item-current-price">${formatRupiah(item.price)}</span>
                            ${hasDiscount ? `<span class="pos-cart-item-original-price">${formatRupiah(originalPrice)}</span>` : ''}
                        </div>
                    </div>
                </div>
                <div class="pos-cart-item-bottom">
                    <div class="pos-cart-item-qty">
                        <button class="pos-qty-btn" onclick="updateQty(${item.id}, 'remove')">−</button>
                        <span class="pos-qty-value">${item.qty}</span>
                        <button class="pos-qty-btn" onclick="updateQty(${item.id}, 'add')">+</button>
                    </div>
                    <button class="pos-delete-btn" onclick="deleteFromCart(${item.id})" title="Remove item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
    });
    
    $('#cartItems').html(html);
}

function clearCart() {
    if (cart.length === 0) return;
    
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: "Semua produk dalam keranjang akan dihapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.value) {
            cart = [];
            selectedVoucher = null;
            renderCart();
            loadProducts();
            calculateTotal();
            NioApp.Toast('Keranjang berhasil dikosongkan', 'success', { position: 'top-right' });
        }
    });
}

// ============================================
// Calculation Functions
// ============================================
function calculateSubtotal() {
    return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
}

function calculateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    
    // Calculate Discount
    let discount = 0;
    if (selectedVoucher) {
        if (selectedVoucher.type === 'percentage') {
            discount = subtotal * (selectedVoucher.amount || 0) / 100; // Corrected to use 'amount'
        } else {
            discount = selectedVoucher.amount || 0; // Corrected to use 'amount'
        }
        
        // Ensure discount does not exceed subtotal
        if (discount > subtotal) {
            discount = subtotal;
        }
        
        $('#discountRow').show();
        $('#voucherBtn').addClass('has-voucher');
        
        // Improved UI for Remove Voucher
        $('#voucherBtnText').html(`
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>${selectedVoucher.name}</span>
                <span class="ms-2 badge badge-dim bg-danger p-1 d-flex align-items-center justify-content-center" 
                      style="width: 25px; height: 25px; cursor: pointer; position: absolute; top: 10px; right: 15px; border-radius: 5px;" 
                      onclick="event.stopPropagation(); removeVoucher()"
                      title="Hapus Voucher">
                    <em class="icon ni ni-cross" style="font-size: 10px;"></em>
                </span>
            </div>
        `);

    } else {
        $('#discountRow').hide();
        $('#voucherBtn').removeClass('has-voucher p-1 ps-3 pe-2');
        $('#voucherBtnText').text('Pilih Voucher');
    }
    
    // Calculate Tax
    let tax = 0;
    if (settings.enableTax) {
        const rate = parseFloat(settings.taxRate) || 0;
        // Tax is usually calculated after discount? Or before? 
        // Standard in Indonesia often DPP = Total - Discount. Tax = DPP * 11%.
        // Or if simple retail, Tax on Subtotal?
        // Let's assume Tax is on (Subtotal - Discount).
        // If tax was previously `subtotal * TAX_RATE`, it didn't account for discount.
        // Let's assume (Subtotal - Discount) * TaxRate / 100.
        // But previously it was const TAX_RATE = 0.11; subtotal * TAX_RATE. 
        // I will follow the previous logic (Tax on Subtotal) unless typical POS logic dictates otherwise. 
        // Actually, usually Tax is on the taxable amount. If you get a discount, you pay less, so tax is less.
        // Let's change to (Subtotal - Discount) * Rate. 
        // BUT, for now let's stick to "Subtotal * Rate" if that was the "dumb" implementation, OR improve it.
        // The user just said "tax settings". I'll use (Subtotal - Discount) * Rate which is cleaner.
        // Wait, looking at previous code: `const tax = subtotal * TAX_RATE;` (Line 395). It ignored discount.
        // I will start with simpler Logic: Tax on Subtotal.
        
        tax = (subtotal - discount) * (rate / 100);
        if (tax < 0) tax = 0;
    }
    
    const total = subtotal - discount + tax;
    
    $('#subtotal').text(formatRupiah(subtotal));
    $('#taxAmount').text(formatRupiah(tax));
    $('#discountAmount').text('- ' + formatRupiah(discount));
    $('#totalAmount').text(formatRupiah(total));
    
    // Update Tax Label if hidden/shown or rate changed
    if (settings.enableTax) {
        $('#taxRow').show();
        $('#taxLabel').text(`Pajak (${settings.taxRate}%)`);
    } else {
        $('#taxRow').hide();
    }
}

// ============================================
// Voucher Functions
// ============================================
function removeVoucher() {
    selectedVoucher = null;
    calculateTotal();
    NioApp.Toast('Voucher berhasil dihapus', 'info', { position: 'top-right' });
}

function openVoucherModal() {
    loadVouchers();
    $('#voucherModal').modal('show');
}

function closeVoucherModal() {
    $('#voucherModal').modal('hide');
}

function loadVouchers() {
    if ($.fn.DataTable.isDataTable('#voucherTable')) {
        $('#voucherTable').DataTable().ajax.reload();
        return;
    }

    NioApp.DataTable('#voucherTable', {
        processing: true,
        serverSide: false, // Client-side for now as we fetch all active promos
        ajax: window.posRoutes.vouchers, // Expecting { data: [...] } from controller
        columns: [
            { data: 'name', name: 'name' },
            { 
                data: 'min_order', 
                name: 'min_order',
                render: function(data, type, row) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                data: 'amount', 
                name: 'amount',
                 render: function(data, type, row) {
                    console.log('data', data);
                    if(row.type === 'fixed') {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                    } else {
                        return data + '%';
                    }
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-primary" onclick="selectVoucher(${row.id})">
                        <em class="icon ni ni-check-circle"></em>
                    </button>`;
                }
            }
        ],
        drawCallback: function(settings) {
            // Store data globally for selectVoucher to access
            window.availableVouchers = this.api().rows().data().toArray();
        }
    });
}

function selectVoucher(id) {
    console.log('Available Vouchers: ', window.availableVouchers)
    const voucher = window.availableVouchers.find(v => v.id === id);
    console.log('Ini Voucher: ', voucher);
    if (!voucher) return;
    
    // Check min order
    if (voucher.min_order > calculateSubtotal()) {
        NioApp.Toast(`Minimal order: Rp ${new Intl.NumberFormat('id-ID').format(voucher.min_order)}`, 'warning', { position: 'top-right' });
        return;
    }
    
    selectedVoucher = voucher;
    renderCart(); 
    calculateTotal(); // Recalculate totals
    closeVoucherModal();
    NioApp.Toast('Voucher berhasil dipasang!', 'success', { position: 'top-right' });
}



// ============================================
// Order Functions
// ============================================
function openCheckoutModal() {
    if (cart.length === 0) {
        NioApp.Toast('Keranjang belanja masih kosong!', 'warning', { position: 'top-right' });
        return;
    }
    
    // Update checkout total in modal
    const totalAmount = $('#totalAmount').text();
    $('#checkoutTotal').text(totalAmount);
    
    $('#checkoutModal').modal('show');
}

function closeCheckoutModal() {
    $('#checkoutModal').modal('hide');
}

function confirmOrder() {
    const orderType = $('#orderType').val();
    const customerName = $('#customerName').val();
    const paymentMethod = $('#paymentMethod').val();
    
    // Validation
    if (!customerName) {
        NioApp.Toast('Silakan isi nama pelanggan!', 'warning', { position: 'top-right' });
        $('#customerName').focus();
        return;
    }
    
    if (!paymentMethod) {
        NioApp.Toast('Silakan pilih metode pembayaran!', 'warning', { position: 'top-right' });
        $('#paymentMethod').focus();
        return;
    }
    
    if (!orderType) {
        NioApp.Toast('Silakan pilih tipe pesanan!', 'warning', { position: 'top-right' });
        return;
    }
    
    // Proceed to place order
    placeOrder();
}

function placeOrder() {
    if (cart.length === 0) {
        NioApp.Toast('Mohon tambahkan produk ke keranjang terlebih dahulu', 'warning', { position: 'top-right' });
        return;
    }
    
    const orderType = $('#orderType').val();
    const customerName = $('#customerName').val();
    const phoneNumber = $('#phoneNumber').val(); // Assuming this field exists or needs to be added to modal if important
    const paymentMethod = $('#paymentMethod').val(); // Sending payment method if backend supports it, though current schema doesn't show payment table. 
    // Wait, SalesOrder schema didn't have payment_method column in recent migration view, but let's send what we have.
    
    if (!orderType) {
        NioApp.Toast('Mohon pilih tipe pesanan', 'warning', { position: 'top-right' });
        return;
    }

    // Prepare Items
    const items = cart.map(item => ({
        product_id: item.id,
        variant_id: item.variant_id,
        quantity: item.qty,
        price: item.price
    }));
    
    const orderData = {
        order_type: orderType,
        customer_name: customerName,
        phone_number: phoneNumber,
        items: items,
        payment_method_id: paymentMethod, // Added payment_method_id
        voucher_id: selectedVoucher ? selectedVoucher.id : null,
        _token: $('meta[name="csrf-token"]').attr('content') // CSRF Token
    };

    // Show Loading
    const btn = $('#checkoutModal .pos-btn-order');
    const originalText = btn.text();
    btn.prop('disabled', true).text('Memproses...');
    
    $.ajax({
        url: window.posRoutes.store,
        type: 'POST',
        data: orderData,
        success: function(response) {
            btn.prop('disabled', false).text(originalText);
            
            if (response.status) {
                // Close checkout modal
                closeCheckoutModal();

                // Store last order ID for printing
                window.lastOrderId = response.data.id;
                window.lastOrderInvoice = response.data.invoice_number || '';
                window.lastOrderAmount = response.data.final_amount || 0;

                // Auto-print via QZ Tray if enabled (legacy desktop flow)
                if (settings && settings.autoPrint && settings.useQzTray) {
                    printReceipt(response.data.id);
                }

                // Show success modal with print options
                showOrderSuccessModal(response.data);

                // Clear cart state
                cart = [];
                selectedVoucher = null;
                $('#customerName').val('');
                $('#paymentMethod').val('');
                $('#orderType').val('dine_in');
                
                renderCart();
                updateProductButtons();
                calculateTotal();
            } else {
                 NioApp.Toast(response.message || 'Gagal menyimpan pesanan', 'error', { position: 'top-right' });
            }
        },
        error: function(xhr, status, error) {
            btn.prop('disabled', false).text(originalText);
            let msg = 'Terjadi kesalahan sistem';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            NioApp.Toast(msg, 'error', { position: 'top-right' });
            console.error('Order Error:', error);
        }
    });
}



// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        $('.modal').modal('hide');
    }
});


// ============================================
// Export Functions
// ============================================
window.loadProducts = loadProducts;
window.addToCartById = addToCartById;
window.placeOrder = placeOrder;
window.openCheckoutModal = openCheckoutModal;
window.closeCheckoutModal = closeCheckoutModal;
window.confirmOrder = confirmOrder;
window.clearCart = clearCart;
window.updateQty = updateQty;
window.clearNotifications = clearNotifications;
window.openSettingsModal = openSettingsModal;
window.closeSettingsModal = closeSettingsModal;
window.saveSettings = saveSettings;
window.markAllRead = markAllRead;
window.openVoucherModal = openVoucherModal;
window.closeVoucherModal = closeVoucherModal;
window.selectVoucher = selectVoucher;
window.removeVoucher = removeVoucher;
window.calculateSubtotal = calculateSubtotal;

// Mobile Menu Functions
function toggleMobileMenu() {
    $('#mobileMenu').toggleClass('active');
    $('#mobileMenuOverlay').toggleClass('active');
}
window.toggleMobileMenu = toggleMobileMenu;


// ============================================
// Notification Functions
// ============================================
function checkNotifications() {
    // 1. Connection Status (Initial)
    if (!navigator.onLine) {
        addNotification('danger', 'Koneksi Terputus', 'Anda dalam mode offline. Transaksi mungkin tidak tersimpan.', 'system-offline');
    }
    
    // 2. Low Stock (Checked during product load)
    updateNotificationBadge();
}

// Note: handleConnectionChange is defined in the Offline Mode Functions section

function checkLowStockProducts(products) {
    if (!products) return;
    
    // Clear old stock notifications first
    notifications = notifications.filter(n => !n.id || !n.id.startsWith('stock-'));
    
    let lowStockCount = 0;
    products.forEach(p => {
        const stock = p.stock !== undefined ? p.stock : 15; // Mock logic
        if (stock < 5) {
            lowStockCount++;
            addNotification('warning', 'Stok Menipis', `Stok <b>${p.name}</b> tersisa ${stock} item.`, `stock-${p.id}`);
        }
    });

    renderNotifications();
    updateNotificationBadge();
}

function addNotification(type, title, message, id = null) {
    // Avoid duplicates for ID-based notifications
    if (id && notifications.some(n => n.id === id)) return;
    
    notifications.unshift({
        id: id || Date.now(),
        type: type, // warning, danger, info, success
        title: title,
        message: message,
        time: new Date(),
        read: false
    });
}

function updateNotificationBadge() {
    const count = notifications.filter(n => !n.read).length;
    const badge = $('#navNotificationBadge');
    
    if (count > 0) {
        badge.text(count).removeClass('d-none');
    } else {
        badge.addClass('d-none');
    }
}

function renderNotifications() {
    if (notifications.length === 0) {
        $('#notificationDropdownList').html(`
            <div class="text-center p-4">
                <div class="mb-2"><em class="icon ni ni-bell opacity-25" style="font-size: 3rem;"></em></div>
                <div class="text-muted small">Tidak ada notifikasi baru</div>
            </div>
        `);
        return;
    }
    
    let html = '';
    notifications.forEach(n => {
        // Icon and Style Mapping
        let iconClass = 'ni-bell';
        let bgClass = 'bg-primary-dim';
        
        if (n.type === 'warning') { iconClass = 'ni-alert-circle'; bgClass = 'bg-warning-dim'; }
        else if (n.type === 'danger') { iconClass = 'ni-cross-circle'; bgClass = 'bg-danger-dim'; }
        else if (n.type === 'success') { iconClass = 'ni-check-circle'; bgClass = 'bg-success-dim'; }
        else if (n.type === 'info') { iconClass = 'ni-info'; bgClass = 'bg-info-dim'; }
        
        const timeString = n.time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        html += `
            <div class="nk-notification-item dropdown-inner">
                <div class="nk-notification-icon">
                    <em class="icon icon-circle ${bgClass} ni ${iconClass}"></em>
                </div>
                <div class="nk-notification-content">
                    <div class="nk-notification-text">${n.title} <span class="d-block text-muted small mt-1">${n.message}</span></div>
                    <div class="nk-notification-time">${timeString}</div>
                </div>
            </div>
        `;
    });
    
    $('#notificationDropdownList').html(html);
}

function markAllRead() {
    notifications.forEach(n => n.read = true);
    updateNotificationBadge();
}

function clearNotifications() {
    notifications = [];
    renderNotifications();
    updateNotificationBadge();
}


// ============================================
// Settings Functions
// ============================================
function loadSettings() {
    const saved = localStorage.getItem('pos-settings');
    if (saved) {
        // Merge saved settings with default to ensure new keys exist
        settings = { ...settings, ...JSON.parse(saved) };
    }
    applySettings();
}

function openSettingsModal() {
    // Populate form with current settings
    $('#settingPrinterTarget').val(settings.printerTarget);
    $('#settingPaperSize').val(settings.paperSize);
    $('#settingAutoPrint').prop('checked', settings.autoPrint);
    
    // Tax Settings
    $('#settingEnableTax').prop('checked', settings.enableTax);
    $('#settingTaxRate').val(settings.taxRate);
    if(settings.enableTax) {
        $('#taxRateGroup').show();
    } else {
        $('#taxRateGroup').hide();
    }
    
    $('#settingEnableTax').on('change', function() {
        if($(this).is(':checked')) {
            $('#taxRateGroup').slideDown();
        } else {
            $('#taxRateGroup').slideUp();
        }
    });

    $('#settingShowImages').prop('checked', settings.showImages);
    $(`input[name="viewMode"][value="${settings.viewMode}"]`).prop('checked', true);
    $('#settingSoundEffect').prop('checked', settings.soundEffect);
    
    $('#settingSoundEffect').prop('checked', settings.soundEffect);

    // QZ Settings
    $('#settingUseQzTray').prop('checked', settings.useQzTray);
    $('#settingQzPrinterName').val(settings.qzPrinterName); // Might need delay if list not loaded
    
    toggleQzSettings();
    $('#settingUseQzTray').on('change', toggleQzSettings);
    
    // Attempt init QZ if enabled but not connected
    if (settings.useQzTray && typeof qz !== 'undefined') {
        initQzTray(); 
    }
    
    $('#settingsModal').modal('show');
}

function closeSettingsModal() {
    $('#settingsModal').modal('hide');
}

function saveSettings() {
    settings.printerTarget = $('#settingPrinterTarget').val();
    settings.paperSize = $('#settingPaperSize').val();
    settings.autoPrint = $('#settingAutoPrint').is(':checked');
    
    settings.enableTax = $('#settingEnableTax').is(':checked');
    settings.taxRate = parseFloat($('#settingTaxRate').val()) || 0;

    settings.showImages = $('#settingShowImages').is(':checked');
    settings.viewMode = $('input[name="viewMode"]:checked').val();
    settings.soundEffect = $('#settingSoundEffect').is(':checked');
    
    localStorage.setItem('pos-settings', JSON.stringify(settings));
    applySettings();
    calculateTotal(); // Recalculate with new tax settings
    closeSettingsModal();
    settings.useQzTray = $('#settingUseQzTray').is(':checked');
    settings.qzPrinterName = $('#settingQzPrinterName').val();
    
    localStorage.setItem('pos-settings', JSON.stringify(settings));
    applySettings();
    calculateTotal(); // Recalculate with new tax settings
    
    if (settings.useQzTray) {
        initQzTray(); // Connect if enabled
    } else if (typeof qz !== 'undefined' && qz.websocket.isActive()) {
        qz.websocket.disconnect(); // Disconnect if disabled
    }

    closeSettingsModal();
    NioApp.Toast('Pengaturan berhasil disimpan!', 'success', { position: 'top-right' });
}

function applySettings() {
    // View Mode (Grid/List)
    $('#productsGrid').removeClass('grid-large view-list');
    
    if (settings.viewMode === 'list') {
        $('#productsGrid').addClass('view-list');
    } else {
        // Grid Default (could add large grid back if needed, but requested to replace)
        // $('#productsGrid').addClass('grid-large'); 
    }
    
    // Show/Hide Images
    if (!settings.showImages) {
        $('#productsGrid').addClass('hidden-images');
    } else {
        $('#productsGrid').removeClass('hidden-images');
    }
}

window.clearNotifications = clearNotifications;
window.openSettingsModal = openSettingsModal;
window.closeSettingsModal = closeSettingsModal;
window.saveSettings = saveSettings;

// ============================================
// Offline Mode Functions
// ============================================

/**
 * Initialize offline mode
 */
async function initOfflineMode() {
    if (typeof OfflineDB !== 'undefined') {
        try {
            await OfflineDB.initDB();
            console.log('[Offline] IndexedDB initialized');
            
            // Update offline status
            updateOfflineStatus();
            
            // Listen for sync complete messages from Service Worker
            if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                navigator.serviceWorker.addEventListener('message', handleSWMessage);
            }
        } catch (error) {
            console.error('[Offline] Failed to initialize IndexedDB:', error);
        }
    } else {
        console.warn('[Offline] OfflineDB module not loaded');
    }
}

/**
 * Handle Service Worker messages
 */
function handleSWMessage(event) {
    if (event.data?.type === 'SYNC_COMPLETE') {
        const result = event.data.data;
        console.log('[Offline] Sync complete:', result);
        
        NioApp.Toast(`Sinkronisasi selesai: ${result.accepted} berhasil, ${result.rejected} ditolak`, 
            result.rejected > 0 ? 'warning' : 'success', 
            { position: 'top-right' }
        );
        
        updateOfflineStatus();
        refreshStockFromServer();
    }
}

/**
 * Handle connection change
 */
function handleConnectionChange(event) {
    isOffline = !navigator.onLine;
    updateOfflineStatus();
    
    if (navigator.onLine) {
        console.log('[Offline] Back online');
        NioApp.Toast('Koneksi internet kembali!', 'success', { position: 'top-right' });
        
        // Trigger sync
        syncPendingTransactions();
        
        // Reload fresh data
        loadCategories();
        loadProducts();
    } else {
        console.log('[Offline] Gone offline');
        NioApp.Toast('Mode Offline - Transaksi akan disimpan lokal', 'warning', { position: 'top-right' });
    }
}

/**
 * Update offline status UI
 */
async function updateOfflineStatus() {
    isOffline = !navigator.onLine;
    
    if (typeof OfflineDB !== 'undefined') {
        try {
            const status = await OfflineDB.getOfflineStatus();
            pendingTransactionsCount = status.pendingCount;
        } catch (e) {
            pendingTransactionsCount = 0;
        }
    }
    
    // Update UI
    if (isOffline || pendingTransactionsCount > 0) {
        let statusText = isOffline ? 'Mode Offline' : 'Online';
        if (pendingTransactionsCount > 0) {
            statusText += ` - ${pendingTransactionsCount} transaksi menunggu sync`;
        }
        
        $('#offlineStatusBar').show().find('#offlineStatusText').text(statusText);
        $('#pendingCount').text(pendingTransactionsCount);
    } else {
        $('#offlineStatusBar').hide();
    }
}

/**
 * Load categories with offline fallback
 */
async function loadCategoriesWithFallback() {
    if (!navigator.onLine && typeof OfflineDB !== 'undefined') {
        // Load from cache
        try {
            const cached = await OfflineDB.getCachedCategories();
            if (cached.length > 0) {
                console.log('[Offline] Loading categories from cache');
                categories = cached;
                renderCategories();
                return;
            }
        } catch (e) {
            console.error('[Offline] Failed to load cached categories:', e);
        }
    }
    
    // Online - load from server and cache
    $.ajax({
        url: window.posRoutes.categories,
        type: 'GET',
        success: async function(response) {
            if (response.status === 'success') {
                categories = response.data;
                renderCategories();
                
                // Cache for offline use
                if (typeof OfflineDB !== 'undefined') {
                    await OfflineDB.cacheCategories(response.data);
                }
            }
        },
        error: async function(xhr, status, error) {
            console.error('Error loading categories:', error);
            // Try cache on error
            if (typeof OfflineDB !== 'undefined') {
                const cached = await OfflineDB.getCachedCategories();
                if (cached.length > 0) {
                    categories = cached;
                    renderCategories();
                }
            }
        }
    });
}

/**
 * Load products with offline fallback
 */
async function loadProductsWithFallback() {
    const categoryId = $('.pos-category-pill.active').data('category');
    const search = $('#searchProduct').val();
    
    $('#productsGrid').html('<div class="pos-loading"><div class="pos-spinner"></div></div>');
    
    if (!navigator.onLine && typeof OfflineDB !== 'undefined') {
        // Load from cache
        try {
            const cached = await OfflineDB.getCachedProducts(categoryId, search);
            if (cached.length > 0) {
                console.log('[Offline] Loading products from cache');
                renderProductsWithOfflineStock(cached);
                return;
            }
        } catch (e) {
            console.error('[Offline] Failed to load cached products:', e);
        }
        $('#productsGrid').html(getEmptyStateHTML('📡', 'Tidak ada data produk offline', 'Silakan koneksikan internet untuk memuat data'));
        return;
    }
    
    // Online - load from server and cache
    $.ajax({
        url: window.posRoutes.products,
        type: 'GET',
        data: {
            category_id: categoryId,
            search: search
        },
        success: async function(response) {
            console.log('Products response:', response);
            if (response.status === 'success') {
                const products = response.data.data || response.data;
                renderProducts(products);
                checkLowStockProducts(products);
                
                // Cache for offline use (only cache all products, not filtered)
                if (typeof OfflineDB !== 'undefined' && (!categoryId || categoryId === 'all') && !search) {
                    await OfflineDB.cacheProducts(products);
                }
            } else {
                $('#productsGrid').html(getEmptyStateHTML('⚠️', 'Gagal memuat produk'));
            }
        },
        error: async function(xhr, status, error) {
            console.error('Error loading products:', error);
            // Try cache on error
            if (typeof OfflineDB !== 'undefined') {
                const cached = await OfflineDB.getCachedProducts(categoryId, search);
                if (cached.length > 0) {
                    renderProductsWithOfflineStock(cached);
                    return;
                }
            }
            $('#productsGrid').html(getEmptyStateHTML('❌', `Gagal memuat produk<br><small>${error}</small>`));
        }
    });
}

/**
 * Render products with offline stock badges
 */
function renderProductsWithOfflineStock(products) {
    if (!products || products.length === 0) {
        $('#productsGrid').html(getEmptyStateHTML('📡', 'Produk tidak ditemukan (offline)', 'Data dari cache lokal'));
        return;
    }
    
    // Store products in global cache
    window.productsCache = {};
    products.forEach(p => {
        window.productsCache[p.id] = {
            id: p.id,
            name: p.name,
            price_display: p.price_display || 0,
            image: p.image || null,
            variants: p.variants || [],
            stock: p.local_stock ?? p.stock ?? 15
        };
    });
    
    let html = '';
    products.forEach(product => {
        const price = product.price_display || 0;
        const inCart = cart.find(item => item.id === product.id);
        const productName = product.name || '';
        const imageSrc = product.image ? product.image : '/images/product-sample.png';
        const localStock = product.local_stock ?? product.stock ?? 15;
        
        // Stock Badge (offline)
        let stockBadge = '';
        let isOutOfStock = false;
        if (localStock <= 0) {
            stockBadge = `<div class="pos-stock-badge out-of-stock">Habis (offline)</div>`;
            isOutOfStock = true;
        } else if (localStock < 5) {
            stockBadge = `<div class="pos-stock-badge">Sisa ${localStock} (offline)</div>`;
        }

        html += `
            <div class="pos-product-card ${isOutOfStock ? 'out-of-stock' : ''}" data-product-id="${product.id}">
                ${stockBadge}   
                <img src="${imageSrc}" class="pos-product-image" alt="${productName}">
                <div class="pos-product-info">
                    <h4 class="pos-product-name">${productName}</h4>
                    <div class="pos-product-price">${formatRupiah(price)}</div>
                    <button class="pos-btn-add ${inCart ? 'pos-btn-add-outline' : 'pos-btn-add-primary'}"
                            onclick="addToCartById(${product.id})"
                            ${isOutOfStock ? 'disabled' : ''}>
                        ${isOutOfStock ? 'Stok Habis' : (inCart ? `Di Keranjang (${inCart.qty})` : '+ Tambah')}
                    </button>
                </div>
            </div>
        `;
    });
    
    $('#productsGrid').html(html);
}

/**
 * Save transaction when offline
 */
async function saveOfflineTransaction(orderData) {
    if (typeof OfflineDB === 'undefined') {
        console.error('[Offline] OfflineDB not available');
        return null;
    }
    
    try {
        const clientId = await OfflineDB.savePendingTransaction(orderData);
        console.log('[Offline] Transaction saved with client_id:', clientId);
        
        // Reduce local stock
        for (const item of orderData.items) {
            await OfflineDB.reduceLocalStock(item.product_id, item.quantity);
        }
        
        // Update status
        await updateOfflineStatus();
        
        // Request background sync if available
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register('sync-transactions');
        }
        
        return clientId;
    } catch (error) {
        console.error('[Offline] Failed to save transaction:', error);
        return null;
    }
}

/**
 * Sync pending transactions
 */
async function syncPendingTransactions() {
    if (typeof OfflineDB === 'undefined') return;
    
    try {
        const pending = await OfflineDB.getPendingTransactions();
        if (pending.length === 0) {
            console.log('[Offline] No pending transactions to sync');
            return;
        }
        
        console.log(`[Offline] Syncing ${pending.length} transactions...`);
        
        // Try background sync first
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register('sync-transactions');
        } else {
            // Fallback: direct sync via AJAX
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: window.posRoutes?.sync || '/pos/sync/transactions',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                contentType: 'application/json',
                data: JSON.stringify({ transactions: pending }),
                success: async function(result) {
                    console.log('[Offline] Sync result:', result);
                    
                    // Remove synced transactions
                    if (result.results?.accepted) {
                        for (const txn of result.results.accepted) {
                            await OfflineDB.removePendingTransaction(txn.client_id);
                        }
                    }
                    
                    NioApp.Toast(`Sinkronisasi: ${result.accepted} berhasil`, 'success', { position: 'top-right' });
                    await updateOfflineStatus();
                    refreshStockFromServer();
                },
                error: function(xhr, status, error) {
                    console.error('[Offline] Sync failed:', error);
                    NioApp.Toast('Gagal sinkronisasi, akan dicoba lagi nanti', 'warning', { position: 'top-right' });
                }
            });
        }
    } catch (error) {
        console.error('[Offline] Sync error:', error);
    }
}

/**
 * Refresh stock from server
 */
async function refreshStockFromServer() {
    if (typeof OfflineDB === 'undefined' || !navigator.onLine) return;
    
    try {
        const response = await fetch(window.posRoutes.products);
        const data = await response.json();
        if (data.status === 'success') {
            await OfflineDB.syncStockFromServer(data.data.data || data.data);
            loadProducts(); // Re-render with fresh stock
        }
    } catch (error) {
        console.error('[Offline] Failed to refresh stock:', error);
    }
}

// ============================================
// Cache Cleanup Functions (for Settings)
// ============================================

async function clearProductCache() {
    if (typeof OfflineDB === 'undefined') return;
    if (!confirm('Hapus cache produk?')) return;
    
    await OfflineDB.clearProductCache();
    NioApp.Toast('Cache produk berhasil dihapus', 'success', { position: 'top-right' });
    updateStorageInfo();
}

async function clearCategoryCache() {
    if (typeof OfflineDB === 'undefined') return;
    if (!confirm('Hapus cache kategori?')) return;
    
    await OfflineDB.clearCategoryCache();
    NioApp.Toast('Cache kategori berhasil dihapus', 'success', { position: 'top-right' });
    updateStorageInfo();
}

async function clearSyncHistory() {
    if (typeof OfflineDB === 'undefined') return;
    if (!confirm('Hapus history sinkronisasi?')) return;
    
    await OfflineDB.clearSyncHistory();
    NioApp.Toast('History sinkronisasi berhasil dihapus', 'success', { position: 'top-right' });
    updateStorageInfo();
}

async function clearAllCache() {
    if (typeof OfflineDB === 'undefined') return;
    if (!confirm('Hapus semua cache? (Transaksi pending akan tetap tersimpan)')) return;
    
    await OfflineDB.clearAllCache();
    NioApp.Toast('Semua cache berhasil dihapus', 'success', { position: 'top-right' });
    updateStorageInfo();
}

async function updateStorageInfo() {
    if (typeof OfflineDB === 'undefined') {
        $('#storageInfo').text('OfflineDB tidak tersedia');
        return;
    }
    
    const info = await OfflineDB.getStorageInfo();
    $('#storageInfo').text(`${info.products} produk, ${info.categories} kategori, ${info.pending} transaksi pending`);
}

// Export offline functions
window.clearProductCache = clearProductCache;
window.clearCategoryCache = clearCategoryCache;
window.clearSyncHistory = clearSyncHistory;
window.clearAllCache = clearAllCache;
window.updateStorageInfo = updateStorageInfo;

// ============================================
// Print Functions
// ============================================

// ============================================
// Print Functions
// ============================================
function printReceipt(orderId) {
    if (!orderId) {
        console.error('No Order ID for printing');
        return;
    }

    // 1. Check if QZ Tray is Enabled AND Device is Desktop (Basic check)
    // Note: Mobile browsers won't connect to localhost wss usually, but we check logic
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (settings.useQzTray && !isMobile && typeof qz !== 'undefined') {
        printViaQzTray(orderId);
        return;
    }

    // 2. Fallback to Browser Print
    const toastId = NioApp.Toast('Memproses struk...', 'info', { position: 'top-right' });
    
    // Create hidden iframe if not exists
    let iframe = document.getElementById('receiptPrintFrame');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'receiptPrintFrame';
        iframe.style.position = 'absolute';
        iframe.style.width = '0px';
        iframe.style.height = '0px';
        iframe.style.border = 'none';
        document.body.appendChild(iframe);
    }
    
    // Use constructed URL with paper size
    const paperSize = settings.paperSize || '80';
    const printUrl = `/pos/print/${orderId}?size=${paperSize}`;
    
    iframe.src = printUrl;
    
    // Print when loaded
    iframe.onload = function() {
        // Wait a tiny bit for render
        setTimeout(() => {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                NioApp.Toast('Struk berhasil dicetak', 'success', { position: 'top-right' });
            } catch (e) {
                console.error('Printing failed', e);
                NioApp.Toast('Gagal mencetak struk', 'error', { position: 'top-right' });
            }
        }, 500);
    };
}

// ============================================
// QZ Tray Functions
// ============================================
function toggleQzSettings() {
    if ($('#settingUseQzTray').is(':checked')) {
        $('#qzPrinterGroup').slideDown();
        $('#qzStatus').slideDown();
        if (typeof qz !== 'undefined' && !qz.websocket.isActive()) {
            initQzTray(); 
        } else {
             findPrinters(); // Refresh list if already connected
        }
    } else {
        $('#qzPrinterGroup').slideUp();
        $('#qzStatus').slideUp();
    }
}

function initQzTray() {
    if (typeof qz === 'undefined') {
        $('#qzStatusText').text('Library QZ tidak dimuat').addClass('text-danger');
        return;
    }

    if (qz.websocket.isActive()) {
         $('#qzStatusText').text('Terhubung').removeClass('text-danger').addClass('text-success');
         findPrinters();
         return;
    }

    $('#qzStatusText').text('Menghubungkan...').removeClass('text-danger text-success');

    qz.websocket.connect().then(function() {
        $('#qzStatusText').text('Terhubung').addClass('text-success');
        findPrinters();
    }).catch(function(e) {
        console.error(e);
        $('#qzStatusText').text('Gagal Terhubung: ' + e).addClass('text-danger');
    });
}

function findPrinters() {
    console.log('Finding printers...');
    if (typeof qz === 'undefined' || !qz.websocket.isActive()) return;
    
    qz.printers.find().then(function(data) {
        const list = $('#settingQzPrinterName');
        const current = settings.qzPrinterName;
        list.empty();
        list.append('<option value="">-- Pilih Printer --</option>');
        
        data.forEach(p => {
            const selected = (p === current) ? 'selected' : '';
            list.append(`<option value="${p}" ${selected}>${p}</option>`);
        });
    }).catch(function(e) {
        console.error(e);
        NioApp.Toast('Gagal memuat list printer', 'error');
    });
}

function printViaQzTray(orderId) {
    if (!settings.qzPrinterName) {
        NioApp.Toast('Printer QZ belum dipilih di pengaturan', 'warning');
        return;
    }

    NioApp.Toast('Mengirim ke printer...', 'info');

    // Fetch the receipt HTML/content
    const paperSize = settings.paperSize || '80';
    const printUrl = `/pos/print/${orderId}?size=${paperSize}`;
    
    // QZ Tray 2.1+ supports HTML printing via Pixel logic
    // We can pass the URL directly if QZ runs on same network or accessible URL
    // BUT since we are on localhost/laragon, QZ Tray (desktop) might see 'localhost' as itself?
    // If the website is hosted, strict CORS might block.
    // Better approach: fetch HTML content via AJAX, then send to QZ.
    
    $.ajax({
        url: printUrl,
        type: 'GET',
        success: function(htmlContent) {
           
           // Configuration
           var config = qz.configs.create(settings.qzPrinterName);
           
           // Print Data
           var data = [{
               type: 'pixel',
               format: 'html',
               flavor: 'plain', // or 'file' if passing URL
               data: htmlContent
           }];
           
           qz.print(config, data).then(function() {
               NioApp.Toast('Print Berhasil (QZ)', 'success');
           }).catch(function(e) {
               console.error(e);
               NioApp.Toast('Print Gagal: ' + e, 'error');
           });
        },
        error: function() {
            NioApp.Toast('Gagal mengambil data struk', 'error');
        }
    });
}

window.printReceipt = printReceipt;
window.findPrinters = findPrinters;
window.toggleQzSettings = toggleQzSettings;
window.syncPendingTransactions = syncPendingTransactions;

// ============================================
// Bluetooth Printer Functions
// ============================================

/**
 * Initialize Bluetooth printer - auto-reconnect if previously paired
 */
function initBluetoothPrinter() {
    if (typeof ThermalPrinter === 'undefined') {
        console.log('[BTPrinter] ThermalPrinter module not loaded');
        return;
    }

    // Update UI with current state
    ThermalPrinter.updateUI();

    // Try auto-reconnect if we have a saved printer
    const saved = ThermalPrinter.getSavedPrinter();
    if (saved) {
        console.log('[BTPrinter] Found saved printer:', saved.name);
        ThermalPrinter.autoReconnect().then(success => {
            if (success) {
                console.log('[BTPrinter] Auto-reconnect initiated for:', saved.name);
            }
        });
    }
}

/**
 * Connect to Bluetooth printer (called from Settings modal)
 */
async function connectBluetoothPrinter() {
    try {
        NioApp.Toast('Membuka dialog Bluetooth...', 'info', { position: 'top-right' });
        const result = await ThermalPrinter.connectPrinter();
        NioApp.Toast(`Terhubung ke ${result.name}!`, 'success', { position: 'top-right' });
    } catch (e) {
        NioApp.Toast(e.message || 'Gagal menghubungkan printer', 'error', { position: 'top-right' });
    }
}

/**
 * Disconnect Bluetooth printer
 */
function disconnectBluetoothPrinter() {
    if (typeof ThermalPrinter !== 'undefined') {
        ThermalPrinter.disconnectPrinter();
        NioApp.Toast('Printer diputuskan', 'info', { position: 'top-right' });
    }
}

/**
 * Forget saved printer device (for switching to different printer)
 */
function forgetPrinterDevice() {
    Swal.fire({
        title: 'Lupakan Perangkat?',
        text: 'Printer akan diputus dan data perangkat dihapus. Anda perlu menghubungkan ulang.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lupakan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.value) {
            ThermalPrinter.forgetPrinter();
            NioApp.Toast('Perangkat printer berhasil dilupakan', 'info', { position: 'top-right' });
        }
    });
}

/**
 * Test print via Bluetooth thermal printer
 */
async function testPrintThermal() {
    try {
        if (!ThermalPrinter.isConnected()) {
            NioApp.Toast('Printer belum terhubung. Silakan hubungkan terlebih dahulu.', 'warning', { position: 'top-right' });
            return;
        }
        NioApp.Toast('Mengirim test print...', 'info', { position: 'top-right' });
        await ThermalPrinter.printTestPage();
        NioApp.Toast('Test print berhasil!', 'success', { position: 'top-right' });
    } catch (e) {
        NioApp.Toast('Test print gagal: ' + e.message, 'error', { position: 'top-right' });
    }
}

/**
 * Handle printer nav button click
 */
function handlePrinterNavClick() {
    if (typeof ThermalPrinter !== 'undefined' && ThermalPrinter.isConnected()) {
        // Already connected - open settings to manage
        openSettingsModal();
    } else {
        // Not connected - try to connect directly
        connectBluetoothPrinter();
    }
}

window.connectBluetoothPrinter = connectBluetoothPrinter;
window.disconnectBluetoothPrinter = disconnectBluetoothPrinter;
window.forgetPrinterDevice = forgetPrinterDevice;
window.testPrintThermal = testPrintThermal;
window.handlePrinterNavClick = handlePrinterNavClick;

// ============================================
// Order Success Modal Functions
// ============================================

/**
 * Show order success modal after successful order
 */
function showOrderSuccessModal(orderData) {
    const invoiceNum = orderData.invoice_number || '#' + String(orderData.id).padStart(5, '0');
    const totalFormatted = formatRupiah(orderData.final_amount || 0);
    
    $('#successOrderInfo').html(`Invoice: <strong>${invoiceNum}</strong><br>Total: <strong>${totalFormatted}</strong>`);
    
    // Update Bluetooth print button state
    if (typeof ThermalPrinter !== 'undefined') {
        ThermalPrinter.updateUI();
    }
    
    $('#orderSuccessModal').modal('show');
}

/**
 * Handle print receipt via Bluetooth thermal printer
 */
async function handlePrintReceiptBluetooth() {
    const orderId = window.lastOrderId;
    if (!orderId) {
        NioApp.Toast('Order ID tidak ditemukan', 'error', { position: 'top-right' });
        return;
    }

    if (!ThermalPrinter.isConnected()) {
        NioApp.Toast('Printer Bluetooth tidak terhubung', 'warning', { position: 'top-right' });
        return;
    }

    try {
        const btn = $('#btnPrintReceiptBluetooth');
        btn.prop('disabled', true).html('<em class="icon ni ni-loader ni-spin me-1"></em> Mencetak...');

        // Fetch receipt data from server
        const response = await $.ajax({
            url: window.posRoutes.receiptData + '/' + orderId,
            type: 'GET'
        });

        if (response.status && response.data) {
            await ThermalPrinter.printReceipt(response.data);
            NioApp.Toast('Struk berhasil dicetak!', 'success', { position: 'top-right' });
        } else {
            NioApp.Toast('Gagal mengambil data struk', 'error', { position: 'top-right' });
        }

        btn.prop('disabled', false).html('<em class="icon ni ni-printer me-1"></em> Cetak Struk (Bluetooth)');
    } catch (e) {
        NioApp.Toast('Gagal mencetak: ' + e.message, 'error', { position: 'top-right' });
        $('#btnPrintReceiptBluetooth').prop('disabled', false).html('<em class="icon ni ni-printer me-1"></em> Cetak Struk (Bluetooth)');
    }
}

/**
 * Handle print receipt via browser (fallback)
 */
function handlePrintReceiptBrowser() {
    const orderId = window.lastOrderId;
    if (!orderId) {
        NioApp.Toast('Order ID tidak ditemukan', 'error', { position: 'top-right' });
        return;
    }
    printReceipt(orderId);
}

/**
 * Close order success modal
 */
function closeOrderSuccessModal() {
    $('#orderSuccessModal').modal('hide');
}

window.showOrderSuccessModal = showOrderSuccessModal;
window.handlePrintReceiptBluetooth = handlePrintReceiptBluetooth;
window.handlePrintReceiptBrowser = handlePrintReceiptBrowser;
window.closeOrderSuccessModal = closeOrderSuccessModal;

// ============================================
// Transaction History Functions
// ============================================

/**
 * Open transaction history modal
 */
function openHistoryModal() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    $('#historyDateFilter').val(today);
    
    $('#historyModal').modal('show');
    loadTransactionHistory();
}

/**
 * Load transaction history
 */
function loadTransactionHistory() {
    const date = $('#historyDateFilter').val();
    
    $('#historyList').html(`
        <div class="text-center p-4 text-muted">
            <em class="icon ni ni-loader ni-spin fs-3"></em>
            <p class="small mt-2">Memuat data...</p>
        </div>
    `);

    $.ajax({
        url: window.posRoutes.transactions,
        type: 'GET',
        data: { date: date },
        success: function(response) {
            if (response.status && response.data && response.data.length > 0) {
                renderTransactionHistory(response.data);
            } else {
                $('#historyList').html(`
                    <div class="text-center p-4 text-muted">
                        <em class="icon ni ni-calendar fs-3 d-block mb-2"></em>
                        <p class="small">Belum ada transaksi pada tanggal ini</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#historyList').html(`
                <div class="text-center p-4 text-danger">
                    <em class="icon ni ni-alert-circle fs-3 d-block mb-2"></em>
                    <p class="small">Gagal memuat riwayat transaksi</p>
                </div>
            `);
        }
    });
}

/**
 * Render transaction history list
 */
function renderTransactionHistory(transactions) {
    let html = '';
    const printerConnected = typeof ThermalPrinter !== 'undefined' && ThermalPrinter.isConnected();

    transactions.forEach(tx => {
        const time = tx.created_at ? new Date(tx.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
        const amount = formatRupiah(tx.final_amount || 0);
        const invoice = tx.invoice_number || '#' + String(tx.id).padStart(5, '0');

        html += `
            <div class="history-item">
                <div class="history-item-left">
                    <div class="history-item-invoice">${invoice}</div>
                    <div class="history-item-meta">
                        ${time} &bull; ${tx.customer_name || 'Pelanggan'} &bull; ${tx.payment_method_name || '-'}
                    </div>
                </div>
                <div class="history-item-right">
                    <div class="history-item-amount">${amount}</div>
                    <button class="history-print-btn btn-print-receipt" 
                            onclick="reprintReceipt(${tx.id})" 
                            ${!printerConnected ? 'disabled title=\"Printer tidak terhubung\"' : 'title=\"Cetak ulang struk\"'}>
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak
                    </button>
                </div>
            </div>
        `;
    });

    $('#historyList').html(html);
}

/**
 * Reprint receipt from transaction history
 */
async function reprintReceipt(orderId) {
    if (typeof ThermalPrinter !== 'undefined' && ThermalPrinter.isConnected()) {
        // Print via Bluetooth
        try {
            NioApp.Toast('Mencetak ulang struk...', 'info', { position: 'top-right' });
            const response = await $.ajax({
                url: window.posRoutes.receiptData + '/' + orderId,
                type: 'GET'
            });

            if (response.status && response.data) {
                await ThermalPrinter.printReceipt(response.data);
                NioApp.Toast('Struk berhasil dicetak ulang!', 'success', { position: 'top-right' });
            } else {
                NioApp.Toast('Gagal mengambil data struk', 'error', { position: 'top-right' });
            }
        } catch (e) {
            NioApp.Toast('Gagal mencetak: ' + e.message, 'error', { position: 'top-right' });
        }
    } else {
        // Fallback to browser print
        printReceipt(orderId);
    }
}

window.openHistoryModal = openHistoryModal;
window.loadTransactionHistory = loadTransactionHistory;
window.reprintReceipt = reprintReceipt;
