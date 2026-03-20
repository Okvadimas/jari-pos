$(document).ready(function() {
    datatable();
    loadSummary();
});

let detailTable = null;

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/finance/affiliate-dashboard/datatable',
            type: 'POST',
            data: function (d) {
                d._token = token;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            error: function (xhr) { handleAjaxError(xhr); }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'affiliate_name', name: 'affiliate_name' },
            { data: 'affiliate_coupon_code', name: 'affiliate_coupon_code' },
            { data: 'total_transaksi', name: 'total_transaksi', className: 'text-center' },
            { data: 'total_baru', name: 'total_baru', className: 'text-center' },
            { data: 'total_perpanjangan', name: 'total_perpanjangan', className: 'text-center' },
            { data: 'total_penjualan', name: 'total_penjualan', className: 'text-end' },
            { data: 'total_komisi', name: 'total_komisi', className: 'text-end' },
            { data: 'komisi_pending', name: 'komisi_pending', className: 'text-end' },
            { data: 'komisi_paid', name: 'komisi_paid', className: 'text-end' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/finance/affiliate-dashboard/summary',
        type: 'GET',
        data: { start_date: $('#start_date').val(), end_date: $('#end_date').val() },
        success: function(response) {
            if (response.status && response.data) {
                $('#summary-total-affiliate').text(response.data.total_affiliate);
                $('#summary-total-transaksi').text(response.data.total_transaksi);
                $('#summary-total-penjualan').text(response.data.total_penjualan);
                $('#summary-total-komisi').text(response.data.total_komisi);
                $('#summary-komisi-pending').text(response.data.komisi_pending);
                $('#summary-komisi-paid').text(response.data.komisi_paid);
            }
        },
        error: function(xhr) { handleAjaxError(xhr); }
    });
}

$('#btn-filter').on('click', function() {
    $('#table-data').DataTable().ajax.reload();
    loadSummary();
});

function detailAffiliate(code, name) {
    $('#detail-affiliate-name').text(name + ' (' + code + ')');

    // Destroy previous detail table if exists
    if (detailTable) {
        detailTable.destroy();
        $('#table-detail tbody').empty();
    }

    detailTable = NioApp.DataTable('#table-detail', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/finance/affiliate-dashboard/detail/' + code,
            type: 'POST',
            data: function (d) {
                d._token = token;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            error: function (xhr) { handleAjaxError(xhr); }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'commission_number', name: 'commission_number' },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'sale_number', name: 'sale_number' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'plan_name', name: 'plan_name' },
            { data: 'type_badge', name: 'is_renewal' },
            { data: 'sale_amount', name: 'sale_amount', className: 'text-end' },
            { data: 'commission_rate', name: 'commission_rate' },
            { data: 'commission_amount', name: 'commission_amount', className: 'text-end' },
            { data: 'status', name: 'status' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });

    $('#modal-detail').modal('show');
}

window.detailAffiliate = detailAffiliate;
