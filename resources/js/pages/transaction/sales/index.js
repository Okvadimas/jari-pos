$(document).ready(function() {
    console.log('Sales Report page scripts loaded');
    datatable();
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
        columns: [
            { data: 'id', name: 'id', render: function(data, type, row) {
                return '#' + data;
            }},
            { data: 'order_date', name: 'order_date' },
            { data: 'customer_name', name: 'company.name' },
            { data: 'total_amount', name: 'total_amount', className: 'text-end' },
            { data: 'final_amount', name: 'final_amount', className: 'text-end' },
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
