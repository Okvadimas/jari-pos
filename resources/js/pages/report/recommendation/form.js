$(document).ready(function() {
    currentHistoryId = window.HistoryId || window.TodayHistoryId || null;

    if (currentHistoryId && currentHistoryId !== 'null') {
        loadSummary(currentHistoryId);
        loadDataTable(currentHistoryId);
    }
});

let currentHistoryId = null;
let dataTable = null;
let globalBaseNominal = 0;       // Stores the baseline nominal from db
let editedNominalDifferences = {}; // Stores differential nominal per product (rowId)
let savedQuantities = {};    // Stores qtys changed by the user meant for saving
let savedDescriptions = {};  // Stores AI descriptions per product id
let globalTotalCogsLimit = 0; // Stores actual COGS for validation

const calculateGrandTotal = () => {
    let sum = globalBaseNominal;
    Object.values(editedNominalDifferences).forEach(diff => sum += diff);
    
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
 * Generate functionality removed for Edit Form
 */
/**
 * Load Summary Cards
 */
const loadSummary = (historyId) => {
    $.ajax({
        url: '/report/stock-recommendation/summary/' + historyId,
        type: 'GET',
        success: function(response) {
            if (response.status) {
                const d = response.data;
                console.log("Summary Data", d);

                const total     = d.total_variants > 0 ? d.total_variants : 1;
                const pctFast   = Math.round((d.total_fast / total) * 100);
                const pctMedium = Math.round((d.total_medium / total) * 100);
                const pctSlow   = Math.round((d.total_slow / total) * 100);
                const pctDead   = Math.round((d.total_dead / total) * 100);

                $('#total-products').text(total);

                // Main Stat Cards
                $('#stat-fast').text(d.total_fast);
                $('#bar-fast').css('width', pctFast + '%');
                $('#pct-fast-text').text(pctFast + '% dari total');

                $('#stat-medium').text(d.total_medium);
                $('#bar-medium').css('width', pctMedium + '%');
                $('#pct-medium-text').text(pctMedium + '% dari total');

                $('#stat-slow').text(d.total_slow);
                $('#bar-slow').css('width', pctSlow + '%');
                $('#pct-slow-text').text(pctSlow + '% dari total');

                $('#stat-dead').text(d.total_dead);
                $('#bar-dead').css('width', pctDead + '%');
                $('#pct-dead-text').text(pctDead + '% dari total');

                // Distribution Stacked Bar
                $('#dist-bar-fast').css('width', pctFast + '%').attr('data-bs-original-title', `Fast Moving: ${d.total_fast} produk (${pctFast}%)`);
                $('#dist-bar-medium').css('width', pctMedium + '%').attr('data-bs-original-title', `Medium Moving: ${d.total_medium} produk (${pctMedium}%)`);
                $('#dist-bar-slow').css('width', pctSlow + '%').attr('data-bs-original-title', `Slow Moving: ${d.total_slow} produk (${pctSlow}%)`);
                $('#dist-bar-dead').css('width', pctDead + '%').attr('data-bs-original-title', `Dead Stock: ${d.total_dead} produk (${pctDead}%)`);

                // Distribution Legend
                $('#dist-stat-fast').text(d.total_fast);
                $('#dist-pct-fast').text(pctFast + '%');

                $('#dist-stat-medium').text(d.total_medium);
                $('#dist-pct-medium').text(pctMedium + '%');

                $('#dist-stat-slow').text(d.total_slow);
                $('#dist-pct-slow').text(pctSlow + '%');

                $('#dist-stat-dead').text(d.total_dead);
                $('#dist-pct-dead').text(pctDead + '%');

                // Set baseline nominal and cogs limit
                globalBaseNominal = parseFloat(d.total_estimated_nominal) || 0;
                globalTotalCogsLimit = d.cogs_balance ? parseFloat(d.cogs_balance) : 0;
                
                // Re-validate UI
                calculateGrandTotal();
                
                // Balance Information
                const grossVal = parseFloat(d.gross_profit_balance) || 0;
                const cogsVal = parseFloat(d.cogs_balance) || 0;
                const balanceTotal = grossVal + cogsVal;
                
                const grossPct = balanceTotal > 0 ? (grossVal / balanceTotal * 100).toFixed(1) : 0;
                const cogsPct = balanceTotal > 0 ? (cogsVal / balanceTotal * 100).toFixed(1) : 0;

                $('#info-total-balance').text('Rp ' + parseInt(balanceTotal).toLocaleString('id-ID'));
                
                $('#info-gross-val').text('Rp ' + parseInt(grossVal).toLocaleString('id-ID'));
                $('#info-gross-pct').text(grossPct + '%');
                $('#info-gross-bar').css('width', grossPct + '%');

                $('#info-cogs-val').text('Rp ' + parseInt(cogsVal).toLocaleString('id-ID'));
                $('#info-cogs-pct').text(cogsPct + '%');
                $('#info-cogs-bar').css('width', cogsPct + '%');

                $('#info-period').text(d.period_start + ' s/d ' + d.period_end);
                $('#info-period-days').text(d.period_days + ' hari');
                $('#th-period-days').text(d.period_days);
                
                // Periode Analisis next to button generate
                $('.period-range').text(d.period_start + ' - ' + d.period_end);

                if ($.fn.tooltip) {
                    $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
                }

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
    // Reset differences when a completely new table strategy is loaded
    editedNominalDifferences = {};
    calculateGrandTotal();

    return NioApp.DataTable('#table-recommendation', {
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        destroy: true,
        ajax: {
            url: '/report/stock-recommendation/datatable',
            type: 'GET',
            data: function(d) {
                d.history_id = historyId;
                d.is_edit = window.IsEdit ? 1 : 0;
            },
            error: function(xhr) {
                console.error('DataTable error', xhr);
            }
        },
        order: [[3, 'desc']], // Default order by performance
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'product_display', name: 'product_name' },
            { data: 'current_stock_display', name: 'current_stock' },
            { data: 'performance_display', name: 'performance_display', orderable: false, searchable: false, className: 'text-center' },
            { data: 'purchase_price_display', name: 'purchase_price', className: 'text-end' },
            { data: 'qty_recommendation', name: 'qty_recommendation', orderable: false, searchable: false, className: 'text-center' },
            { data: 'estimated_nominal', name: 'estimated_nominal', orderable: false, searchable: false, className: 'text-end' },
            // { data: 'ai_description', name: 'ai_description', orderable: false, searchable: false, className: 'text-center', visible: false },
        ],
        columnDefs: [
            { targets: '_all', className: 'nk-tb-col' },
        ],
        drawCallback: function(settings) {
            // Restore saved quantities + AI descriptions on pagination
            applyAIToVisibleRows();
            calculateGrandTotal();
        }
    });
}

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
    const originalQty = parseInt($(this).data('original-qty')) || 0;
    const rowId = $(this).data('id');
    const total = qty * price;
    
    // Format to IDR
    const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');
    
    // Update the nominal label in the datatable row
    $('#est-' + rowId).text(formattedTotal).attr('data-value', total);
    
    // Update difference and Grand Total state
    editedNominalDifferences[rowId] = (qty - originalQty) * price;
    savedQuantities[rowId] = qty;
    calculateGrandTotal();
});

/**
 * Handle Final Save button
 */
$('#btn-save-recommendation').on('click', function(e) {
    e.preventDefault();
    if (!currentHistoryId) return;

    const btn = $(this);
    const originalText = btn.html();

    // Prepare data array of quantities { detail_id: qty }
    const items = Object.keys(savedQuantities).map(id => ({
        id: id,
        qty: savedQuantities[id]
    }));

    if (items.length === 0) {
        NioApp.Toast('Tidak ada data rekomendasi / kuantitas re-stok yang dimasukkan.', 'info', { position: 'top-right' });
        return;
    }

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

    $.ajax({
        url: '/report/stock-recommendation/save/' + currentHistoryId,
        type: 'POST',
        data: {
            items: items
        },
        success: function(response) {
            if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/report/stock-recommendation';
                });
            } else {
                NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
            NioApp.Toast(msg, 'error', { position: 'top-right' });
            btn.prop('disabled', false).html(originalText);
        }
    });
});

/**
 * Handle Generate AI Recommendation button
 */


$('#btn-generate-ai').on('click', function(e) {
    e.preventDefault();
    if (!currentHistoryId) return;

    const btn = $(this);
    const originalText = btn.html();

    btn.prop('disabled', true).html('<em class="spinner-border spinner-border-sm me-1"></em> <span>AI Sedang Menganalisis...</span>');

    $.ajax({
        url: '/report/stock-recommendation/ai/' + currentHistoryId,
        type: 'GET',
        success: function(response) {
            if (response.status && response.data && response.data.recommendations) {
                const { recommendations, products } = response.data;

                // Build AI lookup: id -> { qty, description }
                const aiMap = {};
                recommendations.forEach(r => {
                    aiMap[r.id] = {
                        qty: r.qty_recommendation || 0,
                        desc: r.ai_description || ''
                    };
                });

                // Build price lookup from products: id -> { price, originalQty }
                const priceMap = {};
                products.forEach(p => priceMap[p.id] = {
                    price: parseFloat(p.purchase_price) || 0,
                    originalQty: parseInt(p.qty_restock) || 0
                });

                // Reset state and recalculate from ALL products
                savedQuantities = {};
                editedNominalDifferences = {};
                savedDescriptions = {};
                globalBaseNominal = 0;

                products.forEach(p => {
                    const ai = aiMap[p.id];
                    const aiQty = ai ? ai.qty : p.qty_restock;
                    const price = parseFloat(p.purchase_price) || 0;
                    const originalQty = parseInt(p.qty_restock) || 0;

                    // Base nominal = original DB values
                    globalBaseNominal += originalQty * price;

                    // Difference = AI qty vs original
                    editedNominalDifferences[p.id] = (aiQty - originalQty) * price;

                    // Store for save button
                    if (aiQty > 0) savedQuantities[p.id] = aiQty;

                    // Store AI description
                    if (ai && ai.desc) savedDescriptions[p.id] = ai.desc;
                });

                // Update currently visible inputs + inject descriptions
                applyAIToVisibleRows();

                // Recalculate Grand Total (covers ALL pages via editedNominalDifferences)
                calculateGrandTotal();

                NioApp.Toast(`Berhasil menerapkan rekomendasi AI untuk ${recommendations.length} produk.`, 'success', { position: 'top-right' });
            } else {
                NioApp.Toast('Gagal mendapatkan rekomendasi AI.', 'warning', { position: 'top-right' });
            }
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghubungi layanan AI.';
            NioApp.Toast(msg, 'error', { position: 'top-right' });
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});

/**
 * Apply AI quantities and descriptions to currently visible DataTable rows
 * Uses function declaration (not const) for hoisting — needed by drawCallback
 */
function applyAIToVisibleRows() {
    $('.qty-input').each(function() {
        const rowId = $(this).data('id');

        // Update qty input
        if (savedQuantities[rowId] !== undefined) {
            $(this).val(savedQuantities[rowId]);
            const price = parseFloat($(this).data('price')) || 0;
            const total = savedQuantities[rowId] * price;
            $('#est-' + rowId).text('Rp ' + total.toLocaleString('id-ID')).attr('data-value', total);
        }

        // Inject AI description below estimated nominal
        const descContainer = $('#ai-desc-' + rowId);
        if (savedDescriptions[rowId]) {
            if (descContainer.length) {
                descContainer.text(savedDescriptions[rowId]);
            } else {
                $('#est-' + rowId).after(
                    '<div id="ai-desc-' + rowId + '" class="ai-desc-text">' +
                    '<em class="icon ni ni-bulb-fill me-1"></em>' +
                    savedDescriptions[rowId] +
                    '</div>'
                );
            }
        }
    });
}


window.loadSummary = loadSummary;
window.loadDataTable = loadDataTable;
window.calculateGrandTotal = calculateGrandTotal;