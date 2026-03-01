$(document).ready(function () {
    console.log('Stock Opname Form scripts loaded');

    // Load existing details for edit mode
    if (window.existingDetails && window.existingDetails.length > 0) {
        window.existingDetails.forEach(detail => {
            addItemRow(detail);
        });
    }
});

let rowIndex = 0;

// Add empty item row
$('#btn-add-item').on('click', function () {
    addItemRow();
});

function addItemRow(data = null) {
    const idx = rowIndex++;
    const productName = data ? data.product_name : '';
    const productVariantId = data ? data.product_variant_id : '';
    const systemStock = data ? data.system_stock : 0;
    const physicalStock = data ? data.physical_stock : 0;
    const difference = data ? data.difference : 0;
    const notes = data ? (data.notes || '') : '';

    let diffClass = '';
    let diffPrefix = '';
    if (difference > 0) { diffClass = 'text-success'; diffPrefix = '+'; }
    if (difference < 0) { diffClass = 'text-danger'; }

    const row = `
        <tr id="row-${idx}">
            <td>
                <select class="form-select select-product" name="details[${idx}][product_variant_id]" data-row="${idx}" style="width: 100%;">
                    ${productVariantId ? `<option value="${productVariantId}" selected>${productName}</option>` : '<option value="">Pilih Produk</option>'}
                </select>
                <input type="hidden" name="details[${idx}][system_stock]" id="system-stock-input-${idx}" value="${systemStock}">
            </td>
            <td class="text-end">
                <span class="system-stock" id="system-stock-${idx}">${systemStock}</span>
            </td>
            <td>
                <input type="number" class="form-control text-end physical-stock" name="details[${idx}][physical_stock]" data-row="${idx}" value="${physicalStock}" min="0" placeholder="0">
            </td>
            <td class="text-end">
                <span class="difference fw-bold ${diffClass}" id="difference-${idx}">${diffPrefix}${difference}</span>
            </td>
            <td>
                <input type="text" class="form-control" name="details[${idx}][notes]" value="${notes}" placeholder="Catatan">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-item" data-row="${idx}">
                    <em class="icon ni ni-trash"></em>
                </button>
            </td>
        </tr>
    `;

    $('#item-rows').append(row);

    // Initialize Select2 for this row
    $(`#row-${idx} .select-product`).select2({
        placeholder: 'Pilih Produk',
        allowClear: true,
        ajax: {
            url: '/utility/variants',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.data || data };
            },
            cache: true
        },
        minimumInputLength: 0,
        width: '100%',
    });
}

// On product selection change, fetch system stock
$(document).on('change', '.select-product', function () {
    const rowIdx = $(this).data('row');
    const variantId = $(this).val();

    if (variantId) {
        $.ajax({
            url: '/inventory/stock-opname/system-stock/' + variantId,
            type: 'GET',
            success: function (response) {
                const systemStock = response.data ? response.data.system_stock : 0;
                $(`#system-stock-${rowIdx}`).text(systemStock);
                $(`#system-stock-input-${rowIdx}`).val(systemStock);
                calculateDifference(rowIdx);
            },
            error: function (xhr) {
                $(`#system-stock-${rowIdx}`).text('0');
                $(`#system-stock-input-${rowIdx}`).val(0);
            }
        });
    } else {
        $(`#system-stock-${rowIdx}`).text('0');
        $(`#system-stock-input-${rowIdx}`).val(0);
        calculateDifference(rowIdx);
    }
});

// On physical stock change, calculate difference
$(document).on('input', '.physical-stock', function () {
    const rowIdx = $(this).data('row');
    calculateDifference(rowIdx);
});

function calculateDifference(rowIdx) {
    const systemStock = parseInt($(`#system-stock-input-${rowIdx}`).val()) || 0;
    const physicalStock = parseInt($(`input[name="details[${rowIdx}][physical_stock]"]`).val()) || 0;
    const difference = physicalStock - systemStock;

    let diffClass = 'text-muted';
    let diffPrefix = '';
    if (difference > 0) { diffClass = 'text-success'; diffPrefix = '+'; }
    if (difference < 0) { diffClass = 'text-danger'; }

    $(`#difference-${rowIdx}`).text(diffPrefix + difference).attr('class', 'difference fw-bold ' + diffClass);
}

// Remove item row
$(document).on('click', '.btn-remove-item', function () {
    const rowIdx = $(this).data('row');
    $(`#row-${rowIdx}`).remove();
});

// Submit form
$('#form-data').on('submit', function (e) {
    e.preventDefault();

    // Check if there are items
    if ($('#item-rows tr').length === 0) {
        NioApp.Toast('Minimal harus ada 1 item produk', 'warning', { position: 'top-right' });
        return;
    }

    const formData = $(this).serializeArray();
    const data = {};

    formData.forEach(item => {
        // Handle nested array notation: details[0][product_variant_id]
        const match = item.name.match(/^details\[(\d+)\]\[(.+)\]$/);
        if (match) {
            if (!data.details) data.details = {};
            if (!data.details[match[1]]) data.details[match[1]] = {};
            data.details[match[1]][match[2]] = item.value;
        } else {
            data[item.name] = item.value;
        }
    });

    // Convert details object to array
    if (data.details) {
        data.details = Object.values(data.details);
    }

    $('#btn-save').prop('disabled', true).html('<em class="icon ni ni-loader"></em> Menyimpan...');

    $.ajax({
        url: '/inventory/stock-opname/store',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function (response) {
            if (response.status) {
                NioApp.Toast(response.message, 'success', { position: 'top-right' });
                setTimeout(() => {
                    window.location.href = '/inventory/stock-opname';
                }, 1000);
            } else {
                NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                $('#btn-save').prop('disabled', false).html('<em class="icon ni ni-save"></em><span>Simpan (Draft)</span>');
            }
        },
        error: function (xhr) {
            handleAjaxError(xhr);
            $('#btn-save').prop('disabled', false).html('<em class="icon ni ni-save"></em><span>Simpan (Draft)</span>');
        }
    });
});
