$(document).ready(function () {
    $('#form-data').on('submit', function (e) {
        e.preventDefault();
        
        const id = window.location.pathname.split('/').pop();
        const isEdit = window.location.pathname.includes('edit');
        const url = isEdit ? `/management/payment/update/${id}` : '/management/payment/store';

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btn-save').attr('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            },
            success: function (response) {
                $('#btn-save').attr('disabled', false).html('Simpan');
                if (response.status) {
                    Swal.fire(
                        'Berhasil!',
                        response.message,
                        'success'
                    ).then(() => {
                        window.location.href = '/management/payment';
                    });
                } else {
                    Swal.fire(
                        'Gagal!',
                        response.message,
                        'error'
                    );
                }
            },
            error: function (xhr) {
                $('#btn-save').attr('disabled', false).html('Simpan');
                let message = 'Terjadi kesalahan pada server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join('<br>');
                }
                
                Swal.fire(
                    'Error!',
                    message,
                    'error'
                );
            }
        });
    });
});
