import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: true,
        origin: process.env.VITE_SERVER_URI || 'http://localhost:5173',
        hmr: process.env.VITE_SERVER_URI
            ? {
                  host: 'vite.orewire-laravel.ddev.site',
                  protocol: 'wss',
                  clientPort: 443,
              }
            : undefined,
        cors: process.env.VITE_SERVER_URI
            ? { origin: [/^https?:\/\/.*\.ddev\.site(:\d+)?$/] }
            : undefined,
    },
});
