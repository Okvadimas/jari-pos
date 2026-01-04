/**
 * Login Page JavaScript
 * 
 * This file contains JavaScript specific to the login page.
 * Load this in your login blade template with:
 * @vite('resources/js/pages/auth/login.js')
 */

$(document).ready(function() {
    console.log('Regiter page scripts loaded');

    $('#form-data').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/register',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log(response)
                if (response.status) {
                    NioApp.Toast(response.message, 'success', {
                        position: 'top-right',
                        duration: 2000
                    });
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 2000);
                } else {
                    NioApp.Toast(response.message, 'error', {
                        position: 'top-right',
                        duration: 2000
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                NioApp.Toast(error.message, 'error', {
                    position: 'top-right',
                    duration: 2000
                });
            }
        });        
    });
});
