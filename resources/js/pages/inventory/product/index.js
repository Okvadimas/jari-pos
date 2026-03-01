$(document).ready(function() {
    console.log('Inventory - Unit page scripts loaded');
    datatable();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/inventory/product/datatable',
            type: 'POST',
            data: function (d) {
                d._token = token;
            },
            error: function (xhr) {
                handleAjaxError(xhr);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'nama_produk', name: 'p.name' },
            { data: 'nama_kategori', name: 'c.name' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data produk akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/inventory/product/destroy/' + id,
                type: 'POST',
                dataType: 'JSON',
                success: function(response) {
                    if (response.status) {
                        $('#table-data').DataTable().ajax.reload();
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    } else {
                        NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                    }
                },
                error: function(response) {
                    handleAjaxError(response);
                }
            });
        }
    });
}

window.hapus = hapus;
