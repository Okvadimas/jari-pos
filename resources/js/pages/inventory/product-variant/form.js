$(document).ready(function() {
    console.log('Inventory - Product Form page scripts loaded');

    // Jika edit_price checked maka readonly dihilangkan
    $('#edit_price').on('change', function() {
        if($(this).is(':checked')) {
            $('#purchase_price').attr('readonly', false);
            $('#sell_price').attr('readonly', false);
        } else {
            $('#purchase_price').attr('readonly', true);
            $('#sell_price').attr('readonly', true);
        }
    });

    $('#form-data').submit(function(e) {
        e.preventDefault();

        let $btn = $('#btn-save');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Menyimpan</span>');

        $.ajax({
            url: '/inventory/product-variant/store',
            type: 'POST',
            data: $(this).serialize(),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('<em class="icon ni ni-save"></em><span>Simpan</span>');
            },
            success: function(response) {
                console.log('Success: ', response);
                if(response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function() {
                        window.location.href = '/inventory/product-variant';
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
