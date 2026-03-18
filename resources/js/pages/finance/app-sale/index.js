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
            url: '/finance/app-sale/datatable',
            type: 'POST',
            data: function (d) {
                d._token = token;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            error: function (xhr) { handleAjaxError(xhr); }
        },
        order: [3, 'desc'],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'sale_number', name: 'sale_number' },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'plan_name', name: 'plan_name' },
            { data: 'original_amount', name: 'original_amount', className: 'text-end' },
            { data: 'final_amount', name: 'final_amount', className: 'text-end' },
            { data: 'status', name: 'status' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/finance/app-sale/summary',
        type: 'GET',
        data: { start_date: $('#start_date').val(), end_date: $('#end_date').val() },
        success: function(response) {
            if(response.status && response.data) {
                $('#summary-total-transaksi').text(response.data.total_transaksi);
                $('#summary-total-pemasukan').text(response.data.total_pemasukan);
                $('#summary-total-pending').text(response.data.total_pending);
                $('#summary-total-confirmed').text(response.data.total_confirmed);
            }
        },
        error: function(xhr) { handleAjaxError(xhr); }
    });
}

$('#btn-filter').on('click', function() {
    $('#table-data').DataTable().ajax.reload();
    loadSummary();
});

function detail(id) {
    $.ajax({
        url: '/finance/app-sale/show/' + id,
        type: 'GET',
        success: function(response) {
            if (response.status && response.data) {
                const s = response.data.sale;
                const fmt = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                const statusLabels = { pending: 'Pending', confirmed: 'Dikonfirmasi', cancelled: 'Dibatalkan' };

                $('#detail-customer').text(s.customer_name);
                $('#detail-email').text(s.customer_email || '-');
                $('#detail-plan').text(s.plan_name);
                $('#detail-duration').text(s.duration_months + ' bulan' + (s.is_renewal ? ' (Perpanjangan)' : ''));
                $('#detail-status').html(statusLabels[s.status] || s.status);
                $('#detail-original').text(fmt(s.original_amount));
                $('#detail-discount').text('- ' + fmt(Number(s.discount_amount) + Number(s.affiliate_discount_amount)));
                $('#detail-final').text(fmt(s.final_amount));
                $('#detail-affiliate-coupon').text(s.affiliate_coupon_code || '-');
                $('#detail-discount-coupon').text(s.discount_coupon_code || '-');
                $('#modal-detail').modal('show');
            }
        },
        error: function(xhr) { handleAjaxError(xhr); }
    });
}

function konfirmasi(id) {
    Swal.fire({
        title: 'Konfirmasi Penjualan?',
        text: 'Penjualan akan dikonfirmasi dan komisi affiliate akan otomatis dibuat jika ada kupon affiliate',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Konfirmasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/finance/app-sale/confirm/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                        $('#table-data').DataTable().ajax.reload();
                        loadSummary();
                    } else {
                        NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                    }
                },
                error: function(xhr) { handleAjaxError(xhr); }
            });
        }
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Hapus Penjualan?',
        text: 'Data penjualan akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/finance/app-sale/destroy',
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

window.detail = detail;
window.konfirmasi = konfirmasi;
window.hapus = hapus;
