$(document).ready(function() {
    console.log('User Management Form page scripts loaded');

    $('#form').submit(function(e) {
        e.preventDefault();

        let $btn = $('#btn-save');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Menyimpan</span>');

        $.ajax({
            url: '/management/akses/store',
            type: 'POST',
            data: $(this).serialize(),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('<em class="icon ni ni-save"></em><span>Simpan</span>');
            },
            success: function(response) {
                if(response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function() {
                        window.location.href = '/management/akses';
                    }, 1000);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function(response) {
                handleAjaxError(response);
            }
        });
    });
});
