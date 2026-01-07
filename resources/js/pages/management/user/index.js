$(document).ready(function() {
    console.log('User Management page scripts loaded');
    datatable();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/management/user/datatable',
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', searchable: false },
            { data: 'action', name: 'action', width: '10%', orderable: false, searchable: false },
            { data: 'nama_company', name: 'nama_company' },
            { data: 'name', name: 'name' },
            { data: 'nama_role', name: 'nama_role' },
            { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
                return data === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            }},
        ],
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data user akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/management/user/destroy/' + id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status) {
                        $('#table-data').DataTable().ajax.reload();
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    } else {
                        NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                    }
                },
                error: function(error) {
                    console.log(error);
                    NioApp.Toast('Error while fetching data', 'error', { position: 'top-right' });
                }
            });
        }
    });
}

// Expose function to global scope for onclick handlers (required for Vite bundling)
window.hapus = hapus;