$(document).ready(function() {
    loadSummary();
    datatable();
});

/**
 * Load Summary Card    s
 */
const loadSummary = () => {
    $.ajax({
        url: '/report/stock-recommendation/summary',
        type: 'GET',
        success: function(response) {
            if (response.status) {
                const d = response.data;

                const total     = d.total_variants > 0 ? d.total_variants : 1;
                const pctFast   = Math.round((d.total_fast / total) * 100);
                const pctMedium = Math.round((d.total_medium / total) * 100);
                const pctSlow   = Math.round((d.total_slow / total) * 100);
                const pctDead   = Math.round((d.total_dead / total) * 100);

                // Main Stat Cards
                $('#stat-fast').text(d.total_fast);
                $('#pct-fast-text').text(pctFast + '% dari total');

                $('#stat-medium').text(d.total_medium);
                $('#pct-medium-text').text(pctMedium + '% dari total');

                $('#stat-slow').text(d.total_slow);
                $('#pct-slow-text').text(pctSlow + '% dari total');

                $('#stat-dead').text(d.total_dead);
                $('#pct-dead-text').text(pctDead + '% dari total');
            }
        },
        error: function(xhr) {
            console.error('Failed to load summary', xhr);
        }
    });
}

/**
 * Initialize or reload DataTable
 */
const datatable = () => {

    return NioApp.DataTable('#table-recommendation', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        destroy: true,
        ajax: {
            url: '/report/stock-recommendation/datatable',
            type: 'GET',
            error: function(xhr) {
                console.error('DataTable error', xhr);
            }
        },
        order: [[1, 'desc']], // Default order by analysis date
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'analysis_date_display', name: 'analysis_date' },
            { data: 'total_variants', name: 'total_variants'},
            { data: 'fast_display', name: 'fast_display' },
            { data: 'medium_display', name: 'medium_display' },
            { data: 'slow_display', name: 'slow_display' },
            { data: 'dead_display', name: 'dead_display' },
            { data: 'cogs_balance_display', name: 'cogs_balance' },
            { data: 'total_estimated_amount_display', name: 'total_estimated_amount' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

// Bind Generate event
$('.btn-generate').on('click', function(e) {
    e.preventDefault();
    
    // Show Modal
    $('#modalGenerate').modal('show');
});

// Bind Submit
$('#form-generate').on('submit', function(e) {
    e.preventDefault();

    const startDateVal = $('#start_date').val();
    if (!startDateVal) {
        NioApp.Toast('Tanggal mulai wajib diisi', 'warning', { position: 'top-right' });
        return;
    }

    const btn = $('.btn-submit-generate');
    const originalText = btn.first().html();
    
    // Calculate period_days based on selected start date vs yesterday
    const startDate = new Date(startDateVal);
    const today = new Date();
    const diffTime = Math.abs(today - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    const periodDays = diffDays > 0 ? diffDays : 1;

    btn.prop('disabled', true).html('<em class="spinner-border spinner-border-sm me-1"></em> <span>Memproses</span>');

    $.ajax({
        url: '/report/stock-recommendation/generate',
        type: 'POST',
        data: {
            period_days: periodDays
        },
        success: function(response) {
            if (response.status) {
                NioApp.Toast(response.message, 'success', { position: 'top-right' });
                $('#modalGenerate').modal('hide');
                
                // Reload page after short delay to update history list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                NioApp.Toast(response.message, 'warning', { position: 'top-right' });
            }
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
            NioApp.Toast(msg, 'error', { position: 'top-right' });
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});

// Handle Delete Action
const hapus = (id) => {
    Swal.fire({
        title: 'Hapus Rekomendasi?',
        text: "Data histori rekomendasi beserta detailnya akan dihapus",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/report/stock-recommendation/destroy/${id}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                        $('#table-recommendation').DataTable().ajax.reload(null, false);
                    } else {
                        NioApp.Toast(response.message, 'error', { position: 'top-right' });
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.';
                    NioApp.Toast(msg, 'error', { position: 'top-right' });
                }
            });
        }
    });
}

window.loadSummary = loadSummary;
window.datatable = datatable;
window.hapus = hapus;
