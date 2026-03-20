$(document).ready(function() {
    // ---- Edit Profile ----
    $('#btn-save-profile').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var originalText = btn.text();
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '/profile/update',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-update-profile').serialize(),
            success: function(response) {
                if (response.status) {
                    $('#profile-edit').modal('hide');
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });
                    setTimeout(function() {
                        refreshProfileData();
                    }, 1000);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                NioApp.Toast(msg, 'error', { position: 'top-right' });
            },
            complete: function() {
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // ---- Crop & Update Photo ----
    var cropper;
    var $image = $('#image-to-crop');
    var $inputImage = $('#upload-avatar');
    var $avatarPlaceholder = $('#avatar-placeholder');
    
    // Safely check if elements exist
    if ($image.length > 0 && $inputImage.length > 0) {
        var image = $image[0];

        $('#updatePhotoModal').on('shown.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
            }
            if ($(image).attr('src')) {
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                });
            }
        }).on('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        $inputImage.on('change', function (e) {
            var files = e.target.files;
            
            var done = function (url) {
                $inputImage.val('');
                if ($avatarPlaceholder.length) {
                    $avatarPlaceholder.hide();
                }
                $(image).show();
                image.src = url;
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                });
            };
            
            var reader;
            var file;

            if (files && files.length > 0) {
                file = files[0];

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (event) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        $('#btn-crop-upload').on('click', function() {
            if (!cropper) {
                NioApp.Toast('Silakan pilih gambar terlebih dahulu', 'warning', { position: 'top-right' });
                return;
            };

            var canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
            });

            if (!canvas) {
                NioApp.Toast('Silakan pilih gambar terlebih dahulu', 'warning', { position: 'top-right' });
                return;
            }

            var btn = $(this);
            var originalText = btn.text();
            btn.prop('disabled', true).text('Mengupload...');

            var base64data = canvas.toDataURL('image/jpeg');

            $.ajax({
                url: '/profile/update-picture',
                type: 'POST',
                data: {
                    _token: window.CSRF_TOKEN,
                    image: base64data
                },
                success: function(response) {
                    if (response.status) {
                        $('#updatePhotoModal').modal('hide');
                        NioApp.Toast(response.message, 'success', { position: 'top-right' });
                        setTimeout(function() {
                            refreshProfileData();
                        }, 1000);
                    } else {
                        NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                    }
                },
                error: function(xhr) {
                    let msg = 'Gagal upload foto.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    NioApp.Toast(msg, 'error', { position: 'top-right' });
                },
                complete: function() {
                    btn.prop('disabled', false).text(originalText);
                }
            });
        });
    }
});

// Refresh Data Informasi Akun
function refreshProfileData() {
    $.ajax({
        url: '/profile/data',
        type: 'GET',
        success: function(response) {
            if (response.status) {
                $('#data-name').text(response.data.name);
                $('#data-phone').text(response.data.phone);
                $('#data-birth-date').text(response.data.birth_date ? moment(response.data.birth_date).format('DD MMM, YYYY') : '-');
                $('#data-address').text(response.data.address);
                $('#profile-name').text(response.data.name);

                if (response.data.profile_picture) {
                    $('#profile-picture').attr('src', response.data.profile_picture);
                }
            }
        }
    });
}

// ---- Subscription & Checkout Logic ----
$(document).ready(function() {
    let activeVoucher = '';
    let activeAffiliate = '';
    
    // Format Rupiah helper
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    // Pindah ke Modal awal (reset)
    $('#upgradeModal').on('show.bs.modal', function() {
        $('#step-1-duration').show();
        $('#step-2-payment').hide();
        activeVoucher = '';
        activeAffiliate = '';
        $('#active-vouchers-container').hide();
        $('#badge-voucher').hide().text('');
        $('#badge-affiliate').hide().text('');
        $('#voucher_code').val('');
        $('#voucher-message').text('');
        
        // Pilih durasi ter-awal default
        $('.package-duration-radio:first').prop('checked', true).trigger('change');
    });

    // Ketika durasi ganti, update UI dan hitung ulang kalau ada voucher
    $('.package-duration-radio').on('change', function() {
        let price = $(this).data('price');
        $('#label-subtotal').text(formatRupiah(price));
        calculateVouchers();
    });

    // Kalkulasi voucher via AJAX
    function calculateVouchers() {
        let packageId = $('#selected_package_id').val();
        let duration = $('.package-duration-radio:checked').val();
        let originalPrice = $('.package-duration-radio:checked').data('price');
        
        if (!activeVoucher && !activeAffiliate) {
            // Update UI normal tanpa diskon
            $('#row-discount').hide();
            $('#row-affiliate').hide();
            $('#label-grandtotal').text(formatRupiah(originalPrice));
            $('.copy-grandtotal').text(formatRupiah(originalPrice));
            
            if (typeof finalNominalValue !== 'undefined') finalNominalValue = originalPrice;
            
            return;
        }

        $.ajax({
            url: '/profile/subscription/check-vouchers',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                package_id: packageId,
                duration_months: duration,
                voucher_code: activeVoucher,
                affiliate_code: activeAffiliate
            },
            success: function(res) {
                if (res.status && res.data) {
                    if (res.data.discount_amount > 0) {
                        $('#row-discount').show();
                        $('#label-discount').text('-' + formatRupiah(res.data.discount_amount));
                    } else {
                        $('#row-discount').hide();
                    }
                    
                    if (res.data.affiliate_discount_amount > 0) {
                        $('#row-affiliate').show();
                        $('#label-affiliate').text('-' + formatRupiah(res.data.affiliate_discount_amount));
                    } else {
                        $('#row-affiliate').hide();
                    }

                    $('#label-grandtotal').text(formatRupiah(res.data.final_amount));
                    $('.copy-grandtotal').text(formatRupiah(res.data.final_amount));
                    
                    if (typeof finalNominalValue !== 'undefined') finalNominalValue = res.data.final_amount;
                }
            }
        });
    }

    // Apply Voucher Button
    $('#btn-apply-voucher').on('click', function() {
        let code = $('#voucher_code').val().trim().toUpperCase();
        if (!code) return;

        let packageId = $('#selected_package_id').val();
        let duration = $('.package-duration-radio:checked').val();
        
        let testVoucher = activeVoucher;
        let testAffiliate = activeAffiliate;

        if (!activeVoucher) testVoucher = code;
        else if (!activeAffiliate && code !== activeVoucher) testAffiliate = code;
        else {
            $('#voucher-message').text('Maksimal 2 kode (Reguler & Affiliate) telah digunakan.').addClass('text-danger').removeClass('text-success');
            return;
        }

        let btn = $(this);
        let origText = btn.text();
        btn.prop('disabled', true).text('...');

        $.ajax({
            url: '/profile/subscription/check-vouchers',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                package_id: packageId,
                duration_months: duration,
                voucher_code: testVoucher,
                affiliate_code: testAffiliate
            },
            success: function(res) {
                if (res.status) {
                    $('#voucher-message').text('Kode berhasil diterapkan!').addClass('text-success').removeClass('text-danger');
                    $('#voucher_code').val('');
                    
                    // Update active tokens based on response values
                    if (res.data.discount_amount > 0 && testVoucher) {
                        activeVoucher = testVoucher;
                        $('#badge-voucher').show().text(activeVoucher + ' \u2715').attr('title', 'Klik untuk hapus').css('cursor', 'pointer');
                    }
                    if (res.data.affiliate_discount_amount > 0 && testAffiliate) {
                        activeAffiliate = testAffiliate;
                        $('#badge-affiliate').show().text(activeAffiliate + ' \u2715').attr('title', 'Klik untuk hapus').css('cursor', 'pointer');
                    }
                    
                    if (activeVoucher || activeAffiliate) $('#active-vouchers-container').show();
                    calculateVouchers();
                } else {
                    $('#voucher-message').text(res.message).addClass('text-danger').removeClass('text-success');
                }
            },
            complete: function() {
                btn.prop('disabled', false).text(origText);
            }
        });
    });

    // Remove tags
    $('#badge-voucher').on('click', function() {
        activeVoucher = ''; $(this).hide();
        if(!activeAffiliate) $('#active-vouchers-container').hide();
        calculateVouchers();
    });
    $('#badge-affiliate').on('click', function() {
        activeAffiliate = ''; $(this).hide();
        if(!activeVoucher) $('#active-vouchers-container').hide();
        calculateVouchers();
    });

    // Navigasi Flow
    $('#btn-next-step').on('click', function() {
        $('#step-1-duration').hide();
        $('#step-2-payment').show();
    });
    $('#btn-prev-step').on('click', function() {
        $('#step-2-payment').hide();
        $('#step-1-duration').show();
    });

    // Fitur Copy Nominal
    let finalNominalValue = 0; // Var global sementara untuk nyimpan nominal utuh

    $('#btn-copy-nominal').on('click', function() {
        if (finalNominalValue > 0) {
            navigator.clipboard.writeText(finalNominalValue).then(function() {
                NioApp.Toast('Nominal Rp ' + formatRupiah(finalNominalValue).replace('Rp\xa0', '') + ' disalin!', 'info', {position: 'top-right'});
            });
        }
    });

    // Checkout Submit (WA Konfirmasi)
    $('#btn-checkout').on('click', function() {
        let packageId = $('#selected_package_id').val();
        let duration = $('.package-duration-radio:checked').val();
        let btn = $(this);
        let origText = btn.html();

        btn.prop('disabled', true).html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span>Memproses...</span>');

        $.ajax({
            url: '/profile/subscription/checkout',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                package_id: packageId,
                duration_months: duration,
                voucher_code: activeVoucher,
                affiliate_code: activeAffiliate
            },
            success: function(res) {
                if(res.status && res.data) {
                    $('#upgradeModal').modal('hide');
                    NioApp.Toast(res.message, 'success', {position: 'top-right'});
                    
                    // Buka WhatsApp
                    let waText = "Halo Admin Jari POS, saya telah melakukan pemesanan Paket Jempol selama " + duration + " Bulan sejumlah " + formatRupiah(res.data.final_amount) + " dengan nomor tagihan *" + res.data.sale_number + "*. Mohon segera diproses. Terima kasih.";
                    window.open("https://wa.me/6281649000020?text=" + encodeURIComponent(waText), "_blank");
                } else {
                    NioApp.Toast(res.message, 'error', {position: 'top-right'});
                }
            },
            error: function(xhr) {
                NioApp.Toast('Gagal memproses pesanan. Silahkan hubungi admin.', 'error', {position: 'top-right'});
            },
            complete: function() {
                btn.prop('disabled', false).html(origText);
            }
        });
    });
});
