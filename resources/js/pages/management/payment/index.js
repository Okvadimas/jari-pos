$(document).ready(function () {
    const table = $('#table-data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/management/payment/datatable",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });
});

window.editData = function(id) {
    window.location.href = `/management/payment/edit/${id}`;
}

window.deleteData = function(id) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/management/payment/destroy/${id}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        Swal.fire(
                            'Terhapus!',
                            response.message,
                            'success'
                        ).then(() => {
                            $('#table-data').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan pada server.',
                        'error'
                    );
                }
            });
        }
    })
}
