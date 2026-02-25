$(document).ready(function() {
    if (window.TodayHistoryId && window.TodayHistoryId !== 'null') {
        currentHistoryId = window.TodayHistoryId;
        loadSummary(currentHistoryId);
        loadDataTable(currentHistoryId);
        showSections();
    }
});

let currentHistoryId = null;
let dataTable = null;
let estimatedTotals = {}; // Stores total nominal per product (rowId)
let globalTotalCogsLimit = 0; // Stores actual COGS for validation

const calculateGrandTotal = () => {
    const sum = Object.values(estimatedTotals).reduce((a, b) => a + b, 0);
    $('#grand-total-estimation').text('Rp ' + sum.toLocaleString('id-ID'));
    
    // Check against COGS limit
    if (globalTotalCogsLimit > 0 && sum > globalTotalCogsLimit) {
        $('#warn-est-total').text('Rp ' + sum.toLocaleString('id-ID'));
        $('#warn-cogs-total').text('Rp ' + globalTotalCogsLimit.toLocaleString('id-ID'));
        $('#estimasi-warning').slideDown(200);
    } else {
        $('#estimasi-warning').slideUp(200);
    }
}

/**
 * Generate / Process Moving Status Analysis
 */
const generate = () => {
    const btn = $('#btn-generate');
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
}

/**
 * Load Summary Cards
 */
const loadSummary = (historyId) => {
    $.ajax({
        url: '/report/stock-recommendation/summary/' + historyId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const d = response.data;

                // Summary cards
                $('#summary-fast').text(d.total_fast);
                $('#summary-medium').text(d.total_medium);
                $('#summary-slow').text(d.total_slow);
                $('#summary-dead').text(d.total_dead);

                // Analysis info panel
                $('#info-analysis-date').text(d.analysis_date);

                // Set global cogs limit for warning validation
                globalTotalCogsLimit = d.cogs_balance ? parseFloat(d.cogs_balance) : 0;
                // Re-validate UI
                calculateGrandTotal();

                const gross = d.gross_profit_balance != null
                    ? 'Rp ' + parseInt(d.gross_profit_balance).toLocaleString('id-ID')
                    : '—';
                const cogs = d.cogs_balance != null
                    ? 'Rp ' + parseInt(d.cogs_balance).toLocaleString('id-ID')
                    : '—';

                $('#info-gross').html('<span class="text-muted fw-normal fs-11px">Gross:</span> ' + gross);
                $('#info-cogs').html('<span class="text-muted fw-normal fs-11px">COGS:</span> ' + cogs);

                $('#info-period').text(d.period_start + ' s/d ' + d.period_end);
                $('#info-period-days').text(d.period_days + ' hari');
                $('#th-period-days').text(d.period_days);

                $('#analysis-info').slideDown(300);
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
const loadDataTable = (historyId) => {
    // Reset grand total every time a new table is loaded
    estimatedTotals = {};
    calculateGrandTotal();

    if (dataTable) {
        dataTable.destroy();
        $('#table-recommendation tbody').empty();
    }

    dataTable = NioApp.DataTable('#table-recommendation', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: '/report/stock-recommendation/datatable',
            type: 'post',
            data: function(d) {
                d._token = token;
                d.history_id = historyId;
            },
            error: function(xhr) {
                console.error('DataTable error', xhr);
            }
        },
        order: [[3, 'desc']], // Default order by performance
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'product_display', name: 'product_name' },
            { data: 'current_stock', name: 'current_stock', className: 'text-center' },
            { data: 'performance_display', name: 'performance_display', orderable: false, searchable: false },
            { data: 'purchase_price_display', name: 'purchase_price', className: 'text-end' },
            { data: 'qty_recommendation', name: 'qty_recommendation', orderable: false, searchable: false, className: 'text-center' },
            { data: 'estimated_nominal', name: 'estimated_nominal', orderable: false, searchable: false, className: 'text-end' },
            { data: 'ai_description', name: 'ai_description', orderable: false, searchable: false, className: 'text-center' },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
    });
}

/**
 * Show the sections that are initially hidden
 */
const showSections = () => {
    $('#summary-section').slideDown(300);
    $('#filter-section').slideDown(300);
    $('#table-section').slideDown(300);
}

// Bind Generate event
$('#btn-generate').on('click', generate);

/**
 * Calculate Estimated Nominal when Qty Recommendation changes
 */
$(document).on('input change', '.qty-input', function() {
    let qty = parseInt($(this).val()) || 0;
    
    // Prevent negative numbers
    if (qty < 0) {
        qty = 0;
        $(this).val(0);
    }
    
    const price = parseFloat($(this).data('price')) || 0;
    const rowId = $(this).data('id');
    const total = qty * price;
    
    // Format to IDR
    const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');
    
    // Update the nominal label in the datatable row
    $('#est-' + rowId).text(formattedTotal).attr('data-value', total);
    
    // Update Grand Total in the state dictionary
    estimatedTotals[rowId] = total;
    calculateGrandTotal();
});

window.generate = generate;
window.loadSummary = loadSummary;
window.loadDataTable = loadDataTable;
window.calculateGrandTotal = calculateGrandTotal;
window.showSections = showSections;
