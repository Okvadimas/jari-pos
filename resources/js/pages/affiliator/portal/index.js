$(document).ready(function() {
    datatable();
    loadSummary();
});

const datatable = () => {
    NioApp.DataTable('#table-portal', {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '/affiliate/portal/datatable',
            type: 'POST',
            data: function (d) {
                d._token = token;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            error: function (xhr) { 
                if(xhr.status === 401) window.location.href = '/affiliate/login';
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at', render: function(data) {
                return moment(data).format('DD MMM YYYY');
            }},
            { data: 'sale_amount', name: 'sale_amount', className: 'text-end' },
            { data: 'commission_rate', name: 'commission_rate', className: 'text-center' },
            { data: 'commission_amount', name: 'commission_amount', className: 'text-end text-success fw-bold' },
            { data: 'status', name: 'status', className: 'text-center' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/affiliate/portal/summary',
        type: 'GET',
        data: { 
            start_date: $('#start_date').val(), 
            end_date: $('#end_date').val() 
        },
        success: function(response) {
            if (response.status && response.data) {
                $('#summary-total-transaksi').text(response.data.total_transaksi);
                $('#summary-total-penjualan').text(response.data.total_penjualan);
                $('#summary-total-komisi').text(response.data.total_komisi);
                $('#summary-komisi-pending').text(response.data.komisi_pending);
                $('#summary-komisi-paid').text(response.data.komisi_paid);
            }
        }
    });
}

$('#btn-filter').on('click', function() {
    $('#table-portal').DataTable().ajax.reload();
    loadSummary();
});
