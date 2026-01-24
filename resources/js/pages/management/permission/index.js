$(document).ready(function() {
    console.log('User Management page scripts loaded');
    datatable();
});

const datatable = () => {
    NioApp.DataTable('#table-data', {
        processing: true,
        responsive: false,
        scrollX: true,
        destroy: true,
        ajax: {
            url: '/management/akses/datatable',
            type: 'GET',
            error: function (xhr) {
                if (xhr.status === 419) { // Unauthorized error
                    NioApp.Toast('Sesi kamu sudah habis. Silahkan login ulang 😊', 'error', {position: 'top-right'});
                    window.location.href = "/login"; 
                } else {
                    NioApp.Toast('Terjadi kesalahan saat memuat data. Silahkan coba lagi.', 'error', {position: 'top-right'});
                }
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%', searchable: false },
            { data: 'action', name: 'action', width: '10%', orderable: false, searchable: false },
            { data: 'nama_role', name: 'nama_role' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

function hapus(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data akses/paket akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e85347',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/management/akses/destroy/' + id,
                type: 'POST',
                dataType: 'JSON',
                success: function(response) {
                    if (response.status) {
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                        datatable();
                    } else {
                        NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                    }
                },
                error: function(response) {
                    let statusCode = response.status;
                    if(statusCode >= 500) {
                        NioApp.Toast('Terjadi kesalahan', 'error', { position: 'top-right' });
                    } else {
                        NioApp.Toast(response.responseJSON.message, 'warning', { position: 'top-right' });
                    }
                }
            });
        }
    });
}

// Expose function to global scope for onclick handlers (required for Vite bundling)
window.hapus = hapus;