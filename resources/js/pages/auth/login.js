/**
 * Login Page JavaScript
 * 
 * This file contains JavaScript specific to the login page.
 * Load this in your login blade template with:
 * @vite('resources/js/pages/auth/login.js')
 */

$(document).ready(function() {
    console.log('Login page scripts loaded');

    $('#form-data').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/login',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function() {
                        window.location.href = '/dashboard';
                    }, 1000);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function(xhr, status, error) {
                let statusCode = xhr.status;
                if(statusCode >= 500) {
                    NioApp.Toast('Terjadi kesalahan pada server', 'error', { position: 'top-right' });
                } else {
                    let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    NioApp.Toast(message, 'warning', { position: 'top-right' });
                }
            }
        });        
    });
});
