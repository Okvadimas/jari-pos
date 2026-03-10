$(document).ready(function() {
    $('#form-change-password').submit(function(e) {
        e.preventDefault();

        let $form = $(this);
        let $btn = $('#btn-submit');
        let originalText = $btn.html();
        
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Menyimpan...</span>');    
        
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if(response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    $form[0].reset();
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            },
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html(originalText);
            }
        });        
    });
});
