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
                handleAjaxError(xhr);
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
