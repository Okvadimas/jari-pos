/**
 * Lock Screen Page JavaScript
 *
 * Handles unlock form submission and live clock display.
 * Load this in your lock-screen blade template with:
 * @vite('resources/js/pages/auth/lock-screen.js')
 */

$(document).ready(function () {
    console.log('Lock screen scripts loaded');

    // ─── Live Clock ──────────────────────────────────────────
    function updateClock() {
        const now = new Date();

        // Time
        const hours   = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        $('#lock-time').text(`${hours}:${minutes}:${seconds}`);

        // Date
        const days   = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dayName   = days[now.getDay()];
        const date      = now.getDate();
        const monthName = months[now.getMonth()];
        const year      = now.getFullYear();
        $('#lock-date').text(`${dayName}, ${date} ${monthName} ${year}`);
    }

    updateClock();
    setInterval(updateClock, 1000);

    // ─── Unlock Form ─────────────────────────────────────────
    $('#form-unlock').submit(function (e) {
        e.preventDefault();

        let $btn = $('#btn-unlock');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span> Memverifikasi...</span>');

        $.ajax({
            url: '/unlock-screen',
            type: 'POST',
            data: $(this).serialize(),
            complete: function () {
                $btn.attr('disabled', false);
                $btn.html('<em class="icon ni ni-unlock me-1"></em> Buka Kunci');
            },
            success: function (response) {
                if (response.status) {
                    // Success: show green icon + redirect
                    $('.lock-screen-container').addClass('unlock-success');
                    $('.lock-icon').removeClass('ni-lock-alt').addClass('ni-unlock');
                    NioApp.Toast(response.message, 'success', { position: 'top-right' });

                    setTimeout(function () {
                        window.location.href = '/dashboard';
                    }, 800);
                } else {
                    NioApp.Toast(response.message, 'warning', { position: 'top-right' });
                }
            },
            error: function (xhr) {
                // Shake the form on error
                $('.lock-form-wrapper .card').addClass('shake');
                setTimeout(function () {
                    $('.lock-form-wrapper .card').removeClass('shake');
                }, 600);

                // Clear password field
                $('#password').val('').focus();

                handleAjaxError(xhr);
            }
        });
    });

    // ─── Prevent Back Navigation ─────────────────────────────
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
});
