/**
 * Application Global JavaScript
 * 
 * This file contains global JavaScript that runs on all pages.
 * For page-specific scripts, create files in resources/js/pages/
 * 
 * jQuery is available globally via DashLite's bundle.js as $
 */

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

    window.addEventListener('online', () => {
        Swal.fire({
            icon: 'success',
            title: 'Great!',
            text: 'You are back online. Welcome back!',
        });
    });

    // Select2
    $('.select2').select2();

    // Add your global JavaScript here
    console.log('App.js loaded - jQuery ready!');
});
