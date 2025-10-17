import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import legacy from '@vitejs/plugin-legacy';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/css/app.scss',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/pages/analytics-dashboard.init.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        legacy({
            targets: ['defaults', 'not IE 11'],
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': resolve(__dirname, 'node_modules/bootstrap'),
            '~': resolve(__dirname, 'node_modules'),
            '@': resolve(__dirname, 'resources'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        manifest: true,
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['bootstrap', '@popperjs/core', 'axios'],
                    charts: ['chart.js'],
                    ui: ['sweetalert2', 'flatpickr', 'select2'],
                    datatables: ['datatables.net', 'datatables.net-bs5'],
                    alpine: ['alpinejs'],
                },
            },
        },
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
    server: {
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    optimizeDeps: {
        include: [
            'bootstrap',
            '@popperjs/core',
            'chart.js',
            'aos',
            'axios',
            'sweetalert2',
            'flatpickr',
            'select2',
            'alpinejs',
            'datatables.net',
            'datatables.net-bs5'
        ],
    },
});
