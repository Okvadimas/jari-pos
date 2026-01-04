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
    $(window).on('online', function() {
        NioApp.Toast('You are now online', 'success', {
            position: 'top-right',
            duration: 2000
        });
    });
    $(window).on('offline', function() {
        NioApp.Toast('You are now offline', 'error', {
            position: 'top-right',
            duration: 2000
        });
    });
        

    // Add your global JavaScript here
    console.log('App.js loaded - jQuery ready!');
});
