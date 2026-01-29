$(document).ready(function() {
    console.log('Purchasing Report page scripts loaded');
    datatable();
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
                console.error(xhr);
                NioApp.Toast('Error loading data', 'error', {position: 'top-right'});
            }
        },
        columns: [
            { data: 'id', name: 'id', render: function(data, type, row) {
                return '#' + data;
            }},
            { data: 'purchase_date', name: 'purchase_date' },
            { data: 'supplier_name', name: 'supplier_name' },
            { data: 'total_cost', name: 'total_cost', className: 'text-end' },
            { data: 'reference_note', name: 'reference_note' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

// Refresh table on filter click
$('#btn-filter').on('click', function(e) {
    $('#table-data').DataTable().ajax.reload();
});

// View Details
$(document).on('click', '.btn-detail', function() {
    let id = $(this).data('id');
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
            $('#detail-supplier').text(response.company_name);
            $('#detail-date').text(response.purchase_date_formatted);
            $('#detail-note').text(response.purchase.reference_note || 'No notes');
            
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
                itemsHtml = '<tr><td colspan="5" class="text-center">No items found</td></tr>';
            }
            
            $('#detail-items').html(itemsHtml);
            $('#detail-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.purchase.total_cost));
        },
        error: function(xhr) {
            console.error(xhr);
            NioApp.Toast('Failed to load details', 'error', {position: 'top-right'});
            $('#modal-detail').modal('hide');
        }
    });
});
