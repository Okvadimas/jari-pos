$(document).ready(function() {
    console.log('Rekomendasi Stok (Moving Status) page loaded');
});

let currentHistoryId = null;
let dataTable = null;

/**
 * Generate / Process Moving Status Analysis
 */
$('#btn-generate').on('click', function() {
    const btn = $(this);
    const originalText = btn.html();

    Swal.fire({
        title: 'Proses Analisis Moving Stock?',
        text: 'Sistem akan menganalisis data penjualan 30 hari terakhir dan mengklasifikasikan semua produk.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

            $.ajax({
                url: '/report/stock-recommendation/generate',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                        currentHistoryId = response.history_id;
                        loadSummary(response.history_id);
                        loadDataTable(response.history_id);
                        showSections();

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
        }
    });
});

/**
 * Load Summary Cards
 */
function loadSummary(historyId) {
    $.ajax({
        url: '/report/stock-recommendation/summary/' + historyId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const d = response.data;
                $('#summary-fast').text(d.total_fast);
                $('#summary-medium').text(d.total_medium);
                $('#summary-slow').text(d.total_slow);
                $('#summary-dead').text(d.total_dead);
                $('#analysis-info').text(
                    `Analisis: ${d.analysis_date} | Periode: ${d.period_start} s/d ${d.period_end} (${d.period_days} hari) | Total: ${d.total_variants} produk`
                );
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
function loadDataTable(historyId, movingStatus) {
    if (dataTable) {
        dataTable.destroy();
        $('#table-data tbody').empty();
    }

    dataTable = NioApp.DataTable('#table-data', {
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '/report/stock-recommendation/datatable',
            type: 'GET',
            data: function(d) {
                d.history_id = historyId;
                d.moving_status = movingStatus || '';
            },
            error: function(xhr) {
                console.error('DataTable error', xhr);
            }
        },
        order: [[7, 'desc']], // Order by score descending
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'product_display', name: 'product_name' },
            { data: 'sku', name: 'sku' },
            { data: 'category_name', name: 'category_name' },
            { data: 'total_qty_sold', name: 'total_qty_sold', className: 'text-center' },
            { data: 'total_revenue', name: 'total_revenue', className: 'text-end' },
            { data: 'avg_daily_sales', name: 'avg_daily_sales', className: 'text-center' },
            { data: 'score', name: 'score', className: 'text-center' },
            { data: 'status_badge', name: 'moving_status', className: 'text-center' },
            { data: 'current_stock', name: 'current_stock', className: 'text-center' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

/**
 * Filter by moving status (pill buttons)
 */
$(document).on('click', '.filter-status', function() {
    $('.filter-status').removeClass('active');
    $(this).addClass('active');

    const status = $(this).data('status');

    if (currentHistoryId) {
        loadDataTable(currentHistoryId, status);
    }
});

/**
 * View a specific history from the history table
 */
$(document).on('click', '.btn-view-history', function() {
    const historyId = $(this).data('id');
    currentHistoryId = historyId;
    loadSummary(historyId);
    loadDataTable(historyId);
    showSections();

    // Scroll to top
    $('html, body').animate({ scrollTop: $('#summary-section').offset().top - 80 }, 400);
});

/**
 * Show the sections that are initially hidden
 */
function showSections() {
    $('#summary-section').slideDown(300);
    $('#filter-section').slideDown(300);
    $('#table-section').slideDown(300);
}
