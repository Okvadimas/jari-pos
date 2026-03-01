$(document).ready(function() {
    console.log('Purchasing Form page scripts loaded');
    
    let productVariants = [];
    let rowIndex = 0;

    // Load product variants for dropdown (will add rows after loading)
    loadVariants();

    // Initialize datepicker
    if($('.date-picker').length) {
        $('.date-picker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    }

    // Add item button handler
    $('#btn-add-item').on('click', function() {
        addItemRow();
    });

    // Remove item button handler
    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // Calculate subtotal when quantity or cost changes
    $(document).on('input', '.item-quantity, .item-cost', function() {
        let row = $(this).closest('tr');
        calculateRowSubtotal(row);
        calculateTotal();
    });

    // Form submission
    $('#form-data').submit(function(e) {
        e.preventDefault();

        // Validate at least one item
        let itemRows = $('#item-rows tr');
        if (itemRows.length === 0) {
            NioApp.Toast('Minimal harus ada 1 item pembelian', 'warning', { position: 'top-right' });
            return;
        }

        // Validate each row has required fields
        let valid = true;
        itemRows.each(function() {
            let productId = $(this).find('.item-product').val();
            let quantity = $(this).find('.item-quantity').val();
            let cost = $(this).find('.item-cost').val();

            if (!productId || !quantity || !cost) {
                valid = false;
                return false; // break loop
            }
        });

        if (!valid) {
            NioApp.Toast('Lengkapi semua field item pembelian', 'warning', { position: 'top-right' });
            return;
        }

        let $btn = $('#btn-save');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Menyimpan</span>');

        // Build form data
        let formData = {
            id: $('input[name="id"]').val(),
            supplier_name: $('#supplier_name').val(),
            purchase_date: $('#purchase_date').val(),
            reference_note: $('#reference_note').val(),
            details: []
        };

        // Collect item data
        itemRows.each(function() {
            formData.details.push({
                product_variant_id: $(this).find('.item-product').val(),
                quantity: parseInt($(this).find('.item-quantity').val()) || 0,
                cost_price_per_item: parseFloat($(this).find('.item-cost').val().replace(/\./g, '').replace(',', '.')) || 0
            });
        });

        $.ajax({
            url: '/transaction/purchasing/store',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('<em class="icon ni ni-save"></em><span>Simpan</span>');
            },
            success: function(response) {
                if(response.status) {
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function() {
                        window.location.href = '/transaction/purchasing';
                    }, 1000);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function(response) {
                handleAjaxError(response);
            }
        });
    });

    function loadVariants() {
        $.ajax({
            url: '/utility/variants',
            type: 'GET',
            success: function(response) {
                productVariants = response.data || response;
                
                // Initialize rows AFTER variants are loaded
                if (window.existingDetails && window.existingDetails.length > 0) {
                    window.existingDetails.forEach(function(detail) {
                        addItemRow(detail);
                    });
                } else {
                    // Add one empty row by default
                    addItemRow();
                }
            },
            error: function(xhr) {
                console.error('Failed to load variants:', xhr);
            }
        });
    }

    function addItemRow(data = null) {
        rowIndex++;
        let html = `
            <tr data-row="${rowIndex}">
                <td>
                    <select class="form-select item-product" name="details[${rowIndex}][product_variant_id]" data-search="on">
                        <option value="">Pilih Produk</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control text-end item-quantity" name="details[${rowIndex}][quantity]" min="1" value="${data ? data.quantity : ''}" placeholder="0">
                </td>
                <td>
                    <input type="text" class="form-control text-end item-cost" name="details[${rowIndex}][cost_price_per_item]" value="${data ? formatNumber(data.cost_price_per_item) : ''}" placeholder="0">
                </td>
                <td class="text-end item-subtotal">Rp 0</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-item">
                        <em class="icon ni ni-trash"></em>
                    </button>
                </td>
            </tr>
        `;
        $('#item-rows').append(html);

        // Initialize Select2 for the new row
        let $select = $(`tr[data-row="${rowIndex}"] .item-product`);
        $select.select2({
            placeholder: 'Pilih Produk',
            allowClear: true,
            data: productVariants.map(v => ({ id: v.id, text: v.text })),
            width: '100%'
        });

        // Set value if editing
        if (data && data.product_variant_id) {
            // Add the option and set it as selected
            $select.append(new Option(data.product_name, data.product_variant_id, true, true)).trigger('change');
        }

        // Calculate subtotal if data exists
        if (data) {
            calculateRowSubtotal($(`tr[data-row="${rowIndex}"]`));
            calculateTotal();
        }
    }

    function initSelect2() {
        // Re-initialize select2 with loaded data
        $('.item-product').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: 'Pilih Produk',
                    allowClear: true,
                    data: productVariants.map(v => ({ id: v.id, text: v.text })),
                    width: '100%'
                });
            }
        });
    }

    function calculateRowSubtotal(row) {
        let quantity = parseInt(row.find('.item-quantity').val()) || 0;
        let cost = parseFloat(row.find('.item-cost').val().replace(/\./g, '').replace(',', '.')) || 0;
        let subtotal = quantity * cost;
        row.find('.item-subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
    }

    function calculateTotal() {
        let total = 0;
        $('#item-rows tr').each(function() {
            let quantity = parseInt($(this).find('.item-quantity').val()) || 0;
            let cost = parseFloat($(this).find('.item-cost').val().replace(/\./g, '').replace(',', '.')) || 0;
            total += quantity * cost;
        });
        $('#grand-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
});
