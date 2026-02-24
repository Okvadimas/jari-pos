$(document).ready(function() {
    console.log('Laporan Penjualan page scripts loaded');
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
            url: '/transaction/sales/datatable',
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
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'invoice_number', name: 'invoice_number'},
            { data: 'order_date', name: 'order_date' },
            { data: 'customer_display', name: 'customer_name' },
            { data: 'total_amount', name: 'total_amount', className: 'text-end' },
            { data: 'final_amount', name: 'final_amount', className: 'text-end' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/transaction/sales/summary',
        type: 'GET',
        data: {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        },
        success: function(response) {
            $('#summary-total-transaksi').text(response.total_transaksi);
            $('#summary-total-penjualan').text(response.total_penjualan);
            $('#summary-total-diskon').text(response.total_diskon);
            $('#summary-total-pendapatan').text(response.total_pendapatan);
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
    let url = '/transaction/sales/show/' + id;

    // Show loading or clear previous data
    $('#detail-customer').text('Loading...');
    $('#detail-date').text('Loading...');
    $('#detail-promo').text('-').removeClass('bg-primary bg-secondary').addClass('bg-secondary');
    $('#detail-items').html('<tr><td colspan="5" class="text-center">Loading data...</td></tr>');
    $('#detail-total-amount').text('-');
    $('#detail-discount-manual').text('-');
    $('#detail-final-amount').text('-');
    
    $('#modal-detail').modal('show');

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            $('#detail-customer').text(response.customer_name);
            $('#detail-date').text(response.order_date_formatted);
            
            // Handle promo display
            if (response.promo_name) {
                $('#detail-promo').text(response.promo_name).removeClass('bg-secondary').addClass('bg-primary');
            } else {
                $('#detail-promo').text('Tidak ada').removeClass('bg-primary').addClass('bg-secondary');
            }
            
            let itemsHtml = '';
            
            if (response.details && response.details.length > 0) {
                response.details.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td class="text-end">${item.quantity}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.unit_price)}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.discount)}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                        </tr>
                    `;
                });
            } else {
                itemsHtml = '<tr><td colspan="5" class="text-center">Tidak ada item</td></tr>';
            }
            
            $('#detail-items').html(itemsHtml);
            $('#detail-total-amount').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.sales_order.total_amount));
            $('#detail-discount-manual').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.sales_order.discount_amount));
            $('#detail-final-amount').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.sales_order.final_amount));
        },
        error: function(xhr) {
            handleAjaxError(xhr);
            $('#modal-detail').modal('hide');
        }
    });
};

// Delete function
function hapus(id) {
    Swal.fire({
        title: 'Hapus Penjualan?',
        text: 'Data penjualan akan dihapus secara permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/transaction/sales/destroy',
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

// Cetak Struk
function cetakStruk(id) {
    let url = '/pos/print/' + id + '?size=80';
    let printWindow = window.open(url, 'receipt_' + id, 'width=400,height=600,scrollbars=yes');
    if (printWindow) {
        printWindow.focus();
    }
}

window.cetakStruk = cetakStruk;