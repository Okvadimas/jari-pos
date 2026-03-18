$(document).ready(function() {
    datatable();
    loadSummary();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/finance/discount-coupon/datatable',
            type: 'POST',
            data: function (d) {
                d._token = token;
            },
            error: function (xhr) { handleAjaxError(xhr); }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'value', name: 'value' },
            { data: 'usage', name: 'used_count' },
            { data: 'validity', name: 'valid_from' },
            { data: 'is_active', name: 'is_active' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/finance/discount-coupon/summary',
        type: 'GET',
        success: function(response) {
            if (response.status && response.data) {
                $('#summary-total-kupon').text(response.data.total_kupon);
                $('#summary-total-aktif').text(response.data.total_aktif);
                $('#summary-total-digunakan').text(response.data.total_digunakan);
            }
        },
        error: function(xhr) { handleAjaxError(xhr); }
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Hapus Kupon?',
        text: 'Kupon diskon akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/finance/discount-coupon/destroy',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.status) {
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                        $('#table-data').DataTable().ajax.reload();
                        loadSummary();
                    }
                },
                error: function(xhr) { handleAjaxError(xhr); }
            });
        }
    });
}

window.hapus = hapus;
