/**
 * Verify Email Page JavaScript
 */

$(document).ready(function() {
    console.log('Verify email page scripts loaded');

    $('#resend-form').submit(function(e) {
        e.preventDefault();
        
        let $btn = $('#btn-resend');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span> Mengirim...</span>');

        $.ajax({
            url: '/email/verification-resend',
            type: 'POST',
            data: $(this).serialize(),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('<em class="icon ni ni-send"></em><span>Kirim Ulang Email Verifikasi</span>');
            },
            success: function(response) {
                if (response.status) {
                    NioApp.Toast(response.message, 'success', {
                        position: 'top-right',
                        duration: 3000
                    });
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
