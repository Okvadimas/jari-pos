$(document).ready(function() {
    console.log('Company Management page scripts loaded');
    datatable();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/management/company/datatable',
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
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'address', name: 'address' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data perusahaan akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/management/company/destroy/' + id,
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
