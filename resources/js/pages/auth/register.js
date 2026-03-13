/**
 * Register Page JavaScript
 * 
 * This file contains JavaScript specific to the register page.
 * Load this in your register blade template with:
 * @vite('resources/js/pages/auth/register.js')
 */

$(document).ready(function() {
    console.log('Register page scripts loaded');

    $('#form-data').submit(function(e) {
        e.preventDefault();
        
        let $btn = $('#btn-submit');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span> Mendaftar...</span>');

        $.ajax({
            url: '/register',
            type: 'POST',
            data: $(this).serialize(),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('Daftar Sekarang');
            },
            success: function(response) {
                if (response.status) {
                    NioApp.Toast(response.message, 'success', {
                        position: 'top-right',
                        duration: 3000
                    });
                    setTimeout(() => {
                        window.location.href = '/email/verify';
                    }, 2000);
                } else {
                    NioApp.Toast(response.message, 'error', {
                        position: 'top-right',
                        duration: 3000
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            }
        });        
    });
});
