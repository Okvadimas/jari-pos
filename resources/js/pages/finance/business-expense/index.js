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
            url: '/finance/business-expense/datatable',
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
            { data: 'expense_number', name: 'expense_number' },
            { data: 'expense_date', name: 'expense_date' },
            { data: 'category', name: 'category' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount', className: 'text-end' },
            { data: 'vendor_name', name: 'vendor_name' },
        ],
        columnDefs: [{ targets: '_all', className: 'nk-tb-col' }],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/finance/business-expense/summary',
        type: 'GET',
        data: { start_date: $('#start_date').val(), end_date: $('#end_date').val() },
        success: function(response) {
            if (response.status && response.data) {
                $('#summary-total-transaksi').text(response.data.total_transaksi);
                $('#summary-total-pengeluaran').text(response.data.total_pengeluaran);
                $('#summary-total-server').text(response.data.total_server);
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
        url: '/finance/business-expense/show/' + id,
        type: 'GET',
        success: function(response) {
            if (response.status && response.data) {
                const e = response.data.expense;
                const categories = { server: 'Server', production: 'Produksi', other: 'Lainnya' };
                $('#detail-category').text(categories[e.category] || e.category);
                $('#detail-date').text(response.data.expense_date_formatted);
                $('#detail-vendor').text(e.vendor_name || '-');
                $('#detail-amount').text('Rp ' + new Intl.NumberFormat('id-ID').format(e.amount));
                $('#detail-description').text(e.description);
                $('#detail-note').text(e.reference_note || '-');
                $('#modal-detail').modal('show');
            }
        },
        error: function(xhr) { handleAjaxError(xhr); }
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Hapus Pengeluaran?',
        text: 'Data pengeluaran akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/finance/business-expense/destroy',
                type: 'POST',
                data: { id: id },
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

window.detail = detail;
window.hapus = hapus;
