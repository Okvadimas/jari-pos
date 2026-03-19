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
