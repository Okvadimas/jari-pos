/**
 * Register Page JavaScript
 * 
 * This file contains JavaScript specific to the register page.
 * Load this in your register blade template with:
 * @vite('resources/js/pages/auth/register.js')
 */

$(document).ready(function() {
    console.log('Register page scripts loaded');

    $('#form-data').submit(function(e) {
        e.preventDefault();
        
        let $btn = $('#btn-submit');
        $btn.attr('disabled', true);
        $btn.html('<em class="icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></em><span> Mendaftar...</span>');

        $.ajax({
            url: '/register',
            type: 'POST',
            data: $(this).serialize(),
            complete: function() {
                $btn.attr('disabled', false);
                $btn.html('Daftar Sekarang');
            },
            success: function(response) {
                if (response.status) {
                    NioApp.Toast(response.message, 'success', {
                        position: 'top-right',
                        duration: 3000
                    });
                    setTimeout(() => {
                        window.location.href = '/email/verify';
                    }, 2000);
                } else {
                    NioApp.Toast(response.message, 'error', {
                        position: 'top-right',
                        duration: 3000
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr);
            }
        });        
    });

    // Tab Navigation Logic
    const $btnNext = $('#btn-next-step');
    const $btnPrev = $('#btn-prev-step');
    const $tabStep2 = $('#tab-step-2');
    const $tabStep1 = $('a[href="#step-1"]');
    
    // Tab style updating
    function updateTabStyles($activeTab) {
        $('.nav-tabs-s1 .nav-link').removeClass('active text-primary').addClass('text-secondary').css('border-bottom-color', 'transparent');
        $('.nav-tabs-s1 .nav-link .icon').removeClass('text-primary').addClass('text-secondary');
        
        $activeTab.addClass('active text-primary').removeClass('text-secondary disabled').css('border-bottom-color', 'var(--app-primary)');
        $activeTab.find('.icon').addClass('text-primary').removeClass('text-secondary');
    }

    // Form inputs to validate before next step
    const step1Inputs = ['name', 'username', 'email', 'password'];
    
    $btnNext.on('click', function() {
        let isValid = true;
        step1Inputs.forEach(id => {
            const input = document.getElementById(id);
            if(input && !input.checkValidity()) {
                input.reportValidity();
                isValid = false;
            }
        });
        
        if(isValid) {
            let tab = new bootstrap.Tab($tabStep2[0]);
            tab.show();
            updateTabStyles($tabStep2);
        }
    });
    
    $btnPrev.on('click', function() {
        let tab = new bootstrap.Tab($tabStep1[0]);
        tab.show();
        updateTabStyles($tabStep1);
    });
});
