$(document).ready(function() {
    console.log('Laporan Pembelian page scripts loaded');
    datatable();
    loadSummary();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '/transaction/purchasing/datatable',
            type: 'GET',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            error: function (xhr) {
                handleAjaxError(xhr);
            }
        },
        order: [3, 'desc'],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'order_number', name: 'order_number' },
            { data: 'purchase_date', name: 'purchase_date' },
            { data: 'supplier_display', name: 'supplier_name' },
            { data: 'total_cost', name: 'total_cost', className: 'text-end' },
            { data: 'reference_note', name: 'reference_note' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/transaction/purchasing/summary',
        type: 'GET',
        data: {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        },
        success: function(response) {
            $('#summary-total-transaksi').text(response.total_transaksi);
            $('#summary-total-pembelian').text(response.total_pembelian);
        },
        error: function(xhr) {
            handleAjaxError(xhr);
        }
    });
}

// Refresh table on filter click
$('#btn-filter').on('click', function(e) {
    $('#table-data').DataTable().ajax.reload();
    loadSummary();
});

// View Details
function detail(id) {
    let url = '/transaction/purchasing/show/' + id;

    // Show loading or clear previous data
    $('#detail-supplier').text('Loading...');
    $('#detail-date').text('Loading...');
    $('#detail-items').html('<tr><td colspan="5" class="text-center">Loading data...</td></tr>');
    $('#detail-total').text('-');
    $('#detail-note').text('');
    
    $('#modal-detail').modal('show');

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            $('#detail-supplier').text(response.purchase.supplier_name);
            $('#detail-date').text(response.purchase_date_formatted);
            $('#detail-note').text(response.purchase.reference_note || 'Tidak ada catatan');
            
            let itemsHtml = '';
            let totalCost = 0;
            
            if (response.details && response.details.length > 0) {
                response.details.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.sku}</td>
                            <td class="text-end">${item.quantity}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.cost)}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.total)}</td>
                        </tr>
                    `;
                    totalCost += item.total;
                });
            } else {
                itemsHtml = '<tr><td colspan="5" class="text-center">Tidak ada item</td></tr>';
            }
            
            $('#detail-items').html(itemsHtml);
            $('#detail-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.purchase.total_cost));
        },
        error: function(xhr) {
            handleAjaxError(xhr);
            $('#modal-detail').modal('hide');
        }
    });
}

// Delete function
function hapus(id) {
    Swal.fire({
        title: 'Hapus Pembelian?',
        text: 'Data pembelian akan dihapus secara permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/transaction/purchasing/destroy',
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
                error: function(xhr) {
                    handleAjaxError(xhr);
                }
            });
        }
    });
}

window.detail = detail;
window.hapus = hapus;