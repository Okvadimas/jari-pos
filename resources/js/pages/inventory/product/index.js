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
            url: '/inventory/product/datatable',
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
            { data: 'nama_produk', name: 'nama_produk' },
            { data: 'nama_kategori', name: 'nama_kategori' },
            { data: 'nama_varian', name: 'nama_varian', orderable: false },
            { data: 'harga_beli', name: 'harga_beli', render: function(data) {
                return 'Rp. ' + formatCurrency(data);
            } },
            { data: 'harga_jual', name: 'harga_jual', render: function(data) {
                return 'Rp. ' + formatCurrency(data);
            } },
            { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
                return data === true || data === 1 || data === 'Active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            }},
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
                error: function(error) {
                    console.log(error);
                    NioApp.Toast('Error while fetching data', 'error', { position: 'top-right' });
                }
            });
        }
    });
}

window.hapus = hapus;
