$(document).ready(function() {
    console.log('Inventory - Unit page scripts loaded');
    datatable();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/inventory/unit/datatable',
            type: 'GET',
            error: function (xhr) {
                handleAjaxError(xhr);
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', searchable: false },
            { data: 'action', name: 'action', width: '10%', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data satuan akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/inventory/unit/destroy/' + id,
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
