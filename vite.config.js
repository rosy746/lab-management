import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/inventory.css',
                'resources/css/schedule.css',
                'resources/css/rekap.css',      // rekap publik
                'resources/js/app.js',
                'resources/js/inventory.js',
                'resources/js/schedule.js',
            ],
            refresh: true,
        }),
    ],
});