import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/inventory.css',  // tambah ini
                'resources/js/app.js',
                'resources/js/inventory.js',    // tambah ini
            ],
            refresh: true,
        }),
    ],
});