import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // ================================
                // DashLite Core CSS
                // ================================
                'resources/css/dashlite.css',
                'resources/css/dashlite.rtl.css',
                'resources/css/theme.css',
                'resources/css/style-email.css',

                // DashLite Theme Skins
                'resources/css/skins/theme-blue.css',
                'resources/css/skins/theme-bluelite.css',
                'resources/css/skins/theme-egyptian.css',
                'resources/css/skins/theme-green.css',
                'resources/css/skins/theme-red.css',

                // DashLite CSS Libraries
                'resources/css/libs/bootstrap-icons.css',
                'resources/css/libs/fontawesome-icons.css',
                'resources/css/libs/jstree.css',
                'resources/css/libs/themify-icons.css',

                // DashLite CSS Editors
                'resources/css/editors/quill.css',
                'resources/css/editors/quill.rtl.css',
                'resources/css/editors/summernote.css',
                'resources/css/editors/summernote.rtl.css',
                'resources/css/editors/tinymce.css',
                'resources/css/editors/tinymce.rtl.css',

                // ================================
                // DashLite Core JS
                // ================================
                'resources/js/scripts.js',
                'resources/js/chart.js',
                'resources/js/dragula.js',
                'resources/js/editors.js',
                'resources/js/listbox.js',
                'resources/js/map.js',
                'resources/js/sweetalert.js',
                'resources/js/toastr.js',
                'resources/js/tree.js',

                // DashLite JS Libraries
                'resources/js/libs/datatable-btns.js',
                'resources/js/libs/dragula.js',
                'resources/js/libs/dual-listbox.js',
                'resources/js/libs/fullcalendar.js',
                'resources/js/libs/jkanban.js',
                'resources/js/libs/jqvmap.js',
                'resources/js/libs/jstree.js',
                'resources/js/libs/tagify.js',
                'resources/js/libs/editors/quill.js',
                'resources/js/libs/editors/summernote.js',
                'resources/js/libs/editors/tinymce.js',

                // ================================
                // Project Core Assets
                // ================================
                'resources/css/app.css',
                'resources/js/app.js',

                // ================================
                // Auth Pages
                // ================================
                'resources/js/pages/auth/login.js',
                'resources/js/pages/auth/register.js',
                'resources/css/pages/auth/login.css',
                'resources/css/pages/auth/register.css',

                // ================================
                // Dashboard
                // ================================
                'resources/js/pages/dashboard/index.js',
                'resources/css/pages/dashboard/dashboard.css',

                // ================================
                // Landing Page
                // ================================
                'resources/js/pages/landing/landing.js',

                // ================================
                // Management - User
                // ================================
                'resources/js/pages/management/user/index.js',
                'resources/js/pages/management/user/form.js',

                // ================================
                // Management - Akses
                // ================================
                'resources/js/pages/management/akses/index.js',
                'resources/js/pages/management/akses/form.js',

                // ================================
                // Management - Company
                // ================================
                'resources/js/pages/management/company/index.js',
                'resources/js/pages/management/company/form.js',

                // ================================
                // Management - Payment
                // ================================
                'resources/js/pages/management/payment/index.js',
                'resources/js/pages/management/payment/form.js',

                // ================================
                // Inventory - Unit
                // ================================
                'resources/js/pages/inventory/unit/index.js',
                'resources/js/pages/inventory/unit/form.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Modern Dart Sass API (required for SCSS compilation)
                api: 'modern-compiler',
            },
        },
    },
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        watch: {
            // Prevent infinite reload from Laravel's compiled views
            ignored: ['**/storage/framework/views/**'],
            usePolling: true,
        },
    },
});
