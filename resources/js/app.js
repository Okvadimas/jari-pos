/**
 * Application Global JavaScript
 * 
 * This file contains global JavaScript that runs on all pages.
 * For page-specific scripts, create files in resources/js/pages/
 * 
 * jQuery is available globally via DashLite's bundle.js as $
 */

// Add your global JavaScript here
console.log('App.js loaded - jQuery ready!');

// Global JavaScript that runs on every page
$(document).ready(function() {
    // Example: Global CSRF token setup for $.ajax
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Show Online/Offline Notification        
    if (!navigator.onLine) {
        // User is offline, show SweetAlert notification
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'You are currently offline. Please check your internet connection and try again.',
        });
    }
});

function formatCurrency(value, separator = '.') {
    // hapus semua selain angka
    value = value.replace(/\D/g, '');

    // kalau kosong, balikin kosong
    if (!value) return '';

    // format ribuan
    return value.replace(/\B(?=(\d{3})+(?!\d))/g, separator);
}

/**
 * Format number to Rupiah display string
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string (e.g., "Rp 150.000")
 */
function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
}

$(document).on('input', '.currency-input', function () {
    this.value = formatCurrency(this.value);
});

/**
 * Global AJAX Error Handler
 * Menangani berbagai jenis error response dari Laravel
 * 
 * @param {Object} response - jQuery AJAX error response object
 * @param {Object} options - Optional configuration
 * @param {Function} options.onUnauthorized - Custom handler for 401 errors
 * @param {Function} options.onForbidden - Custom handler for 403 errors
 * @param {Function} options.onValidation - Custom handler for 422 errors
 * @param {Function} options.onCsrf - Custom handler for 419 errors
 */
function handleAjaxError(response, options = {}) {
    console.log('Error: ', response);
    let statusCode = response.status;
    
    if (statusCode === 422) {
        // Validation Error - Laravel punya struktur errors
        let errors = response.responseJSON?.errors;
        if (errors) {
            let firstError = Object.values(errors)[0][0];
            NioApp.Toast(firstError, 'warning', { position: 'top-right' });
        } else {
            let message = response.responseJSON?.message || 'Validasi gagal';
            NioApp.Toast(message, 'warning', { position: 'top-right' });
        }
        if (options.onValidation) options.onValidation(response);
    } else if (statusCode === 401) {
        // Unauthenticated - session expired
        NioApp.Toast('Sesi Anda telah berakhir. Silakan login kembali.', 'error', { position: 'top-right' });
        if (options.onUnauthorized) {
            options.onUnauthorized(response);
        } else {
            // Default: redirect ke login setelah 1.5 detik
            setTimeout(() => { window.location.href = '/login'; }, 1500);
        }
    } else if (statusCode === 403) {
        // Forbidden
        NioApp.Toast('Anda tidak memiliki akses untuk melakukan ini.', 'error', { position: 'top-right' });
        if (options.onForbidden) options.onForbidden(response);
    } else if (statusCode === 404) {
        // Not Found
        NioApp.Toast('Data tidak ditemukan.', 'error', { position: 'top-right' });
    } else if (statusCode === 419) {
        // CSRF Token Mismatch
        NioApp.Toast('Sesi kedaluwarsa. Silakan refresh halaman.', 'error', { position: 'top-right' });
        if (options.onCsrf) {
            options.onCsrf(response);
        } else {
            // Default: refresh halaman setelah 1.5 detik
            setTimeout(() => { location.reload(); }, 1500);
        }
    } else if (statusCode === 429) {
        // Too Many Requests
        NioApp.Toast('Terlalu banyak permintaan. Coba lagi nanti.', 'warning', { position: 'top-right' });
    } else if (statusCode >= 500) {
        // Server Error
        NioApp.Toast('Terjadi kesalahan server.', 'error', { position: 'top-right' });
    } else {
        // Error lainnya (termasuk 400 dari errorResponse)
        let message = response.responseJSON?.message || 'Terjadi kesalahan.';
        NioApp.Toast(message, 'error', { position: 'top-right' });
    }
}

// Expose to global scope for all scripts
window.formatCurrency = formatCurrency;
window.formatRupiah = formatRupiah;
window.handleAjaxError = handleAjaxError;
