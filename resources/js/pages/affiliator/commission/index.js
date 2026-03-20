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
            url: '/finance/affiliate-commission/datatable',
            type: 'POST',
            data: function (d) {
                d._token = token;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            error: function (xhr) { handleAjaxError(xhr); }
        },
        order: [2, 'desc'],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'commission_number', name: 'commission_number' },
            { data: 'affiliate_name', name: 'affiliate_name' },
            { data: 'affiliate_coupon_code', name: 'affiliate_coupon_code' },
            { data: 'renewal_badge', name: 'is_renewal' },
            { data: 'sale_amount', name: 'sale_amount', className: 'text-end' },
            { data: 'commission_rate', name: 'commission_rate' },
            { data: 'commission_amount', name: 'commission_amount', className: 'text-end' },
            { data: 'status', name: 'status' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/finance/affiliate-commission/summary',
        type: 'GET',
        data: { start_date: $('#start_date').val(), end_date: $('#end_date').val() },
        success: function(response) {
            if(response.status && response.data) {
                $('#summary-total-komisi').text(response.data.total_komisi);
                $('#summary-total-nominal').text(response.data.total_nominal);
                $('#summary-total-pending').text(response.data.total_pending);
                $('#summary-total-paid').text(response.data.total_paid);
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
        url: '/finance/affiliate-commission/show/' + id,
        type: 'GET',
        success: function(response) {
            if (response.status && response.data) {
                const c = response.data.commission;
                const s = response.data.sale;
                const fmt = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
                const statusLabels = { pending: 'Pending', paid: 'Dibayar', cancelled: 'Dibatalkan' };

                $('#detail-affiliate').text(c.affiliate_name);
                $('#detail-coupon').text(c.affiliate_coupon_code);
                $('#detail-sale-amount').text(fmt(c.sale_amount));
                $('#detail-rate').text(c.commission_rate + '%');
                $('#detail-commission').text(fmt(c.commission_amount));
                $('#detail-status').text(statusLabels[c.status] || c.status);
                $('#detail-paid-date').text(c.paid_date || '-');
                $('#detail-sale-customer').text(s ? s.customer_name : '-');
                $('#detail-sale-number').text(s ? s.sale_number : '-');
                $('#modal-detail').modal('show');
            }
        },
        error: function(xhr) { handleAjaxError(xhr); }
    });
}

function bayar(id) {
    Swal.fire({
        title: 'Bayar Komisi?',
        text: 'Komisi affiliate akan ditandai sudah dibayar',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/finance/affiliate-commission/pay/' + id,
                type: 'POST',
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
window.bayar = bayar;
