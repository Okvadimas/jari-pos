$(document).ready(function() {
    console.log('Company Management Form page scripts loaded');

    $('#form-data').submit(function(e) {
        e.preventDefault();

        let $btn = $('#btn-save');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Menyimpan</span>');

        $.ajax({
            url: '/management/company/store',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('<em class="icon ni ni-save"></em><span>Simpan</span>');
            },
            success: function(response) {
                if(response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function() {
                        window.location.href = '/management/company';
                    }, 1000);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function(response) {
                let statusCode = response.status;
                if(statusCode >= 500) {
                    NioApp.Toast('Terjadi kesalahan', 'error', { position: 'top-right' });
                } else {
                    let errors = response.responseJSON.errors;
                    let firstError = Object.values(errors)[0][0];
                    NioApp.Toast(firstError, 'warning', { position: 'top-right' });
                }
            }
        });
    });
});
