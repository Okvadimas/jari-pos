$(document).ready(function () {
    console.log('Inventory - Unit Form page scripts loaded');

    $('#form-data').on('submit', function (e) {
        e.preventDefault();

        var id = $('input[name="id"]').val();
        var url = id ? '/inventory/unit/update/' + id : '/inventory/unit/store';

        $.ajax({
            url: url,
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'JSON',
            success: function (response) {
                if (response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function () {
                        window.location.href = '/inventory/unit';
                    }, 1000);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function (error) {
                console.log(error);
                if (error.responseJSON && error.responseJSON.errors) {
                    // Show validation errors
                    $.each(error.responseJSON.errors, function (key, value) {
                        NioApp.Toast(value[0], 'error', { position: 'top-right' });
                    });
                } else {
                    NioApp.Toast('Terjadi kesalahan sistem', 'error', { position: 'top-right' });
                }
            }
        });
    });
});
