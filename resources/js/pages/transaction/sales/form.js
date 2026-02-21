$(document).ready(function() {
    console.log('Sales Form page scripts loaded');
    
    let productVariants = [];
    let rowIndex = 0;

    // Load product variants for dropdown (will add rows after loading)
    loadVariants();
    loadPaymentMethods();

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

    // Calculate subtotal when quantity, price, or discount changes
    $(document).on('input', '.item-quantity, .item-price, .item-discount', function() {
        let row = $(this).closest('tr');
        calculateRowSubtotal(row);
        calculateTotal();
    });

    // Update totals when manual discount changes
    $('#discount_amount').on('input', function() {
        calculateTotal();
    });

    // Form submission
    $('#form-data').submit(function(e) {
        e.preventDefault();

        // Validate at least one item
        let itemRows = $('#item-rows tr');
        if (itemRows.length === 0) {
            NioApp.Toast('Minimal harus ada 1 item penjualan', 'warning', { position: 'top-right' });
            return;
        }

        // Validate each row has required fields
        let valid = true;
        itemRows.each(function() {
            let productId = $(this).find('.item-product').val();
            let quantity = $(this).find('.item-quantity').val();
            let price = $(this).find('.item-price').val();

            if (!productId || !quantity || !price) {
                valid = false;
                return false; // break loop
            }
        });

        if (!valid) {
            NioApp.Toast('Lengkapi semua field item penjualan', 'warning', { position: 'top-right' });
            return;
        }

        let $btn = $('#btn-save');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Menyimpan</span>');

        // Build form data
        let formData = {
            id: $('input[name="id"]').val(),
            customer_name: $('#customer_name').val(),
            order_date: $('#order_date').val(),
            payment_method_id: $('#payment_method_id').val() || null,
            discount_amount: parseFloat($('#discount_amount').val().replace(/\./g, '').replace(',', '.')) || 0,
            details: []
        };

        // Collect item data
        itemRows.each(function() {
            formData.details.push({
                product_variant_id: $(this).find('.item-product').val(),
                quantity: parseInt($(this).find('.item-quantity').val()) || 0,
                sell_price: parseFloat($(this).find('.item-price').val().replace(/\./g, '').replace(',', '.')) || 0,
                discount_amount: parseFloat($(this).find('.item-discount').val().replace(/\./g, '').replace(',', '.')) || 0
            });
        });

        $.ajax({
            url: '/transaction/sales/store',
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
                        window.location.href = '/transaction/sales';
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
                productVariants = response;
                
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

    function loadPaymentMethods() {
        $.ajax({
            url: '/utility/payment-methods',
            type: 'GET',
            success: function(response) {
                let $select = $('#payment_method_id');
                response.forEach(function(item) {
                    $select.append(new Option(item.name, item.id));
                });

                // Set selected value if editing
                if (window.existingPaymentMethodId) {
                    $select.val(window.existingPaymentMethodId);
                }
            },
            error: function(xhr) {
                console.error('Failed to load payment methods:', xhr);
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
                    <input type="text" class="form-control text-end item-price" name="details[${rowIndex}][sell_price]" value="${data ? formatNumber(data.sell_price) : ''}" placeholder="0">
                </td>
                <td>
                    <input type="text" class="form-control text-end item-discount" name="details[${rowIndex}][discount_amount]" value="${data ? formatNumber(data.discount_amount) : '0'}" placeholder="0">
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
            $select.append(new Option(data.product_name, data.product_variant_id, true, true)).trigger('change');
        }

        // Calculate subtotal if data exists
        if (data) {
            calculateRowSubtotal($(`tr[data-row="${rowIndex}"]`));
            calculateTotal();
        }
    }

    function calculateRowSubtotal(row) {
        let quantity = parseInt(row.find('.item-quantity').val()) || 0;
        let price = parseFloat(row.find('.item-price').val().replace(/\./g, '').replace(',', '.')) || 0;
        let discount = parseFloat(row.find('.item-discount').val().replace(/\./g, '').replace(',', '.')) || 0;
        let subtotal = (quantity * price) - discount;
        row.find('.item-subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
    }

    function calculateTotal() {
        let total = 0;
        $('#item-rows tr').each(function() {
            let quantity = parseInt($(this).find('.item-quantity').val()) || 0;
            let price = parseFloat($(this).find('.item-price').val().replace(/\./g, '').replace(',', '.')) || 0;
            let discount = parseFloat($(this).find('.item-discount').val().replace(/\./g, '').replace(',', '.')) || 0;
            total += (quantity * price) - discount;
        });

        let manualDiscount = parseFloat($('#discount_amount').val().replace(/\./g, '').replace(',', '.')) || 0;
        let finalTotal = total - manualDiscount;

        $('#grand-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
        $('#discount-display').text('Rp ' + new Intl.NumberFormat('id-ID').format(manualDiscount));
        $('#final-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(finalTotal));
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
});
