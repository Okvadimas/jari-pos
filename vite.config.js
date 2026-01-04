import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/script.js',
                'resources/css/app.css',
                'resources/js/app.js',
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
        watch: {
            // Prevent infinite reload from Laravel's compiled views
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
