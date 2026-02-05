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

        let $btn = $('#btn-submit');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Masuk</span>');    
        
        $.ajax({
            url: '/login',
            type: 'POST',
            data: $(this).serialize(),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('Masuk');
            },
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
            error: function(xhr) {
                $btn.attr('disabled', false);
                $btn.html('Masuk');

                handleAjaxError(xhr);
            }
        });        
    });
});
