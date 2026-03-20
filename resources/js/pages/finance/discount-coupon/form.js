$(document).ready(function() {
    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        simpan();
    });
});

const simpan = () => {
    const formData = new FormData($('#form-data')[0]);

    // Handle checkbox
    if (!$('#is_active').is(':checked')) {
        formData.set('is_active', '0');
    }

    $('#btn-save').attr('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

    $.ajax({
        url: '/finance/voucher/store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status) {
                NioApp.Toast(response.message, 'success', { position: 'top-right' });
                setTimeout(() => { window.location.href = '/finance/voucher'; }, 1000);
            } else {
                NioApp.Toast(response.message, 'warning', { position: 'top-right' });
            }
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        },
        complete: function() {
            $('#btn-save').attr('disabled', false).html('<em class="icon ni ni-save"></em><span>Simpan</span>');
        }
    });
}
