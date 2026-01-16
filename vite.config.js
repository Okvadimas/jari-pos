import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/scripts.js',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/management/company/index.js',
                'resources/js/pages/management/company/form.js',
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
