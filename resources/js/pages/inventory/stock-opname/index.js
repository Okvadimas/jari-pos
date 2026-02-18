$(document).ready(function() {
    console.log('Stock Opname page scripts loaded');
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
            url: '/inventory/stock-opname/datatable',
            type: 'GET',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.status = $('#filter_status').val();
            },
            error: function (xhr) {
                handleAjaxError(xhr);
            }
        },
        order: [3, 'desc'],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'opname_number', name: 'opname_number' },
            { data: 'opname_date', name: 'opname_date' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false, className: 'text-center' },
            { data: 'total_items', name: 'total_items', className: 'text-center' },
            { data: 'total_difference', name: 'total_difference', className: 'text-center' },
            { data: 'notes', name: 'notes' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

const loadSummary = () => {
    $.ajax({
        url: '/inventory/stock-opname/summary',
        type: 'GET',
        data: {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        },
        success: function(response) {
            $('#summary-total-opname').text(response.total_opname);
            $('#summary-selisih-plus').text('+' + response.total_selisih_plus);
            $('#summary-selisih-minus').text('-' + response.total_selisih_minus);
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
    let url = '/inventory/stock-opname/show/' + id;

    // Show loading
    $('#detail-number').text('Loading...');
    $('#detail-date').text('Loading...');
    $('#detail-status').html('-');
    $('#detail-items').html('<tr><td colspan="6" class="text-center">Loading data...</td></tr>');
    $('#detail-note').text('');
    $('#detail-approval-info').hide();

    $('#modal-detail').modal('show');

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            $('#detail-number').text(response.opname.opname_number);
            $('#detail-date').text(response.opname_date_formatted);
            $('#detail-note').text(response.opname.notes || 'Tidak ada catatan');

            // Status badge
            const badges = {
                'draft': '<span class="badge bg-warning">Draft</span>',
                'approved': '<span class="badge bg-success">Approved</span>',
                'cancelled': '<span class="badge bg-danger">Cancelled</span>',
            };
            $('#detail-status').html(badges[response.opname.status] || '-');

            // Approval info
            if (response.opname.status === 'approved' && response.approved_by_name) {
                $('#detail-approval-info').show();
                $('#detail-approved-by').text(response.approved_by_name);
                $('#detail-approved-at').text(response.approved_at_formatted || '-');
            } else {
                $('#detail-approval-info').hide();
            }

            let itemsHtml = '';

            if (response.details && response.details.length > 0) {
                response.details.forEach(item => {
                    let diffClass = 'text-muted';
                    let diffPrefix = '';
                    if (item.difference > 0) { diffClass = 'text-success'; diffPrefix = '+'; }
                    if (item.difference < 0) { diffClass = 'text-danger'; }

                    itemsHtml += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.sku}</td>
                            <td class="text-end">${item.system_stock}</td>
                            <td class="text-end">${item.physical_stock}</td>
                            <td class="text-end ${diffClass} fw-bold">${diffPrefix}${item.difference}</td>
                            <td>${item.notes || '-'}</td>
                        </tr>
                    `;
                });
            } else {
                itemsHtml = '<tr><td colspan="6" class="text-center">Tidak ada item</td></tr>';
            }

            $('#detail-items').html(itemsHtml);
        },
        error: function(xhr) {
            handleAjaxError(xhr);
            $('#modal-detail').modal('hide');
        }
    });
}

// Approve function
function approve(id) {
    Swal.fire({
        title: 'Approve Stock Opname?',
        text: 'Stok akan disesuaikan berdasarkan hasil opname. Tindakan ini tidak bisa dibatalkan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/inventory/stock-opname/approve/' + id,
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
                error: function(xhr) {
                    handleAjaxError(xhr);
                }
            });
        }
    });
}

// Cancel function
function cancel(id) {
    Swal.fire({
        title: 'Batalkan Stock Opname?',
        text: 'Stock opname akan dibatalkan dan tidak bisa diedit lagi',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Kembali'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/inventory/stock-opname/cancel/' + id,
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
                error: function(xhr) {
                    handleAjaxError(xhr);
                }
            });
        }
    });
}

// Delete function
function hapus(id) {
    Swal.fire({
        title: 'Hapus Stock Opname?',
        text: 'Data stock opname draft akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/inventory/stock-opname/destroy',
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
window.approve = approve;
window.cancel = cancel;
window.hapus = hapus;
